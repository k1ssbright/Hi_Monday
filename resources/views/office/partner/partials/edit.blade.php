<style>
    /* ===== Premium Edit Modals CSS ===== */
    .modal-premium .modal-content {
        border-radius: 16px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }
    
    .modal-premium .modal-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 20px 24px;
    }
    
    .modal-premium .modal-body {
        padding: 24px;
    }
    
    .modal-premium .form-label {
        color: #475569;
        font-weight: 600;
        margin-bottom: 8px;
        letter-spacing: 0.01em;
    }
    
    .modal-premium .form-control, .modal-premium .form-select {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        background-color: #f8fafc;
    }
    
    .modal-premium .form-control:focus, .modal-premium .form-select:focus {
        border-color: #3b82f6;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }
    
    .switch-premium .form-check-input {
        width: 44px;
        height: 24px;
        cursor: pointer;
        border: none;
        background-color: #e2e8f0;
    }
    
    .switch-premium .form-check-input:checked {
        background-color: #10b981;
    }
    
    .switch-premium .form-check-label {
        cursor: pointer;
        font-weight: 500;
        color: #334155;
    }
    
    .btn-premium-primary {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border: none;
        color: white;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        transition: all 0.2s;
    }
    
    .btn-premium-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 12px -2px rgba(37, 99, 235, 0.3);
        color: white;
    }
    
    .btn-premium-info {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        border: none;
        color: white;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        box-shadow: 0 4px 6px -1px rgba(14, 165, 233, 0.2);
        transition: all 0.2s;
    }
    
    .btn-premium-info:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 12px -2px rgba(14, 165, 233, 0.3);
        color: white;
    }
    
    .btn-premium-cancel {
        background-color: #f1f5f9;
        color: #64748b;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .btn-premium-cancel:hover {
        background-color: #e2e8f0;
        color: #334155;
    }
</style>

<!-- Modal แก้ไขข้อมูลคู่ค้าหลัก (Master Data) -->
<div class="modal fade modal-premium" id="editPartnerMasterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary-subtle border-bottom-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                        <i class="ri-building-line fs-18"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold fs-16 mb-0 text-primary-emphasis">แก้ไขข้อมูลคู่ค้าหลัก</h5>
                        <p class="mb-0 text-muted fs-12 mt-1">อัปเดตข้อมูลรายละเอียดของบริษัท (Master Data)</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="{{ route('office.partners.update', $partner) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label fs-13"><i class="ri-barcode-line text-muted me-1"></i> รหัสคู่ค้า <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="partner_code" id="editMasterCode" value="{{ old('partner_code', $partner->partner_code) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13"><i class="ri-price-tag-3-line text-muted me-1"></i> ประเภทคู่ค้า <span class="text-danger">*</span></label>
                            <select class="form-select" name="partner_role" id="editMasterType" required>
                                <option value="customer" @selected(old('partner_role', $partner->role()) === 'customer')>ลูกค้า</option>
                                <option value="vendor" @selected(old('partner_role', $partner->role()) === 'vendor')>ผู้ขาย</option>
                                <option value="both" @selected(old('partner_role', $partner->role()) === 'both')>ลูกค้า & ผู้ขาย</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fs-13"><i class="ri-building-2-line text-muted me-1"></i> ชื่อบริษัท <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="partner_name_th" id="editMasterName" value="{{ old('partner_name_th', $partner->partner_name_th) }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fs-13"><i class="ri-file-text-line text-muted me-1"></i> เลขประจำตัวผู้เสียภาษี <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="partner_tax_no" value="{{ old('partner_tax_no', $partner->partner_tax_no) }}" maxlength="13" required>
                        </div>
                        
                        <div class="col-md-12 mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center bg-light-subtle p-3 rounded-3 border">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1 fs-14">สถานะการใช้งานระบบ</h6>
                                    <p class="text-muted fs-12 mb-0">เปิดเพื่อให้คู่ค้านี้สามารถทำธุรกรรมในระบบได้</p>
                                </div>
                                <div class="form-check form-switch switch-premium mb-0 d-flex align-items-center">
                                    <input class="form-check-input mt-0" type="checkbox" name="active_status" value="1" id="editMasterStatus" @checked($partner->isActive())>
                                    <label class="form-check-label ms-3 fs-13" for="editMasterStatus">เปิดใช้งาน</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light bg-opacity-50 border-top-0 py-3 px-4 rounded-bottom-4">
                    <button type="button" class="btn-premium-cancel" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn-premium-primary"><i class="ri-save-3-line me-1"></i> บันทึกข้อมูลบริษัท</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal แก้ไขข้อมูลสาขา (Branch Data) -->
<div class="modal fade modal-premium" id="editBranchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-info-subtle border-bottom-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                        <i class="ri-store-2-line fs-18"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold fs-16 mb-0 text-info-emphasis">แก้ไขข้อมูลสาขา / สำนักงาน</h5>
                        <p class="mb-0 text-muted fs-12 mt-1">อัปเดตข้อมูลสาขาย่อย หรือ สำนักงานใหญ่</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBranchForm" method="post" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fs-13"><i class="ri-hashtag text-muted me-1"></i> รหัสสาขา (5 หลัก) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="partner_branch_no" id="editBranchCode" maxlength="5" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fs-13"><i class="ri-store-3-line text-muted me-1"></i> ชื่อสาขา / สำนักงาน <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="partner_branch_name_th" id="editBranchName" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fs-13"><i class="ri-file-text-line text-muted me-1"></i> เลขประจำตัวผู้เสียภาษี <span class="text-danger">*</span></label>
                            <input type="text" class="form-control fw-medium" name="partner_tax_no" id="editBranchTax" maxlength="13" required style="letter-spacing: 1px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fs-13"><i class="ri-phone-line text-muted me-1"></i> เบอร์โทรศัพท์สาขา</label>
                            <input type="text" class="form-control" name="partner_tel" id="editBranchPhone">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label fs-13"><i class="ri-price-tag-3-line text-muted me-1"></i> ประเภทสาขานี้</label>
                            <select class="form-select" name="partner_role" id="editBranchType">
                                <option value="customer">ลูกค้า</option>
                                <option value="vendor">ผู้ขาย</option>
                                <option value="both">ลูกค้า & ผู้ขาย</option>
                            </select>
                        </div>
                        
                        <div class="col-md-12 mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center bg-light-subtle p-3 rounded-3 border">
                                <div>
                                    <h6 class="fw-bold text-dark mb-1 fs-14">สถานะการใช้งานสาขา</h6>
                                    <p class="text-muted fs-12 mb-0">เปิดเพื่อให้สามารถออกเอกสารหรือทำรายการกับสาขานี้ได้</p>
                                </div>
                                <div class="form-check form-switch switch-premium mb-0 d-flex align-items-center">
                                    <input class="form-check-input mt-0" type="checkbox" name="active_status" value="1" id="editBranchStatus" checked>
                                    <label class="form-check-label ms-3 fs-13" for="editBranchStatus">เปิดใช้งาน</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light bg-opacity-50 border-top-0 py-3 px-4 rounded-bottom-4">
                    <button type="button" class="btn-premium-cancel" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn-premium-info"><i class="ri-save-3-line me-1"></i> บันทึกข้อมูลสาขา</button>
                </div>
            </form>
        </div>
    </div>
</div>
