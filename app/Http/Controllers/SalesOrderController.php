<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesEditableDocumentCrud;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Sales\Quotation;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderLine;
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

class SalesOrderController extends Controller
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
        $query = SalesOrder::query()
            ->where('business_id', $this->businessId())
            ->with(['currentRevision', 'quotation', 'partner'])
            ->latest('created_at');

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q): void {
                $builder->where('sales_order_no', 'like', "%{$q}%")
                    ->orWhereHas('partner', fn ($partner) => $partner->where('partner_name_th', 'like', "%{$q}%"))
                    ->orWhereHas('quotation', fn ($quotation) => $quotation->where('quotation_no', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            $query->whereHas('currentRevision', fn ($revision) => $revision->where('status', $status));
        }

        $salesOrders = $query->paginate(20)->withQueryString();

        return view('office.sales-orders.index', compact('salesOrders'));
    }

    public function createDirect(): View
    {
        return view('office.sales-orders.form', array_merge(
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
            'order_date' => ['required', 'date'],
            'customer_po_no' => ['nullable', 'string', 'max:255'],
        ]));

        $partner = $this->editable->resolveOptionalPartner($this->businessId(), $data['partner_id'] ?? null);
        $lines = $this->editable->filterLines(
            $this->editable->normalizeLines($data['line'] ?? [], $data['discount_type'] ?? 'percent')
        );
        $businessId = $this->businessId();
        $createBy = $this->createBy();

        $salesOrder = DB::transaction(function () use ($partner, $data, $lines, $businessId, $createBy) {
            $salesOrder = new SalesOrder([
                'business_id' => $businessId,
                'business_branch_id' => currentBusinessBranchId(),
                'opportunity_id' => null,
                'quotation_id' => null,
                'quotation_revision_id' => null,
                'partner_id' => $partner?->id,
                'partner_branch_id' => $partner?->branches()->value('id'),
                'partner_contact_id' => $partner?->contacts()->value('id'),
                'create_by' => $createBy,
            ]);
            $this->salesFlow->allocateSalesOrderNo($salesOrder);
            $salesOrder->save();

            $revision = new SalesOrderRevision(array_merge([
                'sales_order_id' => $salesOrder->id,
                'revision_no' => 1,
                'status' => 'draft',
                'order_date' => $data['order_date'],
                'customer_po_no' => $data['customer_po_no'] ?? null,
                'currency' => 'THB',
                'payment_unit_mode' => $data['payment_unit_mode'] ?? 'percent',
                'create_by' => $createBy,
            ], $this->editable->partyFieldsFromRequest($data), $this->editable->totalsFieldsFromRequest($data)));
            $revision->save();

            $salesOrder->current_revision_id = $revision->id;
            $salesOrder->save();

            $this->editable->persistLines($revision, $lines, $data, SalesOrderLine::class, 'sales_order_revision_id');
            $this->paymentTerms->syncSalesOrderTerms(
                $revision,
                $data['payment'] ?? [],
                $data['payment_unit_mode'] ?? 'percent',
            );

            return $salesOrder;
        });

        return redirect()->route('office.sales-orders.show', $salesOrder)
            ->with('success', 'สร้าง Sales Order เรียบร้อยแล้ว');
    }

    public function edit(SalesOrder $salesOrder): View
    {
        $this->assertSalesOrderOwned($salesOrder);
        $revision = $salesOrder->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            abort(422, 'แก้ไขได้เฉพาะ Sales Order สถานะ draft');
        }

        $salesOrder->load(['partner.contacts', 'partner.branches', 'currentRevision.lines', 'currentRevision.paymentTerms']);

        return $this->editableDocumentEditView(
            $salesOrder,
            $revision,
            'office.sales-orders.form',
            'salesOrder',
            ['draft'],
            [
                'showPaymentTerms' => true,
                'paymentMode' => $revision->payment_unit_mode ?? 'percent',
                'paymentRows' => $revision->paymentTerms->map(fn ($term) => [
                    'condition' => $term->condition,
                    'due_days' => $term->due_days,
                    'percent_amount' => (float) $term->percent_amount,
                    'baht_amount' => (float) $term->baht_amount,
                ])->all(),
            ],
        );
    }

    public function update(Request $request, SalesOrder $salesOrder): RedirectResponse
    {
        $this->assertSalesOrderOwned($salesOrder);
        $revision = $salesOrder->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        return $this->editableDocumentUpdate(
            $request,
            $salesOrder,
            $revision,
            ['draft'],
            [
                'order_date' => ['required', 'date'],
                'customer_po_no' => ['nullable', 'string', 'max:255'],
            ],
            fn (array $data) => [
                'order_date' => $data['order_date'],
                'customer_po_no' => $data['customer_po_no'] ?? null,
                'payment_unit_mode' => $data['payment_unit_mode'] ?? 'percent',
            ],
            SalesOrderLine::class,
            'sales_order_revision_id',
            'office.sales-orders.show',
            withPayment: true,
            afterPersist: function (SalesOrderRevision $rev, array $data): void {
                $this->paymentTerms->syncSalesOrderTerms(
                    $rev,
                    $data['payment'] ?? [],
                    $data['payment_unit_mode'] ?? 'percent',
                );
            },
        );
    }

    public function destroy(SalesOrder $salesOrder): RedirectResponse
    {
        $this->assertSalesOrderOwned($salesOrder);
        $revision = $salesOrder->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        return $this->editableDocumentDestroy(
            $salesOrder,
            $revision,
            'office.sales-orders.index',
        );
    }

    public function cancel(SalesOrder $salesOrder): RedirectResponse
    {
        $this->assertSalesOrderOwned($salesOrder);
        $revision = $salesOrder->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        if (! in_array($revision->status, ['draft', 'confirmed'], true)) {
            throw ValidationException::withMessages(['status' => 'ยกเลิกไม่ได้สำหรับสถานะนี้']);
        }

        $revision->status = 'cancelled';
        $revision->cancelled_at = now();
        $revision->save();

        return back()->with('success', 'ยกเลิก Sales Order เรียบร้อยแล้ว');
    }

    public function create(Quotation $quotation): View
    {
        $this->assertQuotationOwned($quotation);
        if (! $quotation->canCreateSalesOrder()) {
            abort(422, 'สามารถสร้าง Sales Order ได้เฉพาะ Quotation สถานะ accepted เท่านั้น');
        }

        $quotation->load(['currentRevision.lines', 'opportunity', 'partner', 'partnerContact']);

        return view('office.sales-orders.form', compact('quotation'));
    }

    public function store(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->assertQuotationOwned($quotation);
        if (! $quotation->canCreateSalesOrder()) {
            throw ValidationException::withMessages(['quotation' => 'สามารถสร้าง Sales Order ได้เฉพาะ Quotation สถานะ accepted เท่านั้น']);
        }

        $quotationRevision = $quotation->currentRevision;
        if (! $quotationRevision || $quotationRevision->status !== 'accepted') {
            throw ValidationException::withMessages(['quotation' => 'ไม่พบ revision ที่ลูกค้าตอบรับ']);
        }

        $data = $request->validate([
            'order_date' => ['required', 'date'],
            'customer_po_no' => ['nullable', 'string', 'max:255'],
        ]);

        $salesOrder = DB::transaction(function () use ($quotation, $quotationRevision, $data) {
            return $this->salesFlow->createSalesOrderFromQuotation($quotation, $quotationRevision, [
                'status' => 'draft',
                'order_date' => $data['order_date'],
                'customer_po_no' => $data['customer_po_no'] ?? $quotationRevision->customer_po_no,
                'create_by' => $this->createBy(),
            ]);
        });

        return redirect()->route('office.sales-orders.show', $salesOrder)
            ->with('success', 'สร้าง Sales Order (draft) เรียบร้อยแล้ว');
    }

    public function show(SalesOrder $salesOrder): View
    {
        $this->assertSalesOrderOwned($salesOrder);
        $salesOrder->load([
            'quotation',
            'quotationRevision',
            'opportunity',
            'partner',
            'partnerContact',
            'currentRevision.lines.item',
            'currentRevision.confirmedBy.employee.user',
            'currentRevision.paymentTerms',
            'currentRevision.attachments',
            'revisions',
            'billingNotes',
            'deliveryNotes',
            'taxInvoices',
        ]);

        $salesJourney = app(SalesJourneyPresenter::class)->for($salesOrder);

        return view('office.sales-orders.show', compact('salesOrder', 'salesJourney'));
    }

    public function confirm(SalesOrder $salesOrder): RedirectResponse
    {
        $this->assertSalesOrderOwned($salesOrder);
        $revision = $salesOrder->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'ยืนยันได้เฉพาะ Sales Order ที่มีสถานะ draft']);
        }
        $qtStatus = $salesOrder->quotationRevision?->status;
        if ($qtStatus && in_array($qtStatus, ['rejected', 'cancelled', 'superseded'], true)) {
            throw ValidationException::withMessages(['status' => 'Quotation ต้นทางถูกยกเลิกหรือปฏิเสธแล้ว ไม่สามารถยืนยัน SO ได้']);
        }

        DB::transaction(function () use ($salesOrder, $revision) {
            if (blank($salesOrder->partner_id)) {
                $this->partyInfo->linkPartnerFromRevisionSnapshot($salesOrder, $revision, $this->createBy());
                $salesOrder->refresh();
            }

            $revision->status = 'confirmed';
            $revision->confirmed_at = now();
            $revision->confirmed_by = $this->createBy();
            $revision->save();

            $opportunity = $salesOrder->opportunity;
            if ($opportunity && $opportunity->stage !== 'closed_won') {
                $opportunity->stage = 'closed_won';
                $opportunity->probability = 100;
                $opportunity->won_at = now();
                $opportunity->estimated_value = $revision->total_amount;
                $opportunity->save();
            }
        });

        return back()->with('success', 'ยืนยัน Sales Order เรียบร้อยแล้ว');
    }

    public function amend(SalesOrder $salesOrder): RedirectResponse
    {
        $this->assertSalesOrderOwned($salesOrder);
        $current = $salesOrder->currentRevision;
        if (! $current || $current->status !== 'confirmed') {
            throw ValidationException::withMessages(['status' => 'แก้ไข revision ได้เฉพาะ Sales Order ที่ยืนยันแล้ว']);
        }

        DB::transaction(function () use ($salesOrder, $current) {
            $current->load(['lines', 'paymentTerms']);

            $revision = new SalesOrderRevision([
                'sales_order_id' => $salesOrder->id,
                'revision_no' => ((int) $current->revision_no) + 1,
                'supersedes_revision_id' => $current->id,
                'status' => 'draft',
                'order_date' => $current->order_date,
                'customer_po_no' => $current->customer_po_no,
                'currency' => $current->currency,
                'subtotal' => $current->subtotal,
                'discount_amount' => $current->discount_amount,
                'vat_amount' => $current->vat_amount,
                'total_amount' => $current->total_amount,
                'payment_unit_mode' => $current->payment_unit_mode ?: 'percent',
                'create_by' => $this->createBy(),
            ]);
            $revision->save();

            foreach ($current->lines as $line) {
                SalesOrderLine::create([
                    'sales_order_revision_id' => $revision->id,
                    'quotation_line_id' => $line->quotation_line_id,
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

            $rows = $current->paymentTerms->map(fn ($term) => [
                'quotation_payment_term_id' => $term->quotation_payment_term_id,
                'installment_no' => $term->installment_no,
                'condition' => $term->condition,
                'due_days' => $term->due_days,
                'due_date' => $term->due_date?->format('Y-m-d'),
                'percent_amount' => (float) $term->percent_amount,
                'baht_amount' => (float) $term->baht_amount,
            ])->all();
            $this->paymentTerms->syncSalesOrderTerms($revision, $rows, $revision->payment_unit_mode);

            $salesOrder->current_revision_id = $revision->id;
            $salesOrder->save();
        });

        return redirect()->route('office.sales-orders.edit', $salesOrder)
            ->with('success', 'สร้าง revision ใหม่เรียบร้อยแล้ว แก้ไขได้ก่อนยืนยัน');
    }

    private function assertQuotationOwned(Quotation $quotation): void
    {
        if ((string) $quotation->business_id !== $this->businessId()) {
            abort(403);
        }
    }

    private function assertSalesOrderOwned(SalesOrder $salesOrder): void
    {
        if ((string) $salesOrder->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}