@props([
    'revision' => null,
    'metaFields' => [],
    'showPaymentTerms' => false,
    'paymentTerms' => null,
    'paymentMode' => 'percent',
    'attachments' => null,
    'linesVariant' => 'standard',
    'counterpartyTitle' => 'ข้อมูลลูกค้า',
    'issuerTitle' => 'ข้อมูลผู้ขาย',
])

<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="card document-form">
            <!-- Printable Company Header -->
            <div class="d-none d-print-block p-3 border-bottom mb-2">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">ABBL</h5>
                        <p class="mb-0 fs-11 text-muted">สำนักงานใหญ่: 123/45 ถนนสุขุมวิท กรุงเทพมหานคร 10110 | โทร: 02-123-4567 | เลขประจำตัวผู้เสียภาษี: 0105550000000</p>
                    </div>
                </div>
            </div>

            @if ($metaFields !== [])
                <div class="card-body border-bottom border-bottom-dashed p-4">
                    @include('office.partials.document-show-meta', ['fields' => $metaFields])
                </div>
            @endif

            <div class="card-body border-bottom border-bottom-dashed p-4">
                @include('office.partials.document-party-display', [
                    'revision' => $revision,
                    'counterpartyTitle' => $counterpartyTitle,
                    'issuerTitle' => $issuerTitle,
                ])
            </div>

            <div class="card-body p-4 border-bottom border-bottom-dashed">
                @include('office.partials.document-vat-type-display', ['revision' => $revision])
                @include('office.partials.document-lines-readonly', [
                    'revision' => $revision,
                    'variant' => $linesVariant,
                ])
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mt-4 pt-1">
                    <div></div>
                    @include('office.partials.document-totals-summary-display', ['revision' => $revision])
                </div>
            </div>

            @if ($showPaymentTerms)
                <div class="card-body p-4 border-top border-top-dashed">
                    @include('office.partials.payment-terms-display', [
                        'terms' => $paymentTerms ?? collect(),
                        'mode' => $paymentMode,
                    ])
                </div>
            @endif

            @if (($attachments ?? collect())->isNotEmpty())
                <div class="card-body p-4 border-top border-top-dashed d-print-none">
                    @include('office.partials.document-attachments', [
                        'attachments' => $attachments,
                        'embedded' => true,
                    ])
                </div>
            @endif

            @if (isset($related))
                <div class="card-body p-4 border-top border-top-dashed d-print-none">
                    {{ $related }}
                </div>
            @endif

            <!-- Printable Signature Block -->
            <div class="d-none d-print-block p-4 mt-3">
                <div class="row text-center g-4">
                    <div class="col-6">
                        <div class="border rounded-3 p-3">
                            <p class="fs-12 fw-bold text-dark mb-4">ผู้รับเอกสาร / ผู้รับสินค้า (Received By)</p>
                            <div class="mx-auto mb-2" style="border-bottom: 1.5px dashed #64748b; width: 75%; margin-top: 35px;"></div>
                            <p class="fs-11 text-muted mb-0">วันที่ / Date: ......./......./.......</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded-3 p-3">
                            <p class="fs-12 fw-bold text-dark mb-4">ผู้ออกเอกสาร / ผู้มีอำนาจลงนาม (Authorized Signature)</p>
                            <div class="mx-auto mb-2" style="border-bottom: 1.5px dashed #64748b; width: 75%; margin-top: 35px;"></div>
                            <p class="fs-11 text-muted mb-0">วันที่ / Date: ......./......./.......</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (isset($actionbar))
            {{ $actionbar }}
        @endif
    </div>
</div>

