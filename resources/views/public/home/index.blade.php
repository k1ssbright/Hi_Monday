<!doctype html>
<html lang="th" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>
    <meta charset="utf-8" />
    <title>หน้าแรก | Hi Monday</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Hi Monday System Selector Portal" name="description" />
    <meta content="ABBL" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    @include('office.import.style')
    <link href="{{ asset('css/font.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/web.css') }}" rel="stylesheet" type="text/css" />
</head>

<body class="home-body flex-column">

    <!-- Header Section -->
    <header class="header-profile">
        <div class="profile-text">
            <div class="profile-email">{{ $user->email ?? '' }}</div>
            <div class="profile-name">{{ $user->name ?? 'ผู้ใช้งานระบบ' }}</div>
        </div>
        <div class="profile-avatar">
            <i class="ri-user-3-fill fs-24"></i>
        </div>
    </header>

    <!-- Main Content Section -->
    <main class="main-container">
        <!-- Logo -->
        <div class="logo-home">
            <span class="text-one">HI</span><span class="text-click">MONDAY</span>
        </div>

        <!-- Greeting -->
        <h2 class="welcome-title text-center">ยินดีต้อนรับเข้าสู่ระบบ</h2>
        <p class="welcome-subtitle text-center">เลือกระบบที่ต้องการใช้งาน</p>

        <!-- System Cards -->
        <div class="system-cards-row">
            <!-- Card 1: Hi Monday -->
            <a href="{{ route('office.shortcut') }}" class="system-card card-oneclick">
                <div class="icon-oneclick">
                    <i class="ri-function-line"></i>
                </div>
                <div class="card-text-container">
                    <div class="title-oneclick">ระบบ Hi Monday</div>
                </div>
            </a>

            <!-- Card 2: Billing -->
            <a href="#" class="system-card card-billing">
                <div class="icon-billing">
                    <i class="ri-file-list-3-line"></i>
                </div>
                <div class="card-text-container">
                    <div class="title-billing">ระบบวางบิล</div>
                </div>
            </a>

            <!-- Card 3: Issue Management -->
            <a href="#" class="system-card card-issue">
                <div class="icon-issue">
                    <i class="ri-error-warning-line"></i>
                </div>
                <div class="card-text-container">
                    <div class="title-issue">Issue Management System</div>
                    <div class="subtitle-issue">ระบบรับแจ้งปัญหาการใช้งาน</div>
                </div>
            </a>
        </div>

        <!-- Logout Action -->
        <form action="{{ route('logout') }}" method="POST" id="logout-form" class="d-inline-block">
            @csrf
            <button type="submit" class="btn btn-logout border-0" style="background: none; padding: 0;">
                <span class="btn-logout">
                    <i class="ri-logout-box-r-line fs-18"></i>
                    ออกจากระบบ
                </span>
            </button>
        </form>
    </main>

    <!-- Footer Space -->
    <div style="height: 48px;"></div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
</body>

</html>
