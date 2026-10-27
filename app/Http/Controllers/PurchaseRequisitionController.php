<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Partner\Partner;
use App\Models\Purchasing\PurchaseRequisition;
use App\Models\Purchasing\PurchaseRequisitionLine;
use App\Models\Purchasing\PurchaseRequisitionRevision;
use App\Services\DocumentPartyService;
use App\Services\EditableSalesDocumentService;
use App\Services\PurchaseFlowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PurchaseRequisitionController extends Controller
{
    use ResolvesBusinessContext;

    public function __construct(
        private PurchaseFlowService $purchaseFlow,
        private EditableSalesDocumentService $editable,
        private DocumentPartyService $partyInfo,
    ) {}

    public function index(Request $request): View
    {
        $query = PurchaseRequisition::query()
            ->where('business_id', $this->businessId())
            ->with(['currentRevision', 'partner'])
            ->latest('created_at');

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q): void {
                $builder->where('purchase_requisition_no', 'like', "%{$q}%")
                    ->orWhereHas('partner', fn ($partner) => $partner->where('partner_name_th', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            $query->whereHas('currentRevision', fn ($revision) => $revision->where('status', $status));
        }

        $purchaseRequisitions = $query->paginate(20)->withQueryString();

        return view('office.purchase-requisitions.index', compact('purchaseRequisitions'));
    }

    public function createDirect(Request $request): View
    {
        $partner = null;

        if ($request->filled('partner_id')) {
            $partner = Partner::query()
                ->where('business_id', $this->businessId())
                ->with(['contacts', 'branches'])
                ->find($request->string('partner_id')->toString());
        }

        return view('office.purchase-requisitions.form', array_merge(
            $this->editable->formViewData($partner, $this->createBy()),
            [
                'partner' => $partner,
                'seedLines' => $this->editable->seedLinesFromOldInput(),
            ],
        ));
    }

    public function storeStandalone(Request $request): RedirectResponse
    {
        $data = $request->validate(array_merge($this->editable->payloadRules([
            'request_date' => ['required', 'date'],
            'required_date' => ['nullable', 'date', 'after_or_equal:request_date'],
            'note' => ['nullable', 'string'],
        ], withPayment: false), [
            'partner_id' => ['nullable', 'uuid'],
        ]));

        $partner = $this->editable->resolveOptionalPartner($this->businessId(), $data['partner_id'] ?? null);
        $lines = $this->editable->filterLines(
            $this->editable->normalizeLines($data['line'] ?? [], $data['discount_type'] ?? 'percent')
        );
        $businessId = $this->businessId();
        $createBy = $this->createBy();

        $purchaseRequisition = DB::transaction(function () use ($partner, $data, $lines, $businessId, $createBy) {
            $purchaseRequisition = new PurchaseRequisition([
                'business_id' => $businessId,
                'business_branch_id' => currentBusinessBranchId(),
                'partner_id' => $partner?->id,
                'partner_branch_id' => $partner?->branches()->value('id'),
                'partner_contact_id' => $partner?->contacts()->value('id'),
                'create_by' => $createBy,
            ]);
            $this->purchaseFlow->allocatePurchaseRequisitionNo($purchaseRequisition);
            $purchaseRequisition->save();

            $revision = new PurchaseRequisitionRevision(array_merge([
                'purchase_requisition_id' => $purchaseRequisition->id,
                'revision_no' => 1,
                'status' => 'draft',
                'request_date' => $data['request_date'],
                'required_date' => $data['required_date'] ?? null,
                'currency' => 'THB',
                'note' => $data['note'] ?? null,
                'create_by' => $createBy,
            ], $this->editable->partyFieldsFromRequest($data), $this->editable->totalsFieldsFromRequest($data)));
            $revision->save();

            $purchaseRequisition->current_revision_id = $revision->id;
            $purchaseRequisition->save();

            $this->editable->persistLines(
                $revision,
                $lines,
                $data,
                PurchaseRequisitionLine::class,
                'purchase_requisition_revision_id',
            );

            return $purchaseRequisition;
        });

        return redirect()->route('office.purchase-requisitions.show', $purchaseRequisition)
            ->with('success', 'สร้างใบขอซื้อ (PR) เรียบร้อยแล้ว');
    }

    public function edit(PurchaseRequisition $purchaseRequisition): View
    {
        $this->assertPurchaseRequisitionOwned($purchaseRequisition);
        $revision = $purchaseRequisition->currentRevision;
        if (! $revision || ! in_array($revision->status, ['draft', 'rejected'], true)) {
            abort(422, 'แก้ไขได้เฉพาะใบขอซื้อสถานะ draft หรือ rejected');
        }

        $purchaseRequisition->load(['partner.contacts', 'partner.branches', 'currentRevision.lines']);

        return view('office.purchase-requisitions.form', array_merge(
            $this->editable->formViewDataFromRevision(
                $purchaseRequisition->partner,
                $this->createBy(),
                $revision,
            ),
            [
                'purchaseRequisition' => $purchaseRequisition,
                'revision' => $revision,
                'seedLines' => old('line')
                    ? $this->editable->seedLinesFromOldInput()
                    : $this->editable->seedLinesFromRevision($revision),
            ],
        ));
    }

    public function update(Request $request, PurchaseRequisition $purchaseRequisition): RedirectResponse
    {
        $this->assertPurchaseRequisitionOwned($purchaseRequisition);
        $revision = $purchaseRequisition->currentRevision;
        if (! $revision || ! in_array($revision->status, ['draft', 'rejected'], true)) {
            throw ValidationException::withMessages(['status' => 'แก้ไขได้เฉพาะใบขอซื้อสถานะ draft หรือ rejected']);
        }

        $data = $request->validate(array_merge($this->editable->payloadRules([
            'request_date' => ['required', 'date'],
            'required_date' => ['nullable', 'date', 'after_or_equal:request_date'],
            'note' => ['nullable', 'string'],
        ], withPayment: false), [
            'partner_id' => ['nullable', 'uuid'],
        ]));

        $partner = $this->editable->resolveOptionalPartner($this->businessId(), $data['partner_id'] ?? null);
        $lines = $this->editable->filterLines(
            $this->editable->normalizeLines($data['line'] ?? [], $data['discount_type'] ?? 'percent')
        );

        DB::transaction(function () use ($purchaseRequisition, $revision, $partner, $data, $lines) {
            $purchaseRequisition->partner_id = $partner?->id;
            $purchaseRequisition->partner_branch_id = $partner?->branches()->value('id');
            $purchaseRequisition->partner_contact_id = $partner?->contacts()->value('id');
            $purchaseRequisition->save();

            $revision->fill(array_merge([
                'request_date' => $data['request_date'],
                'required_date' => $data['required_date'] ?? null,
                'note' => $data['note'] ?? null,
                'status' => 'draft',
                'rejected_note' => null,
                'rejected_at' => null,
            ], $this->editable->partyFieldsFromRequest($data), $this->editable->totalsFieldsFromRequest($data)));
            $revision->save();

            $this->editable->replaceLinesOnRevision(
                $revision,
                $lines,
                $data,
                PurchaseRequisitionLine::class,
                'purchase_requisition_revision_id',
            );
        });

        return redirect()->route('office.purchase-requisitions.show', $purchaseRequisition)
            ->with('success', 'บันทึกใบขอซื้อเรียบร้อยแล้ว');
    }

    public function destroy(PurchaseRequisition $purchaseRequisition): RedirectResponse
    {
        $this->assertPurchaseRequisitionOwned($purchaseRequisition);
        $revision = $purchaseRequisition->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'ลบได้เฉพาะใบขอซื้อสถานะ draft']);
        }

        $purchaseRequisition->delete();

        return redirect()->route('office.purchase-requisitions.index')
            ->with('success', 'ลบใบขอซื้อเรียบร้อยแล้ว');
    }

    public function cancel(PurchaseRequisition $purchaseRequisition): RedirectResponse
    {
        $this->assertPurchaseRequisitionOwned($purchaseRequisition);
        $revision = $purchaseRequisition->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision ของใบขอซื้อ']);
        }

        if (! in_array($revision->status, ['draft', 'submitted', 'approved'], true)) {
            throw ValidationException::withMessages(['status' => 'ยกเลิกไม่ได้สำหรับสถานะนี้']);
        }

        if ($revision->status === 'approved' && $purchaseRequisition->purchaseOrders()->exists()) {
            throw ValidationException::withMessages(['status' => 'ไม่สามารถยกเลิก PR ที่มี PO แล้ว']);
        }

        $revision->status = 'cancelled';
        $revision->save();

        return back()->with('success', 'ยกเลิกใบขอซื้อเรียบร้อยแล้ว');
    }

    public function show(PurchaseRequisition $purchaseRequisition): View
    {
        $this->assertPurchaseRequisitionOwned($purchaseRequisition);
        $purchaseRequisition->load([
            'partner',
            'partnerContact',
            'currentRevision.lines.item',
            'currentRevision.submittedBy.employee.user',
            'currentRevision.approvedBy.employee.user',
            'revisions',
            'purchaseOrders',
        ]);

        return view('office.purchase-requisitions.show', compact('purchaseRequisition'));
    }

    public function submit(PurchaseRequisition $purchaseRequisition): RedirectResponse
    {
        $this->assertPurchaseRequisitionOwned($purchaseRequisition);
        $revision = $purchaseRequisition->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'ส่งอนุมัติได้เฉพาะใบขอซื้อสถานะ draft']);
        }

        $revision->status = 'submitted';
        $revision->submitted_at = now();
        $revision->submitted_by = $this->createBy();
        $revision->save();

        return back()->with('success', 'ส่งใบขอซื้อเพื่ออนุมัติเรียบร้อยแล้ว');
    }

    public function approve(PurchaseRequisition $purchaseRequisition): RedirectResponse
    {
        $this->assertPurchaseRequisitionOwned($purchaseRequisition);
        $revision = $purchaseRequisition->currentRevision;
        if (! $revision || $revision->status !== 'submitted') {
            throw ValidationException::withMessages(['status' => 'อนุมัติได้เฉพาะใบขอซื้อสถานะ submitted']);
        }

        DB::transaction(function () use ($purchaseRequisition, $revision) {
            if (blank($purchaseRequisition->partner_id)) {
                $this->partyInfo->linkPartnerFromRevisionSnapshot($purchaseRequisition, $revision, $this->createBy());
            }

            $revision->status = 'approved';
            $revision->approved_at = now();
            $revision->approved_by = $this->createBy();
            $revision->save();
        });

        return back()->with('success', 'อนุมัติใบขอซื้อเรียบร้อยแล้ว');
    }

    public function reject(Request $request, PurchaseRequisition $purchaseRequisition): RedirectResponse
    {
        $this->assertPurchaseRequisitionOwned($purchaseRequisition);
        $revision = $purchaseRequisition->currentRevision;
        if (! $revision || $revision->status !== 'submitted') {
            throw ValidationException::withMessages(['status' => 'ปฏิเสธได้เฉพาะใบขอซื้อสถานะ submitted']);
        }

        $data = $request->validate([
            'rejected_note' => ['required', 'string', 'max:1000'],
        ]);

        $revision->status = 'rejected';
        $revision->rejected_note = $data['rejected_note'];
        $revision->rejected_at = now();
        $revision->save();

        return back()->with('success', 'บันทึกการปฏิเสธใบขอซื้อเรียบร้อยแล้ว');
    }

    private function assertPurchaseRequisitionOwned(PurchaseRequisition $purchaseRequisition): void
    {
        if ((string) $purchaseRequisition->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
