<!doctype html>
<html lang="th" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>เข้าสู่ระบบ | Hi Monday</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Hi Monday Login Portal" name="description" />
    <meta content="ABBL" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    @include('office.import.style')
    <link href="{{ asset('css/font.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/web.css') }}" rel="stylesheet" type="text/css" />
</head>

<body class="auth-body">

    <div class="auth-page-wrapper d-flex min-vh-100 p-0 position-relative">
        <!-- Left column: Blue Gradient and Illustration -->
        <div
            class="auth-left-sidebar d-none d-lg-flex flex-column justify-content-between p-5 text-white position-relative">
            <!-- Logo -->
            <div class="logo-oneclick">
                <span class="text-one">HI</span><span class="text-click">MONDAY</span>
            </div>

            <!-- Illustration -->
            <div class="my-auto text-center">
                <img src="{{ asset('assets/images/Programmer-amico.png') }}" class="img-fluid"
                    style="max-height: 380px; filter: drop-shadow(0px 10px 20px rgba(0,0,0,0.1));" alt="Illustration">
            </div>

            <!-- Bottom Text & Logo -->
            <div class="mb-3 text-center w-100">
                <div
                    style="font-family: 'Inter', 'IBM Plex Sans Thai', sans-serif; font-size: 40px; font-weight: 700; line-height: 48px; letter-spacing: -0.8px; color: #FFFFFF; margin-bottom: 4px;">
                    Hi Monday</div>
                <div
                    style="font-family: 'Inter', 'IBM Plex Sans Thai', sans-serif; font-size: 18px; font-weight: 400; line-height: 28px; color: #FFFFFF;">
                    “โปรแกรมบัญชีสำหรับธุรกิจ ช่วยให้บันทึกบัญชีง่ายขึ้น”
                </div>
            </div>
        </div>

        <!-- Right column: Login Form -->
        <div
            class="auth-right-content flex-grow-1 d-flex align-items-center justify-content-center p-4 position-relative">
            <!-- Content Area -->
            <div class="w-100" style="max-width: 549px; padding-top: 40px; padding-bottom: 40px;">
                <!-- Heading -->
                <div class="mb-4">
                    <h1
                        style="font-family: 'Inter', 'IBM Plex Sans Thai', sans-serif; font-size: 30px; font-weight: 600; line-height: 36px; color: #131B2E; letter-spacing: -0.3px; margin-bottom: 8px;">
                        ยินดีต้อนรับ</h1>
                    <h2
                        style="font-family: 'Inter', 'IBM Plex Sans Thai', sans-serif; font-size: 24px; font-weight: 600; line-height: 29px; color: #3C538D; margin-bottom: 12px;">
                        ลงชื่อเข้าใช้เพื่อไปยัง Hi Monday</h2>
                </div>

                <!-- Glass Card Form -->
                <div class="glass-card-form">
                    <form action="{{ url('login') }}" method="POST">
                        @csrf

                        @if ($errors->any())
                            <div class="alert custom-alert fade show d-flex align-items-start gap-3 mb-4" role="alert">
                                <div class="alert-icon">
                                    <i class="ri-error-warning-fill fs-20"></i>
                                </div>
                                <div class="alert-content flex-grow-1">
                                    <div class="alert-title">ข้อมูลไม่ถูกต้อง</div>
                                    <div class="alert-description">กรุณาตรวจสอบอีเมลและรหัสผ่านอีกครั้ง</div>
                                </div>
                                <button type="button" class="btn-alert-close" data-bs-dismiss="alert" aria-label="Close">
                                    <i class="ri-close-line fs-20"></i>
                                </button>
                            </div>
                        @endif

                        <!-- Email Input -->
                        <div class="mb-4">
                            <label for="email"
                                style="font-family: 'Inter', 'IBM Plex Sans Thai', sans-serif; font-size: 14px; font-weight: 600; color: #434655; letter-spacing: 0.14px; margin-bottom: 8px; display: block;">ชื่อผู้ใช้งาน</label>
                            <div class="position-relative">
                                <span class="position-absolute"
                                    style="left: 18px; top: 50%; transform: translateY(-50%); color: #737686; z-index: 5;">
                                    <i class="ri-user-line fs-18"></i>
                                </span>
                                <input type="email"
                                    class="form-control custom-input @if ($errors->any()) is-invalid @endif"
                                    id="email" name="email" value="{{ old('email') }}"
                                    placeholder="กรอกชื่อผู้ใช้งาน" required autocomplete="email" autofocus
                                    style="padding-left: 48px;">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>


                        <!-- Password Input -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label for="password-input"
                                    style="font-family: 'Inter', 'IBM Plex Sans Thai', sans-serif; font-size: 14px; font-weight: 600; color: #434655; letter-spacing: 0.14px; margin-bottom: 0;">รหัสผ่าน</label>
                                <a href="{{ route('password.request') }}"
                                    style="font-family: 'Inter', 'IBM Plex Sans Thai', sans-serif; font-size: 12px; font-weight: 500; color: #3C538D; letter-spacing: 0.6px; text-decoration: none; transition: color 0.2s;"
                                    onmouseover="this.style.color='#2E4070'"
                                    onmouseout="this.style.color='#3C538D'">ลืมรหัสผ่าน?</a>
                            </div>
                            <div class="position-relative">
                                <span class="position-absolute"
                                    style="left: 18px; top: 50%; transform: translateY(-50%); color: #737686; z-index: 5;">
                                    <i class="ri-lock-line fs-18"></i>
                                </span>
                                <input type="password"
                                    class="form-control custom-input pe-5 password-input @if ($errors->any()) is-invalid @endif"
                                    placeholder="กรอกรหัสผ่าน" id="password-input" name="password" required
                                    autocomplete="current-password" style="padding-left: 48px;">
                                <button
                                    class="btn btn-link position-absolute end-0 top-0 h-100 text-decoration-none text-muted"
                                    type="button" id="password-addon" style="padding-right: 16px; z-index: 5;">
                                    <i class="ri-eye-line align-middle fs-18"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Remember Me checkbox -->
                        <div class="form-check mb-4 d-flex align-items-center gap-2">
                            <input class="form-check-input" type="checkbox" name="remember" id="auth-remember-check"
                                {{ old('remember') ? 'checked' : '' }}
                                style="width: 20px; height: 20px; background: #FFFFFF; border: 1px solid #C3C6D7; border-radius: 4px; margin-top: 0; cursor: pointer;">
                            <label class="form-check-label" for="auth-remember-check"
                                style="font-family: 'Inter', 'IBM Plex Sans Thai', sans-serif; font-size: 16px; color: #434655;">จดจำการเข้าสู่ระบบ</label>
                        </div>

                        <!-- Sign In Button -->
                        <button
                            class="btn btn-signin w-100 text-white d-flex justify-content-center align-items-center gap-2"
                            type="submit">
                            เข้าสู่ระบบ
                            <i class="ri-arrow-right-line fs-16"></i>
                        </button>
                    </form>

                    <!-- Footer registration link -->
                    <div class="text-center mt-4"
                        style="font-family: 'Inter', 'IBM Plex Sans Thai', sans-serif; font-size: 16px; color: #434655;">
                        ยังไม่มีบัญชี? <a href="{{ route('register') }}"
                            style="color: #3C538D; font-weight: 600; text-decoration: none; transition: color 0.2s;"
                            onmouseover="this.style.color='#2E4070'"
                            onmouseout="this.style.color='#3C538D'">ลงทะเบียนผู้ใช้งาน</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>

    <!-- password-addon init -->
    <script>
        document.getElementById('password-addon').addEventListener('click', function() {
            var passwordInput = document.getElementById('password-input');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.innerHTML = '<i class="ri-eye-off-line align-middle fs-18"></i>';
            } else {
                passwordInput.type = 'password';
                this.innerHTML = '<i class="ri-eye-line align-middle fs-18"></i>';
            }
        });

        // Clear red borders (is-invalid) when the user starts typing in any input field
        document.querySelectorAll('.custom-input').forEach(function(input) {
            input.addEventListener('input', function() {
                document.querySelectorAll('.custom-input').forEach(function(el) {
                    el.classList.remove('is-invalid');
                });
            });
        });
    </script>
</body>

</html>
