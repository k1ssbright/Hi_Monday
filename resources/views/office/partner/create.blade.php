@extends('office.layout.office')

@section('title', 'เพิ่มคู่ค้าใหม่ | Hi Monday')

@section('style')
    <style>
        /* Premium Background & Typography */
        body {
            background-color: #f1f5f9;
            color: #334155;
        }

        .biz-card-main {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            border: 1px solid #f1f5f9;
            padding: 32px;
        }

        /* Section Headers */
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #e2e8f0;
        }

        .section-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: #eff6ff;
            color: #3b82f6;
            border-radius: 8px;
            font-size: 1.1rem;
        }

        /* Premium Inputs */
        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .form-control,
        .form-select {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 10px 16px;
            font-size: 0.95rem;
            background-color: #f8fafc;
            transition: all 0.2s ease-in-out;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #ffffff;
            border-color: #93c5fd;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        .form-control::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        /* Contact Cards */
        .contact-card {
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 16px;
            background: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
            transition: all 0.3s ease;
            animation: slideDownFade 0.3s ease forwards;
        }

        .contact-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }

        .contact-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #e2e8f0;
        }

        .contact-number {
            font-size: 0.9rem;
            font-weight: 700;
            color: #64748b;
            background: #f1f5f9;
            padding: 4px 12px;
            border-radius: 20px;
        }

        /* Buttons */
        .btn-premium-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }

        .btn-premium-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 8px -1px rgba(37, 99, 235, 0.3);
            color: white;
        }

        .btn-premium-light {
            background: #f1f5f9;
            color: #475569;
            font-weight: 600;
            padding: 10px 24px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .btn-premium-light:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        /* Animations */
        @keyframes slideDownFade {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Business Type Selector Cards */
        .type-card {
            border: 1px solid #CBD5E1;
            border-radius: 10px;
            padding: 14px 16px;
            background-color: #F8FAFC;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .type-card:hover {
            border-color: #3b82f6;
            background-color: #EFF6FF;
        }
        .type-card.active {
            border: 2px solid #3b82f6;
            background-color: #EFF6FF;
        }
        .type-card-radio {
            width: 16px;
            height: 16px;
            accent-color: #3b82f6;
            cursor: pointer;
        }
        .type-card-icon {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            background-color: #E2E8F0;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .type-card.active .type-card-icon {
            background-color: #DBEAFE;
            color: #3b82f6;
        }
        .type-card-title {
            font-weight: 600;
            font-size: 14px;
            color: #1E293B;
            margin-bottom: 2px;
        }
        .type-card-sub {
            font-size: 11px;
            color: #64748B;
        }

        /* Custom Input for Prefix */
        .prefix-other-text {
            border-radius: 10px;
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        .prefix-other-text:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Horizontal Radio Cards */
        .radio-card {
            border: 1px solid #CBD5E1;
            border-radius: 10px;
            padding: 16px 12px;
            background-color: #F8FAFC;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            height: 100%;
            position: relative;
        }
        .radio-card:hover {
            border-color: #3b82f6;
            background-color: #EFF6FF;
        }
        .radio-card.active {
            border: 2px solid #3b82f6;
            background-color: #EFF6FF;
        }
        .radio-card-input {
            position: absolute;
            top: 12px;
            left: 12px;
            width: 16px;
            height: 16px;
            accent-color: #3b82f6;
            cursor: pointer;
            margin: 0;
        }
        .radio-card-title {
            font-weight: 600;
            font-size: 13px;
            color: #1E293B;
            margin-bottom: 2px;
            margin-top: 8px;
            line-height: 1.2;
        }
        .radio-card-sub {
            font-size: 11px;
            color: #64748B;
            line-height: 1.2;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        @include('office.settings.partials.flash')
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted fs-13">เพิ่มคู่ค้าใหม่</span>
            <div class="fs-13 text-muted">
                <span>ภาพรวม</span> <i class="ri-arrow-right-s-line"></i> <a href="{{ route('office.partners.index') }}"
                    class="text-muted text-decoration-none">สมุดรายชื่อคู่ค้า</a> <i class="ri-arrow-right-s-line"></i> <span
                    class="fw-semibold text-dark">เพิ่มคู่ค้าใหม่</span>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark mb-0" style="font-size: 1.3rem;">เพิ่มข้อมูลคู่ค้าใหม่</h4>
        </div>

        <div class="biz-card-main mb-4">
            <form id="createPartnerForm" method="post" action="{{ route('office.partners.store') }}">
                @csrf

                {{-- ═══════════════════════════════════════════════════════ --}}
                {{-- ส่วนที่ 1: ข้อมูลนิติบุคคล (Legal Entity) --}}
                {{-- ═══════════════════════════════════════════════════════ --}}
                <div class="section-title">
                    <div class="section-icon"><i class="ri-building-4-line"></i></div>
                    ข้อมูลนิติบุคคล
                </div>
                <div class="row g-4 mb-4">
                    <!-- คอลัมน์ซ้าย -->
                    <div class="col-md-6 border-end">
                        <div class="mb-3">
                            <label class="form-label fs-13 fw-semibold">รหัสคู่ค้า <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="partner_code" id="addPartnerCode" value="{{ old('partner_code') }}" placeholder="เช่น CUS002" required>
                        </div>
                        <div>
                            <label class="form-label fs-13 fw-semibold">ประเภทนิติบุคคล <span class="text-danger">*</span></label>
                            <div class="d-flex flex-column gap-2 mt-1">
                                <div class="type-card {{ (string) old('partner_type', '1') === '1' ? 'active' : '' }}" data-val="1">
                                    <input type="radio" name="partner_type" class="type-card-radio" value="1" @checked((string) old('partner_type', '1') === '1')>
                                    <div class="type-card-icon"><i class="ri-building-line"></i></div>
                                    <div>
                                        <div class="type-card-title">บริษัท</div>
                                        <div class="type-card-sub">บริษัทจำกัด / มหาชนจำกัด</div>
                                    </div>
                                </div>
                                <div class="type-card {{ (string) old('partner_type') === '2' ? 'active' : '' }}" data-val="2">
                                    <input type="radio" name="partner_type" class="type-card-radio" value="2" @checked((string) old('partner_type') === '2')>
                                    <div class="type-card-icon"><i class="ri-group-line"></i></div>
                                    <div>
                                        <div class="type-card-title">ห้างหุ้นส่วน</div>
                                        <div class="type-card-sub">ห้างหุ้นส่วนจำกัด / สามัญ</div>
                                    </div>
                                </div>
                                <div class="type-card {{ (string) old('partner_type') === '3' ? 'active' : '' }}" data-val="3">
                                    <input type="radio" name="partner_type" class="type-card-radio" value="3" @checked((string) old('partner_type') === '3')>
                                    <div class="type-card-icon"><i class="ri-user-line"></i></div>
                                    <div>
                                        <div class="type-card-title">บุคคลธรรมดา</div>
                                        <div class="type-card-sub">บุคคลธรรมดาประกอบธุรกิจ</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- คอลัมน์ขวา -->
                    <div class="col-md-6 ps-md-4">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label fs-13 fw-semibold">ประเภทคู่ค้า <span class="text-danger">*</span></label>
                                <div class="row g-2 mt-0">
                                    <div class="col-md-4">
                                        <div class="radio-card {{ old('partner_role', 'customer') === 'customer' ? 'active' : '' }}">
                                            <input type="radio" name="partner_role" class="radio-card-input" value="customer" @checked(old('partner_role', 'customer') === 'customer')>
                                            <i class="ri-user-smile-line fs-24 text-primary mt-1"></i>
                                            <div>
                                                <div class="radio-card-title">ลูกค้า</div>
                                                <div class="radio-card-sub">ผู้ซื้อสินค้า / บริการ</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio-card {{ old('partner_role') === 'vendor' ? 'active' : '' }}">
                                            <input type="radio" name="partner_role" class="radio-card-input" value="vendor" @checked(old('partner_role') === 'vendor')>
                                            <i class="ri-store-2-line fs-24 text-success mt-1"></i>
                                            <div>
                                                <div class="radio-card-title">ผู้ขาย</div>
                                                <div class="radio-card-sub">ผู้ขายสินค้า / บริการ</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio-card {{ old('partner_role') === 'both' ? 'active' : '' }}">
                                            <input type="radio" name="partner_role" class="radio-card-input" value="both" @checked(old('partner_role') === 'both')>
                                            <i class="ri-team-line fs-24 text-warning mt-1"></i>
                                            <div>
                                                <div class="radio-card-title">ลูกค้า & ผู้ขาย</div>
                                                <div class="radio-card-sub">เป็นทั้งสองอย่าง</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fs-13 fw-semibold">ชื่อบริษัท<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="partner_name_th" id="addPartnerName" value="{{ old('partner_name_th') }}" placeholder="เช่น บริษัท ตัวอย่าง จำกัด" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fs-13 fw-semibold">เลขประจำตัวผู้เสียภาษีอากร (13 หลัก) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="partner_tax_no" id="addPartnerTax" value="{{ old('partner_tax_no') }}" placeholder="0-0000-00000-00-0" maxlength="13" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fs-13 fw-semibold">สถานะภาษีมูลค่าเพิ่ม <span class="text-danger">*</span></label>
                                <select class="form-select" name="partner_vat_status" id="addPartnerVatStatus" required>
                                    <option value="1" @selected((string) old('partner_vat_status', '1') === '1')>จดทะเบียน VAT</option>
                                    <option value="0" @selected((string) old('partner_vat_status') === '0')>ไม่จดทะเบียน VAT</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════════════════ --}}
                {{-- ส่วนที่ 2: ข้อมูลสาขา / สำนักงาน (Branch) --}}
                {{-- ═══════════════════════════════════════════════════════ --}}
                <div class="section-title mt-5">
                    <div class="section-icon bg-success-subtle text-success"><i class="ri-map-pin-2-line"></i></div>
                    ข้อมูลสาขา / สำนักงาน
                </div>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label fs-13 fw-semibold">รหัสสาขา (5 หลัก) <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="partner_branch_no" id="addPartnerBranchCode" value="{{ old('partner_branch_no', '00000') }}" placeholder="00000"
                            maxlength="5" required>
                        <div class="form-text">00000 = สำนักงานใหญ่</div>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fs-13 fw-semibold">ชื่อสาขา / สำนักงาน <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="partner_branch_name_th" id="addPartnerBranchName" value="{{ old('partner_branch_name_th') }}"
                            placeholder="เช่น สำนักงานใหญ่, สาขาสีลม" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fs-13 fw-semibold">ที่อยู่ <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="partner_address" id="addPartnerAddress" rows="2"
                            placeholder="เลขที่ อาคาร ซอย ถนน แขวง/ตำบล เขต/อำเภอ จังหวัด รหัสไปรษณีย์" required>{{ old('partner_address') }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-13 fw-semibold">หมายเลขโทรศัพท์สำนักงาน <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="partner_tel" id="addPartnerTel" value="{{ old('partner_tel') }}" placeholder="02-XXX-XXXX" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-13 fw-semibold">หมายเลขโทรศัพท์มือถือ</label>
                        <input type="text" class="form-control" name="partner_phone" id="addPartnerMobile" value="{{ old('partner_phone') }}" placeholder="08X-XXX-XXXX">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-13 fw-semibold">โทรสาร (Fax)</label>
                        <input type="text" class="form-control" name="partner_fax" id="addPartnerFax" value="{{ old('partner_fax') }}" placeholder="02-XXX-XXXX">
                    </div>
                </div>

                {{-- ═══════════════════════════════════════════════════════ --}}
                {{-- ส่วนที่ 3: ข้อมูลผู้ติดต่อ (Contact Person) --}}
                {{-- ═══════════════════════════════════════════════════════ --}}
                <div class="d-flex justify-content-between align-items-center mt-5 mb-3 pb-2 border-bottom">
                    <div class="section-title mb-0 border-0 pb-0">
                        <div class="section-icon bg-warning-subtle text-warning"><i class="ri-contacts-book-2-line"></i>
                        </div>
                        ข้อมูลผู้ติดต่อ
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="btnAddContact"
                        style="font-weight: 600;">
                        <i class="ri-add-line fs-14 align-middle"></i> เพิ่มผู้ติดต่อ
                    </button>
                </div>

                <div id="addContactContainer">
                    <!-- ผู้ติดต่อคนที่ 1 (หลัก — ลบไม่ได้) -->
                    <div class="contact-card">
                        <div class="contact-card-header">
                            <span class="contact-number"><i class="ri-user-star-line me-1 text-warning"></i>
                                ผู้ติดต่อคนที่ 1 (หลัก)</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fs-13 fw-semibold">คำนำหน้า <span class="text-danger">*</span></label>
                                <div class="d-flex flex-wrap align-items-center gap-4 mt-1">
                                    <div class="form-check form-check-inline me-0">
                                        <input class="form-check-input prefix-radio" type="radio" name="contacts[0][prefix]" value="นาย" @checked(old('contacts.0.prefix', 'นาย') === 'นาย')>
                                        <label class="form-check-label text-dark">นาย</label>
                                    </div>
                                    <div class="form-check form-check-inline me-0">
                                        <input class="form-check-input prefix-radio" type="radio" name="contacts[0][prefix]" value="นาง" @checked(old('contacts.0.prefix') === 'นาง')>
                                        <label class="form-check-label text-dark">นาง</label>
                                    </div>
                                    <div class="form-check form-check-inline me-0">
                                        <input class="form-check-input prefix-radio" type="radio" name="contacts[0][prefix]" value="นางสาว" @checked(old('contacts.0.prefix') === 'นางสาว')>
                                        <label class="form-check-label text-dark">นางสาว</label>
                                    </div>
                                    <div class="form-check form-check-inline me-0">
                                        <input class="form-check-input prefix-radio" type="radio" name="contacts[0][prefix]" value="อื่นๆ" @checked(old('contacts.0.prefix') === 'อื่นๆ')>
                                        <label class="form-check-label text-dark">อื่นๆ</label>
                                    </div>
                                    <input type="text" class="prefix-other-text {{ old('contacts.0.prefix') === 'อื่นๆ' ? '' : 'd-none' }}" name="contacts[0][other_prefix]" value="{{ old('contacts.0.other_prefix') }}" placeholder="ระบุคำนำหน้า" style="max-width: 180px;">
                                </div>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fs-13 fw-semibold">ชื่อ <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="contacts[0][firstname]" value="{{ old('contacts.0.firstname') }}" placeholder="ชื่อ"
                                    required>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fs-13 fw-semibold">นามสกุล <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="contacts[0][surname]" value="{{ old('contacts.0.surname') }}"
                                    placeholder="นามสกุล" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">หมายเลขโทรศัพท์ <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="contacts[0][phone]" value="{{ old('contacts.0.phone') }}"
                                    placeholder="08X-XXX-XXXX" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">Line ID <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="contacts[0][line_id]" value="{{ old('contacts.0.line_id') }}" placeholder="Line ID"
                                    required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fs-13 fw-semibold">อีเมล</label>
                                <input type="email" class="form-control" name="contacts[0][email]" value="{{ old('contacts.0.email') }}" placeholder="name@email.com">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mt-5 pt-3 border-top">
                    <a href="{{ route('office.partners.index') }}" class="btn btn-premium-light">ยกเลิก</a>
                    <button type="submit" class="btn btn-premium-primary"><i
                            class="ri-save-3-line align-middle me-1"></i> บันทึกข้อมูลคู่ค้า</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        function renumberContacts(containerId) {
            var cards = document.querySelectorAll(containerId + ' .contact-card');
            cards.forEach(function(card, i) {
                var label = card.querySelector('.contact-number');
                if (label) {
                    if (i === 0) {
                        label.innerHTML =
                            '<i class="ri-user-star-line me-1 text-warning"></i> ผู้ติดต่อคนที่ 1 (หลัก)';
                    } else {
                        label.innerHTML = '<i class="ri-user-line me-1 text-primary"></i> ผู้ติดต่อคนที่ ' + (i + 1);
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            var nextContactIndex = document.querySelectorAll('#addContactContainer .contact-card').length;

            document.getElementById('btnAddContact').addEventListener('click', function() {
                var index = nextContactIndex++;
                var container = document.getElementById('addContactContainer');
                var div = document.createElement('div');
                div.className = 'contact-card';
                div.innerHTML =
                    '<div class="contact-card-header">' +
                    '<span class="contact-number"><i class="ri-user-line me-1 text-primary"></i> ผู้ติดต่อคนที่ ' + (index + 1) + '</span>' +
                    '<button type="button" class="btn btn-sm btn-soft-danger rounded-pill px-3 btn-remove-contact" style="font-weight: 600;">' +
                    '<i class="ri-delete-bin-5-line align-middle"></i> ลบ' +
                    '</button>' +
                    '</div>' +
                    '<div class="row g-3">' +
                    '<div class="col-md-12">' +
                        '<label class="form-label fs-13 fw-semibold">คำนำหน้า <span class="text-danger">*</span></label>' +
                        '<div class="d-flex flex-wrap align-items-center gap-4 mt-1">' +
                            '<div class="form-check form-check-inline me-0">' +
                                '<input class="form-check-input prefix-radio" type="radio" name="contacts[' + index + '][prefix]" value="นาย" checked>' +
                                '<label class="form-check-label text-dark">นาย</label>' +
                            '</div>' +
                            '<div class="form-check form-check-inline me-0">' +
                                '<input class="form-check-input prefix-radio" type="radio" name="contacts[' + index + '][prefix]" value="นาง">' +
                                '<label class="form-check-label text-dark">นาง</label>' +
                            '</div>' +
                            '<div class="form-check form-check-inline me-0">' +
                                '<input class="form-check-input prefix-radio" type="radio" name="contacts[' + index + '][prefix]" value="นางสาว">' +
                                '<label class="form-check-label text-dark">นางสาว</label>' +
                            '</div>' +
                            '<div class="form-check form-check-inline me-0">' +
                                '<input class="form-check-input prefix-radio" type="radio" name="contacts[' + index + '][prefix]" value="อื่นๆ">' +
                                '<label class="form-check-label text-dark">อื่นๆ</label>' +
                            '</div>' +
                            '<input type="text" class="prefix-other-text d-none" name="contacts[' + index + '][other_prefix]" placeholder="ระบุคำนำหน้า" style="max-width: 180px;">' +
                        '</div>' +
                    '</div>' +
                    '<div class="col-md-5">' +
                    '<label class="form-label fs-13 fw-semibold">ชื่อ</label>' +
                    '<input type="text" class="form-control" name="contacts[' + index + '][firstname]" placeholder="ชื่อ">' +
                    '</div>' +
                    '<div class="col-md-5">' +
                    '<label class="form-label fs-13 fw-semibold">นามสกุล</label>' +
                    '<input type="text" class="form-control" name="contacts[' + index + '][surname]" placeholder="นามสกุล">' +
                    '</div>' +
                    '<div class="col-md-4">' +
                    '<label class="form-label fs-13 fw-semibold">หมายเลขโทรศัพท์</label>' +
                    '<input type="text" class="form-control" name="contacts[' + index + '][phone]" placeholder="08X-XXX-XXXX">' +
                    '</div>' +
                    '<div class="col-md-4">' +
                    '<label class="form-label fs-13 fw-semibold">Line ID</label>' +
                    '<input type="text" class="form-control" name="contacts[' + index + '][line_id]" placeholder="Line ID">' +
                    '</div>' +
                    '<div class="col-md-4">' +
                    '<label class="form-label fs-13 fw-semibold">อีเมล</label>' +
                    '<input type="email" class="form-control" name="contacts[' + index + '][email]" placeholder="name@email.com">' +
                    '</div>' +
                    '</div>';
                container.appendChild(div);
                renumberContacts('#addContactContainer');
            });

            document.getElementById('addContactContainer').addEventListener('click', function(e) {
                var btn = e.target.closest('.btn-remove-contact');
                if (btn) {
                    var card = btn.closest('.contact-card');
                    if (card) {
                        card.remove();
                        renumberContacts('#addContactContainer');
                    }
                }
            });

            document.getElementById('addContactContainer').addEventListener('change', function(e) {
                if (e.target.classList.contains('prefix-radio')) {
                    var container = e.target.closest('.d-flex');
                    var inputOther = container.querySelector('.prefix-other-text');
                    if (e.target.value === 'อื่นๆ') {
                        inputOther.classList.remove('d-none');
                        inputOther.required = true;
                        inputOther.focus();
                    } else {
                        inputOther.classList.add('d-none');
                        inputOther.required = false;
                        inputOther.value = '';
                    }
                }
            });

            document.addEventListener('click', function(e) {
                var typeCard = e.target.closest('.type-card');
                if (typeCard) {
                    var typeContainer = typeCard.closest('.d-flex') || typeCard.parentElement;
                    typeContainer.querySelectorAll('.type-card').forEach(function(c) {
                        c.classList.remove('active');
                        var r = c.querySelector('.type-card-radio');
                        if (r) r.checked = false;
                    });
                    typeCard.classList.add('active');
                    var typeRadio = typeCard.querySelector('.type-card-radio');
                    if (typeRadio) typeRadio.checked = true;
                }

                var radioCard = e.target.closest('.radio-card');
                if (radioCard) {
                    var roleContainer = radioCard.closest('.row');
                    roleContainer.querySelectorAll('.radio-card').forEach(function(c) {
                        c.classList.remove('active');
                        var r = c.querySelector('.radio-card-input');
                        if (r) r.checked = false;
                    });
                    radioCard.classList.add('active');
                    var roleRadio = radioCard.querySelector('.radio-card-input');
                    if (roleRadio) roleRadio.checked = true;
                }
            });
        });
    </script>
@endsection
