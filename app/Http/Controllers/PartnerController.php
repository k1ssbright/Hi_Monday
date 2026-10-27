<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerBranch;
use App\Models\Partner\PartnerContact;
use App\Services\PartnerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PartnerController extends Controller
{
    use ResolvesBusinessContext;

    public function __construct(private PartnerService $partners) {}

    public function index(Request $request): View
    {
        $businessId = $this->businessId();
        $query = trim((string) $request->input('q', ''));
        $status = (string) $request->input('status', 'all');
        $type = (string) $request->input('type', 'all');
        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $partners = Partner::query()
            ->where('business_id', $businessId)
            ->with(['branches' => fn ($q) => $q->orderBy('partner_status')->orderBy('partner_branch_no')])
            ->withCount('branches')
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($inner) use ($query) {
                    $like = '%'.$query.'%';
                    $inner->where('partner_name_th', 'like', $like)
                        ->orWhere('partner_code', 'like', $like)
                        ->orWhere('partner_tax_no', 'like', $like)
                        ->orWhereHas('branches', function ($branchQuery) use ($like) {
                            $branchQuery->where('partner_branch_name_th', 'like', $like)
                                ->orWhere('partner_branch_no', 'like', $like)
                                ->orWhere('partner_tel', 'like', $like)
                                ->orWhere('partner_phone', 'like', $like);
                        });
                });
            })
            ->when($status === 'active', function ($builder) {
                $builder->whereHas('branches', fn ($q) => $q->where('active_status', true));
            })
            ->when($status === 'inactive', function ($builder) {
                $builder->whereDoesntHave('branches', fn ($q) => $q->where('active_status', true));
            })
            ->when(in_array($type, ['customer', 'vendor', 'both'], true), function ($builder) use ($type) {
                $builder->whereHas('branches', function ($branchQuery) use ($type) {
                    if ($type === 'customer') {
                        $branchQuery->where('is_customer', true)->where('is_vendor', false);
                    } elseif ($type === 'vendor') {
                        $branchQuery->where('is_vendor', true)->where('is_customer', false);
                    } else {
                        $branchQuery->where('is_customer', true)->where('is_vendor', true);
                    }
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $totalPartners = Partner::query()->where('business_id', $businessId)->count();
        $totalBranches = PartnerBranch::query()
            ->whereHas('partner', fn ($q) => $q->where('business_id', $businessId))
            ->count();

        return view('office.partner.index', [
            'partners' => $partners,
            'filters' => [
                'q' => $query,
                'status' => $status,
                'type' => $type,
                'per_page' => $perPage,
            ],
            'totalPartners' => $totalPartners,
            'totalBranches' => $totalBranches,
        ]);
    }

    public function create(): View
    {
        $this->businessId();

        return view('office.partner.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedStore($request);
        $partner = $this->partners->create($this->businessId(), $this->createBy(), $data);

        return redirect()
            ->route('office.partners.show', $partner)
            ->with('success', 'บันทึกข้อมูลคู่ค้าเรียบร้อยแล้ว');
    }

    public function show(Partner $partner): View
    {
        $this->assertOwned($partner);
        $partner->load([
            'branches' => fn ($q) => $q->orderBy('partner_status')->orderBy('partner_branch_no'),
            'branches.contactBranches.contact',
            'contacts',
        ]);

        return view('office.partner.show', [
            'partner' => $partner,
        ]);
    }

    public function update(Request $request, Partner $partner): RedirectResponse
    {
        $this->assertOwned($partner);
        $data = $this->validatedUpdate($request, $partner);
        $this->partners->updatePartner($partner, $data);

        return back()->with('success', 'บันทึกการแก้ไขข้อมูลคู่ค้าเรียบร้อยแล้ว');
    }

    public function updateBranch(Request $request, Partner $partner, PartnerBranch $branch): RedirectResponse
    {
        $this->assertOwned($partner);
        $this->assertOwnedBranch($partner, $branch);
        $data = $request->validate([
            'partner_branch_no' => ['required', 'string', 'max:5'],
            'partner_branch_name_th' => ['required', 'string', 'max:255'],
            'partner_tax_no' => ['nullable', 'string', 'max:20'],
            'partner_tel' => ['nullable', 'string', 'max:255'],
            'partner_phone' => ['nullable', 'string', 'max:255'],
            'partner_role' => ['required', 'in:customer,vendor,both'],
            'active_status' => ['nullable', 'boolean'],
        ]);
        $data['active_status'] = $request->boolean('active_status');
        $this->partners->updateBranch($partner, $branch, $data);

        return back()->with('success', 'บันทึกการแก้ไขข้อมูลสาขาเรียบร้อยแล้ว');
    }

    public function toggleStatus(Request $request, Partner $partner): RedirectResponse
    {
        $this->assertOwned($partner);
        $this->partners->toggleActive($partner, $request->boolean('active_status'));

        return back()->with('success', 'อัปเดตสถานะคู่ค้าเรียบร้อยแล้ว');
    }

    private function validatedStore(Request $request): array
    {
        $data = $request->validate([
            'partner_code' => ['required', 'string', 'max:50', $this->uniqueCode()],
            'partner_type' => ['required', 'integer', 'in:1,2,3'],
            'partner_role' => ['required', 'in:customer,vendor,both'],
            'partner_name_th' => ['required', 'string', 'max:255'],
            'partner_tax_no' => ['required', 'string', 'max:20'],
            'partner_vat_status' => ['required', 'integer', 'in:0,1'],
            'partner_branch_no' => ['required', 'string', 'max:5'],
            'partner_branch_name_th' => ['required', 'string', 'max:255'],
            'partner_address' => ['required', 'string', 'max:2000'],
            'partner_tel' => ['required', 'string', 'max:255'],
            'partner_phone' => ['nullable', 'string', 'max:255'],
            'partner_fax' => ['nullable', 'string', 'max:255'],
            'contacts' => ['required', 'array', 'min:1'],
            'contacts.*.prefix' => ['required', 'in:'.implode(',', PartnerContact::PREFIXES)],
            'contacts.*.other_prefix' => ['nullable', 'string', 'max:50'],
            'contacts.*.firstname' => ['nullable', 'string', 'max:255'],
            'contacts.*.surname' => ['nullable', 'string', 'max:255'],
            'contacts.*.phone' => ['nullable', 'string', 'max:50'],
            'contacts.*.line_id' => ['nullable', 'string', 'max:100'],
            'contacts.*.email' => ['nullable', 'email', 'max:255'],
        ], [
            'partner_code.unique' => 'รหัสคู่ค้านี้ถูกใช้แล้วในบริษัทนี้',
        ]);

        $primary = $data['contacts'][0] ?? [];
        if (! filled($primary['firstname'] ?? null) || ! filled($primary['surname'] ?? null)) {
            throw ValidationException::withMessages([
                'contacts.0.firstname' => 'กรุณากรอกชื่อและนามสกุลผู้ติดต่อหลัก',
            ]);
        }
        if (! filled($primary['phone'] ?? null) && ! filled($primary['line_id'] ?? null)) {
            throw ValidationException::withMessages([
                'contacts.0.phone' => 'กรุณากรอกเบอร์โทรศัพท์หรือ Line ID ของผู้ติดต่อหลัก',
            ]);
        }
        if (($primary['prefix'] ?? '') === 'อื่นๆ' && ! filled($primary['other_prefix'] ?? null)) {
            throw ValidationException::withMessages([
                'contacts.0.other_prefix' => 'กรุณาระบุคำนำหน้าเมื่อเลือกอื่นๆ',
            ]);
        }

        return $data;
    }

    private function validatedUpdate(Request $request, Partner $partner): array
    {
        $data = $request->validate([
            'partner_code' => ['required', 'string', 'max:50', $this->uniqueCode($partner->id)],
            'partner_name_th' => ['required', 'string', 'max:255'],
            'partner_tax_no' => ['required', 'string', 'max:20'],
            'partner_role' => ['required', 'in:customer,vendor,both'],
            'active_status' => ['nullable', 'boolean'],
        ], [
            'partner_code.unique' => 'รหัสคู่ค้านี้ถูกใช้แล้วในบริษัทนี้',
        ]);
        $data['active_status'] = $request->boolean('active_status');

        return $data;
    }

    private function uniqueCode(?string $ignoreId = null): Unique
    {
        $rule = Rule::unique('partners', 'partner_code')
            ->where(fn ($q) => $q->where('business_id', $this->businessId())->whereNull('deleted_at'));

        if ($ignoreId) {
            $rule->ignore($ignoreId);
        }

        return $rule;
    }

    private function assertOwned(Partner $partner): void
    {
        if ((string) $partner->business_id !== $this->businessId()) {
            abort(404);
        }
    }

    private function assertOwnedBranch(Partner $partner, PartnerBranch $branch): void
    {
        if ((string) $branch->partner_id !== (string) $partner->id) {
            abort(404);
        }
    }
}
