@extends('office.layout.office')

@section('title', 'สมุดรายชื่อคู่ค้า | Hi Monday')

@section('style')
<style>
    .biz-card-main {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: none;
        padding: 24px;
    }



    .btn-add-biz {
        background-color: #3b5998;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 10px 22px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background-color 0.2s;
    }

    .btn-add-biz:hover {
        background-color: #2d4373;
        color: #ffffff;
    }

    .filter-label-biz {
        font-size: 0.825rem;
        font-weight: 500;
        color: #64748b;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .filter-input-biz, .filter-select-biz {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 9px 14px;
        font-size: 0.875rem;
        color: #1e293b;
    }

    .filter-input-biz::placeholder {
        color: #94a3b8;
    }

    .btn-search-navy-biz {
        background-color: #3b5998;
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 9px 24px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .btn-search-navy-biz:hover {
        background-color: #2d4373;
        color: #ffffff;
    }

    .btn-reset-filter-biz {
        background-color: #f1f5f9;
        color: #475569;
        border: none;
        border-radius: 8px;
        padding: 9px 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .btn-reset-filter-biz:hover {
        background-color: #e2e8f0;
        color: #1e293b;
    }

    .table-biz-custom {
        width: 100%;
        border-collapse: collapse;
    }

    .table-biz-custom th {
        color: #475569;
        font-size: 0.825rem;
        font-weight: 700;
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        background-color: #f8fafc;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-biz-custom td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .table-biz-custom td.border-end,
    .table-biz-custom th.border-end {
        border-right: 1px solid #e2e8f0 !important;
    }

    .table-biz-custom tr.border-top td {
        border-top: 2px solid #cbd5e1 !important;
    }

    .table-biz-custom tbody tr:nth-child(odd) td:not([rowspan]) {
        background-color: #ffffff;
    }

    .table-biz-custom tbody tr:nth-child(even) td:not([rowspan]) {
        background-color: #edf2f7;
    }

    .table-biz-custom tbody tr:hover td:not([rowspan]) {
        background-color: #cbd5e1;
    }

    .switch-navy .form-check-input {
        width: 44px;
        height: 24px;
        cursor: pointer;
        background-color: #cbd5e1;
        border: none;
    }

    .switch-navy .form-check-input:checked {
        background-color: #3b5998;
        border-color: #3b5998;
    }

    .btn-action-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        border: none;
        color: #ffffff;
        transition: opacity 0.2s;
    }

    .btn-action-view { background-color: #3b5998; }
    .btn-action-edit { background-color: #ef5350; }
    .btn-action-icon:hover { opacity: 0.9; color: #ffffff; }

    .sort-icon {
        color: #cbd5e1;
        font-size: 14px;
        margin-left: 4px;
    }

    .biz-logo-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background-color: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #3b5998;
        font-size: 0.9rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted fs-13">สมุดรายชื่อคู่ค้า</span>
        <div class="fs-13 text-muted">
            <span>ภาพรวม</span> <i class="ri-arrow-right-s-line"></i> <span class="fw-semibold text-dark">สมุดรายชื่อคู่ค้า</span>
        </div>
    </div>

    @include('office.settings.partials.flash')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0" style="font-size: 1.3rem;">สมุดรายชื่อคู่ค้า ( <span id="partnerTotalCount">{{ $totalPartners }} คู่ค้าหลัก / {{ $totalBranches }} รายการสาขา</span> )</h4>
        <a href="{{ route('office.partners.create') }}" class="btn btn-add-biz">
            <i class="ri-add-line fs-18"></i> เพิ่มคู่ค้า
        </a>
    </div>

    <div class="biz-card-main mb-4">
        <form method="get" action="{{ route('office.partners.index') }}" id="partnerFilterForm">
            <div class="row g-3 align-items-end mb-4">
                <div class="col-md-5">
                    <label class="filter-label-biz">
                        คำค้นหา (พิมพ์แล้วกด Enter เพื่อค้นหา) <i class="ri-question-line text-muted"></i>
                    </label>
                    <input type="text" name="q" value="{{ $filters['q'] }}" class="form-control filter-input-biz" placeholder="ค้นหาด้วย ชื่อคู่ค้า, รหัส, เลขผู้เสียภาษี">
                </div>

                <div class="col-md-3">
                    <label class="filter-label-biz">สถานะ</label>
                    <select name="status" class="form-select filter-select-biz" onchange="this.form.submit()">
                        <option value="all" @selected($filters['status'] === 'all')>ทั้งหมด</option>
                        <option value="active" @selected($filters['status'] === 'active')>เปิดใช้งาน</option>
                        <option value="inactive" @selected($filters['status'] === 'inactive')>ปิดใช้งาน</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="filter-label-biz">ประเภทคู่ค้า</label>
                    <select name="type" class="form-select filter-select-biz" onchange="this.form.submit()">
                        <option value="all" @selected($filters['type'] === 'all')>ทั้งหมด</option>
                        <option value="customer" @selected($filters['type'] === 'customer')>ลูกค้า</option>
                        <option value="vendor" @selected($filters['type'] === 'vendor')>ผู้ขาย</option>
                        <option value="both" @selected($filters['type'] === 'both')>ลูกค้า & ผู้ขาย</option>
                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-search-navy-biz flex-fill">ค้นหา</button>
                    <a href="{{ route('office.partners.index') }}" class="btn btn-reset-filter-biz">ResetFilter</a>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2 text-muted fs-13">
                    <span>แสดง</span>
                    <select name="per_page" class="form-select form-select-sm" style="width: 70px;" onchange="this.form.submit()">
                        <option value="10" @selected((int) $filters['per_page'] === 10)>10</option>
                        <option value="25" @selected((int) $filters['per_page'] === 25)>25</option>
                        <option value="50" @selected((int) $filters['per_page'] === 50)>50</option>
                    </select>
                    <span>รายการต่อหน้า</span>
                </div>
            </div>
        </form>

        @include('office.partner.partials.table')
    </div>
</div>


<!-- Modal แก้ไขข้อมูลคู่ค้า -->
<div class="modal fade" id="partnerEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold fs-16 mb-0"><i class="ri-pencil-line text-danger me-1"></i> แก้ไขข้อมูลคู่ค้า</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editPartnerForm" method="post" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <h6 class="fw-bold mb-3 pb-2 border-bottom text-primary">ข้อมูลบริษัทหลัก (Company Info)</h6>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold">รหัสคู่ค้า <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="partner_code" id="editPartnerCode" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold">ประเภทคู่ค้า</label>
                            <select class="form-select" name="partner_role" id="editPartnerType">
                                <option value="customer">ลูกค้า</option>
                                <option value="vendor">ผู้ขาย</option>
                                <option value="both">ลูกค้า & ผู้ขาย</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold">ชื่อบริษัท <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="partner_name_th" id="editPartnerName" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13 fw-semibold">เลขประจำตัวผู้เสียภาษี 13 หลัก <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="partner_tax_no" id="editPartnerTax" maxlength="13" required>
                        </div>
                    </div>
                    <input type="hidden" name="active_status" id="editPartnerActive" value="1">
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-target="#partnerEditModal"]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('editPartnerForm').action = button.dataset.action;
            document.getElementById('editPartnerCode').value = button.dataset.code || '';
            document.getElementById('editPartnerName').value = button.dataset.name || '';
            document.getElementById('editPartnerTax').value = button.dataset.tax || '';
            document.getElementById('editPartnerType').value = button.dataset.role || 'customer';
        });
    });
});
</script>
@endsection
