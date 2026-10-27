<!-- Partner Table with Company-level Status, Actions, and Dynamic Links -->
<div class="table-responsive">
    <table class="table-biz-custom align-middle" id="partnerTable">
        <thead>
            <tr>
                <th class="text-center" style="width: 50px;">ลำดับ</th>
                <th style="width: 250px;">
                    ชื่อคู่ค้า / องค์กรหลัก <i class="ri-arrow-up-down-line sort-icon"></i>
                </th>
                <th style="width: 130px;">
                    รหัสสาขา <i class="ri-arrow-up-down-line sort-icon"></i>
                </th>
                <th>
                    ชื่อสาขา / สำนักงาน <i class="ri-arrow-up-down-line sort-icon"></i>
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
            <!-- Group 1: CUS001 -->
            <tr class="table-group-header">
                <td rowspan="3" class="text-center text-muted fw-bold align-middle border-end bg-light-subtle">1</td>
                <td rowspan="3" class="align-middle bg-light-subtle border-end">
                    <a href="{{ route('office.partner-show', ['id' => 'CUS001']) }}" class="d-block text-decoration-none p-2 rounded-3 company-link-hover">
                        <div class="d-flex align-items-center gap-2">
                            <div class="biz-logo-avatar bg-info text-white fw-bold shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem;">CUS</div>
                            <div>
                                <div class="fw-bold text-info fs-15">
                                    บริษัท สยาม คอมเมิร์ซ จำกัด (CUS001) <i class="ri-arrow-right-circle-line ms-1 fs-16"></i>
                                </div>
                                <small class="text-muted">ลูกค้า • 3 สาขา</small>
                            </div>
                        </div>
                    </a>
                </td>
                <td class="fw-bold text-dark">00000</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-git-commit-line text-info"></i>
                        <span class="fw-semibold text-dark">สำนักงานใหญ่</span>
                    </div>
                </td>
                <td>0105563012345</td>
                <td>022345678</td>
                <td><span class="badge bg-info-subtle text-info">ลูกค้า</span></td>
                <td rowspan="3" class="text-center align-middle border-start bg-light-subtle">
                    <div class="form-check form-switch switch-navy d-inline-block">
                        <input class="form-check-input status-toggle" type="checkbox" checked title="สถานะบริษัท สยาม คอมเมิร์ซ จำกัด">
                    </div>
                </td>
                <td rowspan="3" class="text-center align-middle border-start bg-light-subtle">
                    <div class="d-flex justify-content-center gap-1">
                        <a href="{{ route('office.partner-show', ['id' => 'CUS001']) }}" class="btn-action-icon btn-action-view" title="ดูรายละเอียดบริษัท สยาม คอมเมิร์ซ จำกัด"><i class="ri-eye-fill"></i></a>
                        <button type="button" class="btn-action-icon btn-action-edit" onclick="openPartnerEditModal('1', 'CUS001', 'บริษัท สยาม คอมเมิร์ซ จำกัด', '0105563012345', '022345678', 'ลูกค้า')"><i class="ri-pencil-fill"></i></button>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fw-bold text-dark">00001</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-git-commit-line text-info"></i>
                        <span class="fw-semibold text-dark">สาขาสีลม</span>
                    </div>
                </td>
                <td>0105563012345</td>
                <td>022345679</td>
                <td><span class="badge bg-info-subtle text-info">ลูกค้า</span></td>
            </tr>
            <tr>
                <td class="fw-bold text-dark">00002</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-git-commit-line text-info"></i>
                        <span class="fw-semibold text-dark">สาขาลาดพร้าว</span>
                    </div>
                </td>
                <td>0105563012345</td>
                <td>022345680</td>
                <td><span class="badge bg-info-subtle text-info">ลูกค้า</span></td>
            </tr>

            <!-- Group 2: VND001 -->
            <tr class="table-group-header border-top">
                <td rowspan="2" class="text-center text-muted fw-bold align-middle border-end bg-light-subtle">2</td>
                <td rowspan="2" class="align-middle bg-light-subtle border-end">
                    <a href="{{ route('office.partner-show', ['id' => 'VND001']) }}" class="d-block text-decoration-none p-2 rounded-3 company-link-hover">
                        <div class="d-flex align-items-center gap-2">
                            <div class="biz-logo-avatar bg-warning text-white fw-bold shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem;">VND</div>
                            <div>
                                <div class="fw-bold text-warning fs-15">
                                    บริษัท ไทย ซัพพลาย จำกัด (VND001) <i class="ri-arrow-right-circle-line ms-1 fs-16"></i>
                                </div>
                                <small class="text-muted">ผู้ขาย • 2 สาขา</small>
                            </div>
                        </div>
                    </a>
                </td>
                <td class="fw-bold text-dark">00000</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-git-commit-line text-warning"></i>
                        <span class="fw-semibold text-dark">สำนักงานใหญ่</span>
                    </div>
                </td>
                <td>0105564012350</td>
                <td>023456789</td>
                <td><span class="badge bg-warning-subtle text-warning">ผู้ขาย</span></td>
                <td rowspan="2" class="text-center align-middle border-start bg-light-subtle">
                    <div class="form-check form-switch switch-navy d-inline-block">
                        <input class="form-check-input status-toggle" type="checkbox" checked title="สถานะบริษัท ไทย ซัพพลาย จำกัด">
                    </div>
                </td>
                <td rowspan="2" class="text-center align-middle border-start bg-light-subtle">
                    <div class="d-flex justify-content-center gap-1">
                        <a href="{{ route('office.partner-show', ['id' => 'VND001']) }}" class="btn-action-icon btn-action-view" title="ดูรายละเอียดบริษัท ไทย ซัพพลาย จำกัด"><i class="ri-eye-fill"></i></a>
                        <button type="button" class="btn-action-icon btn-action-edit" onclick="openPartnerEditModal('2', 'VND001', 'บริษัท ไทย ซัพพลาย จำกัด', '0105564012350', '023456789', 'ผู้ขาย')"><i class="ri-pencil-fill"></i></button>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fw-bold text-dark">00001</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-git-commit-line text-warning"></i>
                        <span class="fw-semibold text-dark">สาขาบางนา</span>
                    </div>
                </td>
                <td>0105564012350</td>
                <td>023456790</td>
                <td><span class="badge bg-warning-subtle text-warning">ผู้ขาย</span></td>
            </tr>

            <!-- Group 3: MIX001 -->
            <tr class="table-group-header border-top">
                <td rowspan="2" class="text-center text-muted fw-bold align-middle border-end bg-light-subtle">3</td>
                <td rowspan="2" class="align-middle bg-light-subtle border-end">
                    <a href="{{ route('office.partner-show', ['id' => 'MIX001']) }}" class="d-block text-decoration-none p-2 rounded-3 company-link-hover">
                        <div class="d-flex align-items-center gap-2">
                            <div class="biz-logo-avatar bg-primary text-white fw-bold shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem;">MIX</div>
                            <div>
                                <div class="fw-bold text-primary fs-15">
                                    บริษัท โกลบอล เทรด จำกัด (MIX001) <i class="ri-arrow-right-circle-line ms-1 fs-16"></i>
                                </div>
                                <small class="text-muted">ลูกค้า & ผู้ขาย • 2 สาขา</small>
                            </div>
                        </div>
                    </a>
                </td>
                <td class="fw-bold text-dark">00000</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-git-commit-line text-primary"></i>
                        <span class="fw-semibold text-dark">สำนักงานใหญ่</span>
                    </div>
                </td>
                <td>0105565012360</td>
                <td>024567890</td>
                <td><span class="badge bg-primary-subtle text-primary">ลูกค้า & ผู้ขาย</span></td>
                <td rowspan="2" class="text-center align-middle border-start bg-light-subtle">
                    <div class="form-check form-switch switch-navy d-inline-block">
                        <input class="form-check-input status-toggle" type="checkbox" checked title="สถานะบริษัท โกลบอล เทรด จำกัด">
                    </div>
                </td>
                <td rowspan="2" class="text-center align-middle border-start bg-light-subtle">
                    <div class="d-flex justify-content-center gap-1">
                        <a href="{{ route('office.partner-show', ['id' => 'MIX001']) }}" class="btn-action-icon btn-action-view" title="ดูรายละเอียดบริษัท โกลบอล เทรด จำกัด"><i class="ri-eye-fill"></i></a>
                        <button type="button" class="btn-action-icon btn-action-edit" onclick="openPartnerEditModal('3', 'MIX001', 'บริษัท โกลบอล เทรด จำกัด', '0105565012360', '024567890', 'ลูกค้า & ผู้ขาย')"><i class="ri-pencil-fill"></i></button>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="fw-bold text-dark">00001</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-git-commit-line text-primary"></i>
                        <span class="fw-semibold text-dark">สาขารามอินทรา</span>
                    </div>
                </td>
                <td>0105565012360</td>
                <td>024567891</td>
                <td><span class="badge bg-primary-subtle text-primary">ลูกค้า & ผู้ขาย</span></td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Pagination Footer -->
<div class="d-flex justify-content-between align-items-center mt-4 pt-2 flex-wrap gap-3">
    <span class="text-muted fs-13" id="partnerPaginationInfo">แสดง 3 คู่ค้าหลัก รวม 7 รายการสาขา</span>
    <nav>
        <ul class="pagination pagination-sm mb-0 gap-1">
            <li class="page-item disabled"><a class="page-link" href="#">«</a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item disabled"><a class="page-link" href="#">»</a></li>
        </ul>
    </nav>
</div>
