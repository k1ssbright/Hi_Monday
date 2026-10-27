@extends('office.layout.office')
@section('title', ($opportunity->exists ? 'แก้ไข Opportunity' : 'สร้าง Opportunity') . ' | Hi Monday')

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

        /* Custom Dropdown Combo Component */
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

        /* Birthday / Calendar Icon Wrapper */
        .birthday-icon-wrapper {
            position: relative;
            width: 100%;
        }

        .birthday-icon-wrapper .birthday-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #2F53B4;
            pointer-events: none;
            z-index: 5;
        }

        .birthday-icon-wrapper input.filter-input-emp {
            padding-right: 36px !important;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        @include('office.settings.partials.page-title', [
            'section' => 'งานขาย',
            'parent' => 'Opportunities',
            'parentUrl' => route('office.opportunities.index'),
            'title' => $opportunity->exists ? 'แก้ไข Opportunity' : 'สร้าง Opportunity โดยตรง',
        ])
        @include('office.settings.partials.flash')

        <form method="post" action="{{ $opportunity->exists ? route('office.opportunities.update', $opportunity) : route('office.opportunities.store') }}" novalidate>
            @csrf
            @if ($opportunity->exists)
                @method('PUT')
            @endif
            <div class="row g-4">
                <!-- Left Column: Opportunity Details -->
                <div class="col-lg-8">
                    <div class="emp-card-main">
                        <div class="section-header-title">
                            <i class="ri-briefcase-line"></i>
                            <span>ข้อมูล Opportunity</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="filter-label-emp">
                                    <i class="ri-file-text-line"></i> ชื่อ Opportunity <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="name" class="form-control filter-input-emp"
                                    value="{{ old('name', $opportunity->name) }}" placeholder="ระบุชื่อ Opportunity" required>
                                @error('name')
                                    <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="filter-label-emp">
                                    <i class="ri-money-dollar-circle-line"></i> สกุลเงิน
                                </label>
                                <input type="text" name="currency" class="form-control filter-input-emp"
                                    value="{{ old('currency', $opportunity->currency ?: 'THB') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="filter-label-emp">
                                    <i class="ri-user-star-line"></i> ลูกค้า (Partner) <span class="text-muted fw-normal fs-12 ms-1">(ไม่บังคับ)</span>
                                </label>
                                @php
                                    $selectedPartnerId = old('partner_id', $opportunity->partner_id);
                                    $selectedPartnerName = 'เลือกลูกค้า (ถ้ามี)';
                                    foreach ($partners as $partner) {
                                        if ((string)$selectedPartnerId === (string)$partner->id) {
                                            $selectedPartnerName = $partner->partner_name_th;
                                            break;
                                        }
                                    }
                                @endphp
                                <div class="custom-fp-combo custom-dropdown-combo">
                                    <input type="text"
                                        class="form-control filter-input-emp dropdown-combo-input {{ empty($selectedPartnerId) ? 'is-placeholder' : '' }}"
                                        id="partner_display" placeholder="เลือกลูกค้า (ถ้ามี)"
                                        value="{{ $selectedPartnerName }}" readonly autocomplete="off">
                                    <b class="custom-fp-combo-arrow"></b>
                                    <div class="custom-dropdown-popup">
                                        <div class="custom-dropdown-item {{ empty($selectedPartnerId) ? 'selected' : '' }}"
                                            data-val="">เลือกลูกค้า (ถ้ามี)</div>
                                        @foreach ($partners as $partner)
                                            <div class="custom-dropdown-item {{ (string)$selectedPartnerId === (string)$partner->id ? 'selected' : '' }}"
                                                data-val="{{ $partner->id }}">{{ $partner->partner_name_th }}</div>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="partner_id" id="partner_id_select" value="{{ $selectedPartnerId }}">
                                </div>
                                @error('partner_id')
                                    <div class="text-danger fs-12 mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="filter-label-emp">
                                    <i class="ri-user-add-line"></i> หรือสร้างลูกค้าด่วน <span class="text-muted fw-normal fs-12 ms-1">(ไม่ต้องตั้งค่า master)</span>
                                </label>
                                <input type="text" name="partner_name_quick" class="form-control filter-input-emp"
                                    value="{{ old('partner_name_quick') }}" placeholder="ชื่อลูกค้า / บริษัท">
                            </div>

                            <div class="col-md-6">
                                <label class="filter-label-emp">
                                    <i class="ri-calendar-event-line"></i> วันที่คาดปิด
                                </label>
                                <div class="birthday-icon-wrapper">
                                    <input type="text" name="expected_close_date"
                                        class="form-control filter-input-emp flatpickr-date"
                                        placeholder="วว/ดด/ปปปป"
                                        value="{{ old('expected_close_date', optional($opportunity->expected_close_date)->format('Y-m-d') ?? now()->addDays(30)->format('Y-m-d')) }}" autocomplete="off">
                                    <i class="ri-calendar-line birthday-icon"></i>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="filter-label-emp">
                                    <i class="ri-coin-line"></i> มูลค่าประมาณการ
                                </label>
                                <input type="number" step="0.01" min="0" name="estimated_value"
                                    class="form-control filter-input-emp" value="{{ old('estimated_value', $opportunity->estimated_value ?? 0) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Initial Line Item & Submit -->
                <div class="col-lg-4">
                    <div class="emp-card-main">
                        <div class="section-header-title">
                            <i class="ri-shopping-bag-3-line"></i>
                            <span>รายการงานเริ่มต้น</span>
                        </div>
                        <p class="text-muted fs-12 mb-3">ไม่บังคับ — สามารถเพิ่มหรือแก้ไขทีหลังใน Quotation ได้</p>

                        <div class="mb-3">
                            <label class="filter-label-emp">
                                <i class="ri-box-3-line"></i> สินค้า/บริการ (ถ้ามีในระบบ)
                            </label>
                            @php
                                $selectedItemId = old('line_item_id');
                                $selectedItemName = 'ไม่ระบุ';
                                foreach ($items as $item) {
                                    if ((string)$selectedItemId === (string)$item->id) {
                                        $selectedItemName = $item->name;
                                        break;
                                    }
                                }
                            @endphp
                            <div class="custom-fp-combo custom-dropdown-combo">
                                <input type="text"
                                    class="form-control filter-input-emp dropdown-combo-input {{ empty($selectedItemId) ? 'is-placeholder' : '' }}"
                                    id="item_display" placeholder="ไม่ระบุ" value="{{ $selectedItemName }}" readonly autocomplete="off">
                                <b class="custom-fp-combo-arrow"></b>
                                <div class="custom-dropdown-popup">
                                    <div class="custom-dropdown-item {{ empty($selectedItemId) ? 'selected' : '' }}"
                                        data-val="">ไม่ระบุ</div>
                                    @foreach ($items as $item)
                                        <div class="custom-dropdown-item {{ (string)$selectedItemId === (string)$item->id ? 'selected' : '' }}"
                                            data-val="{{ $item->id }}">{{ $item->name }}</div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="line_item_id" id="line_item_id_select" value="{{ old('line_item_id') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="filter-label-emp">
                                <i class="ri-align-left"></i> คำอธิบาย
                            </label>
                            <textarea name="line_description" class="form-control filter-input-emp" rows="3"
                                placeholder="ไม่บังคับ">{{ old('line_description') }}</textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-6">
                                <label class="filter-label-emp">
                                    <i class="ri-hashtag"></i> จำนวน
                                </label>
                                <input type="number" step="0.01" min="0.01" name="line_qty"
                                    class="form-control filter-input-emp" value="{{ old('line_qty', 1) }}">
                            </div>
                            <div class="col-6">
                                <label class="filter-label-emp">
                                    <i class="ri-price-tag-3-line"></i> ราคา/หน่วย
                                </label>
                                <input type="number" step="0.01" min="0" name="line_unit_price"
                                    class="form-control filter-input-emp" value="{{ old('line_unit_price', 0) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Action Buttons -->
                    <div class="d-flex align-items-center justify-content-end gap-2 mt-3">
                        <a href="{{ route('office.opportunities.index') }}" class="btn-sec-emp">
                            <i class="ri-arrow-left-line"></i>
                            <span>ยกเลิก</span>
                        </a>
                        <button type="submit" class="btn-add-emp">
                            <i class="ri-check-line fs-16"></i>
                            <span>บันทึก Opportunity</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
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
            // Initialize Custom Dropdowns
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
                    if (val === "") {
                        $input.addClass('is-placeholder');
                    } else {
                        $input.removeClass('is-placeholder');
                    }
                    $hidden.val(val).trigger('change');
                    $popup.find('.custom-dropdown-item').removeClass('selected');
                    $(this).addClass('selected');

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

            // Initialize Custom Flatpickr Datepicker
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
        });
    </script>
@endsection
