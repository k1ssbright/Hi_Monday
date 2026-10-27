@php
    $customerParty = $customerParty ?? [];
    $sellerParty = $sellerParty ?? [];
    $counterpartyTitle = $counterpartyTitle ?? 'ข้อมูลลูกค้า';
    $issuerTitle = $issuerTitle ?? 'ข้อมูลผู้ขาย';
    $provinces = $provinces ?? collect();
    $customerAmphures = $customerAmphures ?? collect();
    $customerTambons = $customerTambons ?? collect();
    $isNaturalPerson = (string) ($customerParty['customer_type'] ?? '') === '3';
    $isBranch = (string) ($customerParty['customer_status'] ?? '0') === '1';
@endphp
<div class="row g-4" id="document-party-form">
    {{-- ฝั่งลูกค้า (Customer Party Card) --}}
    <div class="col-lg-6">
        <div class="contact-card h-100">
            <div class="contact-card-header">
                <span class="contact-number">
                    <i class="ri-user-smile-line me-1 text-primary"></i> {{ $counterpartyTitle }}
                </span>
            </div>

            <div class="d-flex align-items-center gap-2 mb-3 pb-1 border-bottom">
                <span class="avatar-xs flex-shrink-0" style="width: 24px; height: 24px;">
                    <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-12">
                        <i class="ri-user-3-line"></i>
                    </span>
                </span>
                <span class="fs-13 fw-semibold text-dark">ข้อมูลผู้ติดต่อ</span>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label fs-13 fw-semibold">ชื่อผู้ติดต่อ</label>
                    <input type="text" name="customer_contact_name" class="form-control"
                        value="{{ $customerParty['customer_contact_name'] ?? '' }}" placeholder="ระบุชื่อ-นามสกุลผู้ติดต่อ">
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-13 fw-semibold">เบอร์โทรศัพท์</label>
                    <input type="text" name="customer_contact_phone" class="form-control"
                        value="{{ $customerParty['customer_contact_phone'] ?? '' }}" placeholder="08X-XXX-XXXX">
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-13 fw-semibold">อีเมล</label>
                    <input type="email" name="customer_email" class="form-control"
                        value="{{ $customerParty['customer_email'] ?? '' }}" placeholder="email@company.com">
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mb-3 pb-1 border-bottom">
                <span class="avatar-xs flex-shrink-0" style="width: 24px; height: 24px;">
                    <span class="avatar-title bg-info-subtle text-info rounded-circle fs-12">
                        <i class="ri-building-line"></i>
                    </span>
                </span>
                <span class="fs-13 fw-semibold text-dark">ข้อมูลธุรกิจ / นิติบุคคล</span>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fs-13 fw-semibold">ประเภทธุรกิจ</label>
                    <select name="customer_type" id="customer_type" class="form-select select2-form-select" data-placeholder="เลือกประเภทธุรกิจ">
                        <option value="">เลือกประเภท</option>
                        <option value="1" @selected((string) ($customerParty['customer_type'] ?? '') === '1')>บริษัท (จำกัด / มหาชน)</option>
                        <option value="2" @selected((string) ($customerParty['customer_type'] ?? '') === '2')>ห้างหุ้นส่วน</option>
                        <option value="3" @selected((string) ($customerParty['customer_type'] ?? '') === '3')>บุคคลธรรมดา</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-13 fw-semibold">เลขประจำตัวผู้เสียภาษี (13 หลัก)</label>
                    <input type="text" name="customer_tax_no" class="form-control" maxlength="13"
                        value="{{ $customerParty['customer_tax_no'] ?? '' }}" placeholder="0-0000-00000-00-0">
                </div>
                <div class="col-12">
                    <label class="form-label fs-13 fw-semibold">ชื่อธุรกิจ / ชื่อลูกค้า</label>
                    <input type="text" name="customer_name" class="form-control"
                        value="{{ $customerParty['customer_name'] ?? '' }}" placeholder="ระบุชื่อบริษัทหรือชื่อลูกค้า">
                </div>
            </div>

            <div id="customer-branch-section" class="mb-3" @if($isNaturalPerson) style="display:none" @endif>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fs-13 fw-semibold">สถานะสาขา</label>
                        <select name="customer_status" id="customer_status" class="form-select select2-form-select" data-placeholder="เลือกสถานะสาขา">
                            <option value="0" @selected((string) ($customerParty['customer_status'] ?? '0') === '0')>สำนักงานใหญ่</option>
                            <option value="1" @selected((string) ($customerParty['customer_status'] ?? '') === '1')>สาขา</option>
                        </select>
                    </div>
                </div>
                <div id="customer-branch-detail-fields" class="row g-3 mt-1" @if(!$isBranch) style="display:none" @endif>
                    <div class="col-md-6">
                        <label class="form-label fs-13 fw-semibold">รหัสสาขา (5 หลัก)</label>
                        <input type="text" name="customer_branch_no" class="form-control" maxlength="5"
                            value="{{ $customerParty['customer_branch_no'] ?? '' }}" placeholder="00001">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-13 fw-semibold">ชื่อสาขา</label>
                        <input type="text" name="customer_branch_name" class="form-control"
                            value="{{ $customerParty['customer_branch_name'] ?? '' }}" placeholder="เช่น สาขาสีลม">
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mt-4 mb-3 pb-1 border-bottom">
                <span class="avatar-xs flex-shrink-0" style="width: 24px; height: 24px;">
                    <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-12">
                        <i class="ri-map-pin-2-line"></i>
                    </span>
                </span>
                <span class="fs-13 fw-semibold text-dark">ที่อยู่สำหรับออกเอกสาร</span>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fs-13 fw-semibold">ที่อยู่</label>
                    <textarea name="customer_address" class="form-control" rows="2"
                        placeholder="เลขที่ อาคาร ซอย ถนน">{{ $customerParty['customer_address'] ?? '' }}</textarea>
                </div>
            </div>

            <div class="mt-2">
                @include('partials.thailand-address-autocomplete', [
                    'provinceName' => 'customer_province_id',
                    'amphureName' => 'customer_amphure_id',
                    'tambonName' => 'customer_tambon_id',
                    'zipName' => 'customer_zip_code',
                    'selectedProvinceId' => old('customer_province_id', $customerParty['customer_province_id'] ?? ''),
                    'selectedAmphureId' => old('customer_amphure_id', $customerParty['customer_amphure_id'] ?? ''),
                    'selectedTambonId' => old('customer_tambon_id', $customerParty['customer_tambon_id'] ?? ''),
                    'selectedZipCode' => old('customer_zip_code', $customerParty['customer_zip_code'] ?? ''),
                    'idPrefix' => 'customer_address',
                    'variant' => 'office',
                    'size' => 'sm',
                ])
            </div>
        </div>
    </div>

    {{-- ฝั่งผู้ออกเอกสาร (Seller Party Card) --}}
    <div class="col-lg-6">
        <div class="contact-card h-100">
            <div class="contact-card-header">
                <span class="contact-number">
                    <i class="ri-store-2-line me-1 text-success"></i> {{ $issuerTitle }}
                </span>
            </div>

            <div class="d-flex align-items-center gap-2 mb-3 pb-1 border-bottom">
                <span class="avatar-xs flex-shrink-0" style="width: 24px; height: 24px;">
                    <span class="avatar-title bg-success-subtle text-success rounded-circle fs-12">
                        <i class="ri-user-star-line"></i>
                    </span>
                </span>
                <span class="fs-13 fw-semibold text-dark">ข้อมูลตัวแทน / ผู้รับผิดชอบ</span>
            </div>

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fs-13 fw-semibold">{{ str_contains($issuerTitle, 'ผู้ซื้อ') ? 'ชื่อผู้ซื้อ / ตัวแทน' : 'ชื่อผู้ขาย / พนักงานขาย' }}</label>
                    <input type="text" name="seller_name" class="form-control"
                        value="{{ $sellerParty['seller_name'] ?? '' }}" placeholder="เช่น สมชาย ใจดี">
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-13 fw-semibold">เบอร์โทรศัพท์</label>
                    <input type="text" name="seller_phone" class="form-control"
                        value="{{ $sellerParty['seller_phone'] ?? '' }}" placeholder="02-XXX-XXXX หรือ 08X-XXX-XXXX">
                </div>
                <div class="col-md-6">
                    <label class="form-label fs-13 fw-semibold">อีเมล</label>
                    <input type="email" name="seller_email" class="form-control"
                        value="{{ $sellerParty['seller_email'] ?? '' }}" placeholder="seller@company.com">
                </div>
            </div>
        </div>
    </div>
</div>
