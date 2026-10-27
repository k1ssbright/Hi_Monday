<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesEditableDocumentCrud;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Sales\Receipt;
use App\Models\Sales\ReceiptLine;
use App\Models\Sales\ReceiptRevision;
use App\Models\Sales\TaxInvoice;
use App\Models\Sales\TaxInvoiceRevision;
use App\Services\EditableSalesDocumentService;
use App\Services\SalesFlowService;
use App\Services\SalesJourneyPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    use HandlesEditableDocumentCrud;
    use ResolvesBusinessContext;

    public function __construct(
        private SalesFlowService $salesFlow,
        private EditableSalesDocumentService $editable,
    ) {}

    public function index(Request $request): View
    {
        $query = Receipt::query()
            ->where('business_id', $this->businessId())
            ->with(['currentRevision', 'partner', 'taxInvoice'])
            ->latest('created_at');

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q): void {
                $builder->where('receipt_no', 'like', "%{$q}%")
                    ->orWhereHas('partner', fn ($partner) => $partner->where('partner_name_th', 'like', "%{$q}%"))
                    ->orWhereHas('taxInvoice', fn ($ti) => $ti->where('tax_invoice_no', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            $query->whereHas('currentRevision', fn ($revision) => $revision->where('status', $status));
        }

        $receipts = $query->paginate(20)->withQueryString();

        return view('office.receipts.index', compact('receipts'));
    }

    public function createDirect(): View
    {
        return view('office.receipts.form', array_merge(
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

        $receipt = DB::transaction(function () use ($partner, $data, $lines, $businessId, $createBy) {
            $receipt = new Receipt([
                'business_id' => $businessId,
                'business_branch_id' => currentBusinessBranchId(),
                'opportunity_id' => null,
                'sales_order_id' => null,
                'sales_order_revision_id' => null,
                'tax_invoice_id' => null,
                'tax_invoice_revision_id' => null,
                'partner_id' => $partner?->id,
                'partner_branch_id' => $partner?->branches()->value('id'),
                'partner_contact_id' => $partner?->contacts()->value('id'),
                'create_by' => $createBy,
            ]);
            $this->salesFlow->allocateReceiptNo($receipt);
            $receipt->save();

            $revision = new ReceiptRevision(array_merge([
                'receipt_id' => $receipt->id,
                'revision_no' => 1,
                'status' => 'draft',
                'issue_date' => $data['issue_date'],
                'currency' => strtoupper(trim($data['currency'] ?? '')) ?: 'THB',
                'note' => $data['note'] ?? null,
                'create_by' => $createBy,
            ], $this->editable->partyFieldsFromRequest($data), $this->editable->totalsFieldsFromRequest($data)));
            $revision->save();

            $receipt->current_revision_id = $revision->id;
            $receipt->save();

            $this->editable->persistLines($revision, $lines, $data, ReceiptLine::class, 'receipt_revision_id');

            return $receipt;
        });

        return redirect()->route('office.receipts.show', $receipt)
            ->with('success', 'สร้าง Receipt เรียบร้อยแล้ว');
    }

    public function create(TaxInvoice $taxInvoice): View
    {
        $this->assertTaxInvoiceOwned($taxInvoice);
        if (! $taxInvoice->canCreateReceipt()) {
            abort(422, 'สามารถสร้าง Receipt ได้เฉพาะ Tax Invoice สถานะ issued เท่านั้น');
        }

        $taxInvoice->load(['currentRevision.lines', 'partner', 'partnerContact', 'salesOrder']);

        return view('office.receipts.form', compact('taxInvoice'));
    }

    public function store(Request $request, TaxInvoice $taxInvoice): RedirectResponse
    {
        $this->assertTaxInvoiceOwned($taxInvoice);
        if (! $taxInvoice->canCreateReceipt()) {
            throw ValidationException::withMessages(['tax_invoice' => 'สามารถสร้าง Receipt ได้เฉพาะ Tax Invoice สถานะ issued เท่านั้น']);
        }

        $tiRevision = $taxInvoice->currentRevision;
        if (! $tiRevision || $tiRevision->status !== 'issued') {
            throw ValidationException::withMessages(['tax_invoice' => 'ไม่พบ Tax Invoice revision ที่ออกแล้ว']);
        }

        $data = $request->validate([
            'issue_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
        ]);

        $receipt = DB::transaction(function () use ($taxInvoice, $tiRevision, $data) {
            $receipt = new Receipt([
                'business_id' => $taxInvoice->business_id,
                'business_branch_id' => $taxInvoice->business_branch_id,
                'opportunity_id' => $taxInvoice->opportunity_id,
                'sales_order_id' => $taxInvoice->sales_order_id,
                'sales_order_revision_id' => $taxInvoice->sales_order_revision_id,
                'tax_invoice_id' => $taxInvoice->id,
                'tax_invoice_revision_id' => $tiRevision->id,
                'partner_id' => $taxInvoice->partner_id,
                'partner_branch_id' => $taxInvoice->partner_branch_id,
                'partner_contact_id' => $taxInvoice->partner_contact_id,
                'create_by' => $this->createBy(),
            ]);
            $this->salesFlow->allocateReceiptNo($receipt);
            $receipt->save();

            $partyFields = $this->editable->extractPartyFieldsFromRevision($tiRevision);
            if (blank($partyFields['customer_name'] ?? null) && blank($partyFields['customer_contact_name'] ?? null)) {
                if ($taxInvoice->partner) {
                    $partyFields = array_merge(
                        app(\App\Services\DocumentPartyService::class)->customerDefaults(
                            $taxInvoice->partner,
                            $taxInvoice->partnerContact,
                            $taxInvoice->partnerBranch
                        ),
                        app(\App\Services\DocumentPartyService::class)->sellerDefaults($this->createBy())
                    );
                }
            }

            $revision = new ReceiptRevision(array_merge([
                'receipt_id' => $receipt->id,
                'revision_no' => 1,
                'status' => 'draft',
                'issue_date' => $data['issue_date'],
                'currency' => $tiRevision->currency ?: 'THB',
                'subtotal' => $tiRevision->subtotal,
                'discount_amount' => $tiRevision->discount_amount,
                'vat_amount' => $tiRevision->vat_amount,
                'total_amount' => $tiRevision->total_amount,
                'payment_method' => $data['payment_method'] ?? 'bank_transfer',
                'payment_ref' => $data['payment_ref'] ?? null,
                'note' => $data['note'] ?? null,
                'create_by' => $this->createBy(),
            ], $partyFields));
            $revision->save();

            $receipt->current_revision_id = $revision->id;
            $receipt->save();

            $this->copyTaxInvoiceLines($tiRevision, $revision);

            return $receipt;
        });

        return redirect()->route('office.receipts.show', $receipt)
            ->with('success', 'สร้าง Receipt เรียบร้อยแล้ว');
    }

    public function edit(Receipt $receipt): View
    {
        $this->assertReceiptOwned($receipt);
        $revision = $receipt->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            abort(422, 'แก้ไขได้เฉพาะ Receipt สถานะ draft');
        }

        return $this->editableDocumentEditView(
            $receipt,
            $revision,
            'office.receipts.form',
            'receipt',
            ['draft'],
        );
    }

    public function update(Request $request, Receipt $receipt): RedirectResponse
    {
        $this->assertReceiptOwned($receipt);
        $revision = $receipt->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        return $this->editableDocumentUpdate(
            $request,
            $receipt,
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
            ReceiptLine::class,
            'receipt_revision_id',
            'office.receipts.show',
        );
    }

    public function destroy(Receipt $receipt): RedirectResponse
    {
        $this->assertReceiptOwned($receipt);
        $revision = $receipt->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        return $this->editableDocumentDestroy(
            $receipt,
            $revision,
            'office.receipts.index',
        );
    }

    public function cancel(Receipt $receipt): RedirectResponse
    {
        $this->assertReceiptOwned($receipt);
        $revision = $receipt->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        return $this->editableDocumentVoid($revision);
    }

    public function show(Receipt $receipt): View
    {
        $this->assertReceiptOwned($receipt);
        $receipt->load([
            'taxInvoice',
            'taxInvoiceRevision',
            'salesOrder',
            'partner',
            'partnerContact',
            'currentRevision.lines.item',
            'revisions',
        ]);

        $salesJourney = app(SalesJourneyPresenter::class)->for($receipt);

        return view('office.receipts.show', compact('receipt', 'salesJourney'));
    }

    public function issue(Receipt $receipt): RedirectResponse
    {
        $this->assertReceiptOwned($receipt);
        $revision = $receipt->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'ออกเอกสารได้เฉพาะ Receipt สถานะ draft']);
        }

        $revision->status = 'issued';
        $revision->issued_at = now();
        $revision->save();

        return back()->with('success', 'ออก Receipt เรียบร้อยแล้ว');
    }

    private function copyTaxInvoiceLines(TaxInvoiceRevision $tiRevision, ReceiptRevision $revision): void
    {
        $tiRevision->loadMissing('lines');
        foreach ($tiRevision->lines as $line) {
            ReceiptLine::create([
                'receipt_revision_id' => $revision->id,
                'sales_order_line_id' => $line->sales_order_line_id,
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

    private function assertTaxInvoiceOwned(TaxInvoice $taxInvoice): void
    {
        if ((string) $taxInvoice->business_id !== $this->businessId()) {
            abort(403);
        }
    }

    private function assertReceiptOwned(Receipt $receipt): void
    {
        if ((string) $receipt->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
