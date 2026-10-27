<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Address\Amphures;
use App\Models\Address\Provinces;
use App\Models\Address\Tambons;
use App\Models\Partner\Partner;
use App\Models\Sales\Opportunity;
use App\Models\Sales\Quotation;
use App\Models\Sales\QuotationLine;
use App\Models\Sales\QuotationRevision;
use App\Models\Sales\SalesDocumentAttachment;
use App\Services\DocumentAttachmentService;
use App\Services\DocumentPartyService;
use App\Services\DocumentTotalsService;
use App\Services\PaymentTermsService;
use App\Services\SalesFlowService;
use App\Services\SalesJourneyPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class QuotationController extends Controller
{
    use ResolvesBusinessContext;

    public function __construct(
        private SalesFlowService $salesFlow,
        private PaymentTermsService $paymentTerms,
        private DocumentAttachmentService $attachments,
        private DocumentPartyService $partyInfo,
        private DocumentTotalsService $totals,
    ) {}

    public function index(Request $request): View
    {
        $query = Quotation::query()
            ->where('business_id', $this->businessId())
            ->with(['currentRevision', 'partner', 'opportunity'])
            ->latest('created_at');

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q): void {
                $builder->where('quotation_no', 'like', "%{$q}%")
                    ->orWhereHas('partner', fn ($partner) => $partner->where('partner_name_th', 'like', "%{$q}%"))
                    ->orWhereHas('opportunity', fn ($op) => $op->where('opportunity_no', 'like', "%{$q}%")->orWhere('name', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            $query->whereHas('currentRevision', fn ($revision) => $revision->where('status', $status));
        }

        $quotations = $query->paginate(20)->withQueryString();

        return view('office.quotations.index', compact('quotations'));
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

        return view('office.quotations.form', $this->quotationFormViewData(null, $partner));
    }

    public function createStandalone(Request $request): RedirectResponse
    {
        return redirect()->route('office.quotations.create', array_filter([
            'partner_id' => $request->input('partner_id'),
        ]));
    }

    public function storeStandalone(Request $request): RedirectResponse
    {
        $data = $request->validate(array_merge($this->quotationPayloadRules(), [
            'partner_id' => ['nullable', 'uuid'],
        ]));

        $partner = null;
        if (filled($data['partner_id'] ?? null)) {
            $partner = Partner::query()
                ->where('business_id', $this->businessId())
                ->findOrFail($data['partner_id']);
        }

        $lines = $this->filterQuotationLines(
            $this->normalizeQuotationLines($data['line'] ?? [], $data['discount_type'] ?? 'percent')
        );

        $businessId = $this->businessId();

        $quotation = DB::transaction(function () use ($partner, $data, $lines, $businessId) {
            $quotation = new Quotation([
                'business_id' => $businessId,
                'business_branch_id' => currentBusinessBranchId(),
                'opportunity_id' => null,
                'partner_id' => $partner?->id,
                'partner_branch_id' => $partner?->branches()->value('id'),
                'partner_contact_id' => $partner?->contacts()->value('id'),
                'create_by' => $this->createBy(),
            ]);
            $this->salesFlow->allocateQuotationNo($quotation);
            $quotation->save();

            $revision = new QuotationRevision(array_merge([
                'quotation_id' => $quotation->id,
                'revision_no' => 1,
                'status' => 'draft',
                'issue_date' => $data['issue_date'],
                'valid_until' => $data['valid_until'] ?? null,
                'currency' => 'THB',
                'payment_unit_mode' => $data['payment_unit_mode'] ?? 'percent',
                'create_by' => $this->createBy(),
            ], $this->partyFieldsFromRequest($data), $this->totalsFieldsFromRequest($data)));
            $revision->save();

            $quotation->current_revision_id = $revision->id;
            $quotation->save();

            $this->persistLines($revision, $lines, $data);
            $this->paymentTerms->syncQuotationTerms(
                $revision,
                $data['payment'] ?? [],
                $data['payment_unit_mode'] ?? 'percent',
            );

            return $quotation;
        });

        return redirect()->route('office.quotations.show', $quotation)
            ->with('success', 'สร้าง Quotation เรียบร้อยแล้ว');
    }

    public function create(Opportunity $opportunity): View
    {
        $this->assertOpportunityOwned($opportunity);
        $opportunity->load(['lines.item', 'partner.contacts', 'partner.branches', 'partnerBranch', 'partnerContact']);

        return view('office.quotations.form', $this->quotationFormViewData($opportunity, $opportunity->partner));
    }

    public function store(Request $request, Opportunity $opportunity): RedirectResponse
    {
        $this->assertOpportunityOwned($opportunity);
        $opportunity->load(['lines']);

        $data = $request->validate($this->quotationPayloadRules());
        $lines = $this->filterQuotationLines(
            $this->normalizeQuotationLines($data['line'] ?? [], $data['discount_type'] ?? 'percent')
        );

        $quotation = DB::transaction(function () use ($opportunity, $data, $lines) {
            $quotation = new Quotation([
                'business_id' => $opportunity->business_id,
                'business_branch_id' => $opportunity->business_branch_id,
                'opportunity_id' => $opportunity->id,
                'partner_id' => $opportunity->partner_id,
                'partner_branch_id' => $opportunity->partner_branch_id,
                'partner_contact_id' => $opportunity->partner_contact_id,
                'create_by' => $this->createBy(),
            ]);
            $this->salesFlow->allocateQuotationNo($quotation);
            $quotation->save();

            $revision = new QuotationRevision(array_merge([
                'quotation_id' => $quotation->id,
                'revision_no' => 1,
                'status' => 'draft',
                'issue_date' => $data['issue_date'],
                'valid_until' => $data['valid_until'] ?? null,
                'currency' => $opportunity->currency ?: 'THB',
                'payment_unit_mode' => $data['payment_unit_mode'] ?? 'percent',
                'create_by' => $this->createBy(),
            ], $this->partyFieldsFromRequest($data), $this->totalsFieldsFromRequest($data)));
            $revision->save();

            $quotation->current_revision_id = $revision->id;
            $quotation->save();

            $this->persistLines($revision, $lines, $data);
            $this->paymentTerms->syncQuotationTerms(
                $revision,
                $data['payment'] ?? [],
                $data['payment_unit_mode'] ?? 'percent',
            );

            if ($opportunity->stage === 'discovery' || $opportunity->stage === 'needs_analysis') {
                $opportunity->stage = 'proposal';
                $opportunity->probability = max((int) $opportunity->probability, 50);
                $opportunity->save();
            }

            return $quotation;
        });

        return redirect()->route('office.quotations.show', $quotation)
            ->with('success', 'สร้าง Quotation เรียบร้อยแล้ว');
    }

    public function edit(Quotation $quotation): View
    {
        $this->assertQuotationOwned($quotation);
        $revision = $quotation->currentRevision;
        if (! $revision || ! in_array($revision->status, ['draft', 'rejected'], true)) {
            abort(422, 'แก้ไขได้เฉพาะ Quotation สถานะ draft หรือ rejected');
        }

        $quotation->load(['partner.contacts', 'partner.branches', 'opportunity', 'currentRevision.lines', 'currentRevision.paymentTerms']);

        $viewData = $this->quotationFormViewData($quotation->opportunity, $quotation->partner);
        $viewData = array_merge($viewData, [
            'quotation' => $quotation,
            'revision' => $revision,
            'issueDate' => old('issue_date', $revision->issue_date?->format('Y-m-d') ?? now()->toDateString()),
            'validUntil' => old('valid_until', $revision->valid_until?->format('Y-m-d')),
            'customerParty' => $this->partyInfo->mergeWithOldInput($this->customerPartyFromRevision($revision)),
            'sellerParty' => $this->partyInfo->mergeWithOldInput($this->sellerPartyFromRevision($revision)),
            'totalsDefaults' => $this->partyInfo->mergeWithOldInput([
                'vat_type' => $revision->vat_type ?? 'exclusive',
                'discount_type' => 'percent',
            ]),
        ]);

        if (old('line')) {
            $viewData['seedLines'] = $this->normalizeQuotationLinesFromOld();
        } else {
            $viewData['seedLines'] = $revision->lines->map(function ($line) {
                $text = (string) $line->description;
                $name = $text;
                $detail = '';
                if (str_contains($text, "\n")) {
                    $parts = explode("\n", $text, 2);
                    $name = $parts[0];
                    $detail = $parts[1] ?? '';
                }

                return [
                    'opportunity_line_id' => $line->opportunity_line_id,
                    'product_service_item_id' => $line->product_service_item_id,
                    'description' => $name,
                    'detail' => $detail,
                    'qty' => $line->quantity,
                    'unit' => $line->unit ?? 'หน่วย',
                    'unit_price' => $line->unit_price,
                    'discount' => $line->discount_amount ?? 0,
                    'vat_percent' => $line->vat_percent ?? 7,
                ];
            })->all();
        }

        return view('office.quotations.form', $viewData);
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->assertQuotationOwned($quotation);
        $revision = $quotation->currentRevision;
        if (! $revision || ! in_array($revision->status, ['draft', 'rejected'], true)) {
            throw ValidationException::withMessages(['status' => 'แก้ไขได้เฉพาะ Quotation สถานะ draft หรือ rejected']);
        }

        $data = $request->validate(array_merge($this->quotationPayloadRules(), [
            'partner_id' => ['nullable', 'uuid'],
        ]));

        $partner = null;
        if (filled($data['partner_id'] ?? null)) {
            $partner = Partner::query()
                ->where('business_id', $this->businessId())
                ->findOrFail($data['partner_id']);
        }

        $lines = $this->filterQuotationLines(
            $this->normalizeQuotationLines($data['line'] ?? [], $data['discount_type'] ?? 'percent')
        );

        DB::transaction(function () use ($quotation, $revision, $partner, $data, $lines) {
            $quotation->partner_id = $partner?->id ?? $quotation->partner_id;
            if ($partner) {
                $quotation->partner_branch_id = $partner->branches()->value('id');
                $quotation->partner_contact_id = $partner->contacts()->value('id');
            }
            $quotation->save();

            $revision->revision_no = ((int) ($revision->revision_no ?? 1)) + 1;
            $revision->fill(array_merge([
                'issue_date' => $data['issue_date'],
                'valid_until' => $data['valid_until'] ?? null,
                'status' => 'draft',
                'rejected_at' => null,
                'rejected_note' => null,
                'payment_unit_mode' => $data['payment_unit_mode'] ?? 'percent',
            ], $this->partyFieldsFromRequest($data), $this->totalsFieldsFromRequest($data)));
            $revision->save();


            QuotationLine::query()->where('quotation_revision_id', $revision->id)->delete();
            $this->persistLines($revision, $lines, $data);
            $this->paymentTerms->syncQuotationTerms(
                $revision,
                $data['payment'] ?? [],
                $data['payment_unit_mode'] ?? 'percent',
            );
        });

        return redirect()->route('office.quotations.show', $quotation)
            ->with('success', 'บันทึก Quotation เรียบร้อยแล้ว');
    }

    public function destroy(Quotation $quotation): RedirectResponse
    {
        $this->assertQuotationOwned($quotation);
        $revision = $quotation->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        $quotation->delete();

        return redirect()->route('office.quotations.index')
            ->with('success', 'ลบ Quotation เรียบร้อยแล้ว');
    }

    public function cancel(Quotation $quotation): RedirectResponse
    {
        $this->assertQuotationOwned($quotation);
        $revision = $quotation->currentRevision;
        if (! $revision) {
            throw ValidationException::withMessages(['status' => 'ไม่พบ revision']);
        }

        if (! in_array($revision->status, ['draft', 'sent', 'approved'], true)) {
            throw ValidationException::withMessages(['status' => 'ยกเลิกไม่ได้สำหรับสถานะนี้']);
        }

        if ($quotation->salesOrders()->exists()) {
            throw ValidationException::withMessages(['status' => 'ไม่สามารถยกเลิก Quotation ที่มี Sales Order แล้ว']);
        }

        $revision->status = 'cancelled';
        $revision->save();

        return back()->with('success', 'ยกเลิก Quotation เรียบร้อยแล้ว');
    }

    public function show(Quotation $quotation): View
    {
        $this->assertQuotationOwned($quotation);
        $quotation->load([
            'opportunity.partner',
            'opportunity.owner.employee.user',
            'partner',
            'partnerContact',
            'currentRevision.lines.item',
            'currentRevision.paymentTerms',
            'currentRevision.attachments',
            'currentRevision.customerProvince',
            'currentRevision.customerAmphure',
            'currentRevision.customerTambon',
            'revisions',
            'salesOrders.currentRevision',
        ]);

        $salesJourney = app(SalesJourneyPresenter::class)->for($quotation);

        return view('office.quotations.show', compact('quotation', 'salesJourney'));
    }

    public function send(Quotation $quotation): RedirectResponse
    {
        $this->assertQuotationOwned($quotation);
        $revision = $quotation->currentRevision;
        if (! $revision || ! in_array($revision->status, ['draft', 'approved'], true)) {
            throw ValidationException::withMessages(['status' => 'สถานะ Quotation ไม่พร้อมสำหรับการส่ง']);
        }
        $revision->status = 'sent';
        $revision->sent_at = now();
        $revision->save();

        return back()->with('success', 'ส่ง Quotation เรียบร้อยแล้ว');
    }

    public function accept(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->assertQuotationOwned($quotation);
        $revision = $quotation->currentRevision;
        if (! $revision || ! in_array($revision->status, ['sent', 'approved'], true)) {
            throw ValidationException::withMessages(['status' => 'ยืนยันการตอบรับได้เฉพาะ Quotation ที่อยู่ในสถานะส่งแล้ว']);
        }
        if ($quotation->salesOrders()->exists()) {
            throw ValidationException::withMessages(['quotation' => 'Quotation นี้มี Sales Order อยู่แล้ว']);
        }

        $data = $request->validate([
            'accepted_note' => ['nullable', 'string'],
            'customer_po_no' => ['nullable', 'string', 'max:255'],
            'po_file' => ['nullable', 'file', 'max:10240'],
            'extra_files' => ['nullable', 'array'],
            'extra_files.*' => ['file', 'max:10240'],
        ]);

        $createBy = $this->createBy();

        $salesOrder = DB::transaction(function () use ($quotation, $revision, $data, $request, $createBy) {
            if ($quotation->opportunity_id) {
                $otherCurrentRevisionIds = Quotation::query()
                    ->where('opportunity_id', $quotation->opportunity_id)
                    ->where('id', '!=', $quotation->id)
                    ->whereNotNull('current_revision_id')
                    ->pluck('current_revision_id');

                QuotationRevision::query()
                    ->whereIn('id', $otherCurrentRevisionIds)
                    ->whereIn('status', ['sent', 'approved'])
                    ->update(['status' => 'superseded']);
            }
            $revision->status = 'accepted';
            $revision->accepted_at = now();
            $revision->accepted_note = $data['accepted_note'] ?? null;
            $revision->customer_po_no = $data['customer_po_no'] ?? null;
            $revision->save();

            if ($request->file('po_file')) {
                $this->attachments->storeOne(
                    $revision,
                    $quotation->business_id,
                    $createBy,
                    SalesDocumentAttachment::KIND_PO,
                    $request->file('po_file'),
                );
            }

            $this->attachments->storeMany(
                $revision,
                $quotation->business_id,
                $createBy,
                SalesDocumentAttachment::KIND_EXTRA,
                $request->file('extra_files'),
            );

            $salesOrder = $this->salesFlow->createSalesOrderFromQuotation($quotation, $revision->fresh('lines'), [
                'status' => 'confirmed',
                'order_date' => now()->toDateString(),
                'customer_po_no' => $revision->customer_po_no,
                'create_by' => $createBy,
                'confirmed_by' => $createBy,
            ]);

            $soRevision = $salesOrder->currentRevision;
            if ($soRevision) {
                $this->attachments->copyTo(
                    $soRevision,
                    $quotation->business_id,
                    $createBy,
                    $revision->attachments()->get()->all(),
                );
            }

            return $salesOrder;
        });

        return redirect()->route('office.sales-orders.show', $salesOrder)
            ->with('success', 'ยืนยัน Quotation และสร้าง Sales Order (confirmed) เรียบร้อยแล้ว');
    }

    public function reject(Request $request, Quotation $quotation): RedirectResponse
    {
        $this->assertQuotationOwned($quotation);
        $revision = $quotation->currentRevision;
        if (! $revision || ! in_array($revision->status, ['sent', 'approved'], true)) {
            throw ValidationException::withMessages(['status' => 'บันทึกการปฏิเสธได้เฉพาะ Quotation ที่อยู่ในสถานะส่งแล้ว']);
        }

        $data = $request->validate([
            'rejected_note' => ['required', 'string'],
        ]);

        $revision->status = 'rejected';
        $revision->rejected_at = now();
        $revision->rejected_note = $data['rejected_note'];
        $revision->save();

        return back()->with('success', 'บันทึกการปฏิเสธ Quotation เรียบร้อยแล้ว');
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     */
    private function persistLines(QuotationRevision $revision, array $lines, array $data = []): void
    {
        $vatType = $data['vat_type'] ?? $revision->vat_type ?? 'exclusive';
        $discountType = $data['discount_type'] ?? 'percent';

        foreach ($lines as $line) {
            $qty = (float) ($line['qty'] ?? 1);
            if ($qty <= 0) {
                $qty = 1;
            }
            $unitPrice = (float) ($line['unit_price'] ?? 0);
            $lineGross = RoundMoney($qty * $unitPrice);
            $vatPercent = array_key_exists('vat_percent', $line) && $line['vat_percent'] !== null && $line['vat_percent'] !== ''
                ? (float) $line['vat_percent']
                : 7.0;

            $lineTotals = $this->totals->calculateLine($line, $vatType, $discountType);
            $discountPercent = $discountType === 'baht' && $lineGross > 0
                ? round(($lineTotals['discount_amount'] / $lineGross) * 100, 2)
                : (float) ($line['discount_percent'] ?? 0);

            QuotationLine::create([
                'quotation_revision_id' => $revision->id,
                'opportunity_line_id' => $line['opportunity_line_id'] ?: null,
                'product_service_item_id' => $line['product_service_item_id'] ?: null,
                'description' => $line['description'],
                'quantity' => $qty,
                'unit' => $line['unit'] ?? null,
                'unit_price' => $unitPrice,
                'discount_percent' => $discountPercent,
                'discount_amount' => $lineTotals['discount_amount'],
                'vat_percent' => $vatPercent,
                'line_total' => $lineTotals['line_total'],
                'cost_reference' => 0,
            ]);
        }

        $documentTotals = $this->totals->calculate($lines, $vatType, false, null, $discountType);
        $revision->fill($this->totals->mapToRevisionTotals($documentTotals));
        $revision->vat_type = $vatType;
        $revision->save();
    }

    /**
     * @return array<string, mixed>
     */
    private function quotationPayloadRules(): array
    {
        return [
            'issue_date' => ['required', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'payment_unit_mode' => ['nullable', 'in:percent,baht'],
            'payment' => ['nullable', 'array'],
            'payment.*.installment_no' => ['nullable', 'integer', 'min:1'],
            'payment.*.condition' => ['nullable', 'string'],
            'payment.*.due_days' => ['nullable', 'integer', 'min:0'],
            'payment.*.percent_amount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'payment.*.baht_amount' => ['nullable', 'numeric', 'min:0'],
            'line' => ['required', 'array', 'min:1'],
            'line.*.description' => ['nullable', 'string'],
            'line.*.detail' => ['nullable', 'string'],
            'line.*.discount' => ['nullable', 'numeric', 'min:0'],
            'line.*.qty' => ['nullable', 'numeric', 'min:0.01'],
            'line.*.unit' => ['nullable', 'string', 'max:50'],
            'line.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'line.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'line.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'line.*.vat_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'line.*.opportunity_line_id' => ['nullable'],
            'line.*.product_service_item_id' => ['nullable'],
            'customer_contact_name' => ['nullable', 'string', 'max:255'],
            'customer_contact_phone' => ['nullable', 'string', 'max:50'],
            'customer_email' => ['nullable', 'string', 'max:255'],
            'customer_type' => ['nullable', 'integer', 'in:1,2,3'],
            'customer_tax_no' => ['nullable', 'string', 'max:13'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_status' => ['nullable', 'integer', 'in:0,1'],
            'customer_branch_no' => ['nullable', 'string', 'max:50'],
            'customer_branch_name' => ['nullable', 'string', 'max:255'],
            'customer_address' => ['nullable', 'string'],
            'customer_province_id' => ['nullable', 'integer', 'exists:provinces,id'],
            'customer_amphure_id' => ['nullable', 'integer', 'exists:amphures,id'],
            'customer_tambon_id' => ['nullable', 'integer', 'exists:tambons,id'],
            'customer_zip_code' => ['nullable', 'string', 'max:10'],
            'vat_type' => ['nullable', 'in:none,inclusive,exclusive'],
            'discount_type' => ['nullable', 'in:percent,baht'],
            'seller_name' => ['nullable', 'string', 'max:255'],
            'seller_phone' => ['nullable', 'string', 'max:50'],
            'seller_email' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string|null>
     */
    private function partyFieldsFromRequest(array $data): array
    {
        $isNaturalPerson = (int) ($data['customer_type'] ?? 0) === 3;
        $isHeadOffice = ! $isNaturalPerson && (int) ($data['customer_status'] ?? 0) === 0;

        return [
            'customer_contact_name' => $data['customer_contact_name'] ?? null,
            'customer_contact_phone' => $data['customer_contact_phone'] ?? null,
            'customer_email' => $data['customer_email'] ?? null,
            'customer_type' => $data['customer_type'] ?? null,
            'customer_tax_no' => $data['customer_tax_no'] ?? null,
            'customer_name' => $data['customer_name'] ?? null,
            'customer_status' => $isNaturalPerson ? null : ($data['customer_status'] ?? null),
            'customer_branch_no' => ($isNaturalPerson || $isHeadOffice) ? null : ($data['customer_branch_no'] ?? null),
            'customer_branch_name' => ($isNaturalPerson || $isHeadOffice) ? null : ($data['customer_branch_name'] ?? null),
            'customer_address' => $data['customer_address'] ?? null,
            'customer_province_id' => $data['customer_province_id'] ?? null,
            'customer_amphure_id' => $data['customer_amphure_id'] ?? null,
            'customer_tambon_id' => $data['customer_tambon_id'] ?? null,
            'customer_zip_code' => $data['customer_zip_code'] ?? null,
            'seller_name' => $data['seller_name'] ?? null,
            'seller_phone' => $data['seller_phone'] ?? null,
            'seller_email' => $data['seller_email'] ?? null,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function totalsFieldsFromRequest(array $data): array
    {
        return [
            'vat_type' => $data['vat_type'] ?? 'exclusive',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function quotationFormViewData(?Opportunity $opportunity, ?Partner $partner): array
    {
        $contact = $opportunity?->partnerContact;
        $branch = $opportunity?->partnerBranch;

        if ($partner && ! $contact) {
            $contact = $partner->contacts->first();
        }
        if ($partner && ! $branch) {
            $branch = $partner->branches->first();
        }

        $customerParty = $partner
            ? $this->partyInfo->mergeWithOldInput($this->partyInfo->customerDefaults($partner, $contact, $branch))
            : [];
        $sellerParty = $this->partyInfo->mergeWithOldInput($this->partyInfo->sellerDefaults($this->createBy()));
        $totalsDefaults = $this->partyInfo->mergeWithOldInput([
            'vat_type' => 'exclusive',
            'discount_type' => 'percent',
        ]);

        $provinceId = $customerParty['customer_province_id'] ?? null;
        $amphureId = $customerParty['customer_amphure_id'] ?? null;

        return [
            'opportunity' => $opportunity,
            'partner' => $partner,
            'issueDate' => now()->toDateString(),
            'validUntil' => now()->addDays(30)->toDateString(),
            'customerParty' => $customerParty,
            'sellerParty' => $sellerParty,
            'totalsDefaults' => $totalsDefaults,
            'provinces' => Provinces::query()->orderBy('name_th')->get(),
            'customerAmphures' => $provinceId
                ? Amphures::query()->where('province_id', $provinceId)->orderBy('name_th')->get()
                : collect(),
            'customerTambons' => $amphureId
                ? Tambons::query()->where('amphure_id', $amphureId)->orderBy('name_th')->get()
                : collect(),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array<int, array<string, mixed>>
     */
    private function normalizeQuotationLines(array $lines, string $discountType = 'percent'): array
    {
        return collect($lines)->map(function (array $line) use ($discountType) {
            $name = trim((string) ($line['description'] ?? ''));
            $detail = trim((string) ($line['detail'] ?? ''));
            $line['description'] = $detail !== '' && $name !== ''
                ? $name."\n".$detail
                : ($name !== '' ? $name : $detail);

            $discount = $line['discount'] ?? null;
            if ($discount !== null && $discount !== '') {
                if ($discountType === 'baht') {
                    $line['discount_amount'] = $discount;
                } else {
                    $line['discount_percent'] = $discount;
                }
            }

            return $line;
        })->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array<int, array<string, mixed>>
     */
    private function filterQuotationLines(array $lines): array
    {
        $filtered = collect($lines)
            ->filter(fn ($line) => filled(trim((string) ($line['description'] ?? ''))))
            ->values()
            ->all();

        if ($filtered === []) {
            throw ValidationException::withMessages(['line' => 'กรุณาระบุรายการอย่างน้อย 1 รายการ']);
        }

        return $filtered;
    }

    /**
     * @return array<string, string|null>
     */
    private function customerPartyFromRevision(QuotationRevision $revision): array
    {
        return [
            'customer_contact_name' => $revision->customer_contact_name,
            'customer_contact_phone' => $revision->customer_contact_phone,
            'customer_email' => $revision->customer_email,
            'customer_type' => $revision->customer_type,
            'customer_tax_no' => $revision->customer_tax_no,
            'customer_name' => $revision->customer_name,
            'customer_status' => $revision->customer_status,
            'customer_branch_no' => $revision->customer_branch_no,
            'customer_branch_name' => $revision->customer_branch_name,
            'customer_address' => $revision->customer_address,
            'customer_province_id' => $revision->customer_province_id,
            'customer_amphure_id' => $revision->customer_amphure_id,
            'customer_tambon_id' => $revision->customer_tambon_id,
            'customer_zip_code' => $revision->customer_zip_code,
        ];
    }

    /**
     * @return array<string, string|null>
     */
    private function sellerPartyFromRevision(QuotationRevision $revision): array
    {
        return [
            'seller_name' => $revision->seller_name,
            'seller_phone' => $revision->seller_phone,
            'seller_email' => $revision->seller_email,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function normalizeQuotationLinesFromOld(): array
    {
        $seedLines = old('line');
        if (! is_array($seedLines)) {
            return [];
        }

        return collect($seedLines)->map(function (array $line) {
            if (! isset($line['detail']) && isset($line['description'])) {
                $text = (string) $line['description'];
                if (str_contains($text, "\n")) {
                    $parts = explode("\n", $text, 2);
                    $line['description'] = $parts[0];
                    $line['detail'] = $parts[1] ?? '';
                }
            }
            $line['discount'] = $line['discount'] ?? $line['discount_percent'] ?? $line['discount_amount'] ?? 0;

            return $line;
        })->all();
    }

    private function assertOpportunityOwned(Opportunity $opportunity): void
    {
        if ((string) $opportunity->business_id !== $this->businessId()) {
            abort(403);
        }
    }

    private function assertQuotationOwned(Quotation $quotation): void
    {
        if ((string) $quotation->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
