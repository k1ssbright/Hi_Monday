<!doctype html>
<html lang="th" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>ลืมรหัสผ่าน | Hi Monday</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Hi Monday Password Reset Portal" name="description" />
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
                <img src="{{ asset('assets/images/software tester-rafiki.png') }}" class="img-fluid"
                    style="max-height: 380px; filter: drop-shadow(0px 10px 20px rgba(0,0,0,0.1));" alt="Illustration">
            </div>

            <!-- Bottom Text & Logo -->
            <div class="mb-3 text-center w-100">
                <div
                    style="font-family: 'Inter'; font-size: 40px; font-weight: 700; line-height: 48px; letter-spacing: -0.8px; color: #FFFFFF; margin-bottom: 4px;">
                    Hi Monday</div>
                <div
                    style="font-family: 'IBM Plex Sans Thai'; font-size: 18px; font-weight: 400; line-height: 28px; color: #FFFFFF;">
                    “โปรแกรมบัญชีสำหรับธุรกิจ ช่วยให้บันทึกบัญชีง่ายขึ้น”
                </div>
            </div>
        </div>

        <!-- Right column: Forgot Password Form -->
        <div
            class="auth-right-content flex-grow-1 d-flex align-items-center justify-content-center p-4 position-relative">
            <!-- Content Area -->
            <div class="w-100" style="max-width: 549px; padding-top: 40px; padding-bottom: 40px;">
                <!-- Heading -->
                <div class="mb-4 text-start">
                    <h1
                        style="font-family: 'Inter', 'IBM Plex Sans Thai', sans-serif; font-size: 32px; font-weight: 600; line-height: 38px; color: #131B2E; letter-spacing: -0.3px; margin-bottom: 8px;">
                        ลืมรหัสผ่าน</h1>
                </div>

                <!-- Glass Card Form -->
                <div class="glass-card-form">
                    <!-- Dark Blue Banner -->
                    <div class="mb-4 text-center p-3 text-dark"
                        style="background: #C1D3FF; border-radius: 14px; font-family: 'IBM Plex Sans Thai', sans-serif; font-size: 16px; font-weight: 500; box-shadow: 0px 4px 10px rgba(28, 77, 143, 0.15);">
                        กรุณากรอกชื่อผู้ใช้งานของคุณ แล้วเราจะส่งคำแนะนำในการตั้งรหัสผ่านใหม่ไปยังอีเมลของคุณ
                    </div>

                    <form action="{{ route('password.email') }}" method="POST">
                        @csrf

                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                <strong>ข้อมูลไม่ถูกต้อง!</strong>
                                <ul class="mb-0 mt-1 ps-3 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <!-- Email Input -->
                        <div class="mb-4">
                            <label for="email"
                                style="font-family: 'IBM Plex Sans Thai', 'Inter', sans-serif; font-size: 14px; font-weight: 500; color: #434655; margin-bottom: 8px; display: block;">อีเมล</label>
                            <div class="position-relative">
                                <span class="position-absolute"
                                    style="left: 18px; top: 50%; transform: translateY(-50%); color: #737686; z-index: 5;">
                                    <i class="ri-mail-line fs-18"></i>
                                </span>
                                <input type="email"
                                    class="form-control custom-input @error('email') is-invalid @enderror"
                                    id="email" name="email" value="{{ old('email') }}"
                                    placeholder="กรอกอีเมลของคุณ" required autocomplete="email" autofocus
                                    style="padding-left: 48px;">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button
                            class="btn btn-signin w-100 text-white d-flex justify-content-center align-items-center gap-2 mb-3"
                            type="submit">
                            ส่งข้อมูลรีเซ็ตรหัสผ่าน
                        </button>

                        <!-- Back to Login Button -->
                        <a href="{{ route('login') }}"
                            class="btn btn-outline-signin w-100 d-flex justify-content-center align-items-center gap-2"
                            style="height: 56px; border: 1.5px solid #3C538D; border-radius: 14px; font-size: 16px; font-weight: 600; color: #3C538D; background-color: #ffffff; text-decoration: none; transition: all 0.3s ease; box-sizing: border-box;"
                            onmouseover="this.style.backgroundColor='#F8FAFC'; this.style.color='#2E4070'; this.style.borderColor='#2E4070';"
                            onmouseout="this.style.backgroundColor='#ffffff'; this.style.color='#3C538D'; this.style.borderColor='#3C538D';">
                            เข้าสู่ระบบ
                        </a>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
</body>

</html>
