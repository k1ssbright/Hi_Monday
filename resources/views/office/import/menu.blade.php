@php
    $authUser = Auth::user();
    $authName = trim(
        collect([$authUser?->prefix, $authUser?->firstname, $authUser?->surname])
            ->filter()
            ->implode(' '),
    );
    if ($authName === '') {
        $authName = $authUser?->email ?? 'User';
    }

    $currentBusinessId = currentBusinessId();
    $currentBranchId = currentBusinessBranchId();

    $userBusinesses = $authUser
        ? $authUser->businesses()
            ->with(['businessBranches' => fn ($query) => $query->orderBy('business_status')->orderBy('business_branch_no')])
            ->orderBy('business_code')
            ->orderBy('business_name_th')
            ->get()
        : collect();
    if ($userBusinesses->isEmpty()) {
        $userBusinesses = \App\Models\Business\Business::with('businessBranches')->get();
    }
@endphp
<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <div class="navbar-brand-box horizontal-logo">
                    <a href="{{ route('office.index') }}" class="logo logo-dark">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="17">
                        </span>
                    </a>
                    <a href="{{ route('office.index') }}" class="logo logo-light">
                        <span class="logo-sm">
                            <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
                        </span>
                        <span class="logo-lg">
                            <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="17">
                        </span>
                    </a>
                </div>

                <button type="button"
                    class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none"
                    id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>

                @php
                    $currentBusiness = $currentBusinessId ? \App\Models\Business\Business::find($currentBusinessId) : null;
                    $currentBranch = $currentBranchId ? \App\Models\Business\BusinessBranch::find($currentBranchId) : null;
                    $activeBizCode = $currentBusiness?->business_code;
                    $activeBizName = $currentBusiness?->business_name_th ?? $currentBusiness?->business_name_en;
                    $activeBranchName = $currentBranch?->business_branch_name_th;

                    $activeLogo = $currentBranch?->business_logo;
                    $activeBizInitial = '—';
                    if ($activeBizName) {
                        $activeBizInitial = mb_substr(trim($activeBizName), 0, 1);
                    } elseif ($activeBizCode) {
                        $activeBizInitial = mb_substr($activeBizCode, 0, 1);
                    }
                @endphp
                <div class="d-none d-md-flex align-items-center ms-2 py-1">
                    <!-- Unified Workspace Bar -->
                    <div class="d-flex align-items-center bg-white rounded-3 border shadow-sm p-1 ps-2 pe-1" 
                         style="border-color: #e2e8f0 !important; background: #ffffff;">
                        
                        <!-- Left: Company Logo Image or Monogram Badge -->
                        @if($activeLogo)
                            <img src="{{ asset('storage/business_logos/' . $activeLogo) }}" alt="{{ $activeBizCode ?? 'โลโก้บริษัท' }}" class="rounded-2 me-2 border flex-shrink-0 shadow-sm" style="width: 36px; height: 36px; object-fit: contain; background: #ffffff; padding: 2px;">
                        @else
                            <div class="d-flex align-items-center justify-content-center rounded-2 me-2 flex-shrink-0" 
                                 style="width: 36px; height: 36px; background: #0f172a; color: #ffffff; font-weight: 700; font-size: 11.5px; letter-spacing: -0.2px;">
                                {{ $activeBizInitial }}
                            </div>
                        @endif

                        <!-- Middle: Stacked Location Status & Company Name -->
                        <div class="d-flex flex-column justify-content-center me-3 lh-sm">
                            <div class="d-flex align-items-center gap-1 mb-0.5" style="font-size: 11px; font-weight: 600; color: #64748b;">
                                <span class="d-inline-block rounded-circle bg-success" style="width: 6px; height: 6px;"></span>
                                <span>{{ $currentBusiness ? 'ตอนนี้คุณอยู่ที่บริษัท' : 'ยังไม่ได้เลือกบริษัท' }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1.5">
                                @if($activeBizCode)
                                    <span class="fw-bold text-dark" style="font-size: 13.5px;">{{ $activeBizCode }}</span>
                                @endif
                                <span class="text-secondary" style="font-size: 13px;">{{ $activeBizName ?? 'กรุณาเลือกบริษัทและสาขา' }}</span>
                                @if($activeBranchName)
                                    <span class="badge bg-light text-dark border fw-medium px-2 py-0.5 rounded-pill ms-1" style="font-size: 11px; border-color: #cbd5e1 !important; color: #334155 !important;">
                                        {{ $activeBranchName }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Vertical Divider -->
                        <div class="vr my-1 me-2" style="height: 24px; opacity: 0.2;"></div>

                        <!-- Right: Integrated Switch Button -->
                        <button type="button" class="btn btn-sm d-flex align-items-center gap-1.5 px-2.5 py-1.5 material-shadow-none" 
                            style="font-size: 12px; font-weight: 600; border-radius: 6px; background-color: #f8fafc; color: #1e293b; border: 1px solid #e2e8f0; height: 34px; transition: all 0.15s ease;"
                            data-bs-toggle="modal" data-bs-target="#changeBusinessModal" title="เปลี่ยนบริษัทและสาขา">
                            <i class="ri-arrow-left-right-line fs-13 text-muted"></i>
                            <span>เปลี่ยนบริษัท</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center">

                <!-- User Display (static, no dropdown) -->
                <div class="header-item topbar-user" style="background-color: transparent !important; padding: 0 16px 0 0;">
                    <span class="d-flex align-items-center gap-2">
                        <span class="text-end">
                            <span class="d-none d-xl-inline-block fw-medium user-name-text">{{ $authUser?->email ?? 'User' }}</span>
                            <span class="d-none d-xl-block fs-12 text-muted text-end">{{ $authName }}</span>
                        </span>
                        <span class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px; border: 1.5px solid #ced4da;">
                            <i class="ri-user-3-line fs-20 text-muted"></i>
                        </span>
                    </span>
                </div>

                <!-- Vertical Divider -->
                <div style="width: 1px; align-self: stretch; background-color: #e9ebec;"></div>

                <!-- Mail Icon -->
                <div class="header-item" style="padding: 0 4px 0 12px;">
                    <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle" style="width: 38px; height: 38px;">
                        <i class="ri-mail-line fs-20 text-muted"></i>
                    </button>
                </div>

                <!-- Settings Icon with Dropdown -->
                <div class="dropdown header-item" style="padding: 0 8px 0 4px;">
                    <button type="button" class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle"
                        id="page-header-settings-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 38px; height: 38px;">
                        <i class="ri-settings-3-line fs-20 text-muted"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a class="dropdown-item" href="{{ route('office.profile') }}"><i
                                class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">ข้อมูลส่วนตัว</span></a>
                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#changeBusinessModal"><i
                                class="mdi mdi-swap-horizontal text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle">เปลี่ยนบริษัท</span></a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle" data-key="t-logout">ออกจากระบบ</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <div class="navbar-brand-box">
        <a href="{{ route('office.index') }}" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/logo-dark.png') }}" alt="" height="17">
            </span>
        </a>
        <a href="{{ route('office.index') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/logo-light.png') }}" alt="" height="17">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>
    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            @php
                $isShortcutActive = request()->routeIs('office.index') || request()->routeIs('office.shortcut');
                $isLeadListActive = request()->routeIs('office.leads.*');
                $isOpportunityActive = request()->routeIs('office.opportunities.*');
                $isQuotationActive = request()->routeIs('office.quotations.*');
                $isSalesOrderActive = request()->routeIs('office.sales-orders.*');
                $isBillingNoteActive = request()->routeIs('office.billing-notes.*');
                $isDeliveryNoteActive = request()->routeIs('office.delivery-notes.*');
                $isTaxInvoiceActive = request()->routeIs('office.tax-invoices.*');
                $isReceiptActive = request()->routeIs('office.receipts.*');
                $isCreditNoteActive = request()->routeIs('office.credit-notes.*');
                $isDebitNoteActive = request()->routeIs('office.debit-notes.*');
                $isPurchaseRequisitionActive = request()->routeIs('office.purchase-requisitions.*');
                $isPurchaseOrderActive = request()->routeIs('office.purchase-orders.*');
                $isBusinessActive = request()->routeIs('office.business.*')
                    || request()->routeIs('office.mybusiness')
                    || request()->routeIs('office.business-show');

                $isPartnerActive = request()->routeIs('office.partners.*')
                    || request()->routeIs('office.partner-show');

                $isPurchasingActive = $isPurchaseRequisitionActive || $isPurchaseOrderActive;

                $isSalesActive =
                    $isLeadListActive ||
                    $isOpportunityActive ||
                    $isQuotationActive ||
                    $isSalesOrderActive ||
                    $isBillingNoteActive ||
                    $isDeliveryNoteActive ||
                    $isTaxInvoiceActive ||
                    $isReceiptActive ||
                    $isCreditNoteActive ||
                    $isDebitNoteActive;

                $isLeadSettingActive =
                    request()->routeIs('office.settings.lead-channels.*') ||
                    request()->routeIs('office.settings.lead-closed-reasons.*') ||
                    request()->routeIs('office.settings.lead-project-types.*') ||
                    request()->routeIs('office.settings.lead-assignment-rules.*') ||
                    request()->routeIs('office.settings.lead-pipeline.*');

                $isGeneralSettingActive =
                    request()->routeIs('office.settings.product-service-categories.*') ||
                    request()->routeIs('office.settings.product-service-items.*') ||
                    request()->routeIs('office.settings.product-service-questions.*') ||
                    request()->routeIs('office.settings.marketing-platforms.*') ||
                    request()->routeIs('office.settings.marketing-objectives.*') ||
                    request()->routeIs('office.settings.marketing-ads.*');

                $showFullMenu = false;
            @endphp

            <ul class="navbar-nav" id="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link menu-link {{ $isShortcutActive ? 'active' : '' }}" href="{{ route('office.index') }}">
                        <i class="ri-home-smile-line"></i> <span>Hi Monday</span>
                    </a>
                </li>

                @if ($showFullMenu)
                <li class="menu-title"><span>เมนูด่วน</span></li>
                <li class="nav-item {{ $isShortcutActive ? 'twocolumn-item-show' : '' }}">
                    <a class="nav-link menu-link {{ $isShortcutActive ? 'active' : '' }}" href="#sidebarShortcuts"
                        data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ $isShortcutActive ? 'true' : 'false' }}" aria-controls="sidebarShortcuts">
                        <i class="ri-flashlight-line"></i> <span>Shortcuts</span>
                    </a>
                    <div class="collapse menu-dropdown {{ $isShortcutActive ? 'show' : '' }}" id="sidebarShortcuts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('office.index') }}"
                                    class="nav-link {{ $isShortcutActive ? 'active' : '' }}">Shortcuts</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item {{ $isBusinessActive ? 'twocolumn-item-show' : '' }}">
                    <a class="nav-link menu-link {{ $isBusinessActive ? 'active' : '' }}" href="#sidebarBusiness"
                        data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ $isBusinessActive ? 'true' : 'false' }}" aria-controls="sidebarBusiness">
                        <i class="ri-building-line"></i> <span>ธุรกิจของฉัน</span>
                    </a>
                    <div class="collapse menu-dropdown {{ $isBusinessActive ? 'show' : '' }}" id="sidebarBusiness">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('office.business.index') }}"
                                    class="nav-link {{ $isBusinessActive ? 'active' : '' }}">ธุรกิจของฉัน</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item {{ $isPartnerActive ? 'twocolumn-item-show' : '' }}">
                    <a class="nav-link menu-link {{ $isPartnerActive ? 'active' : '' }}" href="#sidebarPartners"
                        data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ $isPartnerActive ? 'true' : 'false' }}" aria-controls="sidebarPartners">
                        <i class="ri-contacts-book-line"></i> <span>สมุดรายชื่อคู่ค้า</span>
                    </a>
                    <div class="collapse menu-dropdown {{ $isPartnerActive ? 'show' : '' }}" id="sidebarPartners">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('office.partners.index') }}"
                                    class="nav-link {{ $isPartnerActive ? 'active' : '' }}">สมุดรายชื่อคู่ค้า</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item {{ $isSalesActive ? 'twocolumn-item-show' : '' }}">
                    <a class="nav-link menu-link {{ $isSalesActive ? 'active' : '' }}" href="#sidebarSales"
                        data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ $isSalesActive ? 'true' : 'false' }}" aria-controls="sidebarSales">
                        <i class="ri-hand-coin-fill"></i> <span>งานขาย</span>
                    </a>
                    <div class="collapse menu-dropdown {{ $isSalesActive ? 'show' : '' }}" id="sidebarSales">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('office.leads.index') }}"
                                    class="nav-link {{ $isLeadListActive ? 'active' : '' }}">รายการ Lead</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.opportunities.index') }}"
                                    class="nav-link {{ $isOpportunityActive ? 'active' : '' }}">Opportunities</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.quotations.index') }}"
                                    class="nav-link {{ $isQuotationActive ? 'active' : '' }}">ใบเสนอราคา (QT)</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.sales-orders.index') }}"
                                    class="nav-link {{ $isSalesOrderActive ? 'active' : '' }}">ใบสั่งขาย (SO)</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.billing-notes.index') }}"
                                    class="nav-link {{ $isBillingNoteActive ? 'active' : '' }}">ใบวางบิล (BN)</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.delivery-notes.index') }}"
                                    class="nav-link {{ $isDeliveryNoteActive ? 'active' : '' }}">ใบส่งของ (DLV)</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.tax-invoices.index') }}"
                                    class="nav-link {{ $isTaxInvoiceActive ? 'active' : '' }}">ใบกำกับภาษี (TI)</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.receipts.index') }}"
                                    class="nav-link {{ $isReceiptActive ? 'active' : '' }}">ใบเสร็จรับเงิน (RCT)</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.credit-notes.index') }}"
                                    class="nav-link {{ $isCreditNoteActive ? 'active' : '' }}">ใบลดหนี้ (CN)</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.debit-notes.index') }}"
                                    class="nav-link {{ $isDebitNoteActive ? 'active' : '' }}">ใบเพิ่มหนี้ (DBN)</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item {{ $isPurchasingActive ? 'twocolumn-item-show' : '' }}">
                    <a class="nav-link menu-link {{ $isPurchasingActive ? 'active' : '' }}"
                        href="#sidebarPurchasing"
                        data-bs-toggle="collapse"
                        role="button"
                        aria-expanded="{{ $isPurchasingActive ? 'true' : 'false' }}"
                        aria-controls="sidebarPurchasing">
                        <i class="ri-shopping-cart-2-line"></i> <span>งานจัดซื้อ</span>
                    </a>
                    <div class="collapse menu-dropdown {{ $isPurchasingActive ? 'show' : '' }}" id="sidebarPurchasing">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('office.purchase-requisitions.index') }}" class="nav-link {{ $isPurchaseRequisitionActive ? 'active' : '' }}">ใบขอซื้อ (PR)</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.purchase-orders.index') }}" class="nav-link {{ $isPurchaseOrderActive ? 'active' : '' }}">ใบสั่งซื้อ (PO)</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="menu-title"><span>ตั้งค่าระบบ</span></li>
                <li class="nav-item {{ $isLeadSettingActive ? 'twocolumn-item-show' : '' }}">
                    <a class="nav-link menu-link {{ $isLeadSettingActive ? 'active' : '' }}"
                        href="#sidebarLeadSettings" data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ $isLeadSettingActive ? 'true' : 'false' }}"
                        aria-controls="sidebarLeadSettings">
                        <i class="ri-settings-5-line"></i> <span>การตั้งค่า Lead</span>
                    </a>
                    <div class="collapse menu-dropdown {{ $isLeadSettingActive ? 'show' : '' }}"
                        id="sidebarLeadSettings">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('office.settings.lead-channels.index') }}"
                                    class="nav-link {{ request()->routeIs('office.settings.lead-channels.*') ? 'active' : '' }}">ช่องทางรับ Lead</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.settings.lead-closed-reasons.index') }}"
                                    class="nav-link {{ request()->routeIs('office.settings.lead-closed-reasons.*') ? 'active' : '' }}">เหตุผลปิด Lead</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.settings.lead-project-types.index') }}"
                                    class="nav-link {{ request()->routeIs('office.settings.lead-project-types.*') ? 'active' : '' }}">ประเภทงาน Lead</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.settings.lead-assignment-rules.index') }}"
                                    class="nav-link {{ request()->routeIs('office.settings.lead-assignment-rules.*') ? 'active' : '' }}">กฎการมอบหมาย Lead</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.settings.lead-pipeline.edit') }}"
                                    class="nav-link {{ request()->routeIs('office.settings.lead-pipeline.*') ? 'active' : '' }}">กฎคะแนน Lead / SLA / เป้าหมาย</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item {{ $isGeneralSettingActive ? 'twocolumn-item-show' : '' }}">
                    <a class="nav-link menu-link {{ $isGeneralSettingActive ? 'active' : '' }}"
                        href="#sidebarSettings" data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ $isGeneralSettingActive ? 'true' : 'false' }}"
                        aria-controls="sidebarSettings">
                        <i class="ri-settings-3-line"></i> <span>ตั้งค่า</span>
                    </a>
                    <div class="collapse menu-dropdown {{ $isGeneralSettingActive ? 'show' : '' }}"
                        id="sidebarSettings">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ route('office.settings.product-service-categories.index') }}"
                                    class="nav-link {{ request()->routeIs('office.settings.product-service-categories.*') ? 'active' : '' }}">หมวดสินค้า / บริการ</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.settings.product-service-items.index') }}"
                                    class="nav-link {{ request()->routeIs('office.settings.product-service-items.*') ? 'active' : '' }}">สินค้า / บริการ</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.settings.product-service-questions.index') }}"
                                    class="nav-link {{ request()->routeIs('office.settings.product-service-questions.*') ? 'active' : '' }}">คำถามตามหมวด</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.settings.marketing-platforms.index') }}"
                                    class="nav-link {{ request()->routeIs('office.settings.marketing-platforms.*') ? 'active' : '' }}">แพลตฟอร์มการตลาด</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.settings.marketing-objectives.index') }}"
                                    class="nav-link {{ request()->routeIs('office.settings.marketing-objectives.*') ? 'active' : '' }}">วัตถุประสงค์โฆษณา</a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('office.settings.marketing-ads.index') }}"
                                    class="nav-link {{ request()->routeIs('office.settings.marketing-ads.*') ? 'active' : '' }}">แคมเปญโฆษณา</a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
            </ul>
        </div>
    </div>
    <div class="sidebar-background"></div>
