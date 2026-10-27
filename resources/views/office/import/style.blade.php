    <!-- jsvectormap css -->
    <link href="{{ asset('assets/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />

    <!--Swiper slider css-->
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Sweet Alert css-->
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Select2 CSS -->
    <link href="{{ asset('plugins/select2-4.1.0-rc.0/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wdth,wght@62.5..100,100..900&display=swap" rel="stylesheet">

    @include('office.partials.document-form-styles')
    <link href="{{ asset('assets/libs/@tarekraafat/autocomplete.js/css/autoComplete.css') }}" rel="stylesheet" type="text/css" />
    @include('partials.thailand-address-autocomplete-styles')

    <style>
        /* Keep hamburger icon as 3-line menu at all times (no open/arrow animation) */
        #topnav-hamburger-icon .hamburger-icon.open {
            transform: none;
        }

        #topnav-hamburger-icon .hamburger-icon.open span:nth-child(1) {
            left: 0;
            top: 0;
            width: 80%;
            transform: none;
            transition-delay: 0ms;
        }

        #topnav-hamburger-icon .hamburger-icon.open span:nth-child(2) {
            left: 0;
            top: 6px;
            width: 100%;
            transform: none;
            transition-delay: 0ms;
        }

        #topnav-hamburger-icon .hamburger-icon.open span:nth-child(3) {
            left: 0;
            top: auto;
            bottom: 0;
            width: 60%;
            transform: none;
            transition-delay: 0ms;
        }

        /* Office: always keep both sidebar columns visible */
        [data-layout=twocolumn] body.twocolumn-panel .app-menu {
            width: auto !important;
        }

        [data-layout=twocolumn] body.twocolumn-panel .navbar-brand-box,
        [data-layout=twocolumn] body.twocolumn-panel .navbar-nav {
            display: block !important;
        }

        [data-layout=twocolumn] body.twocolumn-panel .main-content {
            margin-left: calc(220px + 70px) !important;
        }

        [data-layout=twocolumn] body.twocolumn-panel #page-topbar,
        [data-layout=twocolumn] body.twocolumn-panel .footer {
            left: calc(220px + 70px) !important;
        }

        /* Keep a clear gap between page content and the sticky footer */
        .main-content .page-content {
            padding-bottom: 120px;
        }
    </style>
