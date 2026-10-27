@extends('office.layout.office')
@section('title', ($lead->exists ? 'แก้ไข Lead' : 'สร้าง Lead').' | Hi Monday')

@section('style')
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        /* Card Styles */
        .emp-card-main {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
            border: 1px solid #e2e8f0;
            padding: 24px;
            margin-bottom: 24px;
        }

        .section-header-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .section-header-title i {
            color: #2F53B4;
            font-size: 1.25rem;
        }

        /* Form Labels & Inputs */
        .filter-label-emp {
            font-size: 0.85rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 7px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-input-emp {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px;
            height: 42px !important;
            padding: 0 14px;
            font-size: 0.875rem;
            color: #0f172a !important;
            transition: all 0.2s ease;
            box-sizing: border-box;
            width: 100%;
            box-shadow: none;
        }

        textarea.filter-input-emp {
            height: auto !important;
            padding: 10px 14px;
        }

        .filter-input-emp,
        .filter-input-emp:valid,
        .filter-input-emp:invalid,
        .filter-input-emp:active,
        .filter-input-emp:-webkit-autofill,
        .filter-input-emp:-webkit-autofill:hover,
        .filter-input-emp:-webkit-autofill:focus {
            color: #0f172a !important;
            background-color: #ffffff !important;
        }

        .filter-input-emp::placeholder,
        .filter-input-emp::-webkit-input-placeholder,
        .filter-input-emp::-moz-placeholder,
        .filter-input-emp:-ms-input-placeholder {
            color: #94a3b8 !important;
            -webkit-text-fill-color: #94a3b8 !important;
            font-weight: 400 !important;
            opacity: 1 !important;
        }

        .custom-dropdown-combo .dropdown-combo-input.is-placeholder {
            color: #94a3b8 !important;
            -webkit-text-fill-color: #94a3b8 !important;
            font-weight: 400 !important;
        }

        .filter-input-emp:focus {
            border-color: #2F53B4 !important;
            box-shadow: 0 0 0 3.5px rgba(47, 83, 180, 0.14) !important;
            outline: none !important;
        }

        /* Thailand Address Autocomplete Input Fix */
        .thailand-address-autocomplete--office .form-control {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 8px;
            height: 42px !important;
            padding: 0 14px;
            font-size: 0.875rem;
            color: #0f172a !important;
            box-shadow: none;
        }

        .thailand-address-autocomplete--office .form-control,
        .thailand-address-autocomplete--office .form-control:valid,
        .thailand-address-autocomplete--office .form-control:invalid,
        .thailand-address-autocomplete--office .form-control:active,
        .thailand-address-autocomplete--office .form-control:-webkit-autofill {
            color: #0f172a !important;
            background-color: #ffffff !important;
        }

        .thailand-address-autocomplete--office .form-control:focus {
            border-color: #2F53B4 !important;
            box-shadow: 0 0 0 3.5px rgba(47, 83, 180, 0.14) !important;
            outline: none !important;
        }

        /* Buttons */
        .btn-add-emp {
            background: linear-gradient(135deg, #2F53B4 0%, #1E3A8A 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 10px 24px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(47, 83, 180, 0.28);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            cursor: pointer;
        }

        .btn-add-emp:hover {
            background: linear-gradient(135deg, #254394 0%, #172E6E 100%);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(47, 83, 180, 0.38);
        }

        .btn-sec-emp {
            background-color: #ffffff;
            color: #475569;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px 22px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
        }

        .btn-sec-emp:hover {
            background-color: #f8fafc;
            color: #0f172a;
            border-color: #94a3b8;
            transform: translateY(-2px);
        }

        /* Custom Dropdown Combo Component (Register Page Style) */
        .custom-dropdown-combo {
            position: relative !important;
            width: 100% !important;
            height: 42px !important;
        }

        .custom-dropdown-combo .dropdown-combo-input {
            width: 100% !important;
            height: 42px !important;
            padding-right: 36px !important;
            cursor: pointer !important;
            background-color: #FFFFFF !important;
            text-align: left !important;
            box-sizing: border-box !important;
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
            max-height: 155px !important;
            overflow-y: scroll !important;
            display: none;
            padding: 6px 0 !important;
            text-align: left !important;
            scrollbar-width: thin !important;
            scrollbar-color: #2F53B4 #E2E8F0 !important;
        }

        /* Custom Visible Scrollbar Track & Thumb for Dropdown Popup */
        .custom-dropdown-popup::-webkit-scrollbar {
            width: 8px !important;
            height: 8px !important;
            display: block !important;
        }

        .custom-dropdown-popup::-webkit-scrollbar-track {
            background: #E2E8F0 !important;
            border-radius: 6px !important;
            margin: 4px 2px !important;
        }

        .custom-dropdown-popup::-webkit-scrollbar-thumb {
            background: #2F53B4 !important;
            border-radius: 6px !important;
            min-height: 30px !important;
        }

        .custom-dropdown-popup::-webkit-scrollbar-thumb:hover {
            background: #1E3A8A !important;
        }

        .custom-dropdown-item {
            padding: 9px 16px !important;
            font-size: 0.875rem !important;
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

        /* Form Switch Custom */
        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
            cursor: pointer;
        }

        .form-switch .form-check-input:checked {
            background-color: #2F53B4;
            border-color: #2F53B4;
        }

        /* Prefix Radio Styling (Matching Register Page) */
        .form-check-inline-custom {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding-left: 0 !important;
            margin-bottom: 0;
            cursor: pointer;
        }

        .form-check-inline-custom .form-check-input {
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

        .form-check-inline-custom .form-check-input:checked {
            background-color: #2F53B4 !important;
            border-color: #2F53B4 !important;
        }

        .form-check-inline-custom .form-check-input:focus {
            border-color: #2F53B4 !important;
            box-shadow: 0 0 0 3.5px rgba(47, 83, 180, 0.15) !important;
        }

        .form-check-inline-custom .form-check-label {
            margin-bottom: 0 !important;
            line-height: 1.4 !important;
            font-size: 0.875rem !important;
            color: #1E293B !important;
            cursor: pointer;
            user-select: none;
        }
    </style>
@endsection

@section('content')
@php
    $c = $lead->primaryContact;

    if (!function_exists('renderLeadCustomSelect')) {
        function renderLeadCustomSelect($name, array $options, $selectedVal, $placeholder = '—', $id = null) {
            $id = $id ?: 'select_' . str_replace(['[', ']', '.'], '_', $name);
            $selectedVal = (string) ($selectedVal ?? '');
            $selectedLabel = $placeholder;

            foreach ($options as $val => $label) {
                if ((string)$val === $selectedVal) {
                    $selectedLabel = $label;
                    break;
                }
            }

            echo '<div class="custom-fp-combo custom-dropdown-combo">';
            echo '<input type="text" class="form-control filter-input-emp dropdown-combo-input" id="' . e($id) . '_display" placeholder="' . e($placeholder) . '" value="' . e($selectedLabel) . '" readonly autocomplete="off">';
            echo '<b class="custom-fp-combo-arrow"></b>';
            echo '<div class="custom-dropdown-popup">';

            if ($placeholder !== null) {
                $isSelected = ($selectedVal === '') ? 'selected' : '';
                echo '<div class="custom-dropdown-item ' . $isSelected . '" data-val="">' . e($placeholder) . '</div>';
            }

            foreach ($options as $val => $label) {
                $isSelected = ((string)$val === $selectedVal) ? 'selected' : '';
                echo '<div class="custom-dropdown-item ' . $isSelected . '" data-val="' . e($val) . '">' . e($label) . '</div>';
            }

            echo '</div>';
            echo '<input type="hidden" name="' . e($name) . '" id="' . e($id) . '" value="' . e($selectedVal) . '">';
            echo '</div>';
        }
    }
@endphp

<div class="container-fluid">
    @include('office.settings.partials.page-title', [
        'section' => 'งานขาย',
        'parent' => 'Leads',
        'parentUrl' => route('office.leads.index'),
        'title' => $lead->exists ? 'แก้ไข Lead' : 'สร้าง Lead ใหม่',
    ])
    @include('office.settings.partials.flash')

    <form method="post" class="needs-validation" novalidate
          action="{{ $lead->exists ? route('office.leads.update', $lead) : route('office.leads.store') }}">
        @csrf
        @if ($lead->exists) @method('PUT') @endif

        <div class="row">
            <!-- Left Main Column -->
            <div class="col-lg-8">
                <!-- Card 1: ผู้ติดต่อหลัก -->
                <div class="emp-card-main">
                    <div class="section-header-title">
                        <i class="ri-user-follow-line"></i>
                        <span>ผู้ติดต่อหลัก</span>
                    </div>
                    <div class="row g-3">
                        @php
                            $currentPrefix = old('contact_prefix', $c?->prefix ?? 'นาย');
                            $isStandardPrefix = in_array($currentPrefix, ['นาย', 'นาง', 'นางสาว']);
                            $checkedPrefix = $currentPrefix ? ($isStandardPrefix ? $currentPrefix : 'อื่นๆ') : 'นาย';
                        @endphp
                        <div class="col-12">
                            <label class="filter-label-emp">คำนำหน้า</label>
                            <div class="d-flex flex-wrap align-items-center gap-4 mt-1">
                                <div class="form-check form-check-inline-custom me-0">
                                    <input class="form-check-input prefix-radio" type="radio" name="prefix_radio"
                                        id="prefix_mr" value="นาย"
                                        {{ $checkedPrefix === 'นาย' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="prefix_mr">นาย</label>
                                </div>
                                <div class="form-check form-check-inline-custom me-0">
                                    <input class="form-check-input prefix-radio" type="radio" name="prefix_radio"
                                        id="prefix_mrs" value="นาง" {{ $checkedPrefix === 'นาง' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="prefix_mrs">นาง</label>
                                </div>
                                <div class="form-check form-check-inline-custom me-0">
                                    <input class="form-check-input prefix-radio" type="radio" name="prefix_radio"
                                        id="prefix_ms" value="นางสาว" {{ $checkedPrefix === 'นางสาว' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="prefix_ms">นางสาว</label>
                                </div>
                                <div class="form-check form-check-inline-custom me-0">
                                    <input class="form-check-input prefix-radio" type="radio" name="prefix_radio"
                                        id="prefix_other" value="อื่นๆ"
                                        {{ $checkedPrefix === 'อื่นๆ' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="prefix_other">อื่นๆ</label>
                                </div>
                                <input type="text" id="prefix_other_text" class="form-control filter-input-emp"
                                    placeholder="กรอกคำนำหน้า"
                                    value="{{ !$isStandardPrefix ? $currentPrefix : '' }}"
                                    style="max-width: 180px; height: 38px !important; font-size: 0.875rem;">
                            </div>
                            <input type="hidden" name="contact_prefix" id="prefix_final_input"
                                value="{{ $currentPrefix ?: 'นาย' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="filter-label-emp">ชื่อ <span class="text-danger">*</span></label>
                            <input type="text" name="contact_firstname" class="form-control filter-input-emp" value="{{ old('contact_firstname', $c?->firstname) }}" required placeholder="กรอกชื่อผู้ติดต่อ">
                        </div>
                        <div class="col-md-6">
                            <label class="filter-label-emp">นามสกุล</label>
                            <input type="text" name="contact_surname" class="form-control filter-input-emp" value="{{ old('contact_surname', $c?->surname) }}" placeholder="กรอกนามสกุล">
                        </div>
                        <div class="col-md-4">
                            <label class="filter-label-emp">ตำแหน่ง</label>
                            <input type="text" name="contact_position" class="form-control filter-input-emp" value="{{ old('contact_position', $c?->position) }}" placeholder="กรอกตำแหน่ง">
                        </div>
                        <div class="col-md-4">
                            <label class="filter-label-emp">โทร</label>
                            <input type="text" name="contact_phone" class="form-control filter-input-emp" value="{{ old('contact_phone', $c?->phone) }}" placeholder="เบอร์โทรศัพท์">
                        </div>
                        <div class="col-md-4">
                            <label class="filter-label-emp">อีเมล</label>
                            <input type="email" name="contact_email" class="form-control filter-input-emp" value="{{ old('contact_email', $c?->email) }}" placeholder="อีเมลติดต่อ">
                        </div>
                        <div class="col-md-4">
                            <label class="filter-label-emp">Line</label>
                            <input type="text" name="contact_line_id" class="form-control filter-input-emp" value="{{ old('contact_line_id', $c?->line_id) }}" placeholder="Line ID">
                        </div>
                    </div>
                </div>

                <!-- Card 2: ข้อมูลองค์กร -->
                <div class="emp-card-main">
                    <div class="section-header-title">
                        <i class="ri-building-4-line"></i>
                        <span>ข้อมูลองค์กร (Snapshot — ยังไม่สร้าง Partner)</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="filter-label-emp">ชื่อองค์กร</label>
                            <input type="text" name="organization_name" class="form-control filter-input-emp" value="{{ old('organization_name', $lead->organization_name) }}" placeholder="ชื่อองค์กร / บริษัท">
                        </div>
                        <div class="col-md-3">
                            <label class="filter-label-emp">ประเภท</label>
                            @php
                                $orgTypeOpts = ['1' => 'บริษัท', '2' => 'ห้างหุ้นส่วน', '3' => 'บุคคลธรรมดา'];
                                renderLeadCustomSelect('organization_type', $orgTypeOpts, old('organization_type', $lead->organization_type), 'ไม่ทราบ');
                            @endphp
                        </div>
                        <div class="col-md-3">
                            <label class="filter-label-emp">เลขภาษี</label>
                            <input type="text" name="tax_no" maxlength="13" class="form-control filter-input-emp" value="{{ old('tax_no', $lead->tax_no) }}" placeholder="เลขประจำตัวผู้เสียภาษี">
                        </div>
                        <div class="col-md-12">
                            <label class="filter-label-emp">ที่อยู่</label>
                            <input type="text" name="address" class="form-control filter-input-emp" value="{{ old('address', $lead->address) }}" placeholder="เลขที่ ถนน ซอย...">
                        </div>
                    </div>
                    <div class="mt-3">
                        @include('partials.thailand-address-autocomplete', [
                            'selectedProvinceId' => old('province_id', $lead->province_id),
                            'selectedAmphureId' => old('amphure_id', $lead->amphure_id),
                            'selectedTambonId' => old('tambon_id', $lead->tambon_id),
                            'selectedZipCode' => $selectedZipCode ?? '',
                            'idPrefix' => 'lead_address',
                            'variant' => 'office',
                            'required' => false,
                            'rowClass' => 'row g-3 mt-3',
                        ])
                    </div>
                </div>

                <!-- Card 3: ความสนใจ -->
                <div class="emp-card-main">
                    <div class="section-header-title">
                        <i class="ri-file-list-3-line"></i>
                        <span>ความสนใจ</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="filter-label-emp">ชื่องาน</label>
                            <input type="text" name="project_name" class="form-control filter-input-emp" value="{{ old('project_name', $lead->project_name) }}" placeholder="ชื่องาน / โครงการ">
                        </div>
                        <div class="col-md-6">
                            <label class="filter-label-emp">ประเภทงาน</label>
                            @php
                                $projTypeOpts = $projectTypes->pluck('name', 'id')->toArray();
                                renderLeadCustomSelect('lead_project_type_id', $projTypeOpts, old('lead_project_type_id', $lead->lead_project_type_id));
                            @endphp
                        </div>
                        <div class="col-md-6">
                            <label class="filter-label-emp">หมวดสินค้า</label>
                            @php
                                $catOpts = $categories->pluck('name', 'id')->toArray();
                                renderLeadCustomSelect('product_service_category_id', $catOpts, old('product_service_category_id', $lead->product_service_category_id));
                            @endphp
                        </div>
                        <div class="col-md-6">
                            <label class="filter-label-emp">สินค้า / บริการ</label>
                            @php
                                $itemOpts = $items->pluck('name', 'id')->toArray();
                                renderLeadCustomSelect('product_service_item_id', $itemOpts, old('product_service_item_id', $lead->product_service_item_id));
                            @endphp
                        </div>
                        <div class="col-12">
                            <label class="filter-label-emp">ขอบเขตที่เล่ามา</label>
                            <textarea name="project_scope" class="form-control filter-input-emp" rows="3" placeholder="รายละเอียดหรือขอบเขตงานเพิ่มเติม...">{{ old('project_scope', $lead->project_scope) }}</textarea>
                        </div>

                        @if ($questions->isNotEmpty())
                            <div class="col-12 mt-4"><hr><h6 class="text-muted fw-bold">คำถาม Intake</h6></div>
                            @foreach ($questions as $q)
                                @php $ans = $lead->exists ? $lead->intakeAnswers->firstWhere('product_service_question_id', $q->id)?->value : null; @endphp
                                <div class="col-md-6">
                                    <label class="filter-label-emp">{{ $q->label }}</label>
                                    <input type="text" name="intake[{{ $q->id }}]" class="form-control filter-input-emp" value="{{ old('intake.'.$q->id, $ans) }}" placeholder="คำตอบ...">
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Card 4: คะแนนคุณภาพ -->
                <div class="emp-card-main">
                    <div class="section-header-title">
                        <i class="ri-star-line"></i>
                        <span>คะแนนคุณภาพ (ใช้จัดลำดับการติดตาม)</span>
                    </div>
                    <div class="row g-3">
                        @foreach (\App\Models\Lead\Lead::QUALITY_FACTORS as $key => $label)
                            <div class="col-md-6">
                                <label class="filter-label-emp">{{ $label }}</label>
                                @php
                                    $qualityOpts = [4 => '4 (สูงมาก)', 3 => '3 (สูง)', 2 => '2 (ปานกลาง)', 1 => '1 (ต่ำ)'];
                                    renderLeadCustomSelect($key, $qualityOpts, old($key, $lead->{$key}), 'ยังไม่ประเมิน');
                                @endphp
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Sidebar Column -->
            <div class="col-lg-4">
                <div class="emp-card-main">
                    <div class="section-header-title">
                        <i class="ri-focus-2-line"></i>
                        <span>แหล่งที่มาและเจ้าของ</span>
                    </div>
                    <div class="mb-3">
                        <label class="filter-label-emp">สถานะ</label>
                        @php
                            $statusOpts = ['draft' => 'ร่าง', 'new' => 'เข้าคิว (ใหม่)'];
                            renderLeadCustomSelect('status', $statusOpts, old('status', $lead->status ?: 'draft'), null);
                        @endphp
                    </div>
                    <div class="mb-3">
                        <label class="filter-label-emp">สาขา</label>
                        @php
                            $branchOpts = $branches->pluck('business_branch_name_th', 'id')->toArray();
                            renderLeadCustomSelect('business_branch_id', $branchOpts, old('business_branch_id', $lead->business_branch_id));
                        @endphp
                    </div>
                    <div class="mb-3">
                        <label class="filter-label-emp">ช่องทาง</label>
                        @php
                            $channelOpts = $channels->pluck('name', 'id')->toArray();
                            renderLeadCustomSelect('lead_channel_id', $channelOpts, old('lead_channel_id', $lead->lead_channel_id));
                        @endphp
                    </div>
                    <div class="mb-3">
                        <label class="filter-label-emp">แคมเปญ</label>
                        @php
                            $adOpts = $ads->pluck('name', 'id')->toArray();
                            renderLeadCustomSelect('marketing_ad_id', $adOpts, old('marketing_ad_id', $lead->marketing_ad_id));
                        @endphp
                    </div>
                    <div class="mb-3">
                        <label class="filter-label-emp">ผู้รับผิดชอบ Lead</label>
                        @php
                            $empOpts = $employees->mapWithKeys(fn($eb) => [$eb->id => $eb->displayName()])->toArray();
                            renderLeadCustomSelect('assigned_to', $empOpts, old('assigned_to', $lead->assigned_to), 'ตามกฎอัตโนมัติ');
                        @endphp
                    </div>
                    <div class="mb-3">
                        <label class="filter-label-emp">วันที่รับ Lead</label>
                        <div class="birthday-icon-wrapper">
                            <input type="text" name="registration_date" id="registration_date"
                                class="form-control filter-input-emp flatpickr-date @error('registration_date') is-invalid @enderror"
                                value="{{ old('registration_date', optional($lead->registration_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                                placeholder="วว/ดด/ปปปป">
                            <i class="ri-calendar-line birthday-icon"></i>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-4 mt-3">
                        <input class="form-check-input" type="checkbox" name="is_high_focus" id="is_high_focus" value="1" @checked(old('is_high_focus', $lead->is_high_focus))>
                        <label class="form-check-label filter-label-emp mb-0 ms-1" for="is_high_focus">ความสำคัญสูง</label>
                    </div>

                    <label class="filter-label-emp mb-2">UTM Parameters</label>
                    <div class="row g-2">
                        <div class="col-4">
                            <input type="text" name="utm_source" class="form-control filter-input-emp" placeholder="utm_source" value="{{ old('utm_source', $lead->utm_source) }}">
                        </div>
                        <div class="col-4">
                            <input type="text" name="utm_medium" class="form-control filter-input-emp" placeholder="utm_medium" value="{{ old('utm_medium', $lead->utm_medium) }}">
                        </div>
                        <div class="col-4">
                            <input type="text" name="utm_campaign" class="form-control filter-input-emp" placeholder="utm_campaign" value="{{ old('utm_campaign', $lead->utm_campaign) }}">
                        </div>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="d-flex align-items-center justify-content-end gap-3 mb-4">
                    <a href="{{ route('office.leads.index') }}" class="btn-sec-emp w-50">
                        ย้อนกลับ
                    </a>
                    <button class="btn-add-emp w-50" type="submit">
                        <i class="ri-check-line fs-16"></i>
                        <span>บันทึกข้อมูล</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
    @include('partials.thailand-address-autocomplete-script')
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/l10n/th.js') }}"></script>
    <script>
        function buildCustomHeaderControls(fp) {
            var $container = $(fp.calendarContainer);
            var $currentMonth = $container.find('.flatpickr-current-month');
            if (!$currentMonth.length) return;

            $currentMonth.empty();

            var monthNames = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม",
                "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
            ];

            // 1. Day Input
            var $dayInput = $(
                '<input type="text" class="custom-fp-combo-input day-input" placeholder="วัน" maxlength="2" inputmode="numeric" autocomplete="off">'
            );
            var $dayDropdown = $('<div class="custom-fp-dropdown-popup day-dropdown-popup"></div>');
            for (var d = 1; d <= 31; d++) {
                $dayDropdown.append('<div class="custom-fp-combo-item" data-val="' + d + '">' + d + '</div>');
            }
            var $dayWrapper = $('<div class="custom-fp-combo day-combo"></div>').append($dayInput).append(
                '<b class="custom-fp-combo-arrow"></b>').append($dayDropdown);

            // 2. Month Input
            var $monthInput = $(
                '<input type="text" class="custom-fp-combo-input month-input" placeholder="เดือน" autocomplete="off">');
            var $monthDropdown = $('<div class="custom-fp-dropdown-popup month-dropdown-popup"></div>');
            for (var m = 0; m < 12; m++) {
                $monthDropdown.append('<div class="custom-fp-combo-item" data-val="' + m + '">' + monthNames[m] + '</div>');
            }
            var $monthWrapper = $('<div class="custom-fp-combo month-combo"></div>').append($monthInput).append(
                '<b class="custom-fp-combo-arrow"></b>').append($monthDropdown);

            // 3. Year Input
            var currentYear = new Date().getFullYear();
            var $yearInput = $(
                '<input type="text" class="custom-fp-combo-input year-input" placeholder="ปี" maxlength="4" inputmode="numeric" autocomplete="off">'
            );
            var $yearDropdown = $('<div class="custom-fp-dropdown-popup year-dropdown-popup"></div>');
            for (var y = currentYear + 5; y >= 1920; y--) {
                $yearDropdown.append('<div class="custom-fp-combo-item" data-val="' + y + '">' + y + '</div>');
            }
            var $yearWrapper = $('<div class="custom-fp-combo year-combo"></div>').append($yearInput).append(
                '<b class="custom-fp-combo-arrow"></b>').append($yearDropdown);

            $currentMonth.append($dayWrapper).append($monthWrapper).append($yearWrapper);

            function bindCombo($input, $wrapper, $dropdown, onCommit, filterFn) {
                function openMenu() {
                    $('.custom-fp-dropdown-popup').not($dropdown).hide();
                    $('.custom-fp-combo').not($wrapper).removeClass('active');
                    $wrapper.addClass('active');
                    $dropdown.show();
                }

                function closeMenu() {
                    $wrapper.removeClass('active');
                    $dropdown.hide();
                }

                $input.on('focus click', function(e) {
                    e.stopPropagation();
                    openMenu();
                });

                $input.on('input keyup', function(e) {
                    openMenu();
                    var q = $(this).val().trim();
                    if (filterFn) filterFn(q, $dropdown);
                    if (onCommit) onCommit(false);
                });

                $dropdown.on('mousedown click', '.custom-fp-combo-item', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var val = $(this).data('val');
                    var text = $(this).text();
                    $input.val(text).data('selected-val', val);
                    $dropdown.find('.custom-fp-combo-item').removeClass('selected');
                    $(this).addClass('selected');
                    closeMenu();
                    if (onCommit) onCommit(true);
                });

                $(document).on('click.fpcombo', function(e) {
                    if (!$(e.target).closest($wrapper).length) {
                        closeMenu();
                    }
                });
            }

            // Bind Day
            bindCombo($dayInput, $dayWrapper, $dayDropdown, function(isFromClick) {
                var dVal = $dayInput.val().replace(/\D/g, '');
                var d = parseInt(dVal);
                var mVal = $monthInput.data('selected-val');
                var m = mVal !== undefined && mVal !== '' ? parseInt(mVal) : fp.currentMonth;
                var yVal = $yearInput.val().replace(/\D/g, '');
                var y = parseInt(yVal) || fp.currentYear;

                if (d >= 1 && d <= 31 && !isNaN(m) && y >= 1920) {
                    var newDate = new Date(y, m, d);
                    fp.setDate(newDate, true);
                }
            }, function(q, $dd) {
                $dd.find('.custom-fp-combo-item').each(function() {
                    var text = $(this).text();
                    if (!q || text.indexOf(q) === 0) $(this).show();
                    else $(this).hide();
                });
            });

            // Bind Month
            bindCombo($monthInput, $monthWrapper, $monthDropdown, function(isFromClick) {
                var mVal = $monthInput.data('selected-val');
                var m = mVal !== undefined && mVal !== '' ? parseInt(mVal) : NaN;
                var yVal = $yearInput.val().replace(/\D/g, '');
                var y = parseInt(yVal) || fp.currentYear;
                var dVal = $dayInput.val().replace(/\D/g, '');
                var d = parseInt(dVal);

                if (!isNaN(m)) {
                    if (d && y >= 1920) {
                        var newDate = new Date(y, m, d);
                        fp.setDate(newDate, true);
                    } else {
                        fp.jumpToDate(new Date(y, m, 1));
                    }
                }
            }, function(q, $dd) {
                $dd.find('.custom-fp-combo-item').each(function() {
                    var text = $(this).text();
                    var val = $(this).data('val');
                    if (!q || text.indexOf(q) !== -1 || (val + 1).toString() === q) $(this).show();
                    else $(this).hide();
                });
            });

            // Bind Year
            bindCombo($yearInput, $yearWrapper, $yearDropdown, function(isFromClick) {
                var yVal = $yearInput.val().replace(/\D/g, '');
                var y = parseInt(yVal);
                var mVal = $monthInput.data('selected-val');
                var m = mVal !== undefined && mVal !== '' ? parseInt(mVal) : fp.currentMonth;
                var dVal = $dayInput.val().replace(/\D/g, '');
                var d = parseInt(dVal);

                if (yVal.length === 4 && y >= 1920) {
                    if (d && !isNaN(m)) {
                        var newDate = new Date(y, m, d);
                        fp.setDate(newDate, true);
                    } else {
                        fp.jumpToDate(new Date(y, m, 1));
                    }
                }
            }, function(q, $dd) {
                $dd.find('.custom-fp-combo-item').each(function() {
                    var text = $(this).text();
                    if (!q || text.indexOf(q) === 0) $(this).show();
                    else $(this).hide();
                });
            });

            syncCustomHeaderControls(fp);
        }

        function syncCustomHeaderControls(fp) {
            var $container = $(fp.calendarContainer);
            var $dayInput = $container.find('.day-input');
            var $monthInput = $container.find('.month-input');
            var $yearInput = $container.find('.year-input');

            if (!$monthInput.length) return;

            var monthNames = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม",
                "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
            ];

            if (fp.selectedDates.length) {
                var date = fp.selectedDates[0];
                if (document.activeElement !== $dayInput[0]) {
                    $dayInput.val(date.getDate());
                }
                if (document.activeElement !== $monthInput[0]) {
                    $monthInput.val(monthNames[date.getMonth()]).data('selected-val', date.getMonth());
                }
                if (document.activeElement !== $yearInput[0]) {
                    $yearInput.val(date.getFullYear());
                }
            } else {
                if (document.activeElement !== $dayInput[0] && !$dayInput.val()) {
                    $dayInput.val('');
                }
                if (document.activeElement !== $monthInput[0] && $monthInput.data('selected-val') === undefined) {
                    $monthInput.val('');
                }
                if (document.activeElement !== $yearInput[0] && !$yearInput.val()) {
                    $yearInput.val('');
                }
            }
        }

        $(document).ready(function() {
            if (typeof flatpickr !== 'undefined') {
                var thLocale = typeof flatpickr.l10ns.th !== 'undefined' ? $.extend({}, flatpickr.l10ns.th, {
                    firstDayOfWeek: 0
                }) : 'th';
                flatpickr('.flatpickr-date', {
                    locale: thLocale,
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    disableMobile: true,
                    monthSelectorType: 'dropdown',
                    onReady: function(selectedDates, dateStr, instance) {
                        buildCustomHeaderControls(instance);
                    },
                    onMonthChange: function(selectedDates, dateStr, instance) {
                        syncCustomHeaderControls(instance);
                    },
                    onYearChange: function(selectedDates, dateStr, instance) {
                        syncCustomHeaderControls(instance);
                    },
                    onChange: function(selectedDates, dateStr, instance) {
                        syncCustomHeaderControls(instance);
                    }
                });
            }

            $('.custom-dropdown-combo').each(function() {
                var $combo = $(this);
                var $input = $combo.find('.dropdown-combo-input');
                var $popup = $combo.find('.custom-dropdown-popup');
                var $hidden = $combo.find('input[type="hidden"]');

                $combo.on('click', function(e) {
                    e.stopPropagation();
                    $('.custom-dropdown-combo').not($combo).removeClass('active').find('.custom-dropdown-popup').hide();
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
                });
            });

            // Prefix Radio Buttons + "อื่นๆ" Custom Text Input Handler (Matching Register Page)
            $('.prefix-radio').on('change', function() {
                var selectedVal = $(this).val();
                var $otherInput = $('#prefix_other_text');
                var $finalInput = $('#prefix_final_input');

                if (selectedVal === 'อื่นๆ') {
                    $finalInput.val($otherInput.val().trim() || 'อื่นๆ');
                    $otherInput.focus();
                } else {
                    $finalInput.val(selectedVal);
                }
            });

            $('#prefix_other_text').on('click focus input keyup', function() {
                $('#prefix_other').prop('checked', true);
                $('#prefix_final_input').val($(this).val().trim() || 'อื่นๆ');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.custom-dropdown-combo').length) {
                    $('.custom-dropdown-combo').removeClass('active');
                    $('.custom-dropdown-popup').hide();
                }
            });
        });
    </script>
@endsection
