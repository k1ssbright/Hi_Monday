@extends('office.layout.office')
@section('title', 'ภาพรวม / ทางลัด | Hi Monday')

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', ['section' => 'ภาพรวม', 'title' => 'ภาพรวม'])

    <ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#tab-home" role="tab">
                <i class="ri-apps-2-line me-1"></i> ทางลัด / ภาพรวม
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-mydocs" role="tab">
                <i class="ri-file-list-3-line me-1"></i> เอกสารต่างๆ ของฉัน
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="tab-home" role="tabpanel">
            <div class="row mb-4 g-3" role="region" aria-label="ลิงก์ด่วน">
                <div class="col-6 col-md-3">
                    <div class="card card-animate shadow-1 h-100 mb-0 border border-info bg-info-subtle">
                        <a href="{{ route('office.leads.index') }}" class="text-reset text-decoration-none d-block h-100">
                            <div class="card-body py-4 text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <span class="avatar-title bg-light text-info rounded-circle fs-3 material-shadow">
                                        <i class="ri-survey-line"></i>
                                    </span>
                                </div>
                                <h6 class="fw-semibold mb-1">Lead</h6>
                                <p class="text-muted fs-12 mb-0">รายการและสร้าง Lead</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card card-animate shadow-1 h-100 mb-0 border border-primary bg-primary-subtle">
                        <a href="{{ route('office.opportunities.index') }}" class="text-reset text-decoration-none d-block h-100">
                            <div class="card-body py-4 text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <span class="avatar-title bg-light text-primary rounded-circle fs-3 material-shadow">
                                        <i class="ri-git-merge-line"></i>
                                    </span>
                                </div>
                                <h6 class="fw-semibold mb-1">Opportunities</h6>
                                <p class="text-muted fs-12 mb-0">โอกาสทางการขาย</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card card-animate shadow-1 h-100 mb-0 border border-success bg-success-subtle">
                        <a href="{{ route('office.quotations.index') }}" class="text-reset text-decoration-none d-block h-100">
                            <div class="card-body py-4 text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <span class="avatar-title bg-light text-success rounded-circle fs-3 material-shadow">
                                        <i class="ri-file-text-line"></i>
                                    </span>
                                </div>
                                <h6 class="fw-semibold mb-1">Quotations</h6>
                                <p class="text-muted fs-12 mb-0">ใบเสนอราคา</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card card-animate shadow-1 h-100 mb-0 border border-secondary bg-secondary-subtle">
                        <a href="{{ route('office.sales-orders.index') }}" class="text-reset text-decoration-none d-block h-100">
                            <div class="card-body py-4 text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <span class="avatar-title bg-light text-secondary rounded-circle fs-3 material-shadow">
                                        <i class="ri-file-list-3-line"></i>
                                    </span>
                                </div>
                                <h6 class="fw-semibold mb-1">Sales Orders</h6>
                                <p class="text-muted fs-12 mb-0">ใบสั่งขาย</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card card-animate shadow-1 h-100 mb-0 border border-warning bg-warning-subtle">
                        <a href="{{ route('office.billing-notes.index') }}" class="text-reset text-decoration-none d-block h-100">
                            <div class="card-body py-4 text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <span class="avatar-title bg-light text-warning rounded-circle fs-3 material-shadow">
                                        <i class="ri-bill-line"></i>
                                    </span>
                                </div>
                                <h6 class="fw-semibold mb-1">Billing Notes</h6>
                                <p class="text-muted fs-12 mb-0">ใบวางบิล (BN)</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card card-animate shadow-1 h-100 mb-0 border border-danger bg-danger-subtle">
                        <a href="{{ route('office.tax-invoices.index') }}" class="text-reset text-decoration-none d-block h-100">
                            <div class="card-body py-4 text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <span class="avatar-title bg-light text-danger rounded-circle fs-3 material-shadow">
                                        <i class="ri-file-paper-2-line"></i>
                                    </span>
                                </div>
                                <h6 class="fw-semibold mb-1">Tax Invoices</h6>
                                <p class="text-muted fs-12 mb-0">ใบกำกับภาษี (TI)</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card card-animate shadow-1 h-100 mb-0 border border-info bg-info-subtle">
                        <a href="{{ route('office.delivery-notes.index') }}" class="text-reset text-decoration-none d-block h-100">
                            <div class="card-body py-4 text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <span class="avatar-title bg-light text-info rounded-circle fs-3 material-shadow">
                                        <i class="ri-truck-line"></i>
                                    </span>
                                </div>
                                <h6 class="fw-semibold mb-1">Delivery Notes</h6>
                                <p class="text-muted fs-12 mb-0">ใบส่งของ (DLV)</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card card-animate shadow-1 h-100 mb-0 border border-success bg-success-subtle">
                        <a href="{{ route('office.receipts.index') }}" class="text-reset text-decoration-none d-block h-100">
                            <div class="card-body py-4 text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <span class="avatar-title bg-light text-success rounded-circle fs-3 material-shadow">
                                        <i class="ri-cash-line"></i>
                                    </span>
                                </div>
                                <h6 class="fw-semibold mb-1">Receipts</h6>
                                <p class="text-muted fs-12 mb-0">ใบเสร็จรับเงิน (RCT)</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card card-animate shadow-1 h-100 mb-0 border border-secondary bg-secondary-subtle">
                        <a href="{{ route('office.credit-notes.index') }}" class="text-reset text-decoration-none d-block h-100">
                            <div class="card-body py-4 text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <span class="avatar-title bg-light text-secondary rounded-circle fs-3 material-shadow">
                                        <i class="ri-file-reduce-line"></i>
                                    </span>
                                </div>
                                <h6 class="fw-semibold mb-1">Credit Notes</h6>
                                <p class="text-muted fs-12 mb-0">ใบลดหนี้ (CN)</p>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card card-animate shadow-1 h-100 mb-0 border border-dark bg-dark-subtle">
                        <a href="{{ route('office.debit-notes.index') }}" class="text-reset text-decoration-none d-block h-100">
                            <div class="card-body py-4 text-center">
                                <div class="avatar-sm mx-auto mb-3">
                                    <span class="avatar-title bg-light text-dark rounded-circle fs-3 material-shadow">
                                        <i class="ri-file-add-line"></i>
                                    </span>
                                </div>
                                <h6 class="fw-semibold mb-1">Debit Notes</h6>
                                <p class="text-muted fs-12 mb-0">ใบเพิ่มหนี้ (DBN)</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane" id="tab-mydocs" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="avatar-sm">
                            <span class="avatar-title bg-primary-subtle text-primary rounded fs-4">
                                <i class="ri-folder-2-line"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="mb-1">เอกสารต่างๆ ของฉัน</h5>
                            <p class="text-muted mb-0">จัดการเอกสารและไฟล์ของคุณได้อย่างสะดวก รวดเร็ว ปลอดภัย</p>
                        </div>
                    </div>
                    <div class="text-center py-5">
                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px"></lord-icon>
                        <h5 class="mt-3">ยังไม่มีเอกสารที่ออกให้คุณ</h5>
                        <p class="text-muted mb-0">เอกสารที่ออกให้คุณจะปรากฏที่นี่ เมื่อมีการแชร์หรือมอบหมายเอกสาร</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fs-16 mb-3 mt-2">ตรวจสอบ</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card card-animate mb-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-success-subtle text-success rounded fs-3">
                                <i class="ri-survey-line"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted fs-13 mb-1">Lead</p>
                            <h5 class="mb-0">รอตรวจสอบ</h5>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <h4 class="fs-22 fw-semibold ff-secondary mb-0">0</h4>
                            <p class="text-muted fs-12 mb-0">รายการ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-animate mb-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm flex-shrink-0">
                            <span class="avatar-title bg-primary-subtle text-primary rounded fs-3">
                                <i class="ri-git-merge-line"></i>
                            </span>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="text-uppercase fw-medium text-muted fs-13 mb-1">Opportunity</p>
                            <h5 class="mb-0">รอตรวจสอบ</h5>
                        </div>
                        <div class="flex-shrink-0 text-end">
                            <h4 class="fs-22 fw-semibold ff-secondary mb-0">0</h4>
                            <p class="text-muted fs-12 mb-0">รายการ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h5 class="fs-16 mb-3">ตรวจสอบแล้วรออนุมัติ</h5>
    <div class="row g-3 mb-4">
        @foreach ([
            ['ใบเสนอราคา', 'ri-file-text-line', 'success', 0],
            ['ใบสั่งซื้อ', 'ri-shopping-cart-2-line', 'secondary', 0],
            ['Lead', 'ri-survey-line', 'info', 0],
        ] as [$name, $icon, $color, $count])
            <div class="col-md-4">
                <div class="card card-animate mb-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-{{ $color }}-subtle text-{{ $color }} rounded fs-3">
                                    <i class="{{ $icon }}"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $name }}</h6>
                                <p class="text-muted fs-12 mb-0">รออนุมัติ</p>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <h4 class="fs-22 fw-semibold ff-secondary mb-0">{{ $count }}</h4>
                                <p class="text-muted fs-12 mb-0">รายการ</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
