@if (session('success'))
    <div class="alert fade show border-0 shadow-sm p-3 mb-3 d-flex align-items-center justify-content-between" role="alert" style="background-color: #ECFDF5; border-radius: 10px; border-left: 4px solid #10B981 !important;">
        <div class="d-flex align-items-center" style="gap: 10px;">
            <i class="ri-checkbox-circle-fill text-success fs-18 flex-shrink-0"></i>
            <span class="fs-13 fw-semibold text-dark">{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.75rem;"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert fade show border-0 shadow-sm p-3 mb-3 d-flex align-items-center justify-content-between" role="alert" style="background-color: #FEF2F2; border-radius: 10px; border-left: 4px solid #EF4444 !important;">
        <div class="d-flex align-items-center" style="gap: 12px;">
            <i class="ri-error-warning-fill text-danger fs-18 flex-shrink-0"></i>
            <div>
                <div class="fs-13 fw-bold text-danger mb-0.5">เกิดข้อผิดพลาดในการกรอกข้อมูล</div>
                <ul class="mb-0 ps-3 fs-13 text-dark" style="line-height: 1.5;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close ms-3 flex-shrink-0" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.75rem;"></button>
    </div>
@endif
