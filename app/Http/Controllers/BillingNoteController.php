<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesEditableDocumentCrud;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Sales\BillingNote;
use App\Models\Sales\BillingNoteLine;
use App\Models\Sales\BillingNoteRevision;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderPaymentTerm;
use App\Models\Sales\SalesOrderRevision;
use App\Services\DocumentPartyService;
use App\Services\EditableSalesDocumentService;
use App\Services\PaymentTermsService;
use App\Services\SalesFlowService;
use App\Services\SalesJourneyPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BillingNoteController extends Controller
{
    use HandlesEditableDocumentCrud;
    use ResolvesBusinessContext;

    public function __construct(
        private SalesFlowService $salesFlow,
        private PaymentTermsService $paymentTerms,
        private EditableSalesDocumentService $editable,
        private DocumentPartyService $partyInfo,
    ) {}

    public function index(Request $request): View
    {
        $query = BillingNote::query()
            ->where('business_id', $this->businessId())
            ->with(['currentRevision', 'partner', 'salesOrder'])
            ->latest('created_at');

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q): void {
                $builder->where('billing_note_no', 'like', "%{$q}%")
                    ->orWhereHas('partner', fn ($partner) => $partner->where('partner_name_th', 'like', "%{$q}%"))
                    ->orWhereHas('salesOrder', fn ($so) => $so->where('sales_order_no', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            $query->whereHas('currentRevision', fn ($revision) => $revision->where('status', $status));
        }

        $billingNotes = $query->paginate(20)->withQueryString();

        return view('office.billing-notes.index', compact('billingNotes'));
    }

    public function createDirect(): View
    {
        return view('office.billing-notes.form', array_merge(
            $this->editable->formViewData(null, $this->createBy()),
            [
                'standalone' => true,
                'seedLines' => $this->editable->seedLinesFromOldInput(),
            ],
        ));
    }

    public function storeStandalone(Request $request): RedirectResponse
    {
        $data = $request->validate($this->editable->payloadRules([
            'issue_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ], withPayment: false));

        $partner = $this->editable->resolveOptionalPartner($this->businessId(), $data['partner_id'] ?? null);
        $lines = $this->editable->filterLines(
            $this->editable->normalizeLines($data['line'] ?? [], $data['discount_type'] ?? 'percent')
        );
        $businessId = $this->businessId();
        $createBy = $this->createBy();

        $billingNote = DB::transaction(function () use ($partner, $data, $lines, $businessId, $createBy) {
            $billingNote = new BillingNote([
                'business_id' => $businessId,
                'business_branch_id' => currentBusinessBranchId(),
                'opportunity_id' => null,
                'sales_order_id' => null,
                'sales_order_revision_id' => null,
                'partner_id' => $partner?->id,
                'partner_branch_id' => $partner?->branches()->value('id'),
                'partner_contact_id' => $partner?->contacts()->value('id'),
                'create_by' => $createBy,
            ]);
            $this->salesFlow->allocateBillingNoteNo($billingNote);
            $billingNote->save();

            $revision = new BillingNoteRevision(array_merge([
                'billing_note_id' => $billingNote->id,
                'revision_no' => 1,
                'status' => 'draft',
                'issue_date' => $data['issue_date'],
                'currency' => 'THB',
                'note' => $data['note'] ?? null,
                'create_by' => $createBy,
            ], $this->editable->partyFieldsFromRequest($data), $this->editable->totalsFieldsFromRequest($data)));
            $revision->save();

            $billingNote->current_revision_id = $revision->id;
            $billingNote->save();

            $this->editable->persistLines($revision, $lines, $data, BillingNoteLine::class, 'billing_note_revision_id');

            return $billingNote;
        });

        return redirect()->route('office.billing-notes.show', $billingNote)
            ->with('success', 'สร้าง Billing Note เรียบร้อยแล้ว');
    }

    public function create(SalesOrder $salesOrder): View
    {
        $this->assertSalesOrderOwned($salesOrder);
        if (! $salesOrder->canCreateBillingNote()) {
            abort(422, 'สามารถสร้าง Billing Note ได้เฉพาะ Sales Order สถานะ confirmed เท่านั้น');
        }

        $salesOrder->load(['currentRevision.lines', 'currentRevision.paymentTerms', 'partner', 'partnerContact']);

        return view('office.billing-notes.form', compact('salesOrder'));
    }

    public function store(Request $request, SalesOrder $salesOrder): RedirectResponse
    {
        $this->assertSalesOrderOwned($salesOrder);
        if (! $salesOrder->canCreateBillingNote()) {
            throw ValidationException::withMessages(['sales_order' => 'สามารถสร้าง Billing Note ได้เฉพาะ Sales Order สถานะ confirmed เท่านั้น']);
        }

        $soRevision = $salesOrder->currentRevision;
        if (! $soRevision || $soRevision->status !== 'confirmed') {
            throw ValidationException::withMessages(['sales_order' => 'ไม่พบ Sales Order revision ที่ยืนยันแล้ว']);
        }

        $data = $request->validate([
            'issue_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
            'sales_order_payment_term_id' => ['nullable', 'integer'],
        ]);

        $selectedTerm = null;
        if (! empty($data['sales_order_payment_term_id'])) {
            $selectedTerm = SalesOrderPaymentTerm::query()
                ->where('sales_order_revision_id', $soRevision->id)
                ->findOrFail($data['sales_order_payment_term_id']);
        }

        $billingNote = DB::transaction(function () use ($salesOrder, $soRevision, $data, $selectedTerm) {
            $billingNote = new BillingNote([
                'business_id' => $salesOrder->business_id,
                'business_branch_id' => $salesOrder->business_branch_id,
                'opportunity_id' => $salesOrder->opportunity_id,
                'sales_order_id' => $salesOrder->id,
                'sales_order_revision_id' => $soRevision->id,
                'partner_id' => $salesOrder->partner_id,
                'partner_branch_id' => $salesOrder->partner_branch_id,
                'partner_contact_id' => $salesOrder->partner_contact_id,
                'create_by' => $this->createBy(),
            ]);
            $this->salesFlow->allocateBillingNoteNo($billingNote);
            $billingNote->save();

            $ratio = $selectedTerm ? ((float) $selectedTerm->percent_amount / 100) : 1.0;

            $revision = new BillingNoteRevision([
                'billing_note_id' => $billingNote->id,
                'revision_no' => 1,
                'status' => 'draft',
                'issue_date' => $data['issue_date'],
                'currency' => $soRevision->currency,
                'subtotal' => round(((float) $soRevision->subtotal) * $ratio, 2),
                'discount_amount' => round(((float) $soRevision->discount_amount) * $ratio, 2),
                'vat_amount' => round(((float) $soRevision->vat_amount) * $ratio, 2),
                'total_amount' => $selectedTerm
                    ? (float) $selectedTerm->baht_amount
                    : (float) $soRevision->total_amount,
                'note' => $data['note'] ?? null,
                'create_by' => $this->createBy(),
            ]);
            $revision->save();

            $billingNote->current_revision_id = $revision->id;
            $billingNote->save();

            $this->copySalesOrderLines($soRevision, $revision, $ratio);
            $this->paymentTerms->copySalesOrderToBillingNote($soRevision, $revision, $selectedTerm);

            return $billingNote;
        });

        return redirect()->route('office.billing-notes.show', $billingNote)
            ->with('success', 'สร้าง Billing Note เรียบร้อยแล้ว');
    }

    public function edit(BillingNote $billingNote): View
    {
        $this->assertBillingNoteOwned($billingNote);
        $revision = $billingNote->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            abort(422, 'แก้ไขได้เฉพาะ Billing Note สถานะ draft');
        }

        return $this->editableDocumentEditView(
            $billingNote,
            $revision,
            'office.billing-notes.form',
            'billingNote',
            ['draft'],
        );
    }

    public function update(Request $request, BillingNote $billingNote): RedirectResponse
    {
        $this->assertBillingNoteOwned($billingNote);
        $revision = $billingNote->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        return $this->editableDocumentUpdate(
            $request,
            $billingNote,
            $revision,
            ['draft'],
            [
                'issue_date' => ['required', 'date'],
                'note' => ['nullable', 'string'],
            ],
            fn (array $data) => [
                'issue_date' => $data['issue_date'],
                'note' => $data['note'] ?? null,
            ],
            BillingNoteLine::class,
            'billing_note_revision_id',
            'office.billing-notes.show',
        );
    }

    public function destroy(BillingNote $billingNote): RedirectResponse
    {
        $this->assertBillingNoteOwned($billingNote);
        $revision = $billingNote->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        return $this->editableDocumentDestroy(
            $billingNote,
            $revision,
            'office.billing-notes.index',
        );
    }

    public function cancel(BillingNote $billingNote): RedirectResponse
    {
        $this->assertBillingNoteOwned($billingNote);
        $revision = $billingNote->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        return $this->editableDocumentVoid($revision);
    }

    public function show(BillingNote $billingNote): View
    {
        $this->assertBillingNoteOwned($billingNote);
        $billingNote->load([
            'salesOrder',
            'salesOrderRevision',
            'partner',
            'partnerContact',
            'currentRevision.lines.item',
            'currentRevision.paymentTerms',
            'currentRevision.salesOrderPaymentTerm',
            'revisions',
        ]);

        $salesJourney = app(SalesJourneyPresenter::class)->for($billingNote);

        return view('office.billing-notes.show', compact('billingNote', 'salesJourney'));
    }

    public function issue(BillingNote $billingNote): RedirectResponse
    {
        $this->assertBillingNoteOwned($billingNote);
        $revision = $billingNote->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'ออกเอกสารได้เฉพาะ Billing Note สถานะ draft']);
        }

        DB::transaction(function () use ($billingNote, $revision) {
            if (blank($billingNote->partner_id)) {
                $this->partyInfo->linkPartnerFromRevisionSnapshot($billingNote, $revision, $this->createBy());
                $billingNote->refresh();
            }

            $revision->status = 'issued';
            $revision->issued_at = now();
            $revision->save();
        });

        return back()->with('success', 'ออก Billing Note เรียบร้อยแล้ว');
    }

    private function copySalesOrderLines(SalesOrderRevision $soRevision, BillingNoteRevision $revision, float $ratio = 1.0): void
    {
        $soRevision->loadMissing('lines');
        foreach ($soRevision->lines as $line) {
            BillingNoteLine::create([
                'billing_note_revision_id' => $revision->id,
                'sales_order_line_id' => $line->id,
                'product_service_item_id' => $line->product_service_item_id,
                'description' => $line->description,
                'quantity' => $line->quantity,
                'unit' => $line->unit,
                'unit_price' => round(((float) $line->unit_price) * $ratio, 2),
                'discount_amount' => round(((float) $line->discount_amount) * $ratio, 2),
                'vat_percent' => $line->vat_percent,
                'line_total' => round(((float) $line->line_total) * $ratio, 2),
            ]);
        }
    }

    private function assertSalesOrderOwned(SalesOrder $salesOrder): void
    {
        if ((string) $salesOrder->business_id !== $this->businessId()) {
            abort(403);
        }
    }

    private function assertBillingNoteOwned(BillingNote $billingNote): void
    {
        if ((string) $billingNote->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
