@extends('office.layout.office')

@section('title', 'โปรไฟล์ผู้ใช้งาน | Hi Monday')

@section('content')
    @php
        $profileUser = auth()->user();
        $profileName = trim(collect([
            $profileUser?->prefix,
            $profileUser?->firstname,
            $profileUser?->surname,
        ])->filter()->implode(' '));
        $profileBirthday = $profileUser?->birthday
            ? \Illuminate\Support\Carbon::parse($profileUser->birthday)->format('d/m/Y')
            : '';
        $profileInitial = strtoupper(substr($profileUser?->firstname ?: ($profileUser?->email ?? 'U'), 0, 1));
    @endphp
    <div class="container-fluid">
        @include('office.settings.partials.page-title', ['section' => 'บัญชี', 'title' => 'ข้อมูลส่วนตัว'])

        <div class="row">
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mx-auto mb-3">
                            <div class="avatar-lg mx-auto">
                                <div class="avatar-title rounded-circle bg-primary-subtle text-primary fs-24">
                                    {{ $profileInitial }}
                                </div>
                            </div>
                        </div>
                        <h5 class="mb-1">{{ $profileUser?->email ?: '-' }}</h5>
                        <p class="text-muted mb-3">{{ $profileName ?: '-' }}</p>
                        <div class="text-start">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-xs flex-shrink-0">
                                    <span class="avatar-title bg-info-subtle text-info rounded"><i class="ri-mail-line"></i></span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-0 fs-12">อีเมลสำหรับติดต่อ</p>
                                    <h6 class="mb-0">{{ $profileUser?->email ?: '-' }}</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-xs flex-shrink-0">
                                    <span class="avatar-title bg-success-subtle text-success rounded"><i class="ri-phone-line"></i></span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-0 fs-12">เบอร์โทรศัพท์</p>
                                    <h6 class="mb-0">{{ $profileUser?->phone ?: '-' }}</h6>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs flex-shrink-0">
                                    <span class="avatar-title bg-warning-subtle text-warning rounded"><i class="ri-map-pin-line"></i></span>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <p class="text-muted mb-0 fs-12">ที่อยู่</p>
                                    <h6 class="mb-0">{{ $profileUser?->address ?: '-' }}</h6>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info material-shadow mb-0 mt-4 text-start" role="alert">
                            <i class="ri-information-line me-1 align-middle"></i>
                            ข้อมูลนี้ใช้สำหรับการติดต่อและการออกเอกสาร กรุณาตรวจสอบความถูกต้องอย่างสม่ำเสมอ
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#personal-info" role="tab">
                                    <i class="ri-user-3-line me-1"></i> ข้อมูลส่วนตัว
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#change-password" role="tab">
                                    <i class="ri-lock-password-line me-1"></i> เปลี่ยนรหัสผ่าน
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#signature" role="tab">
                                    <i class="ri-edit-line me-1"></i> ลายเซ็น
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#notifications" role="tab">
                                    <i class="ri-notification-3-line me-1"></i> การแจ้งเตือน
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#other-info" role="tab">
                                    <i class="ri-menu-add-fill me-1"></i> ข้อมูลอื่นๆ
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="personal-info" role="tabpanel">
                                <form action="javascript:void(0);">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">คำนำหน้าชื่อ <span class="text-danger">*</span></label>
                                            <select class="form-select">
                                                <option value="นางสาว" @selected(($profileUser?->prefix ?? '') === 'นางสาว')>นางสาว</option>
                                                <option value="นาย" @selected(($profileUser?->prefix ?? '') === 'นาย')>นาย</option>
                                                <option value="นาง" @selected(($profileUser?->prefix ?? '') === 'นาง')>นาง</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">ชื่อจริง <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" value="{{ $profileUser?->firstname ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" value="{{ $profileUser?->surname ?? '' }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">เลขบัตรประชาชน</label>
                                            <input type="text" class="form-control" placeholder="กรุณากรอกเลขบัตรประชาชน">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">วันเกิด</label>
                                            <input type="text" class="form-control" value="{{ $profileBirthday }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" value="{{ $profileUser?->phone ?? '' }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">ที่อยู่ เลขที่</label>
                                            <input type="text" class="form-control" value="{{ $profileUser?->address ?? '' }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">ตำบล อำเภอ จังหวัด รหัสไปรษณีย์</label>
                                            <input type="text" class="form-control" value="{{ $profileUser?->address ?? '' }}">
                                        </div>
                                        <div class="col-12">
                                            <h6 class="fw-semibold mb-1">อีเมล สำหรับออกเอกสาร</h6>
                                            <p class="text-muted fs-12 mb-2">ใช้งานสำหรับการออกเอกสารและการสื่อสารที่เป็นทางการ</p>
                                            <input type="email" class="form-control" value="{{ $profileUser?->email ?? '' }}">
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-success"><i class="ri-check-line align-bottom me-1"></i> บันทึกข้อมูล</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane" id="change-password" role="tabpanel">
                                <form action="javascript:void(0);">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">รหัสผ่านเก่า <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" placeholder="รหัสผ่านเก่า">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" placeholder="รหัสผ่านใหม่">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" placeholder="ยืนยันรหัสผ่าน">
                                        </div>
                                        <div class="col-12 text-end">
                                            <button type="submit" class="btn btn-success"><i class="ri-check-line align-bottom me-1"></i> เปลี่ยนรหัสผ่าน</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane" id="signature" role="tabpanel">
                                <div class="text-center py-3">
                                    <div class="border border-dashed rounded py-5 mb-3 text-muted">อัปโหลดลายเซ็นที่นี่</div>
                                    <div class="d-flex justify-content-center gap-2 mb-3">
                                        <button type="button" class="btn btn-light">รีเซ็ต</button>
                                        <button type="button" class="btn btn-primary">อัปโหลดรูปภาพลายเซ็น</button>
                                    </div>
                                    <p class="text-muted fs-12 mb-3">กรณีต้องการอัปโหลด ขนาดที่แนะนำคือ 400x200px หรือ Ratio 2:1</p>
                                    <div class="text-end">
                                        <button type="button" class="btn btn-success"><i class="ri-check-line align-bottom me-1"></i> บันทึกลายเซ็น</button>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="notifications" role="tabpanel">
                                <form action="javascript:void(0);">
                                    <h6 class="fw-semibold mb-3">การแจ้งเตือนทางอีเมล</h6>
                                    <div class="form-check form-switch form-switch-md mb-4">
                                        <input class="form-check-input" type="checkbox" role="switch" id="emailNotiSwitch" checked>
                                        <label class="form-check-label" for="emailNotiSwitch">แจ้งเตือนเมื่อมีการอัปเดตเคส</label>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-success"><i class="ri-check-line align-bottom me-1"></i> บันทึกการตั้งค่า</button>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane" id="other-info" role="tabpanel">
                                <div class="row g-3">
                                    @foreach ([
                                        ['การลา', 'ri-calendar-event-line', 'success', 'ดูประวัติการลา ยอดการลา และสถานะการอนุมัติล่าสุด', 'ดูข้อมูลการลา'],
                                        ['เวลาการทำงาน', 'ri-time-line', 'info', 'สรุปเวลาการทำงาน และการเข้า-ออกงานย้อนหลัง', 'ดูข้อมูลเวลา'],
                                        ['สลิปเงินเดือน', 'ri-file-paper-2-line', 'warning', 'ดูประวัติการรับเงินเดือน และดาวน์โหลดสลิปเงินเดือน', 'ดูสลิปเงินเดือน'],
                                        ['ผลการประเมิน', 'ri-bar-chart-fill', 'secondary', 'ดูผลการประเมินการปฏิบัติงานของคุณ', 'ดูผลการประเมิน'],
                                        ['การอบรม', 'ri-graduation-cap-line', 'primary', 'ดูประวัติการอบรมที่คุณเข้าร่วม และใบประกาศนียบัตร', 'ดูการอบรม'],
                                        ['สิทธิประโยชน์', 'ri-star-line', 'danger', 'ข้อมูลสิทธิประโยชน์ สวัสดิการ และสิทธิพิเศษของคุณ', 'ดูสิทธิประโยชน์'],
                                    ] as [$title, $icon, $color, $desc, $action])
                                        <div class="col-md-4">
                                            <div class="card card-animate mb-0 border">
                                                <div class="card-body text-center">
                                                    <div class="avatar-sm mx-auto mb-3">
                                                        <span class="avatar-title bg-{{ $color }}-subtle text-{{ $color }} rounded fs-3">
                                                            <i class="{{ $icon }}"></i>
                                                        </span>
                                                    </div>
                                                    <h6 class="mb-2">{{ $title }}</h6>
                                                    <p class="text-muted fs-12 mb-3">{{ $desc }}</p>
                                                    <button type="button" class="btn btn-soft-{{ $color }} btn-sm">{{ $action }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
