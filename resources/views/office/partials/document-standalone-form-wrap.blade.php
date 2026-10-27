@php
    $formId = $formId ?? 'document-form';
    $showPaymentTerms = $showPaymentTerms ?? false;
    $seedLines = $seedLines ?? [];
    $discountType = $discountType ?? old('discount_type', $totalsDefaults['discount_type'] ?? 'percent');
@endphp
@if ($showStandaloneAlert ?? true)
    @include('office.partials.document-standalone-alert')
@endif

<form method="post" action="{{ $formAction }}" id="{{ $formId }}" class="document-form">
    @csrf
    @if (($formMethod ?? 'post') === 'put')
        @method('PUT')
    @endif
    @if ($partner?->id)
        <input type="hidden" name="partner_id" value="{{ $partner->id }}">
    @endif

    <div class="emp-card-main mb-4">
        {{-- ส่วนหัว / ข้อมูลเอกสาร (ถ้ามี) --}}
        @if (isset($headerFieldsView))
            <div class="section-title">
                <div class="section-icon"><i class="ri-file-text-line"></i></div>
                <span>{{ $headerTitle ?? 'ข้อมูลเอกสารและวันที่' }}</span>
            </div>
            <div class="mb-4">
                @include($headerFieldsView, ['revision' => $revision ?? null])
            </div>
        @endif

        {{-- ส่วนที่ 2: ข้อมูลคู่ค้าและผู้ออกเอกสาร --}}
        <div class="section-title {{ isset($headerFieldsView) ? 'mt-5' : '' }}">
            <div class="section-icon bg-success-subtle text-success"><i class="ri-building-4-line"></i></div>
            <span>ข้อมูลคู่ค้าและผู้ออกเอกสาร</span>
        </div>
        <div class="mb-4">
            @include('office.partials.document-party-form', [
                'customerParty' => $customerParty ?? [],
                'sellerParty' => $sellerParty ?? [],
                'provinces' => $provinces ?? collect(),
                'customerAmphures' => $customerAmphures ?? collect(),
                'customerTambons' => $customerTambons ?? collect(),
                'counterpartyTitle' => $counterpartyTitle ?? 'ข้อมูลลูกค้า',
                'issuerTitle' => $issuerTitle ?? 'ข้อมูลผู้ขาย',
            ])
        </div>

        {{-- ส่วนที่ 3: รายการสินค้าและบริการ --}}
        <div class="section-title mt-5">
            <div class="section-icon bg-warning-subtle text-warning"><i class="ri-shopping-cart-2-line"></i></div>
            <span>รายการสินค้าและบริการ</span>
        </div>
        <div class="mb-4">
            @include('office.partials.document-editable-lines-section', [
                'seedLines' => $seedLines,
                'discountType' => $discountType,
                'totalsDefaults' => $totalsDefaults ?? [],
            ])
        </div>

        {{-- ส่วนที่ 4: เงื่อนไขการชำระเงิน (ถ้ามี) --}}
        @if ($showPaymentTerms)
            <div class="section-title mt-5">
                <div class="section-icon bg-info-subtle text-info"><i class="ri-bank-card-line"></i></div>
                <span>เงื่อนไขการชำระเงิน (Payment Terms)</span>
            </div>
            <div class="mb-4">
                @include('office.partials.payment-terms-form', [
                    'embedded' => true,
                    'paymentRows' => $paymentRows ?? null,
                    'paymentMode' => $paymentMode ?? null,
                ])
            </div>
        @endif

        {{-- ส่วนท้าย / ข้อมูลเพิ่มเติม (ถ้ามี) --}}
        @isset($footerFieldsView)
            <div class="section-title mt-5">
                <div class="section-icon bg-secondary-subtle text-secondary"><i class="ri-sticky-note-line"></i></div>
                <span>{{ $footerTitle ?? 'ข้อมูลเพิ่มเติมและหมายเหตุ' }}</span>
            </div>
            <div class="mb-4">
                @include($footerFieldsView, ['revision' => $revision ?? null])
            </div>
        @endisset

        {{-- Action Bar --}}
        @php
            $displayNo = $documentNo ?? null;
            if (! $displayNo && isset($revision)) {
                $doc = $revision->salesOrder
                    ?? $revision->billingNote
                    ?? $revision->deliveryNote
                    ?? $revision->quotation
                    ?? $revision->taxInvoice
                    ?? $revision->receipt
                    ?? $revision->creditNote
                    ?? $revision->debitNote
                    ?? ($document ?? null);
                $displayNo = $doc?->displayNo();
            }
            $statusText = $displayNo ? 'เลขที่: <strong>' . e($displayNo) . '</strong>' : 'เลขที่: ร่างยังไม่ออกเลข';
        @endphp
        @include('office.partials.document-form-actionbar', [
            'cancelUrl' => $cancelUrl,
            'submitLabel' => $submitLabel,
            'left' => $statusText,
            'documentNo' => $displayNo,
            'revisionNo' => $revision?->revision_no ?? null,
            'status' => $revision?->status ?? null,
        ])
    </div>
</form>
