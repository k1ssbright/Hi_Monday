<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Purchasing\PurchaseOrder;
use App\Models\Purchasing\PurchaseOrderLine;
use App\Models\Purchasing\PurchaseOrderRevision;
use App\Models\Purchasing\PurchaseRequisition;
use App\Services\DocumentPartyService;
use App\Services\EditableSalesDocumentService;
use App\Services\PurchaseFlowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PurchaseOrderController extends Controller
{
    use ResolvesBusinessContext;

    public function __construct(
        private PurchaseFlowService $purchaseFlow,
        private EditableSalesDocumentService $editable,
        private DocumentPartyService $partyInfo,
    ) {}

    public function index(Request $request): View
    {
        $query = PurchaseOrder::query()
            ->where('business_id', $this->businessId())
            ->with(['currentRevision', 'purchaseRequisition', 'partner'])
            ->latest('created_at');

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q): void {
                $builder->where('purchase_order_no', 'like', "%{$q}%")
                    ->orWhereHas('partner', fn ($partner) => $partner->where('partner_name_th', 'like', "%{$q}%"))
                    ->orWhereHas('purchaseRequisition', fn ($pr) => $pr->where('purchase_requisition_no', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            $query->whereHas('currentRevision', fn ($revision) => $revision->where('status', $status));
        }

        $purchaseOrders = $query->paginate(20)->withQueryString();

        return view('office.purchase-orders.index', compact('purchaseOrders'));
    }

    public function createDirect(): View
    {
        return view('office.purchase-orders.form', array_merge(
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
            'vendor_reference_no' => ['nullable', 'string', 'max:255'],
        ], withPayment: false));

        $partner = $this->editable->resolveOptionalPartner($this->businessId(), $data['partner_id'] ?? null);
        $lines = $this->editable->filterLines(
            $this->editable->normalizeLines($data['line'] ?? [], $data['discount_type'] ?? 'percent')
        );
        $businessId = $this->businessId();
        $createBy = $this->createBy();

        $purchaseOrder = DB::transaction(function () use ($partner, $data, $lines, $businessId, $createBy) {
            $purchaseOrder = new PurchaseOrder([
                'business_id' => $businessId,
                'business_branch_id' => currentBusinessBranchId(),
                'purchase_requisition_id' => null,
                'purchase_requisition_revision_id' => null,
                'partner_id' => $partner?->id,
                'partner_branch_id' => $partner?->branches()->value('id'),
                'partner_contact_id' => $partner?->contacts()->value('id'),
                'create_by' => $createBy,
            ]);
            $this->purchaseFlow->allocatePurchaseOrderNo($purchaseOrder);
            $purchaseOrder->save();

            $revision = new PurchaseOrderRevision(array_merge([
                'purchase_order_id' => $purchaseOrder->id,
                'revision_no' => 1,
                'status' => 'draft',
                'order_date' => $data['order_date'],
                'vendor_reference_no' => $data['vendor_reference_no'] ?? null,
                'currency' => 'THB',
                'create_by' => $createBy,
            ], $this->editable->partyFieldsFromRequest($data), $this->editable->totalsFieldsFromRequest($data)));
            $revision->save();

            $purchaseOrder->current_revision_id = $revision->id;
            $purchaseOrder->save();

            $this->editable->persistLines(
                $revision,
                $lines,
                $data,
                PurchaseOrderLine::class,
                'purchase_order_revision_id',
            );

            return $purchaseOrder;
        });

        return redirect()->route('office.purchase-orders.show', $purchaseOrder)
            ->with('success', 'สร้างใบสั่งซื้อ (PO) เรียบร้อยแล้ว');
    }

    public function create(PurchaseRequisition $purchaseRequisition): View
    {
        $this->assertPurchaseRequisitionOwned($purchaseRequisition);
        if (! $purchaseRequisition->canCreatePurchaseOrder()) {
            abort(422, 'สามารถสร้าง PO ได้เฉพาะใบขอซื้อสถานะ approved เท่านั้น');
        }

        $purchaseRequisition->load(['currentRevision.lines', 'partner', 'partnerContact']);

        return view('office.purchase-orders.form', compact('purchaseRequisition'));
    }

    public function store(Request $request, PurchaseRequisition $purchaseRequisition): RedirectResponse
    {
        $this->assertPurchaseRequisitionOwned($purchaseRequisition);
        if (! $purchaseRequisition->canCreatePurchaseOrder()) {
            throw ValidationException::withMessages(['purchase_requisition' => 'สามารถสร้าง PO ได้เฉพาะใบขอซื้อสถานะ approved เท่านั้น']);
        }

        $requisitionRevision = $purchaseRequisition->currentRevision;
        if (! $requisitionRevision || $requisitionRevision->status !== 'approved') {
            throw ValidationException::withMessages(['purchase_requisition' => 'ไม่พบ revision ที่อนุมัติแล้ว']);
        }

        $data = $request->validate([
            'order_date' => ['required', 'date'],
            'vendor_reference_no' => ['nullable', 'string', 'max:255'],
        ]);

        $purchaseOrder = DB::transaction(function () use ($purchaseRequisition, $requisitionRevision, $data) {
            return $this->purchaseFlow->createPurchaseOrderFromRequisition($purchaseRequisition, $requisitionRevision, [
                'status' => 'draft',
                'order_date' => $data['order_date'],
                'vendor_reference_no' => $data['vendor_reference_no'] ?? null,
                'create_by' => $this->createBy(),
            ]);
        });

        return redirect()->route('office.purchase-orders.show', $purchaseOrder)
            ->with('success', 'สร้างใบสั่งซื้อ (PO) เรียบร้อยแล้ว');
    }

    public function edit(PurchaseOrder $purchaseOrder): View
    {
        $this->assertPurchaseOrderOwned($purchaseOrder);
        $revision = $purchaseOrder->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            abort(422, 'แก้ไขได้เฉพาะใบสั่งซื้อสถานะ draft');
        }

        $purchaseOrder->load(['partner.contacts', 'partner.branches', 'purchaseRequisition', 'currentRevision.lines']);

        return view('office.purchase-orders.form', array_merge(
            $this->editable->formViewDataFromRevision(
                $purchaseOrder->partner,
                $this->createBy(),
                $revision,
            ),
            [
                'standalone' => true,
                'purchaseOrder' => $purchaseOrder,
                'revision' => $revision,
                'seedLines' => old('line')
                    ? $this->editable->seedLinesFromOldInput()
                    : $this->editable->seedLinesFromRevision($revision),
            ],
        ));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->assertPurchaseOrderOwned($purchaseOrder);
        $revision = $purchaseOrder->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'แก้ไขได้เฉพาะใบสั่งซื้อสถานะ draft']);
        }

        $data = $request->validate($this->editable->payloadRules([
            'order_date' => ['required', 'date'],
            'vendor_reference_no' => ['nullable', 'string', 'max:255'],
        ], withPayment: false));

        $partner = $this->editable->resolveOptionalPartner($this->businessId(), $data['partner_id'] ?? null);
        $lines = $this->editable->filterLines(
            $this->editable->normalizeLines($data['line'] ?? [], $data['discount_type'] ?? 'percent')
        );

        DB::transaction(function () use ($purchaseOrder, $revision, $partner, $data, $lines) {
            $purchaseOrder->partner_id = $partner?->id;
            $purchaseOrder->partner_branch_id = $partner?->branches()->value('id');
            $purchaseOrder->partner_contact_id = $partner?->contacts()->value('id');
            $purchaseOrder->save();

            $revision->fill(array_merge([
                'order_date' => $data['order_date'],
                'vendor_reference_no' => $data['vendor_reference_no'] ?? null,
            ], $this->editable->partyFieldsFromRequest($data), $this->editable->totalsFieldsFromRequest($data)));
            $revision->save();

            $this->editable->replaceLinesOnRevision(
                $revision,
                $lines,
                $data,
                PurchaseOrderLine::class,
                'purchase_order_revision_id',
            );
        });

        return redirect()->route('office.purchase-orders.show', $purchaseOrder)
            ->with('success', 'บันทึกใบสั่งซื้อเรียบร้อยแล้ว');
    }

    public function destroy(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->assertPurchaseOrderOwned($purchaseOrder);
        $revision = $purchaseOrder->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'ลบได้เฉพาะใบสั่งซื้อสถานะ draft']);
        }

        $purchaseOrder->delete();

        return redirect()->route('office.purchase-orders.index')
            ->with('success', 'ลบใบสั่งซื้อเรียบร้อยแล้ว');
    }

    public function cancel(Request $request, PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->assertPurchaseOrderOwned($purchaseOrder);
        $revision = $purchaseOrder->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision ของใบสั่งซื้อ']);
        }

        if (! in_array($revision->status, ['draft', 'confirmed'], true)) {
            throw ValidationException::withMessages(['status' => 'ยกเลิกไม่ได้สำหรับสถานะนี้']);
        }

        $data = $request->validate([
            'cancelled_note' => ['nullable', 'string', 'max:1000'],
        ]);

        $revision->status = 'cancelled';
        $revision->cancelled_note = $data['cancelled_note'] ?? null;
        $revision->cancelled_at = now();
        $revision->save();

        return back()->with('success', 'ยกเลิกใบสั่งซื้อเรียบร้อยแล้ว');
    }

    public function show(PurchaseOrder $purchaseOrder): View
    {
        $this->assertPurchaseOrderOwned($purchaseOrder);
        $purchaseOrder->load([
            'purchaseRequisition',
            'purchaseRequisitionRevision',
            'partner',
            'partnerContact',
            'currentRevision.lines.item',
            'currentRevision.confirmedBy.employee.user',
            'revisions',
        ]);

        return view('office.purchase-orders.show', compact('purchaseOrder'));
    }

    public function confirm(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->assertPurchaseOrderOwned($purchaseOrder);
        $revision = $purchaseOrder->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'ยืนยันได้เฉพาะ PO ที่มีสถานะ draft']);
        }

        $prStatus = $purchaseOrder->purchaseRequisitionRevision?->status;
        if ($prStatus && in_array($prStatus, ['rejected', 'cancelled'], true)) {
            throw ValidationException::withMessages(['status' => 'ใบขอซื้อต้นทางถูกยกเลิกหรือปฏิเสธแล้ว ไม่สามารถยืนยัน PO ได้']);
        }

        DB::transaction(function () use ($purchaseOrder, $revision) {
            if (blank($purchaseOrder->partner_id)) {
                $this->partyInfo->linkPartnerFromRevisionSnapshot($purchaseOrder, $revision, $this->createBy());
                $purchaseOrder->refresh();
            }

            $revision->status = 'confirmed';
            $revision->confirmed_at = now();
            $revision->confirmed_by = $this->createBy();
            $revision->save();
        });

        return back()->with('success', 'ยืนยันใบสั่งซื้อ (PO) เรียบร้อยแล้ว');
    }

    public function amend(PurchaseOrder $purchaseOrder): RedirectResponse
    {
        $this->assertPurchaseOrderOwned($purchaseOrder);
        $current = $purchaseOrder->currentRevision;
        if (! $current || $current->status !== 'confirmed') {
            throw ValidationException::withMessages(['status' => 'แก้ไข revision ได้เฉพาะ PO ที่ยืนยันแล้ว']);
        }

        DB::transaction(function () use ($purchaseOrder, $current) {
            $current->load(['lines']);

            $revision = new PurchaseOrderRevision(array_merge([
                'purchase_order_id' => $purchaseOrder->id,
                'revision_no' => ((int) $current->revision_no) + 1,
                'supersedes_revision_id' => $current->id,
                'status' => 'draft',
                'order_date' => $current->order_date,
                'vendor_reference_no' => $current->vendor_reference_no,
                'currency' => $current->currency,
                'subtotal' => $current->subtotal,
                'discount_amount' => $current->discount_amount,
                'vat_amount' => $current->vat_amount,
                'total_amount' => $current->total_amount,
                'create_by' => $this->createBy(),
            ], $this->purchaseFlow->partySnapshotFromRevision($current)));
            $revision->save();

            foreach ($current->lines as $line) {
                PurchaseOrderLine::create([
                    'purchase_order_revision_id' => $revision->id,
                    'purchase_requisition_line_id' => $line->purchase_requisition_line_id,
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

            $purchaseOrder->current_revision_id = $revision->id;
            $purchaseOrder->save();
        });

        return redirect()->route('office.purchase-orders.edit', $purchaseOrder)
            ->with('success', 'สร้าง revision ใหม่เรียบร้อยแล้ว แก้ไขได้ก่อนยืนยัน');
    }

    private function assertPurchaseRequisitionOwned(PurchaseRequisition $purchaseRequisition): void
    {
        if ((string) $purchaseRequisition->business_id !== $this->businessId()) {
            abort(403);
        }
    }

    private function assertPurchaseOrderOwned(PurchaseOrder $purchaseOrder): void
    {
        if ((string) $purchaseOrder->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
