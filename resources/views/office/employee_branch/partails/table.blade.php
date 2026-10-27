<div class="table-responsive table-card mb-1">
    <table class="table table-hover table-centered align-middle table-nowrap mb-0" id="employeeBranchTable">
        <thead class="table-light text-muted">
            <tr>
                <th scope="col" style="width: 50px;">ลำดับ</th>
                <th scope="col">พนักงาน</th>
                <th scope="col">สาขาธุรกิจ</th>
                <th scope="col">แผนก</th>
                <th scope="col">ตำแหน่งหลัก</th>
                <th scope="col">ประเภทจ่ายเงินเดือน</th>
                <th scope="col" class="text-center" style="width: 150px;">จัดการ</th>
            </tr>
        </thead>
        <tbody class="list">
            @php
                $employeeBranches = $employeeBranches ?? $employeeBranchList ?? collect([]);
            @endphp
            @if (isset($employeeBranches) && $employeeBranches->count() > 0)
                @foreach ($employeeBranches as $index => $item)
                    <tr>
                        <td>
                            @if (method_exists($employeeBranches, 'currentPage'))
                                {{ ($employeeBranches->currentPage() - 1) * $employeeBranches->perPage() + $index + 1 }}
                            @else
                                {{ $index + 1 }}
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs rounded-circle bg-light d-flex align-items-center justify-content-center me-2">
                                    <i class="ri-user-line text-primary"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-body">
                                        @if (!empty($item->employee) && !empty($item->employee->user))
                                            {{ $item->employee->user->prefix ?? $item->employee->user->other_prefix }} {{ $item->employee->user->firstname }} {{ $item->employee->user->surname }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </span>
                                    @if (!empty($item->employee) && !empty($item->employee->employee_no))
                                        <span class="text-muted small">รหัส: {{ $item->employee->employee_no }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold">
                                    @if (!empty($item->branch))
                                        {{ $item->branch->business_branch_name_th ?? 'สำนักงานใหญ่' }}
                                    @else
                                        -
                                    @endif
                                </span>
                                @if (!empty($item->branch) && !empty($item->branch->business_branch_no))
                                    <span class="text-muted small">สาขาที่ {{ $item->branch->business_branch_no }}</span>
                                @else
                                    <span class="text-muted small">สำนักงานหลัก</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            {{ $item->department ?? '-' }}
                        </td>
                        <td>
                            <span class="fw-medium text-dark">{{ $item->employee_position ?? '-' }}</span>
                        </td>
                        <td>
                            @if ($item->is_salary)
                                <span class="badge bg-success-subtle text-success">สาขาจ่ายเงินเดือนหลัก</span>
                            @else
                                <span class="badge bg-light text-muted">ทั่วไป</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-soft-info edit-employee-branch-btn" data-id="{{ $item->id }}" title="แก้ไข">
                                    <i class="ri-edit-2-line"></i>
                                </button>
                                <button class="btn btn-sm btn-soft-danger delete-employee-branch-btn" data-id="{{ $item->id }}" title="ลบ">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <div class="avatar-md mb-3 mx-auto">
                            <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                <i class="ri-git-merge-line"></i>
                            </div>
                        </div>
                        <h5 class="text-muted">ไม่พบข้อมูลสาขาที่พนักงานสังกัด</h5>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

@if (isset($employeeBranches) && method_exists($employeeBranches, 'links'))
    <div class="d-flex justify-content-between align-items-center mt-3 px-2">
        <div class="text-muted">
            แสดง {{ $employeeBranches->firstItem() ?? 0 }} ถึง {{ $employeeBranches->lastItem() ?? 0 }} จากทั้งหมด {{ $employeeBranches->total() ?? 0 }} รายการ
        </div>
        <div class="pagination-container">
            {!! $employeeBranches->links() !!}
        </div>
    </div>
@endif
