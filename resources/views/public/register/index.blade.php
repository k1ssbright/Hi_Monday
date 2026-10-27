<!doctype html>
<html lang="th">

<head>
    <meta charset="utf-8" />
    <title>Register | Hi Monday</title>
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
        .password-req-box {
            margin-top: 8px;
        }

        .password-req-title {
            font-weight: 600;
            font-size: 13px;
            color: #475569;
            margin-bottom: 8px;
        }

        .password-req-list {
            list-style: none;
            padding-left: 0;
            margin-bottom: 0;
        }

        .password-req-list .req-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            margin-bottom: 4px;
            transition: color 0.2s ease;
        }

        .password-req-list .req-item:last-child {
            margin-bottom: 0;
        }

        .password-req-list .req-item.invalid {
            color: #dc3545;
        }

        .password-req-list .req-item.valid {
            color: #198754;
        }

        .password-req-list .req-icon {
            font-size: 15px;
            font-weight: bold;
        }

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
            font-size: 14px !important;
            color: #1E293B !important;
            cursor: pointer;
            user-select: none;
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

    <main class="register-container">
        <div class="d-none">Register | Hi Monday</div>

        <div class="form-card">
            <form action="{{ route('register.step1') }}" method="POST" id="registerForm">
                @csrf

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

                <!-- Section 1: ข้อมูลส่วนตัว (Personal Information) -->
                <div class="form-section">
                    <h2 class="section-heading">
                        <i class="ri-user-line"></i>
                        ข้อมูลส่วนตัว
                    </h2>

                    <div class="row g-3">
                        <!-- คำนำหน้า (Radio Buttons) -->
                        <div class="col-12 form-group-custom">
                            <label class="form-label-custom">
                                คำนำหน้า <span class="required-star">*</span>
                            </label>
                            <div class="d-flex flex-wrap align-items-center gap-4 mt-1">
                                <div class="form-check form-check-inline me-0">
                                    <input class="form-check-input prefix-radio" type="radio" name="prefix_radio"
                                        id="prefix_mr" value="นาย"
                                        {{ old('prefix', 'นาย') == 'นาย' ? 'checked' : '' }}>
                                    <label class="form-check-label text-main" for="prefix_mr">นาย</label>
                                </div>
                                <div class="form-check form-check-inline me-0">
                                    <input class="form-check-input prefix-radio" type="radio" name="prefix_radio"
                                        id="prefix_mrs" value="นาง" {{ old('prefix') == 'นาง' ? 'checked' : '' }}>
                                    <label class="form-check-label text-main" for="prefix_mrs">นาง</label>
                                </div>
                                <div class="form-check form-check-inline me-0">
                                    <input class="form-check-input prefix-radio" type="radio" name="prefix_radio"
                                        id="prefix_ms" value="นางสาว" {{ old('prefix') == 'นางสาว' ? 'checked' : '' }}>
                                    <label class="form-check-label text-main" for="prefix_ms">นางสาว</label>
                                </div>
                                <div class="form-check form-check-inline me-0">
                                    <input class="form-check-input prefix-radio" type="radio" name="prefix_radio"
                                        id="prefix_other" value="อื่นๆ"
                                        {{ (old('prefix') && !in_array(old('prefix'), ['นาย', 'นาง', 'นางสาว'])) || old('prefix') == 'อื่นๆ' ? 'checked' : '' }}>
                                    <label class="form-check-label text-main" for="prefix_other">อื่นๆ</label>
                                </div>
                                <input type="text" id="prefix_other_text" class="custom-input prefix-other-text"
                                    placeholder="กรอกคำนำหน้า"
                                    value="{{ !in_array(old('prefix'), ['นาย', 'นาง', 'นางสาว']) ? old('prefix') : '' }}"
                                    style="max-width: 180px; height: 38px; font-size: 13px;">
                            </div>
                            <input type="hidden" name="prefix" id="prefix_final_input"
                                value="{{ old('prefix', 'นาย') }}">
                            @error('prefix')
                                <div class="field-error-msg">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- ชื่อ -->
                        <div class="col-md-6 col-12 form-group-custom">
                            <label class="form-label-custom">
                                ชื่อ <span class="required-star">*</span>
                            </label>
                            <input type="text" name="firstname"
                                class="custom-input @error('firstname') is-invalid @enderror"
                                value="{{ old('firstname') }}" placeholder="กรอกชื่อจริง">
                            @error('firstname')
                                <div class="field-error-msg">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- นามสกุล -->
                        <div class="col-md-6 col-12 form-group-custom">
                            <label class="form-label-custom">
                                นามสกุล <span class="required-star">*</span>
                            </label>
                            <input type="text" name="surname"
                                class="custom-input @error('surname') is-invalid @enderror"
                                value="{{ old('surname') }}" placeholder="กรอกนามสกุล">
                            @error('surname')
                                <div class="field-error-msg">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- เลขประจำตัวผู้เสียภาษี -->
                        <div class="col-md-6 col-12 form-group-custom">
                            <label class="form-label-custom">
                                เลขประจำตัวผู้เสียภาษี <span class="required-star">*</span>
                            </label>
                            <input type="text" name="tax_no" id="tax_no" maxlength="13"
                                class="custom-input @error('tax_no') is-invalid @enderror"
                                value="{{ old('tax_no') }}" placeholder="กรอกเลขประจำตัวผู้เสียภาษี">
                            @error('tax_no')
                                <div class="field-error-msg">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- วันเกิด -->
                        <div class="col-md-6 col-12 form-group-custom">
                            <label class="form-label-custom">
                                วันเกิด <span class="required-star">*</span>
                            </label>
                            <div class="birthday-icon-wrapper">
                                <input type="text" name="birthday" id="birthday"
                                    class="custom-input flatpickr-birthday @error('birthday') is-invalid @enderror"
                                    value="{{ old('birthday') }}" placeholder="วว/ดด/ปปปป">
                                <i class="ri-calendar-line birthday-icon"></i>
                            </div>
                            @error('birthday')
                                <div class="field-error-msg">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: ข้อมูลการติดต่อ & ที่อยู่แยกช่อง พร้อม Auto-Complete -->
                <div class="form-section mt-4">
                    <h2 class="section-heading">
                        <i class="ri-chat-3-line"></i>
                        ข้อมูลการติดต่อ
                    </h2>

                    <div class="row g-3">
                        <!-- Email -->
                        <div class="col-md-6 col-12 form-group-custom">
                            <label class="form-label-custom">
                                Email <span class="required-star">*</span>
                            </label>
                            <input type="email" name="email"
                                class="custom-input @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                placeholder="กรอกอีเมล">
                            @error('email')
                                <div class="field-error-msg">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- โทรศัพท์ -->
                        <div class="col-md-6 col-12 form-group-custom">
                            <label class="form-label-custom">
                                โทรศัพท์ <span class="required-star">*</span>
                            </label>
                            <input type="text" name="phone"
                                class="custom-input @error('phone') is-invalid @enderror" value="{{ old('phone') }}"
                                placeholder="กรอกเบอร์โทรศัพท์">
                            @error('phone')
                                <div class="field-error-msg">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- ที่อยู่ -->
                        <div class="col-12 form-group-custom">
                            <label class="form-label-custom">
                                ที่อยู่ <span class="required-star">*</span>
                            </label>
                            <input type="text" name="address"
                                class="custom-input @error('address') is-invalid @enderror"
                                value="{{ old('address') }}" placeholder="กรอกที่อยู่ เช่น เลขที่ ถนน">
                            @error('address')
                                <div class="field-error-msg">
                                    <i class="ri-error-warning-line"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-12 p-0 m-0">
                            @include('partials.thailand-address-autocomplete', [
                                'selectedProvinceName' => $selectedProvinceName ?? '',
                                'selectedAmphureName' => $selectedAmphureName ?? '',
                                'selectedTambonName' => $selectedTambonName ?? '',
                                'selectedZipCode' => $selectedZipCode ?? '',
                                'idPrefix' => 'register',
                                'variant' => 'public',
                                'required' => true,
                                'rowClass' => 'row g-3 m-0 p-0',
                            ])
                        </div>
                    </div>
                </div>

                <div class="form-section mt-4">
                    <h2 class="section-heading">
                        <i class="ri-lock-password-line"></i>
                        รหัสผ่าน
                    </h2>

                    <div class="row g-3">
                        <div class="col-md-6 col-12 form-group-custom">
                            <label class="form-label-custom">
                                รหัสผ่าน <span class="required-star">*</span>
                            </label>
                            <div class="position-relative">
                                <input type="password" name="password" id="password-input"
                                    class="custom-input pe-5 @error('password') is-invalid @enderror"
                                    placeholder="กรอกรหัสผ่าน" autocomplete="new-password" required>
                                <button
                                    class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted password-toggle"
                                    type="button" data-target="password-input" aria-label="แสดงรหัสผ่าน">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-md-6 col-12 form-group-custom">
                            <label class="form-label-custom">
                                ยืนยันรหัสผ่าน <span class="required-star">*</span>
                            </label>
                            <div class="position-relative">
                                <input type="password" name="password_confirmation" id="password-confirmation-input"
                                    class="custom-input pe-5 @error('password_confirmation') is-invalid @enderror"
                                    placeholder="กรอกรหัสผ่านอีกครั้ง" autocomplete="new-password" required>
                                <button
                                    class="btn btn-link position-absolute top-50 end-0 translate-middle-y text-muted password-toggle"
                                    type="button" data-target="password-confirmation-input"
                                    aria-label="แสดงรหัสผ่าน">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="password-req-box">
                                <div class="password-req-title">ข้อกำหนดรหัสผ่าน:</div>
                                <ul class="password-req-list">
                                    <li id="req-length" class="req-item invalid">
                                        <i class="ri-close-line req-icon"></i>
                                        <span>มีอย่างน้อย 8 ตัวอักษร</span>
                                    </li>
                                    <li id="req-uppercase" class="req-item invalid">
                                        <i class="ri-close-line req-icon"></i>
                                        <span>มีตัวพิมพ์ใหญ่ (A-Z)</span>
                                    </li>
                                    <li id="req-lowercase" class="req-item invalid">
                                        <i class="ri-close-line req-icon"></i>
                                        <span>มีตัวพิมพ์เล็ก (a-z)</span>
                                    </li>

                                    <li id="req-special" class="req-item invalid">
                                        <i class="ri-close-line req-icon"></i>
                                        <span>มีอักขระพิเศษ (!@#$%^&*)</span>
                                    </li>
                                    <li id="req-match" class="req-item invalid">
                                        <i class="ri-close-line req-icon"></i>
                                        <span>รหัสผ่านและการยืนยันรหัสผ่านตรงกัน</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Footer Buttons (Matching Screenshot) -->
                <div class="d-flex align-items-center gap-3 mt-4 pt-3">
                    <button type="submit" class="btn-submit-blue flex-grow-1">
                        ถัดไป
                        <i class="ri-arrow-right-line fs-18"></i>
                    </button>

                    <a href="{{ route('login') }}" class="btn-back-white">
                        ย้อนกลับ
                    </a>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer Bar -->
    <footer class="register-footer">
        © 2024 Hi Monday Enterprise. All rights reserved.
    </footer>

    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('plugins/select2-4.1.0-rc.0/dist/js/select2.min.js') }}"></script>
    <!-- Flatpickr JS & Thai Locale -->
    <script src="{{ asset('assets/libs/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('assets/libs/flatpickr/l10n/th.js') }}"></script>

    <script>
        // Restrict tax_no input to digits only (max 13 digits, no hyphens)
        const taxInput = document.getElementById('tax_no');
        if (taxInput) {
            taxInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 13) value = value.slice(0, 13);
                e.target.value = value;
            });
        }

        document.querySelectorAll('.password-toggle').forEach(function(button) {
            button.addEventListener('click', function() {
                const targetId = button.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = button.querySelector('i');
                if (!input) return;

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('ri-eye-line', 'ri-eye-off-line');
                } else {
                    input.type = 'password';
                    icon.classList.replace('ri-eye-off-line', 'ri-eye-line');
                }
            });
        });

        // Real-time Password Requirement Validation
        const passwordInput = document.getElementById('password-input');
        const passwordConfirmInput = document.getElementById('password-confirmation-input');

        if (passwordInput) {
            function validatePasswordRequirements() {
                const val = passwordInput.value;
                const confirmVal = passwordConfirmInput ? passwordConfirmInput.value : '';

                const checks = {
                    'req-length': val.length >= 8,
                    'req-uppercase': /[A-Z]/.test(val),
                    'req-lowercase': /[a-z]/.test(val),
                    'req-number': /[0-9]/.test(val),
                    'req-special': /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(val),
                    'req-match': val.length > 0 && confirmVal.length > 0 && val === confirmVal
                };

                for (const [id, isMet] of Object.entries(checks)) {
                    const el = document.getElementById(id);
                    if (!el) continue;
                    const icon = el.querySelector('.req-icon');
                    if (isMet) {
                        el.classList.remove('invalid');
                        el.classList.add('valid');
                        if (icon) {
                            icon.className = 'ri-check-line req-icon';
                        }
                    } else {
                        el.classList.remove('valid');
                        el.classList.add('invalid');
                        if (icon) {
                            icon.className = 'ri-close-line req-icon';
                        }
                    }
                }

                // Dynamic Input Border Colors
                const isPasswordValid = checks['req-length'] && checks['req-uppercase'] && checks['req-lowercase'] &&
                    checks['req-number'] && checks['req-special'];
                if (val.length > 0) {
                    if (isPasswordValid) {
                        passwordInput.classList.remove('is-invalid');
                        passwordInput.classList.add('is-valid');
                    } else {
                        passwordInput.classList.remove('is-valid');
                        passwordInput.classList.add('is-invalid');
                    }
                } else {
                    passwordInput.classList.remove('is-valid', 'is-invalid');
                }

                if (passwordConfirmInput) {
                    if (confirmVal.length > 0) {
                        if (checks['req-match']) {
                            passwordConfirmInput.classList.remove('is-invalid');
                            passwordConfirmInput.classList.add('is-valid');
                        } else {
                            passwordConfirmInput.classList.remove('is-valid');
                            passwordConfirmInput.classList.add('is-invalid');
                        }
                    } else {
                        passwordConfirmInput.classList.remove('is-valid', 'is-invalid');
                    }
                }
            }

            passwordInput.addEventListener('input', validatePasswordRequirements);
            if (passwordConfirmInput) {
                passwordConfirmInput.addEventListener('input', validatePasswordRequirements);
            }
            validatePasswordRequirements();
        }

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
            for (var y = currentYear; y >= 1920; y--) {
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

                if (yVal.length === 4 && y >= 1920 && y <= new Date().getFullYear()) {
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
            // Prefix Radio Buttons + Always-Visible "อื่นๆ" Input Handler
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

            // Auto-select "อื่นๆ" radio when clicking/focusing/typing in the custom input
            $('#prefix_other_text').on('click focus input keyup', function() {
                $('#prefix_other').prop('checked', true);
                $('#prefix_final_input').val($(this).val().trim() || 'อื่นๆ');
            });

            if (typeof flatpickr !== 'undefined') {
                var thLocale = typeof flatpickr.l10ns.th !== 'undefined' ? $.extend({}, flatpickr.l10ns.th, {
                    firstDayOfWeek: 0
                }) : 'th';
                flatpickr('.flatpickr-birthday', {
                    locale: thLocale,
                    dateFormat: 'Y-m-d',
                    altInput: true,
                    altFormat: 'd/m/Y',
                    maxDate: 'today',
                    minDate: '1920-01-01',
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
    @include('partials.thailand-address-autocomplete-script')
</body>

</html>
