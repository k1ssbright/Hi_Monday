<!-- Business Table with Company-level Status, Actions, and Dynamic Links passing Company ID -->
<div class="table-responsive">
    <table class="table-biz-custom align-middle" id="bizTable">
        <thead>
            <tr>
                <th class="text-center" style="width: 50px;">ลำดับ</th>
                <th style="width: 250px;">
                    ชื่อบริษัท / องค์กรหลัก <i class="ri-arrow-up-down-line sort-icon"></i>
                </th>
                <th style="width: 130px;">
                    รหัสธุรกิจ <i class="ri-arrow-up-down-line sort-icon"></i>
                </th>
                <th>
                    ชื่อธุรกิจ / สาขา <i class="ri-arrow-up-down-line sort-icon"></i>
                </th>
                <th>
                    เลขประจำตัวผู้เสียภาษี <i class="ri-arrow-up-down-line sort-icon"></i>
                </th>
                <th>
                    เบอร์โทรศัพท์ <i class="ri-arrow-up-down-line sort-icon"></i>
                </th>
                <th>
                    ประเภท <i class="ri-arrow-up-down-line sort-icon"></i>
                </th>
                <th class="text-center" style="width: 90px;">
                    สถานะ <i class="ri-arrow-up-down-line sort-icon"></i>
                </th>
                <th class="text-center" style="width: 100px;">จัดการ</th>
            </tr>
        </thead>
        <tbody>
            <!-- Group 1: AAA (บริษัท เอบี กรุ๊ป จำกัด) -->
            <tr class="table-group-header">
                <td rowspan="3" class="text-center text-muted fw-bold align-middle border-end bg-light-subtle">1</td>
                <td rowspan="3" class="align-middle bg-light-subtle border-end">
                    <a href="{{ route('office.business-show', ['id' => 'AAA']) }}" class="d-block text-decoration-none p-2 rounded-3 company-link-hover">
                        <div class="d-flex align-items-center gap-2">
                            <div class="biz-logo-avatar bg-primary text-white fw-bold shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem;">AAA</div>
                            <div>
                                <div class="fw-bold text-primary fs-15">
                                    บริษัท เอบี กรุ๊ป จำกัด (AAA) <i class="ri-arrow-right-circle-line ms-1 fs-16"></i>
                                </div>
                                <small class="text-muted">สำนักงานใหญ่ • 3 รายการธุรกิจ</small>
                            </div>
                        </div>
                    </a>
                </td>
                <td class="fw-bold text-dark">AAA-01</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-git-commit-line text-primary"></i>
                        <span class="fw-semibold text-dark">บริษัท เอบี กรุ๊ป จำกัด (สำนักงานใหญ่)</span>
                    </div>
                </td>
                <td>0105562012345</td>
                <td>021234567</td>
                <td>บริษัท จำกัด</td>
                <td rowspan="3" class="text-center align-middle border-start bg-light-subtle">
                    <div class="form-check form-switch switch-navy d-inline-block">
                        <input class="form-check-input status-toggle" type="checkbox" checked title="สถานะบริษัท เอบี กรุ๊ป จำกัด">
                    </div>
                </td>
                <td rowspan="3" class="text-center align-middle border-start bg-light-subtle">
                    <div class="d-flex justify-content-center gap-1">
                        <a href="{{ route('office.business-show', ['id' => 'AAA']) }}" class="btn-action-icon btn-action-view" title="ดูรายละเอียดบริษัท เอบี กรุ๊ป จำกัด"><i class="ri-eye-fill"></i></a>
                        <button type="button" class="btn-action-icon btn-action-edit" onclick="openBizEditModal('1', 'AAA', 'บริษัท เอบี กรุ๊ป จำกัด', '0105562012345', '021234567', 'บริษัท จำกัด')"><i class="ri-pencil-fill"></i></button>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fw-bold text-dark">AAA-02</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-git-commit-line text-primary"></i>
                        <span class="fw-semibold text-dark">บริษัท เอบี กรุ๊ป จำกัด (สาขา 1 - เทคโนโลยี & โซลูชั่น)</span>
                    </div>
                </td>
                <td>0105562012346</td>
                <td>021234568</td>
                <td>บริษัท จำกัด</td>
            </tr>
            <tr>
                <td class="fw-bold text-dark">AAA-03</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-git-commit-line text-primary"></i>
                        <span class="fw-semibold text-dark">บริษัท เอบี กรุ๊ป จำกัด (สาขา 2 - ดิจิทัล ซัพพลาย)</span>
                    </div>
                </td>
                <td>0105562012347</td>
                <td>021234569</td>
                <td>บริษัท จำกัด</td>
            </tr>

            <!-- Group 2: BBB (บริษัท บีบี อินโนเวชั่น จำกัด) -->
            <tr class="table-group-header border-top">
                <td rowspan="2" class="text-center text-muted fw-bold align-middle border-end bg-light-subtle">2</td>
                <td rowspan="2" class="align-middle bg-light-subtle border-end">
                    <a href="{{ route('office.business-show', ['id' => 'BBB']) }}" class="d-block text-decoration-none p-2 rounded-3 company-link-hover">
                        <div class="d-flex align-items-center gap-2">
                            <div class="biz-logo-avatar bg-success text-white fw-bold shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem;">BBB</div>
                            <div>
                                <div class="fw-bold text-success fs-15">
                                    บริษัท บีบี อินโนเวชั่น จำกัด (BBB) <i class="ri-arrow-right-circle-line ms-1 fs-16"></i>
                                </div>
                                <small class="text-muted">องค์กรหลัก • 2 รายการธุรกิจ</small>
                            </div>
                        </div>
                    </a>
                </td>
                <td class="fw-bold text-dark">BBB-01</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-git-commit-line text-success"></i>
                        <span class="fw-semibold text-dark">บริษัท บีบี อินโนเวชั่น จำกัด (สำนักงานใหญ่)</span>
                    </div>
                </td>
                <td>0105562012350</td>
                <td>021234572</td>
                <td>บริษัท จำกัด</td>
                <td rowspan="2" class="text-center align-middle border-start bg-light-subtle">
                    <div class="form-check form-switch switch-navy d-inline-block">
                        <input class="form-check-input status-toggle" type="checkbox" checked title="สถานะบริษัท บีบี อินโนเวชั่น จำกัด">
                    </div>
                </td>
                <td rowspan="2" class="text-center align-middle border-start bg-light-subtle">
                    <div class="d-flex justify-content-center gap-1">
                        <a href="{{ route('office.business-show', ['id' => 'BBB']) }}" class="btn-action-icon btn-action-view" title="ดูรายละเอียดบริษัท บีบี อินโนเวชั่น จำกัด"><i class="ri-eye-fill"></i></a>
                        <button type="button" class="btn-action-icon btn-action-edit" onclick="openBizEditModal('2', 'BBB', 'บริษัท บีบี อินโนเวชั่น จำกัด', '0105562012350', '021234572', 'บริษัท จำกัด')"><i class="ri-pencil-fill"></i></button>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fw-bold text-dark">BBB-02</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-git-commit-line text-success"></i>
                        <span class="fw-semibold text-dark">บริษัท บีบี อินโนเวชั่น จำกัด (สาขา 2)</span>
                    </div>
                </td>
                <td>0105562012351</td>
                <td>021234573</td>
                <td>ห้างหุ้นส่วน</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Pagination Footer -->
<div class="d-flex justify-content-between align-items-center mt-4 pt-2 flex-wrap gap-3">
    <span class="text-muted fs-13" id="bizPaginationInfo">แสดง 2 บริษัทหลัก รวม 5 รายการธุรกิจ</span>
    <nav>
        <ul class="pagination pagination-sm mb-0 gap-1">
            <li class="page-item disabled"><a class="page-link" href="#">«</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item disabled"><a class="page-link" href="#">»</a></li>
        </ul>
    </nav>
</div>
