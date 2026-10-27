<!doctype html>
<html lang="th" data-layout="twocolumn" data-layout-style="default" data-layout-position="fixed" data-topbar="light"
    data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default"
    data-theme-colors="default">

<head>

    <meta charset="utf-8" />
    <title>@yield('title', 'Hi Monday')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    @php($isTemplateRoute = request()->routeIs('template.*'))
    @include($isTemplateRoute ? 'template.import.style' : 'office.import.style')

    @yield('style')

</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        @include($isTemplateRoute ? 'template.import.menu' : 'office.import.menu')

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                @yield('content')
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            2024 © Hi Monday
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Designed & Developed by ABBL
                            </div>
                        </div>
                    </div>
                </div>
            </footer>

        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->


    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>



    @include($isTemplateRoute ? 'template.import.script' : 'office.import.script')

    @yield('script')

    @unless ($isTemplateRoute)
        <script>
            (function() {
                var layout = document.documentElement.getAttribute('data-layout');
                if (layout !== 'twocolumn') {
                    return;
                }
                sessionStorage.setItem('data-layout', 'twocolumn');
                sessionStorage.setItem('data-sidebar-size', 'lg');
                var def = sessionStorage.getItem('defaultAttribute');
                if (def) {
                    try {
                        var attrs = JSON.parse(def);
                        attrs['data-layout'] = 'twocolumn';
                        attrs['data-sidebar-size'] = 'lg';
                        sessionStorage.setItem('defaultAttribute', JSON.stringify(attrs));
                    } catch (e) {}
                }
            })();
        </script>
    @endunless

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>

    @unless ($isTemplateRoute)
        <script>
            (function() {
                if (document.documentElement.getAttribute('data-layout') !== 'twocolumn') {
                    return;
                }

                function initTwoColumnMenuTooltips() {
                    document.querySelectorAll('#two-column-menu .nav-icon').forEach(function(el) {
                        var span = el.querySelector('span');
                        var label = span ? span.textContent.trim() : '';
                        if (!label || bootstrap.Tooltip.getInstance(el)) {
                            return;
                        }
                        new bootstrap.Tooltip(el, {
                            placement: 'right',
                            title: label,
                            trigger: 'hover',
                            container: 'body',
                        });
                    });
                }

                function linkMatchesCurrentPath(link) {
                    var href = link.getAttribute('href');
                    if (!href || href.charAt(0) === '#') {
                        return false;
                    }
                    try {
                        var linkPath = new URL(link.href, window.location.origin).pathname;
                        var currentPath = window.location.pathname;
                        return currentPath === linkPath || currentPath.startsWith(linkPath + '/');
                    } catch (e) {
                        return false;
                    }
                }

                function activateTwoColumnIcon(collapseId) {
                    if (!collapseId) {
                        return;
                    }
                    var iconLink = document.querySelector('#two-column-menu [href="#' + collapseId + '"]');
                    if (!iconLink) {
                        return;
                    }
                    document.querySelectorAll('#two-column-menu .nav-icon.active').forEach(function(el) {
                        el.classList.remove('active');
                    });
                    iconLink.classList.add('active');
                }

                function syncOfficeTwoColumnMenu() {
                    var navbarNav = document.getElementById('navbar-nav');
                    var twoColumnMenu = document.getElementById('two-column-menu');
                    if (!navbarNav || !twoColumnMenu) {
                        return;
                    }

                    document.body.classList.remove('twocolumn-panel');

                    document.querySelectorAll('#navbar-nav .nav-item.twocolumn-item-show').forEach(function(el) {
                        el.classList.remove('twocolumn-item-show');
                    });

                    var activeSubLink = navbarNav.querySelector('.menu-dropdown a.nav-link.active');

                    if (!activeSubLink) {
                        navbarNav.querySelectorAll('.menu-dropdown a.nav-link[href]').forEach(function(link) {
                            if (activeSubLink || !linkMatchesCurrentPath(link)) {
                                return;
                            }
                            link.classList.add('active');
                            activeSubLink = link;
                        });
                    }

                    if (activeSubLink) {
                        var dropdown = activeSubLink.closest('.collapse.menu-dropdown');
                        var topNavItem = dropdown ? dropdown.closest('.nav-item') : null;

                        if (topNavItem) {
                            topNavItem.classList.add('twocolumn-item-show');
                            dropdown.classList.add('show');
                            activateTwoColumnIcon(dropdown.getAttribute('id'));
                            navbarNav.classList.add('twocolumn-nav-hide');
                        }
                    }
                }

                initTwoColumnMenuTooltips();

                document.getElementById('topnav-hamburger-icon')?.addEventListener('click', function() {
                    setTimeout(function() {
                        document.body.classList.remove('twocolumn-panel');
                    }, 0);
                });

                if (document.readyState === 'complete') {
                    syncOfficeTwoColumnMenu();
                } else {
                    window.addEventListener('load', syncOfficeTwoColumnMenu);
                }
            })();
        </script>
    @endunless
</body>

</html>
