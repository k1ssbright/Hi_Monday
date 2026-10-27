<div class="table-responsive table-card mb-1">
    <table class="table table-hover table-centered align-middle table-nowrap mb-0" id="employeeTable">
        <thead class="table-light text-muted">
            <tr>
                <th scope="col" style="width: 50px;">ลำดับ</th>
                <th scope="col">โปรไฟล์</th>
                <th scope="col">รหัสพนักงาน</th>
                <th scope="col">ชื่อ-นามสกุล</th>
                <th scope="col">อีเมล</th>
                <th scope="col">เบอร์โทรศัพท์</th>
                <th scope="col">ผู้บังคับบัญชา</th>
                <th scope="col">สถานะการทำงาน</th>
                <th scope="col" class="text-center" style="width: 150px;">จัดการ</th>
            </tr>
        </thead>
        <tbody class="list">
            @php
                $employeeCollection = $employees ?? $employeeList ?? collect([]);
            @endphp
            @if (isset($employeeCollection) && $employeeCollection->count() > 0)
                @foreach ($employeeCollection as $index => $employee)
                    <tr>
                        <td>
                            @if (method_exists($employeeCollection, 'currentPage'))
                                {{ ($employeeCollection->currentPage() - 1) * $employeeCollection->perPage() + $index + 1 }}
                            @else
                                {{ $index + 1 }}
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 chat-user-img align-self-center">
                                    <img src="{{ (!empty($employee->user) && !empty($employee->user->profile_image) && $employee->user->profile_image != 'user.jpg') ? asset('storage/profile_images/' . $employee->user->profile_image) : asset('assets/images/users/user-dummy-img.jpg') }}"
                                         class="rounded-circle avatar-xs img-thumbnail"
                                         alt="profile"
                                         onerror="this.onerror=null;this.src='{{ asset('assets/images/users/user-dummy-img.jpg') }}';">
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-primary border">{{ $employee->employee_no ?? '-' }}</span>
                        </td>
                        <td>
                            <span class="fw-semibold">
                                @if (!empty($employee->user))
                                    {{ $employee->user->prefix ?? $employee->user->other_prefix }} {{ $employee->user->firstname }} {{ $employee->user->surname }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </span>
                        </td>
                        <td>
                            @if (!empty($employee->user))
                                <span class="text-lowercase">{{ $employee->user->email }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if (!empty($employee->user))
                                <span>{{ $employee->user->phone ?? '-' }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if (!empty($employee->manager) && !empty($employee->manager->user))
                                <div class="d-flex align-items-center">
                                    <span class="small fw-semibold text-body">
                                        {{ $employee->manager->user->firstname }} {{ $employee->manager->user->surname }}
                                    </span>
                                </div>
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($employee->status)
                                <span class="badge bg-success-subtle text-success">ปกติ (กำลังทำงาน)</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">ลาออก/พ้นสภาพ</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-soft-info edit-employee-btn" data-id="{{ $employee->id }}" title="แก้ไข">
                                    <i class="ri-edit-2-line"></i>
                                </button>
                                <button class="btn btn-sm btn-soft-danger delete-employee-btn" data-id="{{ $employee->id }}" title="ลบ">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <div class="avatar-md mb-3 mx-auto">
                            <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                <i class="ri-contacts-line"></i>
                            </div>
                        </div>
                        <h5 class="text-muted">ไม่พบข้อมูลพนักงาน</h5>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

@if (isset($employeeCollection) && method_exists($employeeCollection, 'links'))
    <div class="d-flex justify-content-between align-items-center mt-3 px-2">
        <div class="text-muted">
            แสดง {{ $employeeCollection->firstItem() ?? 0 }} ถึง {{ $employeeCollection->lastItem() ?? 0 }} จากทั้งหมด {{ $employeeCollection->total() ?? 0 }} รายการ
        </div>
        <div class="pagination-container">
            {!! $employeeCollection->links() !!}
        </div>
    </div>
@endif
