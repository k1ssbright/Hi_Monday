<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Partner\Partner;
use App\Models\Sales\Opportunity;
use App\Models\Sales\OpportunityLine;
use App\Models\ProductService\ProductServiceItem;
use App\Services\SalesFlowService;
use App\Services\SalesJourneyPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OpportunityController extends Controller
{
    use ResolvesBusinessContext;

    public function __construct(private SalesFlowService $salesFlow) {}

    public function index(Request $request): View
    {
        $businessId = $this->businessId();
        $query = Opportunity::query()
            ->where('business_id', $businessId)
            ->with(['partner', 'owner.employee.user']);

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('opportunity_no', 'like', "%{$q}%")
                    ->orWhere('name', 'like', "%{$q}%")
                    ->orWhereHas('partner', function ($p) use ($q) {
                        $p->where('partner_name_th', 'like', "%{$q}%")
                          ->orWhere('partner_name_en', 'like', "%{$q}%");
                    });
            });
        }

        if ($request->filled('stage')) {
            $query->where('stage', $request->input('stage'));
        }

        $opportunities = $query->latest()->paginate(20)->withQueryString();

        return view('office.opportunities.index', compact('opportunities'));
    }

    public function show(Opportunity $opportunity): View
    {
        $this->assertOwned($opportunity);
        $opportunity->load([
            'partner',
            'partnerBranch',
            'partnerContact',
            'owner.employee.user',
            'lines.item',
            'quotations.currentRevision',
            'salesOrders',
            'convertedFromLead',
        ]);

        $salesJourney = app(SalesJourneyPresenter::class)->for($opportunity);

        return view('office.opportunities.show', compact('opportunity', 'salesJourney'));
    }

    public function create(): View
    {
        $businessId = $this->businessId();

        return view('office.opportunities.form', [
            'opportunity' => new Opportunity([
                'stage' => 'discovery',
                'probability' => 10,
                'currency' => 'THB',
            ]),
            'partners' => Partner::query()
                ->where('business_id', $businessId)
                ->orderBy('partner_name_th')
                ->get(),
            'items' => ProductServiceItem::query()
                ->where('business_id', $businessId)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $businessId = $this->businessId();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'partner_id' => ['nullable', 'uuid'],
            'partner_name_quick' => ['nullable', 'string', 'max:255'],
            'expected_close_date' => ['nullable', 'date'],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
            'line_description' => ['nullable', 'string'],
            'line_item_id' => ['nullable', 'integer'],
            'line_qty' => ['nullable', 'numeric', 'min:0.01'],
            'line_unit_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $opportunity = DB::transaction(function () use ($data, $businessId) {
            $partnerId = null;
            $partnerBranchId = null;
            $partnerContactId = null;

            if (! empty($data['partner_id'])) {
                $partner = Partner::query()
                    ->where('business_id', $businessId)
                    ->findOrFail($data['partner_id']);
                $partnerId = $partner->id;
                $partnerBranchId = $partner->branches()->value('id');
                $partnerContactId = $partner->contacts()->value('id');
            } elseif (! empty($data['partner_name_quick'])) {
                $partner = Partner::create([
                    'business_id' => $businessId,
                    'partner_type' => 3,
                    'partner_vat_status' => 0,
                    'partner_name_th' => trim((string) $data['partner_name_quick']),
                    'create_by' => $this->createBy(),
                ]);
                $partnerId = $partner->id;
                $partnerBranchId = $partner->branches()->value('id');
                $partnerContactId = $partner->contacts()->value('id');
            }

            $opportunity = new Opportunity([
                'business_id' => $businessId,
                'business_branch_id' => currentBusinessBranchId(),
                'name' => $data['name'],
                'stage' => 'discovery',
                'probability' => 10,
                'expected_close_date' => $data['expected_close_date'] ?? null,
                'estimated_value' => $data['estimated_value'] ?? 0,
                'currency' => strtoupper($data['currency'] ?? 'THB'),
                'partner_id' => $partnerId,
                'partner_branch_id' => $partnerBranchId,
                'partner_contact_id' => $partnerContactId,
                'owner_employee_branch_id' => currentEmployeeBranchId(),
                'create_by' => $this->createBy(),
            ]);
            $this->salesFlow->allocateOpportunityNo($opportunity);
            $opportunity->save();

            $lineDescription = trim((string) ($data['line_description'] ?? ''));
            if ($lineDescription !== '' || ! empty($data['line_item_id']) || ((float) ($data['line_unit_price'] ?? 0)) > 0) {
                $qty = (float) ($data['line_qty'] ?? 1);
                $unitPrice = (float) ($data['line_unit_price'] ?? 0);
                OpportunityLine::create([
                    'business_id' => $businessId,
                    'opportunity_id' => $opportunity->id,
                    'product_service_item_id' => $data['line_item_id'] ?? null,
                    'description' => $lineDescription !== '' ? $lineDescription : 'รายการ',
                    'quantity' => $qty > 0 ? $qty : 1,
                    'unit_price_estimate' => $unitPrice,
                    'line_total_estimate' => round(($qty > 0 ? $qty : 1) * $unitPrice, 2),
                    'create_by' => $this->createBy(),
                ]);

                if (! $opportunity->estimated_value || (float) $opportunity->estimated_value <= 0) {
                    $opportunity->estimated_value = round(($qty > 0 ? $qty : 1) * $unitPrice, 2);
                    $opportunity->save();
                }
            }

            return $opportunity;
        });

        return redirect()->route('office.opportunities.show', $opportunity)
            ->with('success', 'สร้าง Opportunity เรียบร้อยแล้ว');
    }

    public function edit(Opportunity $opportunity): View
    {
        $this->assertOwned($opportunity);
        $businessId = $this->businessId();

        return view('office.opportunities.form', [
            'opportunity' => $opportunity,
            'partners' => Partner::query()
                ->where('business_id', $businessId)
                ->orderBy('partner_name_th')
                ->get(),
            'items' => ProductServiceItem::query()
                ->where('business_id', $businessId)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(Request $request, Opportunity $opportunity): RedirectResponse
    {
        $this->assertOwned($opportunity);
        $businessId = $this->businessId();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'partner_id' => ['nullable', 'uuid'],
            'partner_name_quick' => ['nullable', 'string', 'max:255'],
            'expected_close_date' => ['nullable', 'date'],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'size:3'],
        ]);

        DB::transaction(function () use ($data, $opportunity, $businessId) {
            $partnerId = $opportunity->partner_id;
            $partnerBranchId = $opportunity->partner_branch_id;
            $partnerContactId = $opportunity->partner_contact_id;

            if (! empty($data['partner_id'])) {
                $partner = Partner::query()
                    ->where('business_id', $businessId)
                    ->findOrFail($data['partner_id']);
                $partnerId = $partner->id;
                $partnerBranchId = $partner->branches()->value('id');
                $partnerContactId = $partner->contacts()->value('id');
            } elseif (! empty($data['partner_name_quick'])) {
                $partner = Partner::create([
                    'business_id' => $businessId,
                    'partner_type' => 3,
                    'partner_vat_status' => 0,
                    'partner_name_th' => trim((string) $data['partner_name_quick']),
                    'create_by' => $this->createBy(),
                ]);
                $partnerId = $partner->id;
                $partnerBranchId = $partner->branches()->value('id');
                $partnerContactId = $partner->contacts()->value('id');
            }

            $opportunity->update([
                'name' => $data['name'],
                'expected_close_date' => $data['expected_close_date'] ?? null,
                'estimated_value' => $data['estimated_value'] ?? 0,
                'currency' => strtoupper($data['currency'] ?? 'THB'),
                'partner_id' => $partnerId,
                'partner_branch_id' => $partnerBranchId,
                'partner_contact_id' => $partnerContactId,
            ]);
        });

        return redirect()->route('office.opportunities.show', $opportunity)
            ->with('success', 'แก้ไขข้อมูล Opportunity เรียบร้อยแล้ว');
    }

    public function destroy(Opportunity $opportunity): RedirectResponse
    {
        $this->assertOwned($opportunity);
        $opportunity->delete();

        return redirect()->route('office.opportunities.index')
            ->with('success', 'ลบข้อมูล Opportunity เรียบร้อยแล้ว');
    }

    private function assertOwned(Opportunity $opportunity): void
    {
        if ((string) $opportunity->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}

