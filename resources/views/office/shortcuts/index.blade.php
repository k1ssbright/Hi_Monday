@extends('office.layout.office')

@section('title', 'ภาพรวม / ทางลัด | Hi Monday')

@section('content')
<div class="container-fluid">
    <!-- Top Breadcrumb/Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted fs-13">ภาพรวม</span>
        <span class="text-muted fs-13">ภาพรวม</span>
    </div>

    <!-- Main Card Container -->
    <div class="card p-3 mb-4" style="background: #f8fafc; border-radius: 16px; border: none;">
        <!-- Top Nav Tabs -->
        <ul class="nav overview-tabs mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#shortcut-tab" role="tab">
                    <i class="ri-layout-grid-line"></i> ทางลัด / ภาพรวม
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#docs-tab" role="tab">
                    <i class="ri-file-text-line"></i> เอกสารต่างๆ ของฉัน
                </a>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content">
            <div class="tab-pane active" id="shortcut-tab" role="tabpanel">
                <!-- Shortcuts Grid (2 Rows x 3 Cols) -->
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="javascript:void(0);" class="shortcut-card">
                            <div class="icon-box bg-icon-green">
                                <i class="ri-file-text-line"></i>
                            </div>
                            <div>
                                <div class="shortcut-title">EMS</div>
                                <div class="shortcut-desc">ฟอร์ม/เก็บข้อมูล</div>
                            </div>
                            <i class="ri-arrow-right-s-line shortcut-arrow"></i>
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="javascript:void(0);" class="shortcut-card">
                            <div class="icon-box bg-icon-teal">
                                <i class="ri-file-text-line"></i>
                            </div>
                            <div>
                                <div class="shortcut-title">BMS Admin</div>
                                <div class="shortcut-desc">ฟอร์ม/เก็บข้อมูล</div>
                            </div>
                            <i class="ri-arrow-right-s-line shortcut-arrow"></i>
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="{{ route('office.purchase-requisitions.index') }}" class="shortcut-card">
                            <div class="icon-box bg-icon-blue">
                                <i class="ri-shopping-cart-2-line"></i>
                            </div>
                            <div>
                                <div class="shortcut-title">ใบขอซื้อ</div>
                                <div class="shortcut-desc">จัดซื้อ/ขอซื้อ</div>
                            </div>
                            <i class="ri-arrow-right-s-line shortcut-arrow"></i>
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="javascript:void(0);" class="shortcut-card">
                            <div class="icon-box bg-icon-orange">
                                <i class="ri-error-warning-fill"></i>
                            </div>
                            <div>
                                <div class="shortcut-title">IMS</div>
                                <div class="shortcut-desc">ปัญหา / Issue</div>
                            </div>
                            <i class="ri-arrow-right-s-line shortcut-arrow"></i>
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="javascript:void(0);" class="shortcut-card">
                            <div class="icon-box bg-icon-red">
                                <i class="ri-logout-box-r-line"></i>
                            </div>
                            <div>
                                <div class="shortcut-title">แจ้งลาออก</div>
                                <div class="shortcut-desc">เริ่มกระบวนการ Exit</div>
                            </div>
                            <i class="ri-arrow-right-s-line shortcut-arrow"></i>
                        </a>
                    </div>

                    <div class="col-md-4">
                        <a href="javascript:void(0);" class="shortcut-card">
                            <div class="icon-box bg-icon-indigo">
                                <i class="ri-pie-chart-2-fill"></i>
                            </div>
                            <div>
                                <div class="shortcut-title">My Dashboard</div>
                                <div class="shortcut-desc">งานของฉัน</div>
                            </div>
                            <i class="ri-arrow-right-s-line shortcut-arrow"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="tab-pane" id="docs-tab" role="tabpanel">
                <!-- Banner Header Card -->
                <div class="docs-banner-card mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="docs-banner-icon">
                            <i class="ri-file-text-line"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.15rem;">เอกสารต่างๆ ของฉัน</h5>
                            <p class="text-muted fs-13 mb-0">จัดการเอกสารและไฟล์ของคุณได้อย่างสะดวก รวดเร็ว ปลอดภัย</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="docs-illustration-box">
                            <i class="ri-folder-2-fill folder-icon"></i>
                            <i class="ri-cloud-line cloud-badge"></i>
                        </div>
                        <div class="cloud-btn-circle">
                            <i class="ri-cloud-line fs-18"></i>
                        </div>
                    </div>
                </div>

                <!-- Main Content Card -->
                <div class="docs-main-card">
                    <!-- Top Search & Filter Bar -->
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            <div class="docs-banner-icon" style="width: 40px; height: 40px; font-size: 20px;">
                                <i class="ri-file-text-line"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">เอกสารต่างๆ ของฉัน</h6>
                                <p class="text-muted fs-12 mb-0">ในสรุปการ์ดนี้จะแสดงให้คุณได้ดูบนหน้ารายการทั้งหมด — คุณเก็บเฉพาะของคุณ</p>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <div class="position-relative">
                            <input type="text" class="form-control search-doc-input pe-4" placeholder="ค้นหาเอกสาร..." style="width: 220px;">
                                <i class="ri-search-line position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                            </div>
                            <button type="button" class="btn btn-filter-custom">
                            <i class="ri-filter-3-line"></i> ตัวกรอง
                            </button>
                            <button type="button" class="btn btn-add-doc-custom">
                            <i class="ri-add-line"></i> เพิ่มเอกสาร
                            </button>
                        </div>
                    </div>

                    <!-- Empty State View -->
                    <div class="text-center py-5">
                        <div class="empty-doc-illustration">
                            <div class="empty-box-icon">
                                <i class="ri-inbox-archive-line text-white fs-24"></i>
                            </div>
                            <i class="ri-star-fill star-sparkle" style="top: 25px; left: 25px;"></i>
                            <i class="ri-star-fill star-sparkle" style="top: 40px; right: 30px;"></i>
                            <i class="ri-star-fill star-sparkle" style="bottom: 35px; right: 25px;"></i>
                        </div>

                        <h5 class="fw-bold text-dark mb-2" style="font-size: 1.15rem;">ยังไม่มีเอกสารที่ออกให้คุณ</h5>
                        <p class="text-muted fs-13 mb-4" style="max-width: 320px; margin: 0 auto; line-height: 1.5;">
                            เอกสารที่ออกให้คุณจะปรากฏที่นี่<br>เมื่อมีการแชร์หรือมอบหมายเอกสาร
                        </p>

                        <button type="button" class="btn btn-usage-guide">
                            <i class="ri-question-line"></i> ดูวิธีการใช้งาน
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 1: ตรวจสอบ -->
    <div class="section-title">ตรวจสอบ</div>
    <div class="row g-3">
        <div class="col-md-6">
            <div class="status-card">
                <div class="d-flex align-items-center">
                    <div class="status-icon-circle status-icon-green">
                        <i class="ri-file-text-line"></i>
                    </div>
                    <div class="status-info">
                        <div class="name">EMS</div>
                        <div class="sub">รอตรวจสอบ</div>
                    </div>
                </div>
                <div class="status-count">
                    <div class="num">0</div>
                    <div class="unit">รายการ</div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="status-card">
                <div class="d-flex align-items-center">
                    <div class="status-icon-circle status-icon-teal">
                        <i class="ri-file-text-line"></i>
                    </div>
                    <div class="status-info">
                        <div class="name">BMS</div>
                        <div class="sub">รอตรวจสอบ</div>
                    </div>
                </div>
                <div class="status-count">
                    <div class="num">0</div>
                    <div class="unit">รายการ</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: ตรวจสอบแล้วรออนุมัติ -->
    <div class="section-title">ตรวจสอบแล้วรออนุมัติ</div>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="status-card">
                <div class="d-flex align-items-center">
                    <div class="status-icon-circle status-icon-circle-soft-green">
                        <i class="ri-check-line"></i>
                    </div>
                    <div class="status-info">
                        <div class="name">EMS</div>
                        <div class="sub">รออนุมัติ</div>
                    </div>
                </div>
                <div class="status-count">
                    <div class="num">0</div>
                    <div class="unit">รายการ</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="status-card">
                <div class="d-flex align-items-center">
                    <div class="status-icon-circle status-icon-circle-soft-teal">
                        <i class="ri-check-line"></i>
                    </div>
                    <div class="status-info">
                        <div class="name">BMS</div>
                        <div class="sub">รออนุมัติ</div>
                    </div>
                </div>
                <div class="status-count">
                    <div class="num">0</div>
                    <div class="unit">รายการ</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="status-card">
                <div class="d-flex align-items-center">
                    <div class="status-icon-circle status-icon-circle-soft-blue">
                        <i class="ri-check-line"></i>
                    </div>
                    <div class="status-info">
                        <div class="name">ใบเสนอราคา</div>
                        <div class="sub">รออนุมัติ</div>
                    </div>
                </div>
                <div class="status-count">
                    <div class="num">0</div>
                    <div class="unit">รายการ</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="status-card">
                <div class="d-flex align-items-center">
                    <div class="status-icon-circle status-icon-circle-soft-purple">
                        <i class="ri-check-line"></i>
                    </div>
                    <div class="status-info">
                        <div class="name">ใบสั่งซื้อ</div>
                        <div class="sub">รออนุมัติ</div>
                    </div>
                </div>
                <div class="status-count">
                    <div class="num">0</div>
                    <div class="unit">รายการ</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="status-card">
                <div class="d-flex align-items-center">
                    <div class="status-icon-circle status-icon-circle-soft-orange">
                        <i class="ri-check-line"></i>
                    </div>
                    <div class="status-info">
                        <div class="name">หัก ณ ที่จ่าย (ซื้อ)</div>
                        <div class="sub">รออนุมัติ</div>
                    </div>
                </div>
                <div class="status-count">
                    <div class="num">33</div>
                    <div class="unit">รายการ</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
