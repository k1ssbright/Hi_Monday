@extends('office.layout.office')

@section('title', 'ธุรกิจของฉัน (Business) | Hi Monday')

@section('style')
<style>
    /* Custom Styling for My Business Page */
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

    /* Export Buttons */
    .btn-export {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 6px 14px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }
    .btn-export-excel { color: #16a34a; }
    .btn-export-csv { color: #16a34a; }
    .btn-export-pdf { color: #dc2626; }

    /* Custom Table Styling */
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

    /* เส้นกั้นแนวตั้งขวาของกลุ่มบริษัทหลัก (บางลงและอ่อนสบายตา) */
    .table-biz-custom td.border-end,
    .table-biz-custom th.border-end {
        border-right: 1px solid #e2e8f0 !important;
    }

    /* เส้นกั้นแบ่งระหว่างกลุ่มบริษัท (ปรับลดความหนาและสีอ่อนลง) */
    .table-biz-custom tr.border-top td {
        border-top: 2px solid #cbd5e1 !important;
    }

    /* Zebra Striping (สลับสีเข้ม-ขาว) */
    .table-biz-custom tbody tr:nth-child(odd) td:not([rowspan]) {
        background-color: #ffffff;
    }

    .table-biz-custom tbody tr:nth-child(even) td:not([rowspan]) {
        background-color: #edf2f7;
    }

    .table-biz-custom tbody tr:hover td:not([rowspan]) {
        background-color: #cbd5e1;
    }

    /* Switch Custom Navy */
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

    /* Action Buttons */
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
    <!-- Header Top Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted fs-13">ธุรกิจของฉัน</span>
        <div class="fs-13 text-muted">
            <span>ภาพรวม</span> <i class="ri-arrow-right-s-line"></i> <span class="fw-semibold text-dark">ธุรกิจของฉัน</span>
        </div>
    </div>

    <!-- Title & Add Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0" style="font-size: 1.3rem;">รายการบริษัทและธุรกิจของฉัน ( <span id="bizTotalCount">2 บริษัทหลัก / 5 รายการย่อย</span> )</h4>
        <button type="button" class="btn btn-add-biz" data-bs-toggle="modal" data-bs-target="#addCompanyModal">
            <i class="ri-add-line fs-18"></i> เพิ่มบริษัท
        </button>
    </div>

    <!-- Search & Filters Container Card -->
    <div class="biz-card-main mb-4">
        <div class="row g-3 align-items-end mb-4">
            <!-- Search Field -->
            <div class="col-md-5">
                <label class="filter-label-biz">
                    คำค้นหา (พิมพ์แล้วกด Enter เพื่อค้นหา) <i class="ri-question-line text-muted"></i>
                </label>
                <input type="text" id="bizSearchInput" class="form-control filter-input-biz" placeholder="ค้นหาด้วย ชื่อบริษัท, รหัสย่อ, เลขผู้เสียภาษี">
            </div>

            <!-- Status Select -->
            <div class="col-md-3">
                <label class="filter-label-biz">สถานะ</label>
                <select id="bizStatusSelect" class="form-select filter-select-biz">
                    <option value="all" selected>ทั้งหมด</option>
                    <option value="active">เปิดใช้งาน</option>
                    <option value="inactive">ปิดใช้งาน</option>
                </select>
            </div>

            <!-- Business Type Select -->
            <div class="col-md-2">
                <label class="filter-label-biz">ประเภทธุรกิจ</label>
                <select id="bizTypeSelect" class="form-select filter-select-biz">
                    <option value="all" selected>ทั้งหมด</option>
                    <option value="company">บริษัท จำกัด</option>
                    <option value="partnership">ห้างหุ้นส่วน</option>
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="col-md-2 d-flex gap-2">
                <button type="button" id="bizBtnSearch" class="btn btn-search-navy-biz flex-fill">ค้นหา</button>
                <button type="button" id="bizBtnReset" class="btn btn-reset-filter-biz">ResetFilter</button>
            </div>
        </div>

        <!-- Controls Bar -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2 text-muted fs-13">
                <span>แสดง</span>
                <select class="form-select form-select-sm" style="width: 70px;">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span>รายการต่อหน้า</span>
            </div>
        </div>

        @include('office.business.partails.table')
    </div>
</div>

<!-- Modal ดูรายละเอียดธุรกิจ -->
<div class="modal fade" id="bizViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold fs-16 mb-0"><i class="ri-building-line text-primary me-1"></i> รายละเอียดธุรกิจ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="biz-logo-avatar mx-auto mb-2" id="viewBizAvatar" style="width: 56px; height: 56px; font-size: 1.2rem;">-</div>
                    <h5 class="fw-bold mb-1" id="viewBizName">-</h5>
                    <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill" id="viewBizCode">-</span>
                </div>
                <hr class="my-3 opacity-25">
                <div class="row g-3">
                    <div class="col-6">
                        <small class="text-muted d-block">รหัสประจำตัวผู้เสียภาษี</small>
                        <span class="fw-medium text-dark" id="viewBizTax">-</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block">เบอร์โทรศัพท์</small>
                        <span class="fw-medium text-dark" id="viewBizPhone">-</span>
                    </div>
                    <div class="col-12">
                        <small class="text-muted d-block">ประเภทธุรกิจ</small>
                        <span class="fw-medium text-dark" id="viewBizType">-</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal เพิ่มบริษัทใหม่ -->
<div class="modal fade" id="addCompanyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold fs-16 mb-0"><i class="ri-building-line text-primary me-1"></i> เพิ่มข้อมูลบริษัทใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="saveNewCompany(event)">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fs-13 fw-semibold">รหัสย่อบริษัท (Company Code) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addCompanyCode" placeholder="เช่น CCC" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-13 fw-semibold">ชื่อบริษัท / องค์กรหลัก <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="addCompanyName" placeholder="กรอกชื่อบริษัทเต็ม" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-13 fw-semibold">เลขประจำตัวผู้เสียภาษีบริษัท</label>
                        <input type="text" class="form-control" id="addCompanyTax" placeholder="กรอกเลขผู้เสียภาษี 13 หลัก">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fs-13 fw-semibold">เบอร์โทรศัพท์บริษัท</label>
                            <input type="text" class="form-control" id="addCompanyPhone" placeholder="กรอกเบอร์โทรศัพท์">
                        </div>
                        <div class="col-6">
                            <label class="form-label fs-13 fw-semibold">ประเภทองค์กร</label>
                            <select class="form-select" id="addCompanyType">
                                <option value="สำนักงานใหญ่">สำนักงานใหญ่</option>
                                <option value="องค์กรหลัก">องค์กรหลัก</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">บันทึกสร้างบริษัท</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal แก้ไขข้อมูลธุรกิจ -->
<div class="modal fade" id="bizEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold fs-16 mb-0"><i class="ri-pencil-line text-danger me-1"></i> แก้ไขข้อมูลธุรกิจ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form onsubmit="saveEditBiz(event)">
                <div class="modal-body p-4">
                    <input type="hidden" id="editBizRowId">
                    <div class="mb-3">
                        <label class="form-label fs-13 fw-semibold">รหัสย่อธุรกิจ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editBizCode" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-13 fw-semibold">ชื่อธุรกิจ / บริษัท <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editBizName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fs-13 fw-semibold">เลขประจำตัวผู้เสียภาษี</label>
                        <input type="text" class="form-control" id="editBizTax">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label fs-13 fw-semibold">เบอร์โทรศัพท์</label>
                            <input type="text" class="form-control" id="editBizPhone">
                        </div>
                        <div class="col-6">
                            <label class="form-label fs-13 fw-semibold">ประเภทธุรกิจ</label>
                            <select class="form-select" id="editBizType">
                                <option value="บริษัท จำกัด">บริษัท จำกัด</option>
                                <option value="ห้างหุ้นส่วน">ห้างหุ้นส่วน</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal แสดงข้อมูลธุรกิจทั้งหมดของบริษัท -->
<div class="modal fade" id="companyDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header bg-light border-0 py-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="biz-logo-avatar text-white fw-bold shadow-sm" id="modalCompanyBadge" style="width: 48px; height: 48px; font-size: 1.2rem;">-</div>
                    <div>
                        <h5 class="modal-title fw-bold fs-16 mb-0 text-dark" id="modalCompanyName">-</h5>
                        <small class="text-muted" id="modalCompanySub">-</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark fs-14">
                        <i class="ri-building-4-line text-primary me-1"></i> รายการธุรกิจ/สาขาทั้งหมดภายใต้บริษัทนี้
                    </h6>
                    <span class="badge bg-primary-subtle text-primary px-3 py-1 rounded-pill" id="modalBusinessCount">0 ธุรกิจ</span>
                </div>
                <div class="table-responsive border rounded-3">
                    <table class="table table-hover align-middle mb-0" id="modalSubBusinessTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 120px;">รหัสธุรกิจ</th>
                                <th>ชื่อธุรกิจ / สาขา</th>
                                <th>เลขผู้เสียภาษี</th>
                                <th>เบอร์โทรศัพท์</th>
                                <th>ประเภท</th>
                                <th class="text-center">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody id="modalSubBusinessList">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary btn-sm px-4 rounded-3" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

@section('script')
@php
    $businessShowUrlPattern = route('office.business-show', ['id' => '__ID__']);
@endphp
<script>
const businessShowUrl = (code) => @json($businessShowUrlPattern).replace('__ID__', encodeURIComponent(code));

// Data Store Mockup for Sub Businesses
const companyBusinessData = {
    'AAA': [
        { code: 'AAA-01', name: 'เอบี อินโนเวชั่นส์ (สำนักงานใหญ่)', tax: '0105562012345', phone: '021234567', type: 'บริษัท จำกัด', status: true },
        { code: 'AAA-02', name: 'เอบี เทคโนโลยี & โซลูชั่น', tax: '0105562012346', phone: '021234568', type: 'บริษัท จำกัด', status: true },
        { code: 'AAA-03', name: 'เอบี ดิจิทัล ซัพพลาย', tax: '0105562012347', phone: '021234569', type: 'บริษัท จำกัด', status: true }
    ],
    'BBB': [
        { code: 'BBB-01', name: 'บีบี ลอนดรี้ แมชชีนเนอรี่', tax: '0105562012350', phone: '021234572', type: 'บริษัท จำกัด', status: true },
        { code: 'BBB-02', name: 'บีบีอินโนเวชั่นส์ (สาขา 2)', tax: '0105562012351', phone: '021234573', type: 'ห้างหุ้นส่วน', status: false }
    ]
};

function openCompanyDetailModal(code, name, subText, colorClass) {
    const badge = document.getElementById('modalCompanyBadge');
    badge.textContent = code;
    badge.className = `biz-logo-avatar text-white fw-bold shadow-sm bg-${colorClass}`;
    
    document.getElementById('modalCompanyName').textContent = name;
    document.getElementById('modalCompanySub').textContent = subText;
    
    const listContainer = document.getElementById('modalSubBusinessList');
    listContainer.innerHTML = '';
    
    const items = companyBusinessData[code] || [];
    document.getElementById('modalBusinessCount').textContent = `${items.length} ธุรกิจ`;
    
    items.forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="fw-bold text-dark">${item.code}</td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <i class="ri-git-commit-line text-primary"></i>
                    <span class="fw-semibold text-dark">${item.name}</span>
                </div>
            </td>
            <td>${item.tax}</td>
            <td>${item.phone}</td>
            <td><span class="badge bg-light text-dark border">${item.type}</span></td>
            <td class="text-center">
                ${item.status ? '<span class="badge bg-success-subtle text-success px-2 py-1"><i class="ri-checkbox-circle-fill me-1"></i> เปิดใช้งาน</span>' : '<span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="ri-close-circle-fill me-1"></i> ปิดใช้งาน</span>'}
            </td>
        `;
        listContainer.appendChild(tr);
    });

    const modal = new bootstrap.Modal(document.getElementById('companyDetailModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('bizSearchInput');
    const statusSelect = document.getElementById('bizStatusSelect');
    const typeSelect = document.getElementById('bizTypeSelect');
    const btnSearch = document.getElementById('bizBtnSearch');
    const btnReset = document.getElementById('bizBtnReset');
    const tableRows = document.querySelectorAll('#bizTable tbody tr');

    function filterBizData() {
        const query = searchInput.value.trim().toLowerCase();
        const statusVal = statusSelect.value;
        const typeVal = typeSelect.value;

        let visibleCount = 0;

        tableRows.forEach(row => {
            const textContent = row.textContent.toLowerCase();
            const matchQuery = !query || textContent.includes(query);

            if (matchQuery) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('bizPaginationInfo').textContent = `แสดงรายการที่ตรงตามเงื่อนไข`;
    }

    if (btnSearch) btnSearch.addEventListener('click', filterBizData);
    if (searchInput) searchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') filterBizData();
    });
    if (statusSelect) statusSelect.addEventListener('change', filterBizData);
    if (typeSelect) typeSelect.addEventListener('change', filterBizData);

    if (btnReset) {
        btnReset.addEventListener('click', function() {
            searchInput.value = '';
            statusSelect.value = 'all';
            typeSelect.value = 'all';
            filterBizData();
        });
    }
});

function openBizViewModal(id, code, name, tax, phone, type) {
    document.getElementById('viewBizAvatar').textContent = code;
    document.getElementById('viewBizCode').textContent = code;
    document.getElementById('viewBizName').textContent = name;
    document.getElementById('viewBizTax').textContent = tax;
    document.getElementById('viewBizPhone').textContent = phone;
    document.getElementById('viewBizType').textContent = type;

    const modal = new bootstrap.Modal(document.getElementById('bizViewModal'));
    modal.show();
}

function openBizEditModal(id, code, name, tax, phone, type) {
    document.getElementById('editBizRowId').value = id;
    document.getElementById('editBizCode').value = code;
    document.getElementById('editBizName').value = name;
    document.getElementById('editBizTax').value = tax;
    document.getElementById('editBizPhone').value = phone;
    document.getElementById('editBizType').value = type;

    const modal = new bootstrap.Modal(document.getElementById('bizEditModal'));
    modal.show();
}

let currentGroupIndex = 2;

function saveNewCompany(event) {
    event.preventDefault();
    const code = document.getElementById('addCompanyCode').value.trim();
    const name = document.getElementById('addCompanyName').value.trim();
    const tax = document.getElementById('addCompanyTax').value.trim() || '0105562099999';
    const phone = document.getElementById('addCompanyPhone').value.trim() || '021234999';
    const type = document.getElementById('addCompanyType').value.trim();

    if (!code || !name) return;

    currentGroupIndex++;
    const tbody = document.querySelector('#bizTable tbody');

    const newTr = document.createElement('tr');
    newTr.className = 'table-group-header border-top';
    newTr.innerHTML = `
        <td class="text-center text-muted fw-bold align-middle border-end bg-light-subtle">${currentGroupIndex}</td>
        <td class="align-middle bg-light-subtle border-end">
            <a href="${businessShowUrl(code)}" class="d-block text-decoration-none p-2 rounded-3 company-link-hover">
                <div class="d-flex align-items-center gap-2">
                    <div class="biz-logo-avatar bg-warning text-white fw-bold shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem;">${code}</div>
                    <div>
                        <div class="fw-bold text-dark fs-15">
                            ${name} (${code}) <i class="ri-arrow-right-circle-line ms-1 fs-16"></i>
                        </div>
                        <small class="text-muted">${type} • 1 รายการธุรกิจ</small>
                    </div>
                </div>
            </a>
        </td>
        <td class="fw-bold text-dark">${code}-01</td>
        <td>
            <div class="d-flex align-items-center gap-2">
                <i class="ri-git-commit-line text-warning"></i>
                <span class="fw-semibold text-dark">${name} (สำนักงานใหญ่)</span>
            </div>
        </td>
        <td>${tax}</td>
        <td>${phone}</td>
        <td>บริษัท จำกัด</td>
        <td class="text-center align-middle border-start bg-light-subtle">
            <div class="form-check form-switch switch-navy d-inline-block">
                <input class="form-check-input status-toggle" type="checkbox" checked title="สถานะ${name}">
            </div>
        </td>
        <td class="text-center align-middle border-start bg-light-subtle">
            <div class="d-flex justify-content-center gap-1">
                <a href="${businessShowUrl(code)}" class="btn-action-icon btn-action-view" title="ดูรายละเอียด${name}"><i class="ri-eye-fill"></i></a>
                <button type="button" class="btn-action-icon btn-action-edit" onclick="openBizEditModal('${currentGroupIndex}', '${code}', '${name}', '${tax}', '${phone}', 'บริษัท จำกัด')"><i class="ri-pencil-fill"></i></button>
            </div>
        </td>
    `;

    tbody.appendChild(newTr);

    // Update count title
    document.getElementById('bizTotalCount').textContent = `${currentGroupIndex} บริษัทหลัก / ${currentGroupIndex + 3} รายการย่อย`;

    // Clear form and hide modal
    document.getElementById('addCompanyCode').value = '';
    document.getElementById('addCompanyName').value = '';
    document.getElementById('addCompanyTax').value = '';
    document.getElementById('addCompanyPhone').value = '';

    const modalEl = document.getElementById('addCompanyModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();

    alert(`สร้างบริษัท ${name} เรียบร้อยแล้ว!`);
}

function saveEditBiz(event) {
    event.preventDefault();
    const modalEl = document.getElementById('bizEditModal');
    const modal = bootstrap.Modal.getInstance(modalEl);
    modal.hide();
    alert('บันทึกการแก้ไขข้อมูลธุรกิจเรียบร้อยแล้ว!');
}
</script>
@endsection
@endsection
