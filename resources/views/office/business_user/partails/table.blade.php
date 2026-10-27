<div class="table-responsive table-card mb-1">
    <table class="table table-hover table-centered align-middle table-nowrap mb-0" id="businessUserTable">
        <thead class="table-light text-muted">
            <tr>
                <th scope="col" style="width: 50px;">ลำดับ</th>
                <th scope="col">ข้อมูลธุรกิจ</th>
                <th scope="col">ผู้ใช้งาน</th>
                <th scope="col">อีเมล</th>
                <th scope="col">เบอร์โทรศัพท์</th>
                <th scope="col">วันที่ผูกบัญชี</th>
                <th scope="col" class="text-center" style="width: 150px;">จัดการ</th>
            </tr>
        </thead>
        <tbody class="list">
            @php
                $businessUserCollection = $businessUsers ?? $businessUserList ?? collect([]);
            @endphp
            @if (isset($businessUserCollection) && $businessUserCollection->count() > 0)
                @foreach ($businessUserCollection as $index => $item)
                    <tr>
                        <td>
                            @if (method_exists($businessUserCollection, 'currentPage'))
                                {{ ($businessUserCollection->currentPage() - 1) * $businessUserCollection->perPage() + $index + 1 }}
                            @else
                                {{ $index + 1 }}
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold text-body">{{ $item->business->business_name_th ?? '-' }}</span>
                                <span class="text-muted small">รหัส: {{ $item->business->business_code ?? '-' }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-xs rounded-circle bg-light d-flex align-items-center justify-content-center me-2">
                                    <i class="ri-user-line text-primary"></i>
                                </div>
                                <span class="fw-semibold">{{ $item->user->prefix ?? $item->user->other_prefix }} {{ $item->user->firstname ?? '-' }} {{ $item->user->surname ?? '' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="text-lowercase">{{ $item->user->email ?? '-' }}</span>
                        </td>
                        <td>
                            {{ $item->user->phone ?? '-' }}
                        </td>
                        <td>
                            {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-soft-danger delete-business-user-btn" data-id="{{ $item->id }}" title="ยกเลิกการเชื่อมโยง">
                                    <i class="ri-link-unlink-m"></i> ยกเลิกเชื่อมโยง
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
                                <i class="ri-user-shared-line"></i>
                            </div>
                        </div>
                        <h5 class="text-muted">ไม่พบข้อมูลการผูกบัญชีผู้ใช้กับธุรกิจ</h5>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

@if (isset($businessUserCollection) && method_exists($businessUserCollection, 'links'))
    <div class="d-flex justify-content-between align-items-center mt-3 px-2">
        <div class="text-muted">
            แสดง {{ $businessUserCollection->firstItem() ?? 0 }} ถึง {{ $businessUserCollection->lastItem() ?? 0 }} จากทั้งหมด {{ $businessUserCollection->total() ?? 0 }} รายการ
        </div>
        <div class="pagination-container">
            {!! $businessUserCollection->links() !!}
        </div>
    </div>
@endif
