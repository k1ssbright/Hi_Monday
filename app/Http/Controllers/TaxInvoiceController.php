<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesEditableDocumentCrud;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Sales\BillingNote;
use App\Models\Sales\DeliveryNote;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderRevision;
use App\Models\Sales\TaxInvoice;
use App\Models\Sales\TaxInvoiceLine;
use App\Models\Sales\TaxInvoiceRevision;
use App\Services\DocumentPartyService;
use App\Services\EditableSalesDocumentService;
use App\Services\SalesFlowService;
use App\Services\SalesJourneyPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TaxInvoiceController extends Controller
{
    use HandlesEditableDocumentCrud;
    use ResolvesBusinessContext;

    public function __construct(
        private SalesFlowService $salesFlow,
        private EditableSalesDocumentService $editable,
        private DocumentPartyService $partyInfo,
    ) {}

    public function index(Request $request): View
    {
        $query = TaxInvoice::query()
            ->where('business_id', $this->businessId())
            ->with(['currentRevision', 'partner', 'salesOrder'])
            ->latest('created_at');

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q): void {
                $builder->where('tax_invoice_no', 'like', "%{$q}%")
                    ->orWhereHas('partner', fn ($partner) => $partner->where('partner_name_th', 'like', "%{$q}%"))
                    ->orWhereHas('salesOrder', fn ($so) => $so->where('sales_order_no', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            $query->whereHas('currentRevision', fn ($revision) => $revision->where('status', $status));
        }

        $taxInvoices = $query->paginate(20)->withQueryString();

        return view('office.tax-invoices.index', compact('taxInvoices'));
    }

    public function createDirect(): View
    {
        return view('office.tax-invoices.form', array_merge(
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
            'currency' => ['nullable', 'string', 'max:10'],
            'note' => ['nullable', 'string'],
        ], withPayment: false));

        $partner = $this->editable->resolveOptionalPartner($this->businessId(), $data['partner_id'] ?? null);
        $lines = $this->editable->filterLines(
            $this->editable->normalizeLines($data['line'] ?? [], $data['discount_type'] ?? 'percent')
        );
        $businessId = $this->businessId();
        $createBy = $this->createBy();

        $taxInvoice = DB::transaction(function () use ($partner, $data, $lines, $businessId, $createBy) {
            $taxInvoice = new TaxInvoice([
                'business_id' => $businessId,
                'business_branch_id' => currentBusinessBranchId(),
                'opportunity_id' => null,
                'sales_order_id' => null,
                'sales_order_revision_id' => null,
                'billing_note_id' => null,
                'billing_note_revision_id' => null,
                'delivery_note_id' => null,
                'delivery_note_revision_id' => null,
                'partner_id' => $partner?->id,
                'partner_branch_id' => $partner?->branches()->value('id'),
                'partner_contact_id' => $partner?->contacts()->value('id'),
                'create_by' => $createBy,
            ]);
            $this->salesFlow->allocateTaxInvoiceNo($taxInvoice);
            $taxInvoice->save();

            $revision = new TaxInvoiceRevision(array_merge([
                'tax_invoice_id' => $taxInvoice->id,
                'revision_no' => 1,
                'status' => 'draft',
                'issue_date' => $data['issue_date'],
                'currency' => strtoupper(trim($data['currency'] ?? '')) ?: 'THB',
                'note' => $data['note'] ?? null,
                'create_by' => $createBy,
            ], $this->editable->partyFieldsFromRequest($data), $this->editable->totalsFieldsFromRequest($data)));
            $revision->save();

            $taxInvoice->current_revision_id = $revision->id;
            $taxInvoice->save();

            $this->editable->persistLines($revision, $lines, $data, TaxInvoiceLine::class, 'tax_invoice_revision_id');

            return $taxInvoice;
        });

        return redirect()->route('office.tax-invoices.show', $taxInvoice)
            ->with('success', 'สร้าง Tax Invoice เรียบร้อยแล้ว');
    }

    public function create(Request $request, SalesOrder $salesOrder): View
    {
        $this->assertSalesOrderOwned($salesOrder);
        if (! $salesOrder->canCreateTaxInvoice()) {
            abort(422, 'สามารถสร้าง Tax Invoice ได้เฉพาะ Sales Order สถานะ confirmed เท่านั้น');
        }

        $salesOrder->load(['currentRevision.lines', 'partner', 'partnerContact', 'billingNotes', 'deliveryNotes']);

        $billingNote = null;
        $deliveryNote = null;
        if ($request->filled('billing_note_id')) {
            $billingNote = BillingNote::query()
                ->where('business_id', $this->businessId())
                ->where('sales_order_id', $salesOrder->id)
                ->findOrFail($request->string('billing_note_id')->toString());
        }
        if ($request->filled('delivery_note_id')) {
            $deliveryNote = DeliveryNote::query()
                ->where('business_id', $this->businessId())
                ->where('sales_order_id', $salesOrder->id)
                ->findOrFail($request->string('delivery_note_id')->toString());
        }

        return view('office.tax-invoices.form', compact('salesOrder', 'billingNote', 'deliveryNote'));
    }

    public function store(Request $request, SalesOrder $salesOrder): RedirectResponse
    {
        $this->assertSalesOrderOwned($salesOrder);
        if (! $salesOrder->canCreateTaxInvoice()) {
            throw ValidationException::withMessages(['sales_order' => 'สามารถสร้าง Tax Invoice ได้เฉพาะ Sales Order สถานะ confirmed เท่านั้น']);
        }

        $soRevision = $salesOrder->currentRevision;
        if (! $soRevision || $soRevision->status !== 'confirmed') {
            throw ValidationException::withMessages(['sales_order' => 'ไม่พบ Sales Order revision ที่ยืนยันแล้ว']);
        }

        $data = $request->validate([
            'issue_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
            'billing_note_id' => ['nullable', 'uuid'],
            'delivery_note_id' => ['nullable', 'uuid'],
        ]);

        $billingNote = null;
        $deliveryNote = null;
        if (! empty($data['billing_note_id'])) {
            $billingNote = BillingNote::query()
                ->where('business_id', $this->businessId())
                ->where('sales_order_id', $salesOrder->id)
                ->findOrFail($data['billing_note_id']);
        }
        if (! empty($data['delivery_note_id'])) {
            $deliveryNote = DeliveryNote::query()
                ->where('business_id', $this->businessId())
                ->where('sales_order_id', $salesOrder->id)
                ->findOrFail($data['delivery_note_id']);
        }

        $taxInvoice = DB::transaction(function () use ($salesOrder, $soRevision, $data, $billingNote, $deliveryNote) {
            $taxInvoice = new TaxInvoice([
                'business_id' => $salesOrder->business_id,
                'business_branch_id' => $salesOrder->business_branch_id,
                'opportunity_id' => $salesOrder->opportunity_id,
                'sales_order_id' => $salesOrder->id,
                'sales_order_revision_id' => $soRevision->id,
                'billing_note_id' => $billingNote?->id,
                'billing_note_revision_id' => $billingNote?->current_revision_id,
                'delivery_note_id' => $deliveryNote?->id,
                'delivery_note_revision_id' => $deliveryNote?->current_revision_id,
                'partner_id' => $salesOrder->partner_id,
                'partner_branch_id' => $salesOrder->partner_branch_id,
                'partner_contact_id' => $salesOrder->partner_contact_id,
                'create_by' => $this->createBy(),
            ]);
            $this->salesFlow->allocateTaxInvoiceNo($taxInvoice);
            $taxInvoice->save();

            $revision = new TaxInvoiceRevision([
                'tax_invoice_id' => $taxInvoice->id,
                'revision_no' => 1,
                'status' => 'draft',
                'issue_date' => $data['issue_date'],
                'currency' => $soRevision->currency,
                'subtotal' => $soRevision->subtotal,
                'discount_amount' => $soRevision->discount_amount,
                'vat_amount' => $soRevision->vat_amount,
                'total_amount' => $soRevision->total_amount,
                'note' => $data['note'] ?? null,
                'create_by' => $this->createBy(),
            ]);
            $revision->save();

            $taxInvoice->current_revision_id = $revision->id;
            $taxInvoice->save();

            $this->copySalesOrderLines($soRevision, $revision);

            return $taxInvoice;
        });

        return redirect()->route('office.tax-invoices.show', $taxInvoice)
            ->with('success', 'สร้าง Tax Invoice เรียบร้อยแล้ว');
    }

    public function edit(TaxInvoice $taxInvoice): View
    {
        $this->assertTaxInvoiceOwned($taxInvoice);
        $revision = $taxInvoice->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            abort(422, 'แก้ไขได้เฉพาะ Tax Invoice สถานะ draft');
        }

        return $this->editableDocumentEditView(
            $taxInvoice,
            $revision,
            'office.tax-invoices.form',
            'taxInvoice',
            ['draft'],
        );
    }

    public function update(Request $request, TaxInvoice $taxInvoice): RedirectResponse
    {
        $this->assertTaxInvoiceOwned($taxInvoice);
        $revision = $taxInvoice->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        return $this->editableDocumentUpdate(
            $request,
            $taxInvoice,
            $revision,
            ['draft'],
            [
                'issue_date' => ['required', 'date'],
                'currency' => ['nullable', 'string', 'max:10'],
                'note' => ['nullable', 'string'],
            ],
            fn (array $data) => [
                'issue_date' => $data['issue_date'],
                'currency' => strtoupper(trim($data['currency'] ?? '')) ?: 'THB',
                'note' => $data['note'] ?? null,
            ],
            TaxInvoiceLine::class,
            'tax_invoice_revision_id',
            'office.tax-invoices.show',
        );
    }

    public function destroy(TaxInvoice $taxInvoice): RedirectResponse
    {
        $this->assertTaxInvoiceOwned($taxInvoice);
        $revision = $taxInvoice->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        return $this->editableDocumentDestroy(
            $taxInvoice,
            $revision,
            'office.tax-invoices.index',
        );
    }

    public function cancel(TaxInvoice $taxInvoice): RedirectResponse
    {
        $this->assertTaxInvoiceOwned($taxInvoice);
        $revision = $taxInvoice->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        return $this->editableDocumentVoid($revision);
    }

    public function show(TaxInvoice $taxInvoice): View
    {
        $this->assertTaxInvoiceOwned($taxInvoice);
        $taxInvoice->load([
            'salesOrder',
            'salesOrderRevision',
            'billingNote',
            'deliveryNote',
            'partner',
            'partnerContact',
            'currentRevision.lines.item',
            'revisions',
            'receipts',
            'creditNotes',
            'debitNotes',
        ]);

        $salesJourney = app(SalesJourneyPresenter::class)->for($taxInvoice);

        return view('office.tax-invoices.show', compact('taxInvoice', 'salesJourney'));
    }

    public function issue(TaxInvoice $taxInvoice): RedirectResponse
    {
        $this->assertTaxInvoiceOwned($taxInvoice);
        $revision = $taxInvoice->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'ออกเอกสารได้เฉพาะ Tax Invoice สถานะ draft']);
        }

        DB::transaction(function () use ($taxInvoice, $revision) {
            if (blank($taxInvoice->partner_id)) {
                $this->partyInfo->linkPartnerFromRevisionSnapshot($taxInvoice, $revision, $this->createBy());
                $taxInvoice->refresh();
            }

            $revision->status = 'issued';
            $revision->issued_at = now();
            $revision->save();
        });

        return back()->with('success', 'ออก Tax Invoice เรียบร้อยแล้ว');
    }

    private function copySalesOrderLines(SalesOrderRevision $soRevision, TaxInvoiceRevision $revision): void
    {
        $soRevision->loadMissing('lines');
        foreach ($soRevision->lines as $line) {
            TaxInvoiceLine::create([
                'tax_invoice_revision_id' => $revision->id,
                'sales_order_line_id' => $line->id,
                'product_service_item_id' => $line->product_service_item_id,
                'description' => $line->description,
                'quantity' => $line->quantity,
                'unit' => $line->unit,
                'unit_price' => $line->unit_price,
                'discount_amount' => $line->discount_amount,
                'vat_percent' => $line->vat_percent,
                'line_total' => $line->line_total,
            ]);
        }
    }

    private function assertSalesOrderOwned(SalesOrder $salesOrder): void
    {
        if ((string) $salesOrder->business_id !== $this->businessId()) {
            abort(403);
        }
    }

    private function assertTaxInvoiceOwned(TaxInvoice $taxInvoice): void
    {
        if ((string) $taxInvoice->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