</div>
<div class="vertical-overlay"></div>
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<!-- Modal สลับธุรกิจและสาขา -->
<div class="modal fade" id="changeBusinessModal" tabindex="-1" aria-labelledby="changeBusinessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 820px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden; background-color: #ffffff;">
            
            <!-- Modal Header -->
            <div class="modal-header border-0 px-4 pt-4 pb-2 d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold text-dark mb-0 fs-17" id="changeBusinessModalLabel">สลับธุรกิจและสาขา</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body px-4 py-3" style="max-height: 72vh; overflow-y: auto;">
                @foreach($userBusinesses as $bizIndex => $biz)
                    @php
                        $bName = $biz->business_name_th ?? $biz->business_name_en;
                        $bCode = $biz->business_code;
                        $isOwner = ($bizIndex === 0);
                        $branches = $biz->businessBranches ?? collect();
                        
                        // Pick distinct logo colors/styles for different businesses
                        $logoStyles = [
                            ['bg' => '#eef2ff', 'color' => '#3b82f6', 'icon' => 'ri-building-2-fill'],
                            ['bg' => '#eff6ff', 'color' => '#2563eb', 'icon' => 'ri-store-2-fill'],
                            ['bg' => '#f0fdf4', 'color' => '#16a34a', 'icon' => 'ri-community-fill'],
                        ];
                        $logoStyle = $logoStyles[$bizIndex % count($logoStyles)];
                    @endphp

                    <!-- Business Group Card -->
                    <div class="business-group-container p-3 mb-3 rounded-4" style="background: linear-gradient(180deg, #f3f6fb 0%, #f8fafc 100%); border: 1px solid #e9edf5;">
                        
                        <!-- Business Header Row -->
                        <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                            <div class="d-flex align-items-center gap-3">
                                <div class="business-logo-box bg-white rounded-3 d-flex align-items-center justify-content-center border shadow-sm" style="width: 44px; height: 44px; flex-shrink: 0;">
                                    <span class="fw-bold fs-14 text-dark" style="letter-spacing: -0.5px; font-family: sans-serif;">{{ $bCode ? substr($bCode, 0, 4) : '—' }}</span>
                                </div>
                                <div>
                                    <span class="fw-bold text-dark fs-14" style="letter-spacing: 0.2px;">{{ $bName ?? '—' }}</span>
                                </div>
                            </div>
                            <div>
                                @if($isOwner)
                                    <span class="badge rounded-pill" style="background: #e6f9f0; color: #10b981; font-weight: 500; font-size: 11.5px; padding: 5px 12px;">เจ้าของธุรกิจ</span>
                                @else
                                    <span class="badge rounded-pill" style="background: #e0f2fe; color: #0284c7; font-weight: 500; font-size: 11.5px; padding: 5px 12px;">พนักงาน</span>
                                @endif
                            </div>
                        </div>

                        <!-- Branches Grid -->
                        <div class="row g-2">
                            @forelse($branches as $branchIndex => $branch)
                                @php
                                    $branchCode = $branch->business_branch_code ?: $branch->id;
                                    $isSelected = ($currentBranchId && ((string)$branch->id === (string)$currentBranchId || (string)$branch->business_branch_code === (string)$currentBranchId));
                                    $branchName = $branch->business_branch_name_th ?: ('สาขา ' . ($branch->business_branch_no ?: '000' . $branchIndex));
                                    
                                    // Status subtitle
                                    if ($isOwner) {
                                        $subText = 'สถานะ: เปิดให้บริการ';
                                    } else {
                                        $subText = ($branchIndex === 0) ? 'บทบาท: ผู้จัดการสาขา' : 'บทบาท: พนักงานขาย';
                                    }
                                @endphp
                                <div class="col-12 col-md-4">
                                    <div class="branch-select-card bg-white p-3 rounded-3 position-relative {{ $isSelected ? 'is-selected' : '' }}"
                                        data-branch-code="{{ $branchCode }}"
                                        style="cursor: pointer; min-height: 74px; transition: all 0.18s ease-in-out; border: {{ $isSelected ? '2px solid #2563eb' : '1px solid #e5e7eb' }}; background-color: {{ $isSelected ? '#f0f5ff' : '#ffffff' }} !important;">
                                        
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <div class="fw-bold text-dark fs-13 text-truncate pe-1 branch-title-text" style="max-width: 85%;">
                                                {{ $branchName }}
                                            </div>
                                            <div class="branch-radio-indicator flex-shrink-0">
                                                @if($isSelected)
                                                    <i class="ri-checkbox-circle-fill text-primary fs-18"></i>
                                                @else
                                                    <i class="ri-checkbox-blank-circle-line fs-18 text-muted" style="opacity: 0.6;"></i>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-muted fs-11 branch-subtitle-text" style="font-size: 11px !important;">
                                            {{ $subText }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center py-2 text-muted fs-12">
                                    ไม่พบสาขาในระบบ
                                </div>
                            @endforelse
                        </div>

                    </div>
                @endforeach
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-0 px-4 py-3 d-flex justify-content-end align-items-center gap-3" style="background-color: #ffffff;">
                <button type="button" class="btn btn-link text-decoration-none text-dark fw-medium fs-13 px-2 shadow-none" data-bs-dismiss="modal">
                    ยกเลิก
                </button>
                <button type="button" class="btn text-white fw-semibold px-4 py-2 shadow-none" id="modal-btn-confirm-branch" style="background-color: #0f4c9c; border-radius: 8px; font-size: 13.5px; min-width: 150px;">
                    ยืนยันการสลับธุรกิจ
                </button>
            </div>

        </div>
    </div>
</div>

<style>
.branch-select-card:hover:not(.is-selected) {
    border-color: #93c5fd !important;
    background-color: #f8faff !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.08);
}
.branch-select-card.is-selected {
    box-shadow: 0 0 0 1px #2563eb, 0 4px 12px rgba(37, 99, 235, 0.12);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let selectedBranchCode = null;

    // Find initially active branch
    const initialSelectedCard = document.querySelector('.branch-select-card.is-selected');
    if (initialSelectedCard) {
        selectedBranchCode = initialSelectedCard.getAttribute('data-branch-code');
    }

    // Handle branch card click
    document.querySelectorAll('.branch-select-card').forEach(function (card) {
        card.addEventListener('click', function () {
            // Unselect all cards
            document.querySelectorAll('.branch-select-card').forEach(function (c) {
                c.classList.remove('is-selected');
                c.style.border = '1px solid #e5e7eb';
                c.style.backgroundColor = '#ffffff';
                const indicator = c.querySelector('.branch-radio-indicator');
                if (indicator) {
                    indicator.innerHTML = '<i class="ri-checkbox-blank-circle-line fs-18 text-muted" style="opacity: 0.6;"></i>';
                }
            });

            // Select clicked card
            this.classList.add('is-selected');
            this.style.border = '2px solid #2563eb';
            this.style.backgroundColor = '#f0f5ff';
            const indicator = this.querySelector('.branch-radio-indicator');
            if (indicator) {
                indicator.innerHTML = '<i class="ri-checkbox-circle-fill text-primary fs-18"></i>';
            }

            selectedBranchCode = this.getAttribute('data-branch-code');
        });
    });

    // Handle Confirm Switch Button
    const confirmBtn = document.getElementById('modal-btn-confirm-branch');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            if (selectedBranchCode) {
                this.disabled = true;
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> กำลังสลับ...';
                window.location.href = "{{ url('app') }}/" + encodeURIComponent(selectedBranchCode) + "/office";
            }
        });
    }
});
</script>
