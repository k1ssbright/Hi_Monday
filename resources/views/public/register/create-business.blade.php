<!doctype html>
<html lang="th">

<head>
    <meta charset="utf-8" />
    <title>ลงทะเบียนธุรกิจ | Hi Monday</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Hi Monday Registration Portal" name="description" />
    <meta content="Hi Monday Enterprise" name="author" />
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    @include('office.import.style')
    <link
        href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600;700&family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/font.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/web.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .register-container .custom-input.is-valid,
        .register-body .custom-input.is-valid {
            border-color: #198754 !important;
            background-image: none !important;
        }

        .register-container .custom-input.is-valid:focus,
        .register-body .custom-input.is-valid:focus {
            border-color: #198754 !important;
            box-shadow: 0 0 0 3.5px rgba(25, 135, 84, 0.15) !important;
        }

        .register-container .custom-input.is-invalid,
        .register-body .custom-input.is-invalid {
            border-color: #dc3545 !important;
            background-image: none !important;
        }

        .register-container .custom-input.is-invalid:focus,
        .register-body .custom-input.is-invalid:focus {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 3.5px rgba(220, 53, 69, 0.15) !important;
        }

        /* Flexbox Radio Button Alignment & Theme Color Fix */
        .register-container .form-check,
        .register-body .form-check {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding-left: 0 !important;
            margin-bottom: 0;
            cursor: pointer;
        }

        .register-container .form-check .form-check-input,
        .register-body .form-check .form-check-input {
            float: none !important;
            margin-left: 0 !important;
            margin-top: 0 !important;
            width: 18px !important;
            height: 18px !important;
            cursor: pointer;
            flex-shrink: 0;
            border: 1px solid #CBD5E1;
            background-color: #FFFFFF;
            transition: all 0.15s ease;
        }

        .register-container .form-check .form-check-input:checked,
        .register-body .form-check .form-check-input:checked {
            background-color: #2F53B4 !important;
            border-color: #2F53B4 !important;
        }

        .register-container .form-check .form-check-input:focus,
        .register-body .form-check .form-check-input:focus {
            border-color: #2F53B4 !important;
            box-shadow: 0 0 0 3.5px rgba(47, 83, 180, 0.15) !important;
        }

        .register-container .form-check .form-check-label,
        .register-body .form-check .form-check-label {
            margin-bottom: 0 !important;
            line-height: 1.4 !important;
            font-size: 13.5px !important;
            color: #1E293B !important;
            cursor: pointer;
            user-select: none;
        }

        /* Custom Dropdown Combo Component */
        .custom-dropdown-combo {
            position: relative !important;
            width: 100% !important;
            height: 46px !important;
        }

        .custom-dropdown-combo .dropdown-combo-input {
            width: 100% !important;
            height: 46px !important;
            padding-right: 36px !important;
            cursor: pointer !important;
            background-color: #FFFFFF !important;
            text-align: left !important;
        }

        .custom-dropdown-combo .custom-fp-combo-arrow {
            position: absolute !important;
            right: 14px !important;
            top: 50% !important;
            margin-top: -3px !important;
            width: 0 !important;
            height: 0 !important;
            border-color: #2F53B4 transparent transparent transparent !important;
            border-style: solid !important;
            border-width: 6px 5px 0 5px !important;
            pointer-events: none !important;
            transition: transform 0.2s ease !important;
        }

        .custom-dropdown-combo.active .custom-fp-combo-arrow {
            transform: rotate(180deg) !important;
        }

        .custom-dropdown-popup {
            position: absolute !important;
            top: calc(100% + 4px) !important;
            left: 0 !important;
            right: 0 !important;
            background: #FFFFFF !important;
            border: 1px solid #CBD5E1 !important;
            border-radius: 10px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
            z-index: 1050 !important;
            max-height: 220px !important;
            overflow-y: auto !important;
            display: none;
            padding: 6px 0 !important;
            text-align: left !important;
        }

        .custom-dropdown-item {
            padding: 10px 16px !important;
            font-size: 13.5px !important;
            color: #1E293B !important;
            cursor: pointer !important;
            transition: background-color 0.15s ease !important;
            text-align: left !important;
        }

        .custom-dropdown-item:hover {
            background-color: #EFF6FF !important;
            color: #2F53B4 !important;
        }

        .custom-dropdown-item.selected {
            background-color: #DBEAFE !important;
            color: #2F53B4 !important;
            font-weight: 600 !important;
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
            border-color: #2F53B4;
            background-color: #EFF6FF;
        }

        .type-card.active {
            border: 2px solid #2F53B4;
            background-color: #EFF6FF;
        }

        .type-card-radio {
            width: 16px;
            height: 16px;
            accent-color: #2F53B4;
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
            color: #2F53B4;
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

        /* Inline File Input Box */
        .inline-file-box {
            display: flex;
            align-items: center;
            border: 1px solid #CBD5E1;
            border-radius: 10px;
            background-color: #FFFFFF;
            overflow: hidden;
            height: 46px;
        }

        .inline-file-btn {
            background-color: #EFF6FF !important;
            color: #2F53B4 !important;
            font-size: 13.5px !important;
            font-weight: 500 !important;
            padding: 0 18px !important;
            height: 100% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            border-right: 1px solid #CBD5E1 !important;
            white-space: nowrap !important;
            margin-bottom: 0 !important;
            margin-top: 0 !important;
            line-height: 1 !important;
            box-sizing: border-box !important;
        }

        .inline-file-name {
            font-size: 13.5px !important;
            color: #64748B !important;
            padding: 0 16px !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            display: flex !important;
            align-items: center !important;
            height: 100% !important;
            margin-bottom: 0 !important;
            line-height: 1 !important;
        }

        /* Dropzone Upload */
        .upload-dropzone {
            border: 2px dashed #CBD5E1;
            border-radius: 12px;
            padding: 24px 16px;
            text-align: center;
            background-color: #F8FAFC;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .upload-dropzone:hover {
            border-color: #2F53B4;
            background-color: #EFF6FF;
        }

        .upload-icon {
            font-size: 26px;
            color: #2F53B4;
            margin-bottom: 6px;
        }

        .upload-text {
            font-size: 13px;
            font-weight: 500;
            color: #2F53B4;
        }

        .upload-file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        /* External Premium File Preview Card (Matching Screenshot) */
        .file-preview-card {
            background-color: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            padding: 12px 16px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }

        .file-preview-card:hover {
            border-color: #CBD5E1;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
        }

        .file-preview-thumb {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #E2E8F0;
            flex-shrink: 0;
            background-color: #F8FAFC;
        }

        .file-preview-icon {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            border: 1px solid #FCA5A5;
            background-color: #FEF2F2;
            color: #DC2626;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .file-preview-name {
            font-size: 14px;
            font-weight: 600;
            color: #1E293B;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .file-preview-size {
            font-size: 12px;
            color: #64748B;
        }

        .file-preview-remove {
            background: transparent;
            border: none;
            color: #EF4444;
            font-size: 20px;
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }

        .file-preview-remove:hover {
            background-color: #FEF2F2;
            color: #DC2626;
        }

        .sub-form-card {
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 20px 24px;
            transition: all 0.2s ease;
        }

        .sub-form-card:hover {
            border-color: #CBD5E1;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
        }
    </style>
</head>

<body class="register-body">

    <!-- Top Blue Banner Header -->
    <div class="register-banner">
        <div class="container text-center">
            <h1 class="banner-title">ลงทะเบียนผู้ใช้งานระบบ</h1>
            <p class="banner-subtitle">
                สร้างบัญชีผู้ใช้งานสำหรับเข้าใช้งานระบบ Hi Monday กรอกข้อมูลให้ครบถ้วนเพื่อเริ่มต้นใช้งาน
            </p>
        </div>
    </div>

    <!-- Main Container -->
    <main class="register-container">
        <div class="d-none">Register Business | Hi Monday</div>

        <div class="form-card">
            <form action="{{ route('register.store') }}" method="POST" enctype="multipart/form-data"
                id="createBusinessForm">
                @csrf

                <!-- Hidden Step 1 Data Fields -->
                @foreach (['prefix', 'firstname', 'surname', 'tax_no', 'birthday', 'email', 'phone', 'address', 'province_id', 'amphure_id', 'tambon_id', 'password', 'password_confirmation'] as $field)
                    <input type="hidden" name="{{ $field }}"
                        value="{{ old($field, session("register_step1.{$field}")) }}">
                @endforeach

                @if ($errors->any())
                    <div class="error-alert-block mb-4">
                        <div class="error-alert-title mb-2">
                            <i class="ri-error-warning-fill" style="font-size: 18px; color: var(--oc-danger);"></i>
                            กรุณาตรวจสอบความถูกต้องของข้อมูลอีกครั้ง
                        </div>
                        <ul class="mb-0 ps-3" style="font-size: 13px; color: var(--oc-danger);">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Section 1: ประเภทและสถานะทางภาษี -->
                <div class="form-section">
                    <h2 class="section-heading">
                        <i class="ri-building-4-line"></i>
                        ประเภทและสถานะทางภาษี
                    </h2>

                    <div class="row g-3">
                        <!-- ประเภทธุรกิจ Custom Dropdown Combo Component -->
                        <div class="col-md-4 col-12 form-group-custom">
                            <label class="form-label-custom">
                                ประเภทธุรกิจ <span class="required-star">*</span>
                            </label>
                            <div class="custom-fp-combo custom-dropdown-combo">
                                <input type="text"
                                    class="custom-input dropdown-combo-input @error('business_type') is-invalid @enderror"
                                    id="business_type_display" placeholder="เลือกประเภทธุรกิจ"
                                    value="{{ old('business_type') == '2' ? 'ห้างหุ้นส่วน' : (old('business_type') == '3' ? 'บุคคลธรรมดา' : 'บริษัท') }}"
                                    readonly autocomplete="off">
                                <b class="custom-fp-combo-arrow"></b>
                                <div class="custom-dropdown-popup">
                                    <div class="custom-dropdown-item {{ old('business_type', '1') == '1' ? 'selected' : '' }}"
                                        data-val="1">บริษัท</div>
                                    <div class="custom-dropdown-item {{ old('business_type') == '2' ? 'selected' : '' }}"
                                        data-val="2">ห้างหุ้นส่วน</div>
                                    <div class="custom-dropdown-item {{ old('business_type') == '3' ? 'selected' : '' }}"
                                        data-val="3">บุคคลธรรมดา</div>
                                </div>
                                <input type="hidden" name="business_type" id="business_type_select"
                                    value="{{ old('business_type', '1') }}">
                            </div>
                            @error('business_type')
                                <div class="field-error-msg">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- เลขประจำตัวผู้เสียภาษี -->
                        <div class="col-md-4 col-12 form-group-custom">
                            <label class="form-label-custom">
                                เลขประจำตัวผู้เสียภาษี
                            </label>
                            <input type="text" name="business_tax_no" id="business_tax_no" maxlength="13"
                                class="custom-input @error('business_tax_no') is-invalid @enderror"
                                value="{{ old('business_tax_no', session('register_step1.tax_no')) }}"
                                placeholder="เช่น 0105558123456">
                            @error('business_tax_no')
                                <div class="field-error-msg">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- สถานะจดภาษีมูลค่าเพิ่ม -->
                        <div class="col-md-4 col-12 form-group-custom">
                            <label class="form-label-custom">
                                สถานะจดภาษีมูลค่าเพิ่ม <span class="required-star">*</span>
                            </label>
                            <div class="d-flex flex-column gap-2 mt-1">
                                <div class="form-check form-check-inline me-0">
                                    <input class="form-check-input" type="radio" name="business_vat_status"
                                        id="vat_1" value="1"
                                        {{ old('business_vat_status', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label text-main" for="vat_1">
                                        จดภาษีมูลค่าเพิ่ม</label>
                                </div>
                                <div class="form-check form-check-inline me-0">
                                    <input class="form-check-input" type="radio" name="business_vat_status"
                                        id="vat_0" value="0"
                                        {{ old('business_vat_status') == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label text-main" for="vat_0">
                                        ยังไม่เข้าระบบภาษีมูลค่าเพิ่ม</label>
                                </div>
                            </div>
                            @error('business_vat_status')
                                <div class="field-error-msg">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- 3 Business Type Selector Cards -->
                        <div class="col-12 mt-2">
                            <div class="row g-3">
                                <div class="col-md-4 col-12">
                                    <div class="type-card {{ old('business_type', '1') == '1' ? 'active' : '' }}"
                                        data-val="1">
                                        <input type="radio" name="type_card_radio" class="type-card-radio"
                                            {{ old('business_type', '1') == '1' ? 'checked' : '' }}>
                                        <div class="type-card-icon">
                                            <i class="ri-building-line"></i>
                                        </div>
                                        <div>
                                            <div class="type-card-title">บริษัท</div>
                                            <div class="type-card-sub">นิติบุคคลประเภทบริษัท</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="type-card {{ old('business_type') == '2' ? 'active' : '' }}"
                                        data-val="2">
                                        <input type="radio" name="type_card_radio" class="type-card-radio"
                                            {{ old('business_type') == '2' ? 'checked' : '' }}>
                                        <div class="type-card-icon">
                                            <i class="ri-group-line"></i>
                                        </div>
                                        <div>
                                            <div class="type-card-title">ห้างหุ้นส่วน</div>
                                            <div class="type-card-sub">นิติบุคคลประเภทห้างหุ้นส่วน</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-12">
                                    <div class="type-card {{ old('business_type') == '3' ? 'active' : '' }}"
                                        data-val="3">
                                        <input type="radio" name="type_card_radio" class="type-card-radio"
                                            {{ old('business_type') == '3' ? 'checked' : '' }}>
                                        <div class="type-card-icon">
                                            <i class="ri-user-line"></i>
                                        </div>
                                        <div>
                                            <div class="type-card-title">บุคคลธรรมดา</div>
                                            <div class="type-card-sub">บุคคลธรรมดาประกอบธุรกิจ</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ชื่อออกเอกสารภาษี (Conditional Display: บุคคลธรรมดา Only) -->
                        @php
                            $step1Prefix = old('prefix', session('register_step1.prefix', ''));
                            $step1Firstname = old('firstname', session('register_step1.firstname', ''));
                            $step1Surname = old('surname', session('register_step1.surname', ''));
                            $step1OwnerFullName = trim($step1Prefix . ' ' . $step1Firstname . ' ' . $step1Surname);
                        @endphp
                        <div class="col-12 form-group-custom mt-2 d-none" id="tax_name_option_container">
                            <div class="sub-form-card">
                                <label class="form-label-custom">
                                    ชื่อที่ออกเอกสารเกี่ยวกับภาษี <span class="required-star">*</span>
                                </label>
                                <div class="d-flex flex-wrap align-items-center gap-4 mt-2">
                                    <div class="form-check form-check-inline me-0">
                                        <input class="form-check-input" type="radio" name="tax_name_type"
                                            id="tax_name_owner" value="owner"
                                            {{ old('tax_name_type', 'owner') == 'owner' ? 'checked' : '' }}>
                                        <label class="form-check-label text-main" for="tax_name_owner">
                                            ชื่อเจ้าของธุรกิจ
                                            @if ($step1OwnerFullName)
                                                <span class="text-muted fw-normal">({{ $step1OwnerFullName }})</span>
                                            @endif
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline me-0">
                                        <input class="form-check-input" type="radio" name="tax_name_type"
                                            id="tax_name_custom" value="custom"
                                            {{ old('tax_name_type') == 'custom' ? 'checked' : '' }}>
                                        <label class="form-check-label text-main"
                                            for="tax_name_custom">ชื่ออื่น</label>
                                    </div>
                                </div>

                                <!-- Custom Tax Name Fields (คำนำหน้า radio, ชื่อ, นามสกุล) -->
                                <div class="mt-3 pt-3 border-top d-none" id="custom_tax_name_fields">
                                    <div class="row g-3">
                                        <!-- คำนำหน้า (Radio Buttons matching Index Page) -->
                                        <div class="col-12 form-group-custom">
                                            <label class="form-label-custom">
                                                คำนำหน้า <span class="required-star">*</span>
                                            </label>
                                            <div class="d-flex flex-wrap align-items-center gap-4 mt-1">
                                                <div class="form-check form-check-inline me-0">
                                                    <input class="form-check-input tax-prefix-radio" type="radio"
                                                        name="tax_prefix_radio" id="tax_prefix_mr" value="นาย"
                                                        {{ old('tax_prefix', 'นาย') == 'นาย' ? 'checked' : '' }}>
                                                    <label class="form-check-label text-main"
                                                        for="tax_prefix_mr">นาย</label>
                                                </div>
                                                <div class="form-check form-check-inline me-0">
                                                    <input class="form-check-input tax-prefix-radio" type="radio"
                                                        name="tax_prefix_radio" id="tax_prefix_mrs" value="นาง"
                                                        {{ old('tax_prefix') == 'นาง' ? 'checked' : '' }}>
                                                    <label class="form-check-label text-main"
                                                        for="tax_prefix_mrs">นาง</label>
                                                </div>
                                                <div class="form-check form-check-inline me-0">
                                                    <input class="form-check-input tax-prefix-radio" type="radio"
                                                        name="tax_prefix_radio" id="tax_prefix_ms" value="นางสาว"
                                                        {{ old('tax_prefix') == 'นางสาว' ? 'checked' : '' }}>
                                                    <label class="form-check-label text-main"
                                                        for="tax_prefix_ms">นางสาว</label>
                                                </div>
                                                <div class="form-check form-check-inline me-0">
                                                    <input class="form-check-input tax-prefix-radio" type="radio"
                                                        name="tax_prefix_radio" id="tax_prefix_other" value="อื่นๆ"
                                                        {{ (old('tax_prefix') && !in_array(old('tax_prefix'), ['นาย', 'นาง', 'นางสาว'])) || old('tax_prefix') == 'อื่นๆ' ? 'checked' : '' }}>
                                                    <label class="form-check-label text-main"
                                                        for="tax_prefix_other">อื่นๆ</label>
                                                </div>
                                                <input type="text" id="tax_prefix_other_text"
                                                    class="custom-input prefix-other-text" placeholder="กรอกคำนำหน้า"
                                                    value="{{ !in_array(old('tax_prefix'), ['นาย', 'นาง', 'นางสาว']) ? old('tax_prefix') : '' }}"
                                                    style="max-width: 180px; height: 38px; font-size: 13px;">
                                            </div>
                                            <input type="hidden" name="tax_prefix" id="tax_prefix_final_input"
                                                value="{{ old('tax_prefix', 'นาย') }}">
                                        </div>

                                        <!-- ชื่อ -->
                                        <div class="col-md-6 col-12 form-group-custom">
                                            <label class="form-label-custom">
                                                ชื่อ <span class="required-star">*</span>
                                            </label>
                                            <input type="text" name="tax_firstname" id="tax_firstname"
                                                class="custom-input" value="{{ old('tax_firstname') }}"
                                                placeholder="กรอกชื่อจริง">
                                        </div>

                                        <!-- นามสกุล -->
                                        <div class="col-md-6 col-12 form-group-custom">
                                            <label class="form-label-custom">
                                                นามสกุล <span class="required-star">*</span>
                                            </label>
                                            <input type="text" name="tax_surname" id="tax_surname"
                                                class="custom-input" value="{{ old('tax_surname') }}"
                                                placeholder="กรอกนามสกุล">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ชื่อธุรกิจ -->
                        <div class="col-md-6 col-12 form-group-custom">
                            <label class="form-label-custom">
                                ชื่อธุรกิจ <span class="required-star">*</span>
                            </label>
                            <input type="text" name="business_name_th"
                                class="custom-input @error('business_name_th') is-invalid @enderror"
                                value="{{ old('business_name_th') }}" placeholder="ชื่อธุรกิจ" required>
                            @error('business_name_th')
                                <div class="field-error-msg">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- รหัสธุรกิจ -->
                        <div class="col-md-6 col-12 form-group-custom">
                            <label class="form-label-custom">
                                รหัสธุรกิจ
                            </label>
                            <input type="text" name="business_code"
                                class="custom-input @error('business_code') is-invalid @enderror"
                                value="{{ old('business_code') }}" placeholder="รหัสธุรกิจ">
                        </div>

                        <!-- ที่อยู่ -->
                        <div class="col-md-6 col-12 form-group-custom">
                            <label class="form-label-custom">
                                ที่อยู่ <span class="required-star">*</span>
                            </label>
                            <input type="text" name="business_address"
                                class="custom-input @error('business_address') is-invalid @enderror"
                                value="{{ old('business_address', session('register_step1.address')) }}"
                                placeholder="ที่อยู่" required>
                            @error('business_address')
                                <div class="field-error-msg">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- เอกสาร ภ.พ.20 (Conditional Display: VAT Registered Only) -->
                        <div class="col-md-6 col-12 form-group-custom" id="pp20_field_container">
                            <label class="form-label-custom">
                                เอกสาร ภ.พ.20
                            </label>
                            <div class="inline-file-box">
                                <label for="pp20_file" class="inline-file-btn">เลือกไฟล์</label>
                                <span class="inline-file-name" id="pp20_file_name">ยังไม่ได้เลือกไฟล์</span>
                                <input type="file" name="pp20_file" id="pp20_file" class="d-none"
                                    accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                            <div class="field-helper" style="font-size: 11px;">รองรับไฟล์ pdf, jpg, png (ขนาดไม่เกิน
                                5MB)</div>

                            <!-- External Preview Card (Matching Screenshot) -->
                            <div class="file-preview-card d-none" id="pp20_preview_card">
                                <div class="d-flex align-items-center gap-3 overflow-hidden me-2">
                                    <img id="pp20_img_preview" class="file-preview-thumb d-none">
                                    <div id="pp20_pdf_icon" class="file-preview-icon d-none">
                                        <i class="ri-file-pdf-fill"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div id="pp20_preview_filename" class="file-preview-name">filename.png</div>
                                        <div id="pp20_preview_filesize" class="file-preview-size">0 KB</div>
                                    </div>
                                </div>
                                <button type="button" class="file-preview-remove" id="btn_remove_pp20"
                                    title="ลบไฟล์">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ข้อมูลสาขา -->
                        <div class="col-12 form-group-custom mt-2">
                            <label class="form-label-custom">
                                ข้อมูลสาขา <span class="required-star">*</span>
                            </label>
                            <div class="d-flex align-items-center gap-4 mt-2">
                                <div class="form-check form-check-inline me-0">
                                    <input class="form-check-input" type="radio" name="is_headquarter"
                                        id="is_hq_0" value="0"
                                        {{ old('is_headquarter', '0') == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label text-main" for="is_hq_0">สำนักงานใหญ่</label>
                                </div>
                                <div class="form-check form-check-inline me-0">
                                    <input class="form-check-input" type="radio" name="is_headquarter"
                                        id="is_hq_1" value="1"
                                        {{ old('is_headquarter') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label text-main" for="is_hq_1">สาขา</label>
                                </div>
                            </div>
                        </div>

                        <!-- Branch Fields (Conditional Display: Branch Only) -->
                        <div class="col-12 w-100 d-none" id="branch_fields_container">
                            <div class="row g-3">
                                <div class="col-md-6 col-12 form-group-custom">
                                    <label class="form-label-custom">
                                        รหัสสาขา <span class="required-star">*</span>
                                    </label>
                                    <input type="text" name="business_branch_no" id="business_branch_no"
                                        class="custom-input" value="{{ old('business_branch_no', '00000') }}"
                                        placeholder="รหัสสาขา">
                                </div>
                                <div class="col-md-6 col-12 form-group-custom">
                                    <label class="form-label-custom">
                                        ชื่อสาขา <span class="required-star">*</span>
                                    </label>
                                    <input type="text" name="business_branch_name_th" id="business_branch_name_th"
                                        class="custom-input"
                                        value="{{ old('business_branch_name_th', 'สำนักงานใหญ่') }}"
                                        placeholder="ชื่อสาขา">
                                </div>
                            </div>
                        </div>

                        <!-- Corporate English Fields (Conditional Display: Company / Partnership Only) -->
                        <div class="col-12 w-100" id="corporate_english_fields_container">
                            <div class="row g-3">
                                <div class="col-md-6 col-12 form-group-custom">
                                    <label class="form-label-custom">
                                        ชื่อธุรกิจ
                                    </label>
                                    <input type="text" name="business_name_en" class="custom-input"
                                        value="{{ old('business_name_en') }}" placeholder="ชื่อธุรกิจ">
                                </div>

                                <div class="col-md-6 col-12 form-group-custom">
                                    <label class="form-label-custom">
                                        ที่อยู่
                                    </label>
                                    <input type="text" name="address_line1_en" class="custom-input"
                                        value="{{ old('address_line1_en') }}" placeholder="ที่อยู่ เช่น เลขที่">
                                </div>
                            </div>
                        </div>

                        <!-- ข้อมูลที่อยู่ (ตำบล/แขวง, อำเภอ/เขต, จังหวัด, รหัสไปรษณีย์) Auto-Complete -->
                        <div class="col-12 p-0 m-0">
                            @include('partials.thailand-address-autocomplete', [
                                'provinceName' => 'business_province_id',
                                'amphureName' => 'business_amphure_id',
                                'tambonName' => 'business_tambon_id',
                                'zipName' => 'business_zip_code',
                                'selectedProvinceId' => $selectedProvinceId ?? '',
                                'selectedAmphureId' => $selectedAmphureId ?? '',
                                'selectedTambonId' => $selectedTambonId ?? '',
                                'selectedProvinceName' => $selectedProvinceName ?? '',
                                'selectedAmphureName' => $selectedAmphureName ?? '',
                                'selectedTambonName' => $selectedTambonName ?? '',
                                'selectedZipCode' => $selectedZipCode ?? '',
                                'idPrefix' => 'business',
                                'variant' => 'public',
                                'required' => true,
                                'rowClass' => 'row g-3 m-0 p-0',
                            ])
                        </div>
                    </div>
                </div>

                <!-- Section 2: Accounting Financial Year -->
                <div class="form-section mt-4">
                    <h2 class="section-heading">
                        <i class="ri-calendar-event-line"></i>
                        Accounting Financial Year (เดือน) <span class="required-star">*</span>
                    </h2>

                    <div class="row g-3">
                        <div class="col-6 form-group-custom">
                            @php
                                $months = [
                                    1 => '1 (มกราคม)',
                                    2 => '2 (กุมภาพันธ์)',
                                    3 => '3 (มีนาคม)',
                                    4 => '4 (เมษายน)',
                                    5 => '5 (พฤษภาคม)',
                                    6 => '6 (มิถุนายน)',
                                    7 => '7 (กรกฎาคม)',
                                    8 => '8 (สิงหาคม)',
                                    9 => '9 (กันยายน)',
                                    10 => '10 (ตุลาคม)',
                                    11 => '11 (พฤศจิกายน)',
                                    12 => '12 (ธันวาคม)',
                                ];
                                $selectedMonthNum = old('business_account_finance_year', (int) date('n'));
                                $selectedMonthText =
                                    $selectedMonthNum && isset($months[$selectedMonthNum])
                                        ? $months[$selectedMonthNum]
                                        : '';
                            @endphp
                            <div class="custom-fp-combo custom-dropdown-combo" style="max-width: 480px;">
                                <input type="text"
                                    class="custom-input dropdown-combo-input @error('business_account_finance_year') is-invalid @enderror"
                                    id="finance_year_display" placeholder="เลือกเดือน"
                                    value="{{ $selectedMonthText }}" readonly autocomplete="off">
                                <b class="custom-fp-combo-arrow"></b>
                                <div class="custom-dropdown-popup">
                                    @foreach ($months as $num => $monthName)
                                        <div class="custom-dropdown-item {{ $selectedMonthNum == $num ? 'selected' : '' }}"
                                            data-val="{{ $num }}">{{ $monthName }}</div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="business_account_finance_year"
                                    id="business_account_finance_year" value="{{ $selectedMonthNum }}">
                            </div>
                            <div class="field-helper" style="font-size: 11px;">เดือนที่ใช้เริ่มต้นรอบบัญชีขององค์กร
                            </div>
                            @error('business_account_finance_year')
                                <div class="field-error-msg">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 3: ข้อมูลการติดต่อ -->
                <div class="form-section mt-4">
                    <h2 class="section-heading">
                        <i class="ri-phone-line"></i>
                        ข้อมูลการติดต่อ
                    </h2>

                    <div class="row g-3">
                        <div class="col-md-4 col-12 form-group-custom">
                            <label class="form-label-custom">เบอร์โทรสำนักงาน</label>
                            <input type="text" name="office_phone" class="custom-input"
                                value="{{ old('office_phone') }}" placeholder="เบอร์โทรสำนักงาน">
                        </div>

                        <div class="col-md-4 col-12 form-group-custom">
                            <label class="form-label-custom">เบอร์โทรศัพท์มือถือ</label>
                            <input type="text" name="mobile_phone" class="custom-input"
                                value="{{ old('mobile_phone', session('register_step1.phone')) }}"
                                placeholder="เบอร์โทรศัพท์มือถือ">
                        </div>

                        <div class="col-md-4 col-12 form-group-custom">
                            <label class="form-label-custom">โทรสาร (Fax)</label>
                            <input type="text" name="fax" class="custom-input" value="{{ old('fax') }}"
                                placeholder="เบอร์โทรสาร">
                        </div>

                        <div class="col-12 form-group-custom">
                            <label class="form-label-custom">เว็บไซต์</label>
                            <input type="text" name="website" class="custom-input" value="{{ old('website') }}"
                                placeholder="https://www.example.com">
                        </div>
                    </div>
                </div>

                <!-- Section 4: เอกสารและสื่อของธุรกิจ -->
                <div class="form-section mt-4">
                    <h2 class="section-heading">
                        <i class="ri-image-line"></i>
                        เอกสารและสื่อของธุรกิจ
                    </h2>

                    <div class="row g-3">
                        <!-- โลโก้บริษัท -->
                        <div class="col-md-6 col-12 form-group-custom">
                            <label class="form-label-custom">โลโก้บริษัท</label>
                            <div class="upload-dropzone" onclick="document.getElementById('logo_file').click();">
                                <i class="ri-upload-cloud-line upload-icon"></i>
                                <div class="upload-text" id="logo_text">คลิกเพื่ออัปโหลด</div>
                                <input type="file" name="logo_file" id="logo_file" class="upload-file-input"
                                    accept="image/*">
                            </div>

                            <!-- External Preview Card (Matching Screenshot) -->
                            <div class="file-preview-card d-none" id="logo_preview_card">
                                <div class="d-flex align-items-center gap-3 overflow-hidden me-2">
                                    <img id="logo_img_preview" class="file-preview-thumb">
                                    <div class="overflow-hidden">
                                        <div id="logo_preview_filename" class="file-preview-name">logo.png</div>
                                        <div id="logo_preview_filesize" class="file-preview-size">0 KB</div>
                                    </div>
                                </div>
                                <button type="button" class="file-preview-remove" id="btn_remove_logo"
                                    title="ลบไฟล์">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ตรายางอิเล็กทรอนิกส์ -->
                        <div class="col-md-6 col-12 form-group-custom">
                            <label class="form-label-custom">ตรายางอิเล็กทรอนิกส์</label>
                            <div class="upload-dropzone" onclick="document.getElementById('stamp_file').click();">
                                <i class="ri-upload-cloud-line upload-icon"></i>
                                <div class="upload-text" id="stamp_text">คลิกเพื่ออัปโหลด</div>
                                <input type="file" name="stamp_file" id="stamp_file" class="upload-file-input"
                                    accept="image/*">
                            </div>

                            <!-- External Preview Card (Matching Screenshot) -->
                            <div class="file-preview-card d-none" id="stamp_preview_card">
                                <div class="d-flex align-items-center gap-3 overflow-hidden me-2">
                                    <img id="stamp_img_preview" class="file-preview-thumb">
                                    <div class="overflow-hidden">
                                        <div id="stamp_preview_filename" class="file-preview-name">stamp.png</div>
                                        <div id="stamp_preview_filesize" class="file-preview-size">0 KB</div>
                                    </div>
                                </div>
                                <button type="button" class="file-preview-remove" id="btn_remove_stamp"
                                    title="ลบไฟล์">
                                    <i class="ri-close-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section mt-4">
                    <h2 class="section-heading">
                        <i class="ri-shield-check-line"></i>
                        การยอมรับเงื่อนไข
                    </h2>

                    <div class="d-flex flex-column gap-3">
                        <div class="form-check @error('accept_terms_privacy') is-invalid @enderror">
                            <input class="form-check-input register-consent-checkbox" type="checkbox"
                                name="accept_terms_privacy" id="accept_terms_privacy" value="1"
                                @checked(old('accept_terms_privacy'))>
                            <label class="form-check-label" for="accept_terms_privacy">
                                ข้าพเจ้ายอมรับ <strong>ข้อกำหนดการใช้บริการ</strong> และ
                                <strong>นโยบายความเป็นส่วนตัว</strong>
                                <span class="required-star">*</span>
                            </label>
                            @error('accept_terms_privacy')
                                <div class="field-error-msg mt-1">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-check @error('accept_data_accuracy') is-invalid @enderror">
                            <input class="form-check-input register-consent-checkbox" type="checkbox"
                                name="accept_data_accuracy" id="accept_data_accuracy" value="1"
                                @checked(old('accept_data_accuracy'))>
                            <label class="form-check-label" for="accept_data_accuracy">
                                ข้าพเจ้ายืนยันว่าข้อมูลส่วนบุคคล ข้อมูลธุรกิจ และข้อมูลภาษีที่กรอก
                                <strong>เป็นความจริง ถูกต้อง</strong> และข้าพเจ้ามี
                                <strong>อำนาจในการลงทะเบียน</strong>ในนามของธุรกิจนี้
                                <span class="required-star">*</span>
                            </label>
                            @error('accept_data_accuracy')
                                <div class="field-error-msg mt-1">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-check @error('accept_uploaded_media_use') is-invalid @enderror">
                            <input class="form-check-input register-consent-checkbox" type="checkbox"
                                name="accept_uploaded_media_use" id="accept_uploaded_media_use" value="1"
                                @checked(old('accept_uploaded_media_use'))>
                            <label class="form-check-label" for="accept_uploaded_media_use">
                                ข้าพเจ้ายินยอมให้ระบบ <strong>จัดเก็บและใช้โลโก้ ตรายางอิเล็กทรอนิกส์ และเอกสาร ภ.พ.20 (ถ้ามี)</strong>
                                เพื่อแสดงบนเอกสารที่ออกจากระบบ เช่น ใบเสนอราคา ใบกำกับภาษี ฯลฯ
                                <span class="required-star">*</span>
                            </label>
                            @error('accept_uploaded_media_use')
                                <div class="field-error-msg mt-1">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-check @error('accept_disclaimer') is-invalid @enderror">
                            <input class="form-check-input register-consent-checkbox" type="checkbox"
                                name="accept_disclaimer" id="accept_disclaimer" value="1"
                                @checked(old('accept_disclaimer'))>
                            <label class="form-check-label" for="accept_disclaimer">
                                ข้าพเจ้ารับทราบว่า Hi Monday เป็น <strong>เครื่องมือช่วยจัดการธุรกิจ/เอกสาร</strong>
                                ไม่ใช่ที่ปรึกษาทางบัญชีหรือภาษี — ผู้ใช้ต้อง
                                <strong>ตรวจสอบความถูกต้องของเอกสารก่อนใช้งานจริง</strong>
                                <span class="required-star">*</span>
                            </label>
                            @error('accept_disclaimer')
                                <div class="field-error-msg mt-1">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Footer Buttons (Matching Screenshot) -->
                <div class="d-flex align-items-center gap-3 mt-4 pt-3">
                    <button type="submit" class="btn-submit-blue flex-grow-1" id="btnSubmitBusiness" disabled>
                        บันทึก
                        <i class="ri-check-line fs-18"></i>
                    </button>

                    <a href="{{ route('register') }}" class="btn-back-white">
                        ย้อนกลับ
                    </a>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer Copyright -->
    <footer class="register-footer">
        <div class="container text-center">
            <p class="copyright-text">
                © {{ date('Y') }} Hi Monday. All rights reserved. Powered by Hi Monday Solution.
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Custom Dropdown Combo Component Click & Selection Handler
            $('.custom-dropdown-combo').each(function() {
                var $combo = $(this);
                var $input = $combo.find('.dropdown-combo-input');
                var $popup = $combo.find('.custom-dropdown-popup');
                var $hidden = $combo.find('input[type="hidden"]');

                $combo.on('click', function(e) {
                    e.stopPropagation();
                    $('.custom-dropdown-combo').not($combo).removeClass('active').find(
                        '.custom-dropdown-popup').hide();
                    $combo.toggleClass('active');
                    $popup.toggle();
                });

                $popup.on('click', '.custom-dropdown-item', function(e) {
                    e.stopPropagation();
                    var val = $(this).data('val');
                    var text = $(this).text().trim();

                    $input.val(text);
                    $hidden.val(val).trigger('change');
                    $popup.find('.custom-dropdown-item').removeClass('selected');
                    $(this).addClass('selected');

                    $combo.removeClass('active');
                    $popup.hide();

                    // Sync 3 Type Cards if business_type changed
                    if ($hidden.attr('id') === 'business_type_select') {
                        $('.type-card').removeClass('active');
                        $('.type-card-radio').prop('checked', false);
                        var $target = $('.type-card[data-val="' + val + '"]');
                        $target.addClass('active');
                        $target.find('.type-card-radio').prop('checked', true);
                    }
                });
            });

            // Type Card Selector Click Handler
            $('.type-card').on('click', function() {
                var val = $(this).data('val');
                $('.type-card').removeClass('active');
                $('.type-card-radio').prop('checked', false);
                $(this).addClass('active');
                $(this).find('.type-card-radio').prop('checked', true);

                var $combo = $('#business_type_select').closest('.custom-dropdown-combo');
                var $item = $combo.find('.custom-dropdown-item[data-val="' + val + '"]');
                if ($item.length) {
                    $combo.find('.dropdown-combo-input').val($item.text().trim());
                    $combo.find('.custom-dropdown-item').removeClass('selected');
                    $item.addClass('selected');
                }
                $('#business_type_select').val(val).trigger('change');
            });

            // Conditional Display Handler
            function updateFormConditionalDisplay() {
                var busType = String($('#business_type_select').val() || '1');
                var vatStatus = $('input[name="business_vat_status"]:checked').val() || '1';
                var hqStatus = $('input[name="is_headquarter"]:checked').val() || '0';

                // 1. VAT Status Conditional Display (ภ.พ.20)
                if (vatStatus === '1') {
                    $('#pp20_field_container').removeClass('d-none');
                } else {
                    $('#pp20_field_container').addClass('d-none');
                }

                // 2. Business Type Conditional Display (Corporate English Info)
                if (busType === '3') { // บุคคลธรรมดา
                    $('#corporate_english_fields_container').addClass('d-none');
                } else { // บริษัท (1) / ห้างหุ้นส่วน (2)
                    $('#corporate_english_fields_container').removeClass('d-none');
                }

                // 3. Headquarter / Branch Conditional Display
                if (hqStatus === '0') { // สำนักงานใหญ่
                    $('#branch_fields_container').addClass('d-none');
                    if (!$('#business_branch_no').data('user-typed')) {
                        $('#business_branch_no').val('00000');
                    }
                    if (!$('#business_branch_name_th').data('user-typed')) {
                        $('#business_branch_name_th').val('สำนักงานใหญ่');
                    }
                } else { // สาขา
                    $('#branch_fields_container').removeClass('d-none');
                    if ($('#business_branch_no').val() === '00000') {
                        $('#business_branch_no').val('');
                    }
                    if ($('#business_branch_name_th').val() === 'สำนักงานใหญ่') {
                        $('#business_branch_name_th').val('');
                    }
                }

                // 4. Tax Name Option Conditional Display (Individual Only)
                updateTaxNameDisplay();
            }

            function updateTaxNameDisplay() {
                var busType = String($('#business_type_select').val() || '1');
                var taxNameType = $('input[name="tax_name_type"]:checked').val() || 'owner';

                if (busType === '3') { // บุคคลธรรมดา
                    $('#tax_name_option_container').removeClass('d-none');
                    if (taxNameType === 'custom') {
                        $('#custom_tax_name_fields').removeClass('d-none');
                    } else {
                        $('#custom_tax_name_fields').addClass('d-none');
                    }
                } else {
                    $('#tax_name_option_container').addClass('d-none');
                    $('#custom_tax_name_fields').addClass('d-none');
                }
            }

            $('input[name="tax_name_type"]').on('change', updateTaxNameDisplay);

            $('.tax-prefix-radio').on('change', function() {
                var val = $(this).val();
                if (val === 'อื่นๆ') {
                    var otherText = $('#tax_prefix_other_text').val().trim();
                    $('#tax_prefix_final_input').val(otherText || 'อื่นๆ');
                } else {
                    $('#tax_prefix_final_input').val(val);
                }
            });

            $('#tax_prefix_other_text').on('input', function() {
                if ($('#tax_prefix_other').is(':checked')) {
                    $('#tax_prefix_final_input').val($(this).val().trim() || 'อื่นๆ');
                }
            });

            // Track user typing in branch fields
            $('#business_branch_no, #business_branch_name_th').on('input', function() {
                $(this).data('user-typed', true);
            });

            // Event Listeners for Conditional Display
            $('#business_type_select').on('change', updateFormConditionalDisplay);
            $('input[name="business_vat_status"]').on('change', updateFormConditionalDisplay);
            $('input[name="is_headquarter"]').on('change', updateFormConditionalDisplay);

            // Initial call on DOM ready
            updateFormConditionalDisplay();

            function formatFileSize(bytes) {
                if (!bytes || bytes === 0) return '0 Bytes';
                var k = 1024;
                var sizes = ['Bytes', 'KB', 'MB', 'GB'];
                var i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // PP20 File Preview Handler (Image / PDF)
            $('#pp20_file').on('change', function() {
                var file = this.files && this.files.length > 0 ? this.files[0] : null;
                if (file) {
                    $('#pp20_file_name').text(file.name);
                    $('#pp20_preview_filename').text(file.name);
                    $('#pp20_preview_filesize').text(formatFileSize(file.size));
                    $('#pp20_preview_card').removeClass('d-none').addClass('d-flex');

                    if (file.type.startsWith('image/')) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $('#pp20_img_preview').attr('src', e.target.result).removeClass('d-none');
                            $('#pp20_pdf_icon').addClass('d-none');
                        };
                        reader.readAsDataURL(file);
                    } else {
                        $('#pp20_img_preview').addClass('d-none');
                        $('#pp20_pdf_icon').removeClass('d-none');
                    }
                } else {
                    resetPp20Preview();
                }
            });

            function resetPp20Preview() {
                $('#pp20_file').val('');
                $('#pp20_file_name').text('ยังไม่ได้เลือกไฟล์');
                $('#pp20_preview_card').addClass('d-none').removeClass('d-flex');
                $('#pp20_img_preview').attr('src', '').addClass('d-none');
                $('#pp20_pdf_icon').addClass('d-none');
            }

            $('#btn_remove_pp20').on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                resetPp20Preview();
            });

            // Logo File Preview Handler
            $('#logo_file').on('change', function() {
                var file = this.files && this.files.length > 0 ? this.files[0] : null;
                if (file && file.type.startsWith('image/')) {
                    $('#logo_text').text('เลือกไฟล์แล้ว: ' + file.name);
                    $('#logo_preview_filename').text(file.name);
                    $('#logo_preview_filesize').text(formatFileSize(file.size));
                    $('#logo_preview_card').removeClass('d-none').addClass('d-flex');

                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#logo_img_preview').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                } else {
                    resetLogoPreview();
                }
            });

            function resetLogoPreview() {
                $('#logo_file').val('');
                $('#logo_text').text('คลิกเพื่ออัปโหลด');
                $('#logo_img_preview').attr('src', '');
                $('#logo_preview_card').addClass('d-none').removeClass('d-flex');
            }

            $('#btn_remove_logo').on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                resetLogoPreview();
            });

            // Stamp File Preview Handler
            $('#stamp_file').on('change', function() {
                var file = this.files && this.files.length > 0 ? this.files[0] : null;
                if (file && file.type.startsWith('image/')) {
                    $('#stamp_text').text('เลือกไฟล์แล้ว: ' + file.name);
                    $('#stamp_preview_filename').text(file.name);
                    $('#stamp_preview_filesize').text(formatFileSize(file.size));
                    $('#stamp_preview_card').removeClass('d-none').addClass('d-flex');

                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#stamp_img_preview').attr('src', e.target.result);
                    };
                    reader.readAsDataURL(file);
                } else {
                    resetStampPreview();
                }
            });

            function resetStampPreview() {
                $('#stamp_file').val('');
                $('#stamp_text').text('คลิกเพื่ออัปโหลด');
                $('#stamp_img_preview').attr('src', '');
                $('#stamp_preview_card').addClass('d-none').removeClass('d-flex');
            }

            $('#btn_remove_stamp').on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                resetStampPreview();
            });

            function syncRegisterConsentSubmit() {
                var allChecked = $('.register-consent-checkbox').length === $('.register-consent-checkbox:checked').length;
                $('#btnSubmitBusiness').prop('disabled', !allChecked);
            }

            $('#createBusinessForm').on('submit', function(event) {
                syncRegisterConsentSubmit();
                if ($('#btnSubmitBusiness').prop('disabled')) {
                    event.preventDefault();
                    alert('กรุณายอมรับเงื่อนไขที่จำเป็นทั้งหมดก่อนบันทึก');
                    return;
                }

                if (!$('#business_account_finance_year').val()) {
                    event.preventDefault();
                    alert('กรุณาเลือกเดือนเริ่มต้นรอบบัญชี');
                    return;
                }

                if (!$('[name="business_tambon_id"]').val()) {
                    event.preventDefault();
                    alert('กรุณาเลือกตำบล/อำเภอ/จังหวัดของธุรกิจจากรายการที่แสดง');
                }
            });

            $('.register-consent-checkbox').on('change', syncRegisterConsentSubmit);
            syncRegisterConsentSubmit();
        });
    </script>

    @include('partials.thailand-address-autocomplete-script')
</body>

</html>
