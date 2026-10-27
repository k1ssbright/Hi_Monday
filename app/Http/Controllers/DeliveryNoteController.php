<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesEditableDocumentCrud;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Sales\BillingNote;
use App\Models\Sales\DeliveryNote;
use App\Models\Sales\DeliveryNoteLine;
use App\Models\Sales\DeliveryNoteRevision;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderRevision;
use App\Services\DocumentPartyService;
use App\Services\EditableSalesDocumentService;
use App\Services\SalesFlowService;
use App\Services\SalesJourneyPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DeliveryNoteController extends Controller
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
        $query = DeliveryNote::query()
            ->where('business_id', $this->businessId())
            ->with(['currentRevision', 'partner', 'salesOrder'])
            ->latest('created_at');

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q): void {
                $builder->where('delivery_note_no', 'like', "%{$q}%")
                    ->orWhereHas('partner', fn ($partner) => $partner->where('partner_name_th', 'like', "%{$q}%"))
                    ->orWhereHas('salesOrder', fn ($so) => $so->where('sales_order_no', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            $query->whereHas('currentRevision', fn ($revision) => $revision->where('status', $status));
        }

        $deliveryNotes = $query->paginate(20)->withQueryString();

        return view('office.delivery-notes.index', compact('deliveryNotes'));
    }

    public function createDirect(): View
    {
        return view('office.delivery-notes.form', array_merge(
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

        $deliveryNote = DB::transaction(function () use ($partner, $data, $lines, $businessId, $createBy) {
            $deliveryNote = new DeliveryNote([
                'business_id' => $businessId,
                'business_branch_id' => currentBusinessBranchId(),
                'opportunity_id' => null,
                'sales_order_id' => null,
                'sales_order_revision_id' => null,
                'billing_note_id' => null,
                'billing_note_revision_id' => null,
                'partner_id' => $partner?->id,
                'partner_branch_id' => $partner?->branches()->value('id'),
                'partner_contact_id' => $partner?->contacts()->value('id'),
                'create_by' => $createBy,
            ]);
            $this->salesFlow->allocateDeliveryNoteNo($deliveryNote);
            $deliveryNote->save();

            $revision = new DeliveryNoteRevision(array_merge([
                'delivery_note_id' => $deliveryNote->id,
                'revision_no' => 1,
                'status' => 'draft',
                'issue_date' => $data['issue_date'],
                'currency' => 'THB',
                'note' => $data['note'] ?? null,
                'create_by' => $createBy,
            ], $this->editable->partyFieldsFromRequest($data), $this->editable->totalsFieldsFromRequest($data)));
            $revision->save();

            $deliveryNote->current_revision_id = $revision->id;
            $deliveryNote->save();

            $this->editable->persistLines($revision, $lines, $data, DeliveryNoteLine::class, 'delivery_note_revision_id');

            return $deliveryNote;
        });

        return redirect()->route('office.delivery-notes.show', $deliveryNote)
            ->with('success', 'สร้าง Delivery Note เรียบร้อยแล้ว');
    }

    public function create(Request $request, SalesOrder $salesOrder): View
    {
        $this->assertSalesOrderOwned($salesOrder);
        if (! $salesOrder->canCreateDeliveryNote()) {
            abort(422, 'สามารถสร้าง Delivery Note ได้เฉพาะ Sales Order สถานะ confirmed เท่านั้น');
        }

        $salesOrder->load(['currentRevision.lines', 'partner', 'partnerContact', 'billingNotes.currentRevision']);

        $billingNote = null;
        if ($request->filled('billing_note_id')) {
            $billingNote = BillingNote::query()
                ->where('business_id', $this->businessId())
                ->where('sales_order_id', $salesOrder->id)
                ->findOrFail($request->string('billing_note_id')->toString());
        }

        return view('office.delivery-notes.form', compact('salesOrder', 'billingNote'));
    }

    public function store(Request $request, SalesOrder $salesOrder): RedirectResponse
    {
        $this->assertSalesOrderOwned($salesOrder);
        if (! $salesOrder->canCreateDeliveryNote()) {
            throw ValidationException::withMessages(['sales_order' => 'สามารถสร้าง Delivery Note ได้เฉพาะ Sales Order สถานะ confirmed เท่านั้น']);
        }

        $soRevision = $salesOrder->currentRevision;
        if (! $soRevision || $soRevision->status !== 'confirmed') {
            throw ValidationException::withMessages(['sales_order' => 'ไม่พบ Sales Order revision ที่ยืนยันแล้ว']);
        }

        $data = $request->validate([
            'issue_date' => ['required', 'date'],
            'note' => ['nullable', 'string'],
            'billing_note_id' => ['nullable', 'uuid'],
        ]);

        $billingNote = null;
        if (! empty($data['billing_note_id'])) {
            $billingNote = BillingNote::query()
                ->where('business_id', $this->businessId())
                ->where('sales_order_id', $salesOrder->id)
                ->findOrFail($data['billing_note_id']);
        }

        $deliveryNote = DB::transaction(function () use ($salesOrder, $soRevision, $data, $billingNote) {
            $deliveryNote = new DeliveryNote([
                'business_id' => $salesOrder->business_id,
                'business_branch_id' => $salesOrder->business_branch_id,
                'opportunity_id' => $salesOrder->opportunity_id,
                'sales_order_id' => $salesOrder->id,
                'sales_order_revision_id' => $soRevision->id,
                'billing_note_id' => $billingNote?->id,
                'billing_note_revision_id' => $billingNote?->current_revision_id,
                'partner_id' => $salesOrder->partner_id,
                'partner_branch_id' => $salesOrder->partner_branch_id,
                'partner_contact_id' => $salesOrder->partner_contact_id,
                'create_by' => $this->createBy(),
            ]);
            $this->salesFlow->allocateDeliveryNoteNo($deliveryNote);
            $deliveryNote->save();

            $revision = new DeliveryNoteRevision([
                'delivery_note_id' => $deliveryNote->id,
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

            $deliveryNote->current_revision_id = $revision->id;
            $deliveryNote->save();

            $this->copySalesOrderLines($soRevision, $revision);

            return $deliveryNote;
        });

        return redirect()->route('office.delivery-notes.show', $deliveryNote)
            ->with('success', 'สร้าง Delivery Note เรียบร้อยแล้ว');
    }

    public function edit(DeliveryNote $deliveryNote): View
    {
        $this->assertDeliveryNoteOwned($deliveryNote);
        $revision = $deliveryNote->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            abort(422, 'แก้ไขได้เฉพาะ Delivery Note สถานะ draft');
        }

        return $this->editableDocumentEditView(
            $deliveryNote,
            $revision,
            'office.delivery-notes.form',
            'deliveryNote',
            ['draft'],
        );
    }

    public function update(Request $request, DeliveryNote $deliveryNote): RedirectResponse
    {
        $this->assertDeliveryNoteOwned($deliveryNote);
        $revision = $deliveryNote->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        return $this->editableDocumentUpdate(
            $request,
            $deliveryNote,
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
            DeliveryNoteLine::class,
            'delivery_note_revision_id',
            'office.delivery-notes.show',
        );
    }

    public function destroy(DeliveryNote $deliveryNote): RedirectResponse
    {
        $this->assertDeliveryNoteOwned($deliveryNote);
        $revision = $deliveryNote->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        return $this->editableDocumentDestroy(
            $deliveryNote,
            $revision,
            'office.delivery-notes.index',
        );
    }

    public function cancel(DeliveryNote $deliveryNote): RedirectResponse
    {
        $this->assertDeliveryNoteOwned($deliveryNote);
        $revision = $deliveryNote->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        return $this->editableDocumentVoid($revision);
    }

    public function show(DeliveryNote $deliveryNote): View
    {
        $this->assertDeliveryNoteOwned($deliveryNote);
        $deliveryNote->load([
            'salesOrder',
            'salesOrderRevision',
            'billingNote',
            'partner',
            'partnerContact',
            'currentRevision.lines.item',
            'revisions',
        ]);

        $salesJourney = app(SalesJourneyPresenter::class)->for($deliveryNote);

        return view('office.delivery-notes.show', compact('deliveryNote', 'salesJourney'));
    }

    public function issue(DeliveryNote $deliveryNote): RedirectResponse
    {
        $this->assertDeliveryNoteOwned($deliveryNote);
        $revision = $deliveryNote->currentRevision;
        if (! $revision || $revision->status !== 'draft') {
            throw ValidationException::withMessages(['status' => 'ออกเอกสารได้เฉพาะ Delivery Note สถานะ draft']);
        }

        DB::transaction(function () use ($deliveryNote, $revision) {
            if (blank($deliveryNote->partner_id)) {
                $this->partyInfo->linkPartnerFromRevisionSnapshot($deliveryNote, $revision, $this->createBy());
                $deliveryNote->refresh();
            }

            $revision->status = 'issued';
            $revision->issued_at = now();
            $revision->save();
        });

        return back()->with('success', 'ออก Delivery Note เรียบร้อยแล้ว');
    }

    private function copySalesOrderLines(SalesOrderRevision $soRevision, DeliveryNoteRevision $revision): void
    {
        $soRevision->loadMissing('lines');
        foreach ($soRevision->lines as $line) {
            DeliveryNoteLine::create([
                'delivery_note_revision_id' => $revision->id,
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

    private function assertDeliveryNoteOwned(DeliveryNote $deliveryNote): void
    {
        if ((string) $deliveryNote->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
