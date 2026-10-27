@php
    use App\Services\DocumentPartyService;

    $revision = $revision ?? null;
    $counterpartyTitle = $counterpartyTitle ?? 'ข้อมูลลูกค้า';
    $issuerTitle = $issuerTitle ?? 'ข้อมูลผู้ขาย';
    $partyInfo = app(DocumentPartyService::class);

    $parentDoc = $revision?->receipt 
        ?? $revision?->taxInvoice 
        ?? $revision?->billingNote 
        ?? $revision?->deliveryNote 
        ?? $revision?->salesOrder 
        ?? $revision?->quotation 
        ?? $revision?->creditNote 
        ?? $revision?->debitNote;

    $partner = $parentDoc?->partner 
        ?? $parentDoc?->taxInvoice?->partner 
        ?? $parentDoc?->salesOrder?->partner;

    $contact = $parentDoc?->partnerContact 
        ?? $parentDoc?->taxInvoice?->partnerContact 
        ?? $parentDoc?->salesOrder?->partnerContact 
        ?? $partner?->contacts?->first();

    $branch = $parentDoc?->partnerBranch 
        ?? $parentDoc?->taxInvoice?->partnerBranch 
        ?? $parentDoc?->salesOrder?->partnerBranch 
        ?? $partner?->branches?->first();

    $customerDefaults = $partner ? $partyInfo->customerDefaults($partner, $contact, $branch) : [];
    $sellerDefaults = $partyInfo->sellerDefaults($parentDoc?->create_by ?? null);

    $contactName = $revision?->customer_contact_name ?: ($customerDefaults['customer_contact_name'] ?? '');
    $contactPhone = $revision?->customer_contact_phone ?: ($customerDefaults['customer_contact_phone'] ?? '');
    $email = $revision?->customer_email ?: ($customerDefaults['customer_email'] ?? '');
    $customerType = $revision?->customer_type ?? ($customerDefaults['customer_type'] ?? null);
    $taxNo = $revision?->customer_tax_no ?: ($customerDefaults['customer_tax_no'] ?? '');
    $customerName = $revision?->customer_name ?: ($customerDefaults['customer_name'] ?? '');
    $customerStatus = $revision?->customer_status ?? ($customerDefaults['customer_status'] ?? null);
    $branchNo = $revision?->customer_branch_no ?: ($customerDefaults['customer_branch_no'] ?? '');
    $branchName = $revision?->customer_branch_name ?: ($customerDefaults['customer_branch_name'] ?? '');
    $address = $revision?->customer_address ?: ($customerDefaults['customer_address'] ?? '');
    $provinceId = $revision?->customer_province_id ?? ($customerDefaults['customer_province_id'] ?? null);
    $amphureId = $revision?->customer_amphure_id ?? ($customerDefaults['customer_amphure_id'] ?? null);
    $tambonId = $revision?->customer_tambon_id ?? ($customerDefaults['customer_tambon_id'] ?? null);
    $zipCode = $revision?->customer_zip_code ?: ($customerDefaults['customer_zip_code'] ?? '');

    $sellerName = $revision?->seller_name ?: ($sellerDefaults['seller_name'] ?? '');
    $sellerPhone = $revision?->seller_phone ?: ($sellerDefaults['seller_phone'] ?? '');
    $sellerEmail = $revision?->seller_email ?: ($sellerDefaults['seller_email'] ?? '');

    $isNaturalPerson = (int) ($customerType ?? 0) === 3;
    $isBranch = (int) ($customerStatus ?? 0) === 1;
    $addressLine = $partyInfo->formatAddressLine(
        $address,
        $tambonId,
        $amphureId,
        $provinceId,
    );
@endphp
@if ($revision || $parentDoc)
<div class="row g-3 mt-1" id="document-party-display">
    <div class="col-lg-6">
        <div class="border rounded-3 p-3 h-100">
            <h6 class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                <span class="avatar-xs flex-shrink-0">
                    <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-16">
                        <i class="ri-building-line"></i>
                    </span>
                </span>
                {{ $counterpartyTitle }}
            </h6>

            <p class="text-muted fs-12 mb-2">ผู้ติดต่อ</p>
            <div class="row g-2 mb-3">
                <div class="col-md-12">
                    <label class="form-label fs-13">ชื่อผู้ติดต่อ</label>
                    <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $contactName ?: '-' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-13">เบอร์โทร</label>
                    <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $contactPhone ?: '-' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-13">อีเมล</label>
                    <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $email ?: '-' }}">
                </div>
            </div>

            <p class="text-muted fs-12 mb-2">ธุรกิจ</p>
            <div class="row g-2 mb-3">
                <div class="col-md-6">
                    <label class="form-label fs-13">ประเภทธุรกิจ</label>
                    <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $partyInfo->partnerTypeLabel($customerType) ?: '-' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-13">เลขประจำตัวผู้เสียภาษี</label>
                    <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $taxNo ?: '-' }}">
                </div>
                <div class="col-12">
                    <label class="form-label fs-13">ชื่อธุรกิจ / ชื่อลูกค้า</label>
                    <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $customerName ?: '-' }}">
                </div>
            </div>

            @unless ($isNaturalPerson)
                <div class="mb-3">
                    <p class="text-muted fs-12 mb-2">สาขา</p>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fs-13">สถานะ</label>
                            <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $isBranch ? 'สาขา' : 'สำนักงานใหญ่' }}">
                        </div>
                    </div>
                    @if ($isBranch)
                        <div class="row g-2 mt-2">
                            <div class="col-md-6">
                                <label class="form-label fs-13">รหัสสาขา</label>
                                <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $branchNo ?: '-' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-13">ชื่อสาขา</label>
                                <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $branchName ?: '-' }}">
                            </div>
                        </div>
                    @endif
                </div>
            @endunless

            <div class="row g-2">
                <div class="col-12">
                    <label class="form-label fs-13">ที่อยู่</label>
                    <textarea class="form-control form-control-sm bg-light" rows="2" readonly>{{ $addressLine ?: '-' }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-13">รหัสไปรษณีย์</label>
                    <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $zipCode ?: '-' }}">
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="border rounded-3 p-3 h-100">
            <h6 class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                <span class="avatar-xs flex-shrink-0">
                    <span class="avatar-title bg-success-subtle text-success rounded-circle fs-16">
                        <i class="ri-user-star-line"></i>
                    </span>
                </span>
                {{ $issuerTitle }}
            </h6>
            <div class="row g-2">
                <div class="col-12">
                    <label class="form-label fs-13">{{ str_contains($issuerTitle, 'ผู้ซื้อ') ? 'ผู้ซื้อ' : 'ผู้ขาย' }}</label>
                    <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $sellerName ?: '-' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-13">เบอร์โทร</label>
                    <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $sellerPhone ?: '-' }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-13">อีเมล</label>
                    <input type="text" class="form-control form-control-sm bg-light" readonly value="{{ $sellerEmail ?: '-' }}">
                </div>
            </div>
        </div>
    </div>
</div>
@endif

