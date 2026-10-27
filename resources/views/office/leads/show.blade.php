@extends('office.layout.office')
@section('title', ($lead->registration_no ?: 'Lead Record') . ' | Hi Monday')

@section('style')
    <link href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        /* Card Main */
        .emp-card-main {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03), 0 1px 3px rgba(0, 0, 0, 0.02);
            border: 1px solid #e2e8f0;
            padding: 20px;
            margin-bottom: 20px;
        }

        .section-header-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .section-header-title i {
            color: #2F53B4;
            font-size: 1.15rem;
        }

        /* Buttons */
        .btn-add-emp {
            background: linear-gradient(135deg, #2F53B4 0%, #1E3A8A 100%);
            color: #ffffff !important;
            border: none;
            border-radius: 10px;
            padding: 8px 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(47, 83, 180, 0.25);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            height: 40px;
            white-space: nowrap;
        }

        .btn-add-emp:hover {
            background: linear-gradient(135deg, #254394 0%, #172E6E 100%);
            color: #ffffff !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(47, 83, 180, 0.35);
        }

        .btn-sec-emp {
            background-color: #ffffff;
            color: #2F53B4 !important;
            border: 1.5px solid #2F53B4;
            border-radius: 10px;
            padding: 7px 18px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            box-shadow: 0 2px 5px rgba(47, 83, 180, 0.06);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            height: 40px;
        }

        .btn-sec-emp:hover {
            background-color: #EFF6FF;
            color: #1E3A8A !important;
            border-color: #1E3A8A;
            transform: translateY(-1px);
        }

        /* Form Labels & Inputs */
        .filter-label-emp {
            font-size: 0.825rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .filter-input-emp {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 10px !important;
            height: 40px;
            padding: 0 12px;
            font-size: 0.85rem;
            color: #0f172a !important;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        textarea.filter-input-emp {
            height: auto !important;
            padding: 10px 12px;
        }

        .filter-input-emp:focus {
            border-color: #2F53B4 !important;
            box-shadow: 0 0 0 3px rgba(47, 83, 180, 0.15) !important;
            outline: none;
        }

        .filter-input-emp:-webkit-autofill,
        .filter-input-emp:-webkit-autofill:hover,
        .filter-input-emp:-webkit-autofill:focus {
            -webkit-text-fill-color: #0f172a !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* Placeholder Light Grey Colors (Strict Override) */
        .filter-input-emp::placeholder,
        .filter-input-emp::-webkit-input-placeholder,
        .filter-input-emp::-moz-placeholder,
        .filter-input-emp:-ms-input-placeholder {
            color: #94a3b8 !important;
            -webkit-text-fill-color: #94a3b8 !important;
            font-weight: 400 !important;
            opacity: 1 !important;
        }

        /* Soft Pastel Blue Box Background */
        .bg-pastel-blue {
            background-color: #eff6ff !important;
            border: 1px solid #dbeafe !important;
            border-radius: 16px !important;
        }

        /* Custom Dropdown Combo Styles */
        .custom-dropdown-combo {
            position: relative;
            width: 100%;
        }

        .custom-dropdown-combo .dropdown-combo-input {
            cursor: pointer;
            background-color: #ffffff !important;
            padding-right: 32px;
        }

        .custom-dropdown-combo .dropdown-combo-input.is-placeholder {
            color: #94a3b8 !important;
            -webkit-text-fill-color: #94a3b8 !important;
            font-weight: 400 !important;
        }

        .custom-dropdown-arrow {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 4.5px solid transparent;
            border-right: 4.5px solid transparent;
            border-top: 5px solid #2F53B4;
            pointer-events: none;
            transition: transform 0.2s ease;
        }

        .custom-dropdown-combo.active .custom-dropdown-arrow {
            transform: translateY(-50%) rotate(180deg);
        }

        .custom-dropdown-popup {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
            max-height: 150px;
            overflow-y: scroll;
            scrollbar-width: thin;
            scrollbar-color: #2F53B4 #E2E8F0;
            z-index: 999;
            display: none;
            padding: 4px 0;
        }

        .custom-dropdown-popup::-webkit-scrollbar {
            width: 5px;
        }

        .custom-dropdown-popup::-webkit-scrollbar-track {
            background: #E2E8F0;
            border-radius: 10px;
        }

        .custom-dropdown-popup::-webkit-scrollbar-thumb {
            background: #2F53B4;
            border-radius: 10px;
        }

        .custom-dropdown-item {
            padding: 8px 12px;
            font-size: 0.85rem;
            color: #1e293b;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .custom-dropdown-item:hover,
        .custom-dropdown-item.selected {
            background-color: #eff6ff;
            color: #2F53B4;
            font-weight: 600;
        }

        /* Birthday / Calendar Icon Wrapper */
        .birthday-icon-wrapper {
            position: relative;
            width: 100%;
        }

        .birthday-icon-wrapper .birthday-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            color: #2F53B4;
            pointer-events: none;
            z-index: 5;
        }

        /* Nav Tabs Custom */
        .nav-tabs-custom {
            border-bottom: 2px solid #f1f5f9;
            gap: 2px;
        }

        .nav-tabs-custom .nav-link {
            font-weight: 600;
            color: #64748b;
            border: none;
            border-bottom: 2.5px solid transparent;
            padding: 8px 16px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            border-radius: 0;
            background: transparent !important;
        }

        .nav-tabs-custom .nav-link:hover {
            color: #2F53B4;
        }

        .nav-tabs-custom .nav-link.active {
            color: #2F53B4 !important;
            border-bottom-color: #2F53B4 !important;
            font-weight: 700;
        }

        .info-label {
            font-size: 0.775rem;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
        }

        .info-value {
            font-size: 0.875rem;
            color: #0f172a;
            font-weight: 600;
            word-break: break-word;
        }

        .table-th-emp {
            font-size: 0.825rem;
            font-weight: 600;
            color: #475569;
            padding: 10px 14px;
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Action Buttons (Matched with Index Page) */
        .btn-action-item {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            border: 1px solid transparent;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            background: transparent;
            text-decoration: none;
            flex-shrink: 0;
            margin: 0;
        }

        .btn-action-delete {
            background-color: #FEF2F2;
            color: #DC2626;
            border-color: #FCA5A5;
        }

        .btn-action-delete:hover {
            background-color: #DC2626;
            color: #ffffff;
            border-color: #DC2626;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(220, 38, 38, 0.25);
        }
    </style>
    @include('office.partials.sales-journey-styles')
@endsection

@section('content')
    @php
        $badge = match ($lead->status) {
            'new' => 'bg-info-subtle text-info',
            'contacting' => 'bg-primary-subtle text-primary',
            'nurturing' => 'bg-warning-subtle text-warning',
            'qualified', 'converted' => 'bg-success-subtle text-success',
            default => 'bg-secondary-subtle text-secondary',
        };

        $statusOptsMap = [];
        foreach (\App\Models\Lead\Lead::STATUS_LABELS as $k => $lab) {
            if ($k !== 'converted') {
                $statusOptsMap[$k] = $lab;
            }
        }

        $closedReasonsMap = $closedReasons->pluck('name', 'id')->toArray();
        $branchOptsMap = $branches->pluck('business_branch_name_th', 'id')->toArray();
        $empOptsMap = $employees->mapWithKeys(fn($eb) => [$eb->id => $eb->displayName()])->toArray();

        $activityTypeOptsMap = [];
        foreach (\App\Models\Lead\LeadActivity::TYPE_LABELS as $k => $lab) {
            $activityTypeOptsMap[$k] = $lab;
        }

        $contactOptsMap = $lead->contacts->mapWithKeys(fn($p) => [$p->id => $p->displayName()])->toArray();

        function renderLeadCustomSelectShow($name, array $options, $selectedVal, $placeholder = '—', $id = null)
        {
            $id = $id ?: 'select_' . str_replace(['[', ']', '.'], '_', $name);
            $selectedVal = (string) ($selectedVal ?? '');
            $selectedLabel = $placeholder;
            $isPlaceholder = true;

            foreach ($options as $val => $label) {
                if ((string) $val === $selectedVal && $selectedVal !== '') {
                    $selectedLabel = $label;
                    $isPlaceholder = false;
                    break;
                }
            }

            $inputClass =
                'form-control filter-input-emp dropdown-combo-input' . ($isPlaceholder ? ' is-placeholder' : '');

            echo '<div class="custom-dropdown-combo">';
            echo '<input type="text" class="' .
                $inputClass .
                '" id="' .
                e($id) .
                '_display" placeholder="' .
                e($placeholder) .
                '" value="' .
                e($selectedLabel) .
                '" readonly autocomplete="off">';
            echo '<b class="custom-dropdown-arrow"></b>';
            echo '<div class="custom-dropdown-popup">';

            if ($placeholder !== null) {
                $isSelected = $selectedVal === '' ? 'selected' : '';
                echo '<div class="custom-dropdown-item ' . $isSelected . '" data-val="">' . e($placeholder) . '</div>';
            }

            foreach ($options as $val => $label) {
                $isSelected = (string) $val === $selectedVal ? 'selected' : '';
                echo '<div class="custom-dropdown-item ' .
                    $isSelected .
                    '" data-val="' .
                    e($val) .
                    '">' .
                    e($label) .
                    '</div>';
            }

            echo '</div>';
            echo '<input type="hidden" name="' . e($name) . '" id="' . e($id) . '" value="' . e($selectedVal) . '">';
            echo '</div>';
        }
    @endphp

    <div class="container-fluid">
        @include('office.settings.partials.page-title', [
            'section' => 'งานขาย',
            'parent' => 'Leads',
            'parentUrl' => route('office.leads.index'),
            'title' => $lead->registration_no ?: 'ร่าง',
        ])

        <!-- Page Header & Action Bar -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">
                    {{ $lead->registration_no ?: 'ร่าง' }}
                    @if ($lead->registration_no)
                        <span class="badge {{ $badge }} fs-12 align-middle ms-1">{{ $lead->statusLabel() }}</span>
                    @endif
                    @if ($lead->tier)
                        <span class="badge bg-primary-subtle text-primary fs-12 ms-1">{{ $lead->tier }}</span>
                    @endif
                </h4>
                <p class="text-muted fs-13 mb-0">รายละเอียด Lead สำหรับการติดตามและการคัดกรองเชิงธุรกิจ</p>
            </div>
            <div class="hstack gap-2 flex-wrap">
                @if ($lead->status !== 'converted')
                    <form method="post" action="{{ route('office.leads.convert', $lead) }}"
                        class="d-flex align-items-center gap-2">
                        @csrf
                        <input type="text" name="opportunity_name" class="form-control filter-input-emp"
                            placeholder="ชื่อ Opportunity (ไม่บังคับ)" style="min-width:220px;">
                        <button type="submit" class="btn-add-emp text-nowrap">
                            <i class="ri-share-forward-line fs-15"></i>
                            <span class="text-nowrap">Convert เป็น Opportunity</span>
                        </button>
                    </form>
                @elseif ($lead->converted_opportunity_id)
                    <a href="{{ route('office.opportunities.show', $lead->converted_opportunity_id) }}" class="btn-add-emp">
                        <i class="ri-external-link-line fs-15"></i>
                        <span>เปิด Opportunity</span>
                    </a>
                @endif
                @unless ($lead->isLocked())
                    <a href="{{ route('office.leads.edit', $lead) }}" class="btn-sec-emp">
                        <i class="ri-pencil-line fs-15"></i>
                        <span>แก้ไข</span>
                    </a>
                @endunless
                <a href="{{ route('office.leads.index') }}" class="btn-sec-emp">
                    <i class="ri-arrow-left-line fs-15"></i>
                    <span>กลับหน้ารายการ</span>
                </a>
            </div>
        </div>

        @include('office.settings.partials.flash')

        <div class="row g-3">
            <!-- Left Sidebar Column -->
            <div class="col-xl-4 col-lg-5">
                <!-- Card 1: ข้อมูลผู้ติดต่อหลัก & องค์กร (Neat 2-Column Grid) -->
                <div class="emp-card-main">
                    <div class="section-header-title">
                        <i class="ri-user-3-line"></i>
                        <span>ผู้ติดต่อหลัก</span>
                    </div>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <div class="info-label">ชื่อ - นามสกุล</div>
                            <div class="info-value">{{ $lead->primaryContact?->displayName() ?? '-' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">ตำแหน่ง</div>
                            <div class="info-value">{{ $lead->primaryContact?->position ?: '-' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="info-label"><i class="ri-phone-line me-1"></i>โทรศัพท์</div>
                            <div class="info-value">{{ $lead->primaryContact?->phone ?: '-' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="info-label"><i class="ri-mail-line me-1"></i>อีเมล</div>
                            <div class="info-value text-truncate" title="{{ $lead->primaryContact?->email }}">
                                {{ $lead->primaryContact?->email ?: '-' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="info-label"><i class="ri-chat-1-line me-1"></i>LINE ID</div>
                            <div class="info-value">{{ $lead->primaryContact?->line_id ?: '-' }}</div>
                        </div>
                    </div>

                    <hr class="my-3" style="border-color: #f1f5f9;">

                    <div class="section-header-title mb-2 pb-2 border-0">
                        <i class="ri-building-4-line"></i>
                        <span>องค์กร & งาน</span>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="info-label">องค์กร</div>
                            <div class="info-value">{{ $lead->organization_name ?: '-' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">โครงการ / ขอบเขตงาน</div>
                            <div class="info-value">{{ $lead->project_name ?: $lead->project_scope ?: '-' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">ช่องทาง</div>
                            <div class="info-value">{{ $lead->channel?->name ?? '-' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="info-label">สาขา</div>
                            <div class="info-value">{{ $lead->branch?->business_branch_name_th ?? '-' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">เจ้าของ</div>
                            <div class="info-value">{{ $lead->assignedTo?->displayName() ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                @unless ($lead->isLocked())
                    <!-- Card 2: ปรับสถานะ -->
                    <div class="emp-card-main">
                        <div class="section-header-title">
                            <i class="ri-price-tag-3-line"></i>
                            <span>ปรับสถานะ</span>
                        </div>
                        <form method="post" action="{{ route('office.leads.status', $lead) }}" novalidate>
                            @csrf
                            <div class="mb-2.5">
                                <label class="filter-label-emp">สถานะ</label>
                                @php renderLeadCustomSelectShow('status', $statusOptsMap, $lead->status, null); @endphp
                            </div>
                            <div class="mb-2.5 mt-2">
                                <label class="filter-label-emp">เหตุผลปิด (ถ้ามี)</label>
                                @php renderLeadCustomSelectShow('lead_closed_reason_id', $closedReasonsMap, null, 'เหตุผลปิด (ถ้ามี)'); @endphp
                            </div>
                            <div class="mb-2.5 mt-2">
                                <label class="filter-label-emp">วันที่ทบทวน Nurture</label>
                                <div class="birthday-icon-wrapper">
                                    <input type="text" name="nurture_review_at"
                                        class="form-control filter-input-emp flatpickr-date" placeholder="วว/ดด/ปปปป">
                                    <i class="ri-calendar-line birthday-icon"></i>
                                </div>
                            </div>
                            <div class="mb-3 mt-2">
                                <label class="filter-label-emp">หมายเหตุ</label>
                                <textarea name="closed_note" class="form-control filter-input-emp" rows="2"
                                    placeholder="กรอกหมายเหตุเพิ่มเติม..."></textarea>
                            </div>
                            <button class="btn-add-emp w-100" type="submit">
                                <i class="ri-check-line fs-15"></i>
                                <span>บันทึกสถานะ</span>
                            </button>
                        </form>
                        <p class="text-muted fs-12 mt-2 mb-0 text-center">เมื่อ Lead ผ่านเกณฑ์แล้ว กรุณาใช้ปุ่ม Convert เป็น
                            Opportunity</p>
                    </div>

                    <!-- Card 3: มอบหมาย -->
                    <div class="emp-card-main">
                        <div class="section-header-title">
                            <i class="ri-user-shared-line"></i>
                            <span>มอบหมาย</span>
                        </div>
                        <form method="post" action="{{ route('office.leads.assign', $lead) }}" novalidate>
                            @csrf
                            <div class="mb-2.5">
                                <label class="filter-label-emp">สาขา</label>
                                @php renderLeadCustomSelectShow('business_branch_id', $branchOptsMap, (string)$lead->business_branch_id, null); @endphp
                            </div>
                            <div class="mb-2.5 mt-2">
                                <label class="filter-label-emp">ผู้รับผิดชอบ</label>
                                @php renderLeadCustomSelectShow('assigned_to', $empOptsMap, (string)$lead->assigned_to, 'คิวส่วนกลาง'); @endphp
                            </div>
                            <div class="mb-3 mt-2">
                                <label class="filter-label-emp">เหตุผลการมอบหมาย</label>
                                <input type="text" name="note" class="form-control filter-input-emp"
                                    placeholder="กรอกเหตุผล...">
                            </div>
                            <button class="btn-add-emp w-100" type="submit">
                                <i class="ri-user-shared-line fs-15"></i>
                                <span>มอบหมาย</span>
                            </button>
                        </form>
                    </div>
                @endunless
            </div>

            <!-- Right Main Column -->
            <div class="col-xl-8 col-lg-7">
                <div class="emp-card-main">
                    <!-- Navigation Tabs -->
                    <ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab-act" role="tab">
                                <i class="ri-history-line me-1"></i> กิจกรรม
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-people" role="tab">
                                <i class="ri-contacts-line me-1"></i> ผู้ติดต่อ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-hist" role="tab">
                                <i class="ri-time-line me-1"></i> ประวัติ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab-journey" role="tab">
                                <i class="ri-git-branch-line me-1"></i> ไทม์ไลน์งานขาย
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Tab 1: กิจกรรม -->
                        <div class="tab-pane active" id="tab-act" role="tabpanel">
                            @unless ($lead->isLocked())
                                <div class="bg-pastel-blue p-3 mb-3">
                                    <div class="fw-bold text-dark mb-2.5 fs-13">
                                        <i class="ri-add-circle-line me-1 text-primary"></i> บันทึกกิจกรรมใหม่
                                    </div>
                                    <form method="post" action="{{ route('office.leads.activities.store', $lead) }}"
                                        novalidate>
                                        @csrf
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-3">
                                                <label class="filter-label-emp">ประเภทกิจกรรม</label>
                                                @php renderLeadCustomSelectShow('type', $activityTypeOptsMap, 'call', null); @endphp
                                            </div>
                                            <div class="col-md-3">
                                                <label class="filter-label-emp">ทิศทาง</label>
                                                @php renderLeadCustomSelectShow('direction', ['outbound' => 'ออก', 'inbound' => 'เข้า'], '', 'ทิศทาง'); @endphp
                                            </div>
                                            <div class="col-md-3">
                                                <label class="filter-label-emp">วัน/เวลา เกิดขึ้น</label>
                                                <div class="birthday-icon-wrapper">
                                                    <input type="text" name="occurred_at"
                                                        class="form-control filter-input-emp flatpickr-datetime"
                                                        value="{{ now()->format('Y-m-d H:i') }}" required
                                                        placeholder="วัน/เวลา">
                                                    <i class="ri-calendar-line birthday-icon"></i>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="filter-label-emp">คนที่คุย</label>
                                                @php renderLeadCustomSelectShow('lead_contact_id', $contactOptsMap, '', 'คนที่คุย'); @endphp
                                            </div>
                                            <div class="col-12">
                                                <label class="filter-label-emp">สรุปรายละเอียด <span
                                                        class="text-danger">*</span></label>
                                                <textarea name="summary" class="form-control filter-input-emp" rows="2"
                                                    placeholder="สรุปบทสนทนาหรือกิจกรรมที่เกิดขึ้น..." required></textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="filter-label-emp">ผลการติดต่อ</label>
                                                <input type="text" name="outcome" class="form-control filter-input-emp"
                                                    placeholder="เช่น ถึงคน / ไม่รับ">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="filter-label-emp">งานถัดไป</label>
                                                <input type="text" name="next_action"
                                                    class="form-control filter-input-emp" placeholder="ระบุงานถัดไป...">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="filter-label-emp">กำหนดงานถัดไป</label>
                                                <div class="birthday-icon-wrapper">
                                                    <input type="text" name="next_action_at"
                                                        class="form-control filter-input-emp flatpickr-datetime"
                                                        placeholder="วัน/เวลา">
                                                    <i class="ri-calendar-line birthday-icon"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <button class="btn-add-emp" type="submit">
                                                <i class="ri-check-line fs-15"></i>
                                                <span>บันทึกกิจกรรม</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endunless

                            <!-- Timeline Activities -->
                            <div class="acitivity-timeline py-2">
                                @forelse ($lead->activities as $act)
                                    @php
                                        $typeConfigs = [
                                            'call' => ['border' => '#2F53B4'],
                                            'chat' => ['border' => '#10B981'],
                                            'meeting' => ['border' => '#F59E0B'],
                                            'email' => ['border' => '#06B6D4'],
                                            'note' => ['border' => '#64748B'],
                                            'task' => ['border' => '#8B5CF6'],
                                        ];
                                        $cfg = $typeConfigs[$act->type] ?? ['border' => '#2F53B4'];
                                    @endphp
                                    <div class="mb-3 bg-white border border-slate-200 overflow-hidden shadow-sm"
                                        style="border-radius: 16px;">
                                        <!-- Top Header Bar (No icons, clean text, harmonious colors) -->
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 bg-light-subtle border-bottom border-light"
                                            style="padding: 10px 24px;">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <!-- Activity Type (No Icon, clean text) -->
                                                <div class="d-inline-flex align-items-center fs-13 fw-bold text-dark">
                                                    <span>{{ \App\Models\Lead\LeadActivity::TYPE_LABELS[$act->type] ?? $act->type }}</span>
                                                    @if ($act->direction)
                                                        <span class="text-dark me-1">:</span><span
                                                            class="fw-normal text-muted">{{ $act->direction == 'outbound' ? 'ออก' : 'เข้า' }}</span>
                                                    @endif
                                                </div>

                                                @if ($act->contact)
                                                    <span class="text-muted opacity-40 mx-2">•</span>
                                                    <div class="fs-13 d-inline-flex align-items-center">
                                                        <span class="text-muted me-1">คุยกับ:</span>
                                                        <span
                                                            class="fw-bold text-dark">{{ $act->contact->displayName() }}</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Right Side: Time, Performer & Distinct Delete Button -->
                                            <div class="d-flex align-items-center gap-3 ms-auto">
                                                <div class="d-inline-flex align-items-center gap-2 fs-12 text-muted">
                                                    <span>{{ $act->occurred_at?->format('d/m/Y H:i') }}</span>
                                                    @if ($act->performer)
                                                        <span class="text-muted opacity-40">•</span>
                                                        <span
                                                            class="fw-medium text-dark">{{ $act->performer->displayName() }}</span>
                                                    @endif
                                                </div>

                                                @unless ($lead->isLocked())
                                                    <form method="POST"
                                                        action="{{ route('office.leads.activities.destroy', [$lead, $act]) }}"
                                                        class="d-inline-flex align-items-center"
                                                        onsubmit="return confirm('คุณต้องการลบกิจกรรมนี้หรือไม่?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-action-item btn-action-delete"
                                                            title="ลบกิจกรรม">
                                                            <i class="ri-delete-bin-line"></i>
                                                        </button>
                                                    </form>
                                                @endunless
                                            </div>
                                        </div>

                                        <!-- Body Content -->
                                        <div style="padding: 14px 24px;">
                                            <div class="fs-12 fw-semibold text-muted mb-1">สรุปรายละเอียด</div>
                                            <div class="fs-13 text-dark mb-0"
                                                style="white-space: pre-line; line-height: 1.65;">{{ $act->summary }}
                                            </div>
                                            @if ($act->outcome || $act->next_action)
                                                <div class="d-flex flex-wrap align-items-center gap-3 border-top border-light-subtle fs-13"
                                                    style="margin-top: 12px; padding-top: 12px;">
                                                    @if ($act->outcome)
                                                        <div class="d-inline-flex align-items-center">
                                                            <span class="text-muted me-1">ผลการติดต่อ:</span>
                                                            <span class="fw-bold text-dark">{{ $act->outcome }}</span>
                                                        </div>
                                                    @endif
                                                    @if ($act->outcome && $act->next_action)
                                                        <span class="text-muted opacity-40">•</span>
                                                    @endif
                                                    @if ($act->next_action)
                                                        <div class="d-inline-flex align-items-center">
                                                            <span class="text-muted me-1">งานถัดไป:</span>
                                                            <span class="fw-bold text-dark">{{ $act->next_action }}</span>
                                                            @if ($act->next_action_at)
                                                                <span
                                                                    class="text-muted fw-normal ms-1">({{ $act->next_action_at->format('d/m/Y H:i') }})</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center text-muted py-4 fs-13">
                                        <i class="ri-inbox-line fs-22 d-block mb-1 text-muted"></i>
                                        ยังไม่มีกิจกรรม
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Tab 2: ผู้ติดต่อ -->
                        <div class="tab-pane" id="tab-people" role="tabpanel">
                            <div class="table-responsive rounded-3 border mb-3">
                                <table class="table align-middle mb-0" style="width: 100%;">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3 py-2.5" style="width: 30%;">ชื่อ - นามสกุล</th>
                                            <th class="py-2.5" style="width: 25%;">โทรศัพท์</th>
                                            <th class="py-2.5" style="width: 25%;">อีเมล</th>
                                            <th class="py-2.5" style="width: 20%;">LINE ID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($lead->contacts as $p)
                                            <tr>
                                                <td class="ps-3 py-2.5">
                                                    <span class="fw-bold text-dark fs-13">{{ $p->displayName() }}</span>
                                                    @if ($p->is_primary)
                                                        <span
                                                            class="badge bg-success-subtle text-success ms-1 fs-11">หลัก</span>
                                                    @endif
                                                    @if ($p->position)
                                                        <div class="text-muted fs-12">{{ $p->position }}</div>
                                                    @endif
                                                </td>
                                                <td class="text-dark fs-13">{{ $p->phone ?: '-' }}</td>
                                                <td class="text-dark fs-13">{{ $p->email ?: '-' }}</td>
                                                <td class="text-dark fs-13">{{ $p->line_id ?: '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3 fs-13">
                                                    ไม่มีข้อมูลผู้ติดต่อ
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @unless ($lead->isLocked())
                                <div class="bg-pastel-blue p-3">
                                    <div class="fw-bold text-dark mb-2.5 fs-13">
                                        <i class="ri-user-add-line me-1 text-primary"></i> เพิ่มผู้ติดต่อใหม่
                                    </div>
                                    <form method="post" action="{{ route('office.leads.contacts.store', $lead) }}"
                                        novalidate>
                                        @csrf
                                        <div class="row g-2 mb-2">
                                            <div class="col-md-4">
                                                <label class="filter-label-emp">ชื่อ <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="firstname" class="form-control filter-input-emp"
                                                    placeholder="ชื่อ" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="filter-label-emp">นามสกุล</label>
                                                <input type="text" name="surname" class="form-control filter-input-emp"
                                                    placeholder="นามสกุล">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="filter-label-emp">เบอร์โทรศัพท์</label>
                                                <input type="text" name="phone" class="form-control filter-input-emp"
                                                    placeholder="เบอร์โทรศัพท์">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="filter-label-emp">อีเมล</label>
                                                <input type="email" name="email" class="form-control filter-input-emp"
                                                    placeholder="อีเมล">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="filter-label-emp">LINE ID</label>
                                                <input type="text" name="line_id" class="form-control filter-input-emp"
                                                    placeholder="LINE ID">
                                            </div>
                                            <div class="col-md-4 d-flex align-items-end mb-1">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="is_primary"
                                                        id="is_primary" value="1">
                                                    <label class="form-check-label filter-label-emp mb-0 ms-1"
                                                        for="is_primary">ตั้งเป็นผู้ติดต่อหลัก</label>
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn-add-emp" type="submit">
                                            <i class="ri-add-line fs-15"></i>
                                            <span>เพิ่มผู้ติดต่อ</span>
                                        </button>
                                    </form>
                                </div>
                            @endunless
                        </div>

                        <!-- Tab 3: ประวัติ -->
                        <div class="tab-pane" id="tab-hist" role="tabpanel">
                            <!-- Section 1: ประวัติสถานะ -->
                            <h6 class="fw-bold text-dark mb-2 fs-13">ประวัติการเปลี่ยนสถานะ</h6>
                            <div class="mb-4">
                                @forelse ($lead->statusLogs as $log)
                                    <div class="d-flex align-items-center justify-content-between py-1.5">
                                        <div class="fs-13 text-dark">
                                            @php
                                                $fromLabel = $log->from_status
                                                    ? \App\Models\Lead\Lead::STATUS_LABELS[$log->from_status] ??
                                                        $log->from_status
                                                    : null;
                                                $toLabel =
                                                    \App\Models\Lead\Lead::STATUS_LABELS[$log->to_status] ??
                                                    $log->to_status;
                                            @endphp

                                            @if ($fromLabel && $log->from_status !== $log->to_status)
                                                <span class="text-muted">{{ $fromLabel }}</span>
                                                <i class="ri-arrow-right-line mx-1.5 text-muted fs-12"></i>
                                                <span class="fw-bold text-primary">{{ $toLabel }}</span>
                                            @else
                                                <span class="text-muted">สถานะ:</span>
                                                <span class="fw-bold text-primary ms-1">{{ $toLabel }}</span>
                                            @endif

                                            @if ($log->reason)
                                                <span class="text-muted fs-12 ms-2">(เหตุผล:
                                                    {{ $log->reason->name }})</span>
                                            @endif
                                        </div>
                                        <div class="text-muted fs-12">
                                            {{ $log->created_at?->format('d/m/Y H:i') }} @if ($log->changedBy)
                                                · {{ $log->changedBy->displayName() }}
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted fs-13 py-1">— ไม่มีประวัติสถานะ —</div>
                                @endforelse
                            </div>

                            <!-- Section 2: ประวัติการมอบหมาย -->
                            <h6 class="fw-bold text-dark mb-2 fs-13">ประวัติการมอบหมาย</h6>
                            <div class="mb-4">
                                @forelse ($lead->assignments as $as)
                                    <div class="d-flex align-items-center justify-content-between py-1.5">
                                        <div class="fs-13 text-dark">
                                            @if ($as->reason && $as->reason !== 'created')
                                                <span class="text-muted">{{ $as->reason }}</span>
                                                <i class="ri-arrow-right-line mx-1.5 text-muted fs-12"></i>
                                            @elseif ($as->reason === 'created')
                                                <span class="text-muted">มอบหมายแรกเริ่ม:</span>
                                            @endif
                                            <span
                                                class="fw-bold text-primary ms-1">{{ $as->toAssignee?->displayName() ?? 'คิวส่วนกลาง' }}</span>
                                        </div>
                                        <div class="text-muted fs-12">
                                            {{ $as->created_at?->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted fs-13 py-1">— ไม่มีประวัติการมอบหมาย —</div>
                                @endforelse
                            </div>

                            <!-- Section 3: Inbound Touch -->
                            <h6 class="fw-bold text-dark mb-2 fs-13">Inbound Touch</h6>
                            <div>
                                @forelse ($lead->inboundTouches as $t)
                                    <div class="d-flex align-items-center justify-content-between py-1.5">
                                        <div class="fs-13 text-dark">
                                            ช่องทาง: <span
                                                class="fw-bold text-primary">{{ $t->channel?->name ?: 'ไม่ระบุ' }}</span>
                                        </div>
                                        <div class="text-muted fs-12">
                                            {{ $t->contacted_at?->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-muted fs-13 py-1">— ไม่มีข้อมูล Inbound Touch —</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Tab 4: ไทม์ไลน์งานขาย -->
                        <div class="tab-pane" id="tab-journey" role="tabpanel">
                            @include('office.partials.sales-journey-timeline', ['journey' => $salesJourney ?? []])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
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

                flatpickr('.flatpickr-datetime', {
                    locale: thLocale,
                    enableTime: true,
                    time_24hr: true,
                    dateFormat: 'Y-m-d H:i',
                    altInput: true,
                    altFormat: 'd/m/Y H:i',
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

            // Custom Select Combo Handlers
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

                    if (val === '' || val === null || text === $input.attr('placeholder')) {
                        $input.addClass('is-placeholder');
                    } else {
                        $input.removeClass('is-placeholder');
                    }

                    $combo.removeClass('active');
                    $popup.hide();
                });
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
