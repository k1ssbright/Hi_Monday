<?php

namespace App\Services;

use App\Models\Lead\Lead;
use App\Models\Sales\BillingNote;
use App\Models\Sales\CreditNote;
use App\Models\Sales\DebitNote;
use App\Models\Sales\DeliveryNote;
use App\Models\Sales\Opportunity;
use App\Models\Sales\Quotation;
use App\Models\Sales\Receipt;
use App\Models\Sales\SalesOrder;
use App\Models\Sales\SalesOrderPaymentTerm;
use App\Models\Sales\TaxInvoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class SalesJourneyPresenter
{
    /**
     * @return array<string, mixed>
     */
    public function for(Model $document): array
    {
        $context = $this->resolveContext($document);

        $lead = $context['lead'];
        $opportunity = $context['opportunity'];
        $quotation = $context['quotation'];
        $salesOrder = $context['salesOrder'];
        $currentType = $context['current_type'];
        $currentId = $context['current_id'];

        if ($salesOrder) {
            $this->loadSalesOrderGraph($salesOrder);
        }

        $upstream = $this->buildUpstream($lead, $opportunity, $quotation, $salesOrder, $currentType, $currentId);
        $lanes = $salesOrder
            ? $this->buildLanes($salesOrder, $currentType, $currentId)
            : [];
        $billingRatio = $this->buildBillingRatio($lanes);
        $activePanel = $this->buildActivePanel($lanes, $salesOrder, $document, $currentType);

        $totalAmount = $salesOrder?->currentRevision
            ? (float) $salesOrder->currentRevision->total_amount
            : ($opportunity ? (float) $opportunity->estimated_value : 0.0);

        $partnerName = $salesOrder?->partner?->partner_name_th
            ?? $opportunity?->partner?->partner_name_th
            ?? $lead?->organization_name
            ?? null;

        $projectName = $opportunity?->name
            ?? $lead?->project_name
            ?? null;

        $subtitleParts = array_filter([
            $projectName,
            $partnerName,
            $totalAmount > 0 ? 'มูลค่ารวม ฿'.number_format($totalAmount, 2) : null,
        ]);

        return [
            'title' => 'ไทม์ไลน์งานขาย & งวดชำระเงิน',
            'subtitle' => implode(' | ', $subtitleParts),
            'installment_count' => count($lanes),
            'total_amount' => $totalAmount,
            'upstream' => $upstream,
            'upstream_complete' => $salesOrder
                && $salesOrder->currentRevision
                && in_array($salesOrder->currentRevision->status, ['confirmed', 'closed'], true),
            'lanes' => $lanes,
            'billing_ratio' => $billingRatio,
            'active_panel' => $activePanel,
            'current_type' => $currentType,
            'has_sales_order' => (bool) $salesOrder,
        ];
    }

    /**
     * @return array{
     *     lead: ?Lead,
     *     opportunity: ?Opportunity,
     *     quotation: ?Quotation,
     *     salesOrder: ?SalesOrder,
     *     current_type: string,
     *     current_id: string|null
     * }
     */
    private function resolveContext(Model $document): array
    {
        $lead = null;
        $opportunity = null;
        $quotation = null;
        $salesOrder = null;
        $currentType = class_basename($document);
        $currentId = $document->getKey();

        if ($document instanceof Lead) {
            $currentType = 'lead';
            $lead = $document;
            if ($lead->converted_opportunity_id) {
                $opportunity = Opportunity::query()
                    ->with(['partner', 'quotations.currentRevision', 'salesOrders.currentRevision'])
                    ->find($lead->converted_opportunity_id);
            }
        } elseif ($document instanceof Opportunity) {
            $currentType = 'opportunity';
            $opportunity = $document->loadMissing(['partner', 'convertedFromLead', 'quotations.currentRevision', 'salesOrders.currentRevision']);
            $lead = $opportunity->convertedFromLead;
        } elseif ($document instanceof Quotation) {
            $currentType = 'quotation';
            $quotation = $document->loadMissing(['opportunity.convertedFromLead', 'opportunity.partner', 'partner', 'currentRevision', 'salesOrders.currentRevision']);
            $opportunity = $quotation->opportunity;
            $lead = $opportunity?->convertedFromLead;
            $salesOrder = $quotation->salesOrders->sortByDesc('created_at')->first();
        } elseif ($document instanceof SalesOrder) {
            $currentType = 'sales_order';
            $salesOrder = $document->loadMissing(['opportunity.convertedFromLead', 'opportunity.partner', 'quotation.currentRevision', 'partner', 'currentRevision']);
            $quotation = $salesOrder->quotation;
            $opportunity = $salesOrder->opportunity;
            $lead = $opportunity?->convertedFromLead;
        } elseif ($document instanceof BillingNote) {
            $currentType = 'billing_note';
            $document->loadMissing(['salesOrder.opportunity.convertedFromLead', 'salesOrder.quotation', 'salesOrder.partner', 'opportunity']);
            $salesOrder = $document->salesOrder;
            $quotation = $salesOrder?->quotation;
            $opportunity = $document->opportunity ?? $salesOrder?->opportunity;
            $lead = $opportunity?->convertedFromLead;
        } elseif ($document instanceof DeliveryNote) {
            $currentType = 'delivery_note';
            $document->loadMissing(['salesOrder.opportunity.convertedFromLead', 'salesOrder.quotation', 'salesOrder.partner', 'opportunity', 'billingNote']);
            $salesOrder = $document->salesOrder;
            $quotation = $salesOrder?->quotation;
            $opportunity = $document->opportunity ?? $salesOrder?->opportunity;
            $lead = $opportunity?->convertedFromLead;
        } elseif ($document instanceof TaxInvoice) {
            $currentType = 'tax_invoice';
            $document->loadMissing(['salesOrder.opportunity.convertedFromLead', 'salesOrder.quotation', 'salesOrder.partner', 'opportunity', 'billingNote', 'deliveryNote']);
            $salesOrder = $document->salesOrder;
            $quotation = $salesOrder?->quotation;
            $opportunity = $document->opportunity ?? $salesOrder?->opportunity;
            $lead = $opportunity?->convertedFromLead;
        } elseif ($document instanceof Receipt) {
            $currentType = 'receipt';
            $document->loadMissing(['taxInvoice.salesOrder.opportunity.convertedFromLead', 'taxInvoice.salesOrder.quotation', 'taxInvoice.salesOrder.partner', 'salesOrder', 'opportunity']);
            $salesOrder = $document->salesOrder ?? $document->taxInvoice?->salesOrder;
            $quotation = $salesOrder?->quotation;
            $opportunity = $document->opportunity ?? $salesOrder?->opportunity;
            $lead = $opportunity?->convertedFromLead;
        } elseif ($document instanceof CreditNote) {
            $currentType = 'credit_note';
            $document->loadMissing(['taxInvoice.salesOrder.opportunity.convertedFromLead', 'taxInvoice.salesOrder.quotation', 'salesOrder', 'opportunity']);
            $salesOrder = $document->salesOrder ?? $document->taxInvoice?->salesOrder;
            $quotation = $salesOrder?->quotation;
            $opportunity = $document->opportunity ?? $salesOrder?->opportunity;
            $lead = $opportunity?->convertedFromLead;
        } elseif ($document instanceof DebitNote) {
            $currentType = 'debit_note';
            $document->loadMissing(['taxInvoice.salesOrder.opportunity.convertedFromLead', 'taxInvoice.salesOrder.quotation', 'salesOrder', 'opportunity']);
            $salesOrder = $document->salesOrder ?? $document->taxInvoice?->salesOrder;
            $quotation = $salesOrder?->quotation;
            $opportunity = $document->opportunity ?? $salesOrder?->opportunity;
            $lead = $opportunity?->convertedFromLead;
        }

        // Prefer SO/QT related to current doc; otherwise pick from opportunity
        if (! $salesOrder && $opportunity) {
            $opportunity->loadMissing(['salesOrders.currentRevision', 'quotations.currentRevision', 'convertedFromLead', 'partner']);
            $salesOrder = $opportunity->salesOrders->sortByDesc('created_at')->first();
            $lead = $lead ?? $opportunity->convertedFromLead;
        }

        if (! $quotation && $salesOrder?->quotation_id) {
            $quotation = $salesOrder->quotation ?? Quotation::query()->with('currentRevision')->find($salesOrder->quotation_id);
        }

        if (! $quotation && $opportunity) {
            $quotation = $opportunity->quotations->sortByDesc('created_at')->first();
        }

        if ($opportunity && ! $lead) {
            $opportunity->loadMissing('convertedFromLead');
            $lead = $opportunity->convertedFromLead;
        }

        return [
            'lead' => $lead,
            'opportunity' => $opportunity,
            'quotation' => $quotation,
            'salesOrder' => $salesOrder,
            'current_type' => $currentType,
            'current_id' => $currentId,
        ];
    }

    private function loadSalesOrderGraph(SalesOrder $salesOrder): void
    {
        $salesOrder->loadMissing([
            'partner',
            'quotation.currentRevision',
            'opportunity.convertedFromLead',
            'opportunity.partner',
            'currentRevision.paymentTerms',
            'billingNotes.currentRevision',
            'deliveryNotes.currentRevision',
            'deliveryNotes.billingNote',
            'taxInvoices.currentRevision',
            'taxInvoices.billingNote',
            'taxInvoices.deliveryNote',
            'taxInvoices.receipts.currentRevision',
            'taxInvoices.creditNotes.currentRevision',
            'taxInvoices.debitNotes.currentRevision',
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildUpstream(
        ?Lead $lead,
        ?Opportunity $opportunity,
        ?Quotation $quotation,
        ?SalesOrder $salesOrder,
        string $currentType,
        mixed $currentId,
    ): array {
        $nodes = [];

        $nodes[] = $this->upstreamNode(
            key: 'lead',
            code: 'LEAD',
            label: 'โอกาสใหม่ (Lead)',
            displayNo: $lead?->registration_no,
            status: $lead ? $this->mapLeadStatus($lead) : 'empty',
            url: $lead ? route('office.leads.show', ['lead' => $lead]) : null,
            isCurrent: $currentType === 'lead' && $lead && (string) $lead->id === (string) $currentId,
            exists: (bool) $lead,
        );

        $nodes[] = $this->upstreamNode(
            key: 'opportunity',
            code: 'OPP',
            label: 'Opportunity',
            displayNo: $opportunity?->opportunity_no,
            status: $opportunity ? $this->mapOpportunityStatus($opportunity) : 'empty',
            url: $opportunity ? route('office.opportunities.show', ['opportunity' => $opportunity]) : null,
            isCurrent: $currentType === 'opportunity' && $opportunity && (string) $opportunity->id === (string) $currentId,
            exists: (bool) $opportunity,
        );

        $qtStatus = $quotation?->currentRevision?->status;
        $nodes[] = $this->upstreamNode(
            key: 'quotation',
            code: 'QT',
            label: $qtStatus ? 'ใบเสนอราคา ('.$qtStatus.')' : 'ใบเสนอราคา',
            displayNo: $quotation?->displayNo(),
            status: $quotation ? $this->mapRevisionDocStatus($qtStatus) : 'empty',
            url: $quotation ? route('office.quotations.show', ['quotation' => $quotation]) : null,
            isCurrent: $currentType === 'quotation' && $quotation && (string) $quotation->id === (string) $currentId,
            exists: (bool) $quotation,
        );

        $soStatus = $salesOrder?->currentRevision?->status;
        $soAmount = $salesOrder?->currentRevision?->total_amount;
        $nodes[] = $this->upstreamNode(
            key: 'sales_order',
            code: 'SO',
            label: $soAmount !== null
                ? 'Sales Order • ฿'.number_format((float) $soAmount, 2)
                : 'Sales Order',
            displayNo: $salesOrder?->displayNo(),
            status: $salesOrder ? $this->mapSalesOrderStatus($soStatus) : 'empty',
            url: $salesOrder ? route('office.sales-orders.show', ['salesOrder' => $salesOrder]) : null,
            isCurrent: $currentType === 'sales_order' && $salesOrder && (string) $salesOrder->id === (string) $currentId,
            exists: (bool) $salesOrder,
            isHub: true,
        );

        return $nodes;
    }

    /**
     * @return array<string, mixed>
     */
    private function upstreamNode(
        string $key,
        string $code,
        string $label,
        ?string $displayNo,
        string $status,
        ?string $url,
        bool $isCurrent,
        bool $exists,
        bool $isHub = false,
    ): array {
        return compact('key', 'code', 'label', 'displayNo', 'status', 'url', 'isCurrent', 'exists', 'isHub') + [
            'is_current' => $isCurrent,
            'is_hub' => $isHub,
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function buildLanes(SalesOrder $salesOrder, string $currentType, mixed $currentId): array
    {
        $revision = $salesOrder->currentRevision;
        $terms = $revision?->paymentTerms ?? collect();
        $soTotal = (float) ($revision?->total_amount ?? 0);

        $billingNotes = $salesOrder->billingNotes ?? collect();
        $deliveryNotes = $salesOrder->deliveryNotes ?? collect();
        $taxInvoices = $salesOrder->taxInvoices ?? collect();

        if ($terms->isEmpty()) {
            return [$this->buildSingleLane(
                salesOrder: $salesOrder,
                installmentNo: 1,
                label: 'เรียกเก็บเต็มจำนวน',
                amount: $soTotal,
                percent: 100.0,
                term: null,
                billingNotes: $billingNotes,
                deliveryNotes: $deliveryNotes,
                taxInvoices: $taxInvoices,
                currentType: $currentType,
                currentId: $currentId,
                isOnlyLane: true,
            )];
        }

        $lanes = [];
        $assignedBnIds = [];
        $assignedDlvIds = [];
        $assignedTiIds = [];

        foreach ($terms->sortBy('installment_no')->values() as $index => $term) {
            /** @var SalesOrderPaymentTerm $term */
            $matchedBns = $billingNotes->filter(function (BillingNote $bn) use ($term) {
                $rev = $bn->currentRevision;
                if (! $rev) {
                    return false;
                }
                if ($rev->sales_order_payment_term_id && (string) $rev->sales_order_payment_term_id === (string) $term->id) {
                    return true;
                }

                return $rev->installment_no !== null && (int) $rev->installment_no === (int) $term->installment_no;
            });

            $bn = $matchedBns->sortByDesc(fn (BillingNote $b) => $b->created_at?->timestamp ?? 0)->first();
            if ($bn) {
                $assignedBnIds[] = (string) $bn->id;
            }

            $matchedDlvs = $deliveryNotes->filter(function (DeliveryNote $dlv) use ($bn) {
                if (! $bn) {
                    return false;
                }

                return $dlv->billing_note_id && (string) $dlv->billing_note_id === (string) $bn->id;
            });
            $dlv = $matchedDlvs->sortByDesc(fn (DeliveryNote $d) => $d->created_at?->timestamp ?? 0)->first();
            if ($dlv) {
                $assignedDlvIds[] = (string) $dlv->id;
            }

            $matchedTis = $taxInvoices->filter(function (TaxInvoice $ti) use ($bn, $dlv) {
                if ($bn && $ti->billing_note_id && (string) $ti->billing_note_id === (string) $bn->id) {
                    return true;
                }
                if ($dlv && $ti->delivery_note_id && (string) $ti->delivery_note_id === (string) $dlv->id) {
                    return true;
                }

                return false;
            });
            $ti = $matchedTis->sortByDesc(fn (TaxInvoice $t) => $t->created_at?->timestamp ?? 0)->first();
            if ($ti) {
                $assignedTiIds[] = (string) $ti->id;
            }

            $amount = $this->termAmount($term, $soTotal);
            $percent = $this->termPercent($term, $soTotal);

            $lanes[] = $this->composeLane(
                salesOrder: $salesOrder,
                installmentNo: (int) $term->installment_no,
                label: $term->condition ?: ('งวดที่ '.$term->installment_no),
                amount: $amount,
                percent: $percent,
                dueDate: $term->due_date,
                bn: $bn,
                dlv: $dlv,
                ti: $ti,
                currentType: $currentType,
                currentId: $currentId,
                includeDlv: true,
            );
        }

        // Attach leftover BN/TI without term match into first unfinished / last lane
        $leftoverBns = $billingNotes->reject(fn (BillingNote $bn) => in_array((string) $bn->id, $assignedBnIds, true));
        $leftoverDlvs = $deliveryNotes->reject(fn (DeliveryNote $dlv) => in_array((string) $dlv->id, $assignedDlvIds, true));
        $leftoverTis = $taxInvoices->reject(fn (TaxInvoice $ti) => in_array((string) $ti->id, $assignedTiIds, true));

        if ($lanes !== [] && ($leftoverBns->isNotEmpty() || $leftoverTis->isNotEmpty() || $leftoverDlvs->isNotEmpty())) {
            foreach ($lanes as $i => $lane) {
                if ($lane['steps']['bn']['exists']) {
                    continue;
                }
                $bn = $leftoverBns->shift();
                if (! $bn) {
                    break;
                }
                $dlv = $leftoverDlvs->first(fn (DeliveryNote $d) => $d->billing_note_id && (string) $d->billing_note_id === (string) $bn->id)
                    ?? $leftoverDlvs->shift();
                $ti = $leftoverTis->first(fn (TaxInvoice $t) => $t->billing_note_id && (string) $t->billing_note_id === (string) $bn->id)
                    ?? $leftoverTis->shift();
                if ($dlv) {
                    $leftoverDlvs = $leftoverDlvs->reject(fn (DeliveryNote $d) => (string) $d->id === (string) $dlv->id)->values();
                }
                if ($ti) {
                    $leftoverTis = $leftoverTis->reject(fn (TaxInvoice $t) => (string) $t->id === (string) $ti->id)->values();
                }
                $lanes[$i] = $this->composeLane(
                    salesOrder: $salesOrder,
                    installmentNo: $lane['installment_no'],
                    label: $lane['label'],
                    amount: $lane['amount'],
                    percent: $lane['percent'],
                    dueDate: $lane['due_date'] ?? null,
                    bn: $bn,
                    dlv: $dlv,
                    ti: $ti,
                    currentType: $currentType,
                    currentId: $currentId,
                    includeDlv: true,
                );
            }
        }

        return $this->markActiveLanes($lanes);
    }

    /**
     * @param  Collection<int, BillingNote>  $billingNotes
     * @param  Collection<int, DeliveryNote>  $deliveryNotes
     * @param  Collection<int, TaxInvoice>  $taxInvoices
     * @return array<string, mixed>
     */
    private function buildSingleLane(
        SalesOrder $salesOrder,
        int $installmentNo,
        string $label,
        float $amount,
        float $percent,
        ?SalesOrderPaymentTerm $term,
        Collection $billingNotes,
        Collection $deliveryNotes,
        Collection $taxInvoices,
        string $currentType,
        mixed $currentId,
        bool $isOnlyLane,
    ): array {
        $bn = $billingNotes->sortByDesc(fn (BillingNote $b) => $b->created_at?->timestamp ?? 0)->first();
        $dlv = null;
        if ($bn) {
            $dlv = $deliveryNotes->first(fn (DeliveryNote $d) => $d->billing_note_id && (string) $d->billing_note_id === (string) $bn->id);
        }
        $dlv = $dlv ?? $deliveryNotes->sortByDesc(fn (DeliveryNote $d) => $d->created_at?->timestamp ?? 0)->first();
        $ti = null;
        if ($bn) {
            $ti = $taxInvoices->first(fn (TaxInvoice $t) => $t->billing_note_id && (string) $t->billing_note_id === (string) $bn->id);
        }
        if (! $ti && $dlv) {
            $ti = $taxInvoices->first(fn (TaxInvoice $t) => $t->delivery_note_id && (string) $t->delivery_note_id === (string) $dlv->id);
        }
        $ti = $ti ?? $taxInvoices->sortByDesc(fn (TaxInvoice $t) => $t->created_at?->timestamp ?? 0)->first();

        $lane = $this->composeLane(
            salesOrder: $salesOrder,
            installmentNo: $installmentNo,
            label: $label,
            amount: $amount,
            percent: $percent,
            dueDate: $term?->due_date,
            bn: $bn,
            dlv: $dlv,
            ti: $ti,
            currentType: $currentType,
            currentId: $currentId,
            includeDlv: true,
        );

        return $this->markActiveLanes([$lane])[0];
    }

    /**
     * @return array<string, mixed>
     */
    private function composeLane(
        SalesOrder $salesOrder,
        int $installmentNo,
        string $label,
        float $amount,
        float $percent,
        mixed $dueDate,
        ?BillingNote $bn,
        ?DeliveryNote $dlv,
        ?TaxInvoice $ti,
        string $currentType,
        mixed $currentId,
        bool $includeDlv,
    ): array {
        $dueDateValue = null;
        $dueDateDisplay = null;
        if ($dueDate instanceof \DateTimeInterface) {
            $dueDateValue = $dueDate->format('Y-m-d');
            $dueDateDisplay = $dueDate->format('d/m/Y');
        } elseif (is_string($dueDate) && $dueDate !== '') {
            $dueDateValue = $dueDate;
            try {
                $dueDateDisplay = \Illuminate\Support\Carbon::parse($dueDate)->format('d/m/Y');
            } catch (\Throwable) {
                $dueDateDisplay = $dueDate;
            }
        }

        $receipt = $ti?->receipts?->sortByDesc(fn (Receipt $r) => $r->created_at?->timestamp ?? 0)->first();
        $creditNotes = $ti?->creditNotes ?? collect();
        $debitNotes = $ti?->debitNotes ?? collect();

        $dlvStep = $this->docStep(
            type: 'delivery_note',
            code: 'DLV',
            labelPending: 'DLV (รอส่งมอบ)',
            labelDone: 'DLV (ส่งมอบแล้ว)',
            labelActive: 'DLV (กำลังส่งมอบ)',
            document: $dlv,
            status: $dlv?->currentRevision?->status,
            url: $dlv ? route('office.delivery-notes.show', ['deliveryNote' => $dlv]) : null,
            displayNo: $dlv?->displayNo(),
            isCurrent: $currentType === 'delivery_note' && $dlv && (string) $dlv->id === (string) $currentId,
        );

        $bnStep = $this->docStep(
            type: 'billing_note',
            code: 'BN',
            labelPending: 'BN (รอวางบิล)',
            labelDone: 'BN (วางบิลแล้ว)',
            labelActive: 'BN (กำลังวางบิล)',
            document: $bn,
            status: $bn?->currentRevision?->status,
            url: $bn ? route('office.billing-notes.show', ['billingNote' => $bn]) : null,
            displayNo: $bn?->displayNo(),
            isCurrent: $currentType === 'billing_note' && $bn && (string) $bn->id === (string) $currentId,
        );

        $tiStep = $this->docStep(
            type: 'tax_invoice',
            code: 'TI',
            labelPending: 'TI (รอออกใบแจ้งหนี้)',
            labelDone: 'TI (ใบกำกับภาษี)',
            labelActive: 'TI (กำลังออกใบกำกับ)',
            document: $ti,
            status: $ti?->currentRevision?->status,
            url: $ti ? route('office.tax-invoices.show', ['taxInvoice' => $ti]) : null,
            displayNo: $ti?->displayNo(),
            isCurrent: in_array($currentType, ['tax_invoice', 'credit_note', 'debit_note'], true)
                && $ti
                && (
                    ($currentType === 'tax_invoice' && (string) $ti->id === (string) $currentId)
                    || ($currentType === 'credit_note' && $creditNotes->contains(fn (CreditNote $c) => (string) $c->id === (string) $currentId))
                    || ($currentType === 'debit_note' && $debitNotes->contains(fn (DebitNote $d) => (string) $d->id === (string) $currentId))
                ),
        );

        $rctStep = $this->docStep(
            type: 'receipt',
            code: 'RCT',
            labelPending: 'RCT (รอรับชำระ)',
            labelDone: 'RCT (ใบเสร็จรับเงิน)',
            labelActive: 'RCT (กำลังรับชำระ)',
            document: $receipt,
            status: $receipt?->currentRevision?->status,
            url: $receipt ? route('office.receipts.show', ['receipt' => $receipt]) : null,
            displayNo: $receipt?->displayNo(),
            isCurrent: $currentType === 'receipt' && $receipt && (string) $receipt->id === (string) $currentId,
        );

        $steps = [];
        // Deposit-style (มี BN แต่ไม่มี DLV) → BN→TI→RCT; ถ้ายังไม่เริ่มหรืองวดมี DLV → แสดง DLV ด้วย
        $showDlv = $includeDlv && ($dlv !== null || $bn === null);
        if ($showDlv) {
            $steps['dlv'] = $dlvStep;
        }
        $steps['bn'] = $bnStep;
        $steps['ti'] = $tiStep;
        $steps['rct'] = $rctStep;

        $laneStatus = $this->laneStatusFromSteps($steps);
        $progress = $this->laneProgress($steps);
        $statusLabel = $this->laneStatusLabel($laneStatus, $steps);

        $actions = $this->laneActions($salesOrder, $bn, $dlv, $ti, $receipt);

        return [
            'installment_no' => $installmentNo,
            'label' => $label,
            'amount' => $amount,
            'percent' => $percent,
            'due_date' => $dueDateValue,
            'due_date_display' => $dueDateDisplay,
            'status' => $laneStatus,
            'status_label' => $statusLabel,
            'is_active' => false,
            'progress' => $progress,
            'steps' => $steps,
            'actions' => $actions,
            'credit_notes' => $creditNotes->map(fn (CreditNote $c) => [
                'display_no' => $c->displayNo(),
                'url' => route('office.credit-notes.show', ['creditNote' => $c]),
                'is_current' => $currentType === 'credit_note' && (string) $c->id === (string) $currentId,
            ])->values()->all(),
            'debit_notes' => $debitNotes->map(fn (DebitNote $d) => [
                'display_no' => $d->displayNo(),
                'url' => route('office.debit-notes.show', ['debitNote' => $d]),
                'is_current' => $currentType === 'debit_note' && (string) $d->id === (string) $currentId,
            ])->values()->all(),
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $steps
     */
    private function laneStatusLabel(string $laneStatus, array $steps): string
    {
        if ($laneStatus === 'paid') {
            return 'ชำระครบแล้ว (Paid 100%)';
        }
        if ($laneStatus === 'upcoming') {
            return 'ยังไม่เริ่ม (Upcoming)';
        }

        foreach ($steps as $step) {
            if (($step['state'] ?? null) === 'active' || ($step['is_current'] ?? false)) {
                return match ($step['type'] ?? '') {
                    'delivery_note' => 'กำลังดำเนินการส่งมอบ',
                    'billing_note' => 'กำลังดำเนินการวางบิล',
                    'tax_invoice' => 'กำลังออกใบกำกับภาษี',
                    'receipt' => 'กำลังรับชำระเงิน',
                    default => 'กำลังดำเนินการ',
                };
            }
        }

        return 'กำลังดำเนินการ';
    }

    /**
     * @param  array<string, array<string, mixed>>  $steps
     */
    private function laneStatusFromSteps(array $steps): string
    {
        $rct = $steps['rct'] ?? null;
        if ($rct && ($rct['state'] ?? null) === 'done') {
            return 'paid';
        }

        foreach ($steps as $step) {
            if (($step['exists'] ?? false) || ($step['is_current'] ?? false)) {
                return 'active';
            }
        }

        return 'upcoming';
    }

    /**
     * @param  array<string, array<string, mixed>>  $steps
     */
    private function laneProgress(array $steps): float
    {
        $list = array_values($steps);
        $count = count($list);
        if ($count === 0) {
            return 0;
        }
        $done = 0;
        $partial = 0;
        foreach ($list as $step) {
            if (($step['state'] ?? null) === 'done') {
                $done++;
            } elseif (($step['state'] ?? null) === 'active' || ($step['exists'] ?? false)) {
                $partial += 0.5;
            }
        }

        return min(100, round((($done + $partial) / $count) * 100, 1));
    }

    /**
     * @param  list<array<string, mixed>>  $lanes
     * @return list<array<string, mixed>>
     */
    private function markActiveLanes(array $lanes): array
    {
        $activeIndex = null;
        foreach ($lanes as $i => $lane) {
            foreach ($lane['steps'] as $step) {
                if ($step['is_current'] ?? false) {
                    $activeIndex = $i;
                    break 2;
                }
            }
        }

        if ($activeIndex === null) {
            foreach ($lanes as $i => $lane) {
                if ($lane['status'] === 'active') {
                    $activeIndex = $i;
                    break;
                }
            }
        }

        if ($activeIndex === null) {
            foreach ($lanes as $i => $lane) {
                if ($lane['status'] === 'upcoming') {
                    $activeIndex = $i;
                    break;
                }
            }
        }

        if ($activeIndex === null && $lanes !== []) {
            $activeIndex = count($lanes) - 1;
        }

        foreach ($lanes as $i => &$lane) {
            $lane['is_active'] = $i === $activeIndex;
            // Mark first non-done step as active within active lane
            if ($lane['is_active']) {
                $marked = false;
                foreach ($lane['steps'] as $key => &$step) {
                    if ($step['state'] === 'done') {
                        continue;
                    }
                    if (! $marked) {
                        if ($step['exists']) {
                            $step['state'] = 'active';
                        }
                        $marked = true;
                    }
                }
                unset($step);
            }
        }
        unset($lane);

        return $lanes;
    }

    /**
     * @return array<string, mixed>
     */
    private function docStep(
        string $type,
        string $code,
        string $labelPending,
        string $labelDone,
        string $labelActive,
        ?Model $document,
        ?string $status,
        ?string $url,
        ?string $displayNo,
        bool $isCurrent,
    ): array {
        $exists = (bool) $document;
        $state = 'pending';
        if ($exists) {
            if (in_array($status, ['issued', 'confirmed', 'closed'], true)) {
                $state = 'done';
            } elseif (in_array($status, ['voided', 'cancelled'], true)) {
                $state = 'voided';
            } else {
                $state = 'active';
            }
        }

        $label = match ($state) {
            'done' => $labelDone,
            'active' => $labelActive,
            default => $labelPending,
        };

        return [
            'type' => $type,
            'code' => $code,
            'label' => $label,
            'exists' => $exists,
            'status' => $status,
            'state' => $state,
            'url' => $url,
            'display_no' => $displayNo,
            'is_current' => $isCurrent,
        ];
    }

    /**
     * @return list<array{label: string, url: string, tone: string}>
     */
    private function laneActions(
        SalesOrder $salesOrder,
        ?BillingNote $bn,
        ?DeliveryNote $dlv,
        ?TaxInvoice $ti,
        ?Receipt $receipt,
    ): array {
        $actions = [];

        if ($dlv) {
            $actions[] = [
                'label' => 'เปิดดูใบส่งมอบ',
                'url' => route('office.delivery-notes.show', ['deliveryNote' => $dlv]),
                'tone' => 'secondary',
            ];
        } elseif ($salesOrder->canCreateDeliveryNote()) {
            $actions[] = [
                'label' => 'สร้างใบส่งมอบ (DLV)',
                'url' => route('office.sales-orders.delivery-notes.create', ['salesOrder' => $salesOrder]),
                'tone' => 'secondary',
            ];
        }

        if ($bn) {
            $actions[] = [
                'label' => 'เปิดดูใบวางบิล',
                'url' => route('office.billing-notes.show', ['billingNote' => $bn]),
                'tone' => 'secondary',
            ];
        } elseif ($salesOrder->canCreateBillingNote()) {
            $actions[] = [
                'label' => 'สร้างใบวางบิล (BN)',
                'url' => route('office.sales-orders.billing-notes.create', ['salesOrder' => $salesOrder]),
                'tone' => 'primary',
            ];
        }

        if ($ti) {
            $actions[] = [
                'label' => 'เปิดดูใบกำกับภาษี',
                'url' => route('office.tax-invoices.show', ['taxInvoice' => $ti]),
                'tone' => 'secondary',
            ];
            if ($ti->canCreateReceipt() && ! $receipt) {
                $actions[] = [
                    'label' => 'บันทึกรับชำระเงิน',
                    'url' => route('office.tax-invoices.receipts.create', ['taxInvoice' => $ti]),
                    'tone' => 'success',
                ];
            }
        } elseif ($salesOrder->canCreateTaxInvoice()) {
            $actions[] = [
                'label' => 'ออกใบกำกับภาษี (TI)',
                'url' => route('office.sales-orders.tax-invoices.create', ['salesOrder' => $salesOrder]),
                'tone' => 'primary',
            ];
        }

        if ($receipt) {
            $actions[] = [
                'label' => 'เปิดดูใบเสร็จ',
                'url' => route('office.receipts.show', ['receipt' => $receipt]),
                'tone' => 'success',
            ];
        }

        return $actions;
    }

    /**
     * @param  list<array<string, mixed>>  $lanes
     * @return list<array<string, mixed>>
     */
    private function buildBillingRatio(array $lanes): array
    {
        return array_map(function (array $lane) {
            return [
                'installment_no' => $lane['installment_no'],
                'label' => $lane['label'],
                'percent' => $lane['percent'],
                'amount' => $lane['amount'],
                'status' => $lane['status'],
                'width' => max(0, min(100, (float) $lane['percent'])),
            ];
        }, $lanes);
    }

    /**
     * @param  list<array<string, mixed>>  $lanes
     * @return array<string, mixed>|null
     */
    private function buildActivePanel(array $lanes, ?SalesOrder $salesOrder, Model $document, string $currentType): ?array
    {
        $active = collect($lanes)->firstWhere('is_active', true);
        if (! $active && $lanes !== []) {
            $active = $lanes[0];
        }

        if (! $active) {
            // Pre-SO journey: show current upstream document
            return [
                'title' => $this->currentDocumentTitle($document, $currentType),
                'doc_no' => $this->currentDocumentNo($document, $currentType),
                'amount' => null,
                'due_date_display' => null,
                'status_text' => 'ยังไม่มีใบสั่งขาย — ดำเนินการต้นน้ำก่อนแตกงวด',
                'actions' => $this->preSalesActions($document, $currentType),
            ];
        }

        $focusStep = null;
        foreach ($active['steps'] as $step) {
            if ($step['is_current'] ?? false) {
                $focusStep = $step;
                break;
            }
        }
        if (! $focusStep) {
            foreach ($active['steps'] as $step) {
                if (($step['state'] ?? null) === 'active') {
                    $focusStep = $step;
                    break;
                }
            }
        }
        if (! $focusStep) {
            foreach (array_reverse($active['steps']) as $step) {
                if ($step['exists'] ?? false) {
                    $focusStep = $step;
                    break;
                }
            }
        }

        $title = 'งวดที่ '.$active['installment_no'].': '.$active['label'];
        $statusText = $active['status_label'] ?? '';
        if ($focusStep) {
            $title = match ($focusStep['type'] ?? '') {
                'billing_note' => 'ใบวางบิล งวดที่ '.$active['installment_no'].' (Billing Note)',
                'delivery_note' => 'ใบส่งมอบ งวดที่ '.$active['installment_no'].' (Delivery Note)',
                'tax_invoice' => 'ใบกำกับภาษี งวดที่ '.$active['installment_no'].' (Tax Invoice)',
                'receipt' => 'ใบเสร็จรับเงิน งวดที่ '.$active['installment_no'].' (Receipt)',
                default => $focusStep['label'].' · งวดที่ '.$active['installment_no'],
            };
            $statusText = match ($focusStep['type'] ?? '') {
                'billing_note' => ($focusStep['state'] ?? '') === 'done'
                    ? 'วางบิลแล้ว — รอขั้นตอนถัดไป'
                    : 'รอการชำระเงินจากลูกค้า / ดำเนินการวางบิล',
                'delivery_note' => 'อยู่ระหว่างส่งมอบสินค้า/บริการ',
                'tax_invoice' => 'อยู่ระหว่างออกใบกำกับภาษี',
                'receipt' => 'อยู่ระหว่างรับชำระเงิน',
                default => $statusText,
            };
            if (! empty($active['due_date_display']) && ($focusStep['type'] ?? '') === 'billing_note') {
                $statusText .= ' (ครบกำหนด '.$active['due_date_display'].')';
            }
        }

        return [
            'title' => $title,
            'doc_no' => $focusStep['display_no'] ?? null,
            'amount' => $active['amount'],
            'due_date_display' => $active['due_date_display'],
            'status_text' => $statusText,
            'actions' => $active['actions'] ?? [],
            'credit_notes' => $active['credit_notes'] ?? [],
            'debit_notes' => $active['debit_notes'] ?? [],
        ];
    }

    /**
     * @return list<array{label: string, url: string, tone: string}>
     */
    private function preSalesActions(Model $document, string $currentType): array
    {
        $actions = [];

        if ($document instanceof Lead && $document->converted_opportunity_id) {
            $actions[] = [
                'label' => 'เปิด Opportunity',
                'url' => route('office.opportunities.show', ['opportunity' => $document->converted_opportunity_id]),
                'tone' => 'primary',
            ];
        }

        if ($document instanceof Opportunity) {
            $actions[] = [
                'label' => 'สร้างใบเสนอราคา',
                'url' => route('office.opportunities.quotations.create', ['opportunity' => $document]),
                'tone' => 'primary',
            ];
        }

        if ($document instanceof Quotation && method_exists($document, 'canCreateSalesOrder') && $document->canCreateSalesOrder()) {
            $actions[] = [
                'label' => 'สร้าง Sales Order',
                'url' => route('office.quotations.sales-orders.create', ['quotation' => $document]),
                'tone' => 'primary',
            ];
        }

        return $actions;
    }

    private function currentDocumentTitle(Model $document, string $currentType): string
    {
        return match ($currentType) {
            'lead' => 'Lead',
            'opportunity' => 'Opportunity',
            'quotation' => 'ใบเสนอราคา',
            'sales_order' => 'Sales Order',
            'billing_note' => 'ใบวางบิล',
            'delivery_note' => 'ใบส่งมอบ',
            'tax_invoice' => 'ใบกำกับภาษี',
            'receipt' => 'ใบเสร็จรับเงิน',
            'credit_note' => 'ใบลดหนี้',
            'debit_note' => 'ใบเพิ่มหนี้',
            default => 'เอกสาร',
        };
    }

    private function currentDocumentNo(Model $document, string $currentType): ?string
    {
        if ($document instanceof Lead) {
            return $document->registration_no;
        }
        if ($document instanceof Opportunity) {
            return $document->opportunity_no;
        }
        if (method_exists($document, 'displayNo')) {
            return $document->displayNo();
        }

        return null;
    }

    private function termAmount(SalesOrderPaymentTerm $term, float $soTotal): float
    {
        if ($term->baht_amount !== null && (float) $term->baht_amount > 0) {
            return (float) $term->baht_amount;
        }
        if ($term->percent_amount !== null) {
            return round($soTotal * ((float) $term->percent_amount) / 100, 2);
        }

        return 0.0;
    }

    private function termPercent(SalesOrderPaymentTerm $term, float $soTotal): float
    {
        if ($term->percent_amount !== null) {
            return (float) $term->percent_amount;
        }
        if ($soTotal > 0 && $term->baht_amount !== null) {
            return round(((float) $term->baht_amount) / $soTotal * 100, 2);
        }

        return 0.0;
    }

    private function mapLeadStatus(Lead $lead): string
    {
        if ($lead->status === 'converted' || $lead->converted_opportunity_id) {
            return 'done';
        }
        if (in_array($lead->status, ['unqualified', 'duplicate', 'archived'], true)) {
            return 'voided';
        }

        return 'active';
    }

    private function mapOpportunityStatus(Opportunity $opportunity): string
    {
        if ($opportunity->stage === 'closed_won') {
            return 'done';
        }
        if ($opportunity->stage === 'closed_lost') {
            return 'voided';
        }

        return 'active';
    }

    private function mapRevisionDocStatus(?string $status): string
    {
        if (in_array($status, ['accepted', 'approved', 'issued', 'confirmed', 'closed'], true)) {
            return 'done';
        }
        if (in_array($status, ['rejected', 'cancelled', 'expired', 'voided', 'superseded'], true)) {
            return 'voided';
        }
        if ($status) {
            return 'active';
        }

        return 'empty';
    }

    private function mapSalesOrderStatus(?string $status): string
    {
        if (in_array($status, ['confirmed', 'closed'], true)) {
            return 'done';
        }
        if ($status === 'cancelled') {
            return 'voided';
        }
        if ($status) {
            return 'active';
        }

        return 'empty';
    }
}
