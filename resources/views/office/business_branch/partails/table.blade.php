<div class="table-responsive table-card mb-1">
    <table class="table table-hover table-centered align-middle table-nowrap mb-0" id="businessBranchTable">
        <thead class="table-light text-muted">
            <tr>
                <th scope="col" style="width: 50px;">ลำดับ</th>
                <th scope="col">โลโก้</th>
                <th scope="col">ธุรกิจหลัก</th>
                <th scope="col">รหัส / ชื่อสาขา</th>
                <th scope="col">ประเภท</th>
                <th scope="col">การติดต่อ</th>
                <th scope="col">ที่อยู่สาขา</th>
                <th scope="col">เป้าหมายยอดขาย</th>
                <th scope="col" class="text-center" style="width: 150px;">จัดการ</th>
            </tr>
        </thead>
        <tbody class="list">
            @php
                $branchCollection = $businessBranches ?? $branches ?? collect([]);
            @endphp
            @if (isset($branchCollection) && $branchCollection->count() > 0)
                @foreach ($branchCollection as $index => $branch)
                    <tr>
                        <td>
                            @if (method_exists($branchCollection, 'currentPage'))
                                {{ ($branchCollection->currentPage() - 1) * $branchCollection->perPage() + $index + 1 }}
                            @else
                                {{ $index + 1 }}
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if (!empty($branch->business_logo))
                                    <img src="{{ asset('storage/business_logos/' . $branch->business_logo) }}" 
                                         alt="logo" 
                                         class="avatar-xs rounded img-thumbnail"
                                         onerror="this.onerror=null;this.src='{{ asset('assets/images/companies/img-1.png') }}';">
                                @else
                                    <div class="avatar-xs bg-light rounded d-flex align-items-center justify-content-center">
                                        <i class="ri-building-line text-muted"></i>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $branch->business->business_name_th ?? '-' }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center gap-1 mb-1">
                                    <span class="badge bg-light text-primary border">
                                        {{ !empty($branch->business_branch_no) ? 'สาขาที่ ' . $branch->business_branch_no : 'สำนักงานใหญ่' }}
                                    </span>
                                </div>
                                <span class="fw-semibold text-body">{{ $branch->business_branch_name_th ?? 'สำนักงานใหญ่' }}</span>
                                @if ($branch->business_en_status == 1 && !empty($branch->business_branch_name_en))
                                    <span class="text-muted small">{{ $branch->business_branch_name_en }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if ($branch->business_status == 0)
                                <span class="badge bg-danger-subtle text-danger">สำนักงานใหญ่</span>
                            @elseif ($branch->business_status == 1)
                                <span class="badge bg-success-subtle text-success">สาขา</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @if (!empty($branch->business_phone))
                                    <span class="small"><i class="ri-cellphone-line align-bottom text-muted me-1"></i>{{ $branch->business_phone }}</span>
                                @endif
                                @if (!empty($branch->business_tel))
                                    <span class="small"><i class="ri-phone-line align-bottom text-muted me-1"></i>{{ $branch->business_tel }}</span>
                                @endif
                                @if (!empty($branch->business_website))
                                    <span class="small"><i class="ri-global-line align-bottom text-muted me-1"></i><a href="{{ $branch->business_website }}" target="_blank">{{ $branch->business_website }}</a></span>
                                @endif
                                @if (empty($branch->business_phone) && empty($branch->business_tel) && empty($branch->business_website))
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="text-wrap" style="max-width: 250px;">
                            @php
                                $addressParts = [];
                                if (!empty($branch->business_address)) {
                                    $addressParts[] = $branch->business_address;
                                }
                                if ($branch->tambon) {
                                    $addressParts[] = 'ต.' . ($branch->tambon->name_th ?? $branch->tambon);
                                }
                                if ($branch->amphure) {
                                    $addressParts[] = 'อ.' . ($branch->amphure->name_th ?? $branch->amphure);
                                }
                                if ($branch->province) {
                                    $addressParts[] = 'จ.' . ($branch->province->name_th ?? $branch->province);
                                }
                                
                                // ดึงรหัสไปรษณีย์จากตำบล (รองรับทั้ง Object และ Array)
                                if ($branch->tambon) {
                                    $zipCode = null;
                                    if (is_object($branch->tambon)) {
                                        $zipCode = $branch->tambon->zip_code ?? null;
                                    } elseif (is_array($branch->tambon)) {
                                        $zipCode = $branch->tambon['zip_code'] ?? $branch->tambon['รหัสไปรษณีย์'] ?? null;
                                    }
                                    if (!empty($zipCode)) {
                                        $addressParts[] = $zipCode;
                                    }
                                }
                            @endphp
                            @if (count($addressParts) > 0)
                                {{ implode(' ', $addressParts) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-semibold text-primary">฿{{ number_format($branch->sales_target_amount ?? 0, 2) }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-soft-info edit-business-branch-btn" data-id="{{ $branch->id }}" title="แก้ไข">
                                    <i class="ri-edit-2-line"></i>
                                </button>
                                <button class="btn btn-sm btn-soft-danger delete-business-branch-btn" data-id="{{ $branch->id }}" title="ลบ">
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
                                <i class="ri-git-branch-line"></i>
                            </div>
                        </div>
                        <h5 class="text-muted">ไม่พบข้อมูลสาขาธุรกิจ</h5>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

@if (isset($branchCollection) && method_exists($branchCollection, 'links'))
    <div class="d-flex justify-content-between align-items-center mt-3 px-2">
        <div class="text-muted">
            แสดง {{ $branchCollection->firstItem() ?? 0 }} ถึง {{ $branchCollection->lastItem() ?? 0 }} จากทั้งหมด {{ $branchCollection->total() ?? 0 }} รายการ
        </div>
        <div class="pagination-container">
            {!! $branchCollection->links() !!}
        </div>
    </div>
@endif
