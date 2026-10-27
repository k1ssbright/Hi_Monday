<div class="table-responsive table-card mb-1">
    <table class="table table-hover table-centered align-middle table-nowrap mb-0" id="userTable">
        <thead class="table-light text-muted">
            <tr>
                <th scope="col" style="width: 50px;">ลำดับ</th>
                <th scope="col">โปรไฟล์</th>
                <th scope="col">ชื่อ-นามสกุล</th>
                <th scope="col">อีเมล</th>
                <th scope="col">เบอร์โทรศัพท์</th>
                <th scope="col">วันเกิด</th>
                <th scope="col">เลขประจำตัวผู้เสียภาษี</th>
                <th scope="col">ที่อยู่</th>
                <th scope="col">สถานะ</th>
                <th scope="col" class="text-center" style="width: 150px;">จัดการ</th>
            </tr>
        </thead>
        <tbody class="list">
            @if (isset($users) && $users->count() > 0)
                @foreach ($users as $index => $user)
                    <tr>
                        <td>
                            @if (method_exists($users, 'currentPage'))
                                {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                            @else
                                {{ $index + 1 }}
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 chat-user-img online user-own-img align-self-center">
                                    <img src="{{ !empty($user->profile_image) && $user->profile_image != 'user.jpg' ? asset('storage/profile_images/' . $user->profile_image) : asset('assets/images/users/user-dummy-img.jpg') }}"
                                        class="rounded-circle avatar-xs img-thumbnail" alt="profile"
                                        onerror="this.onerror=null;this.src='{{ asset('assets/images/users/user-dummy-img.jpg') }}';">
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold">
                                {{ $user->prefix ?? $user->other_prefix }} {{ $user->firstname }} {{ $user->surname }}
                            </div>
                        </td>
                        <td>
                            <span class="text-lowercase">{{ $user->email }}</span>
                        </td>
                        <td>
                            {{ $user->phone ?? '-' }}
                        </td>
                        <td>
                            {{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('d/m/Y') : '-' }}
                        </td>
                        <td>
                            <code class="text-dark">{{ $user->tax_no ?? '-' }}</code>
                        </td>
                        <td class="text-wrap" style="max-width: 250px;">
                            @php
                                $addressParts = [];
                                if (!empty($user->address)) {
                                    $addressParts[] = $user->address;
                                }
                                if ($user->tambon) {
                                    $addressParts[] = 'ต.' . ($user->tambon->name_th ?? $user->tambon);
                                }
                                if ($user->amphure) {
                                    $addressParts[] = 'อ.' . ($user->amphure->name_th ?? $user->amphure);
                                }
                                if ($user->province) {
                                    $addressParts[] = 'จ.' . ($user->province->name_th ?? $user->province);
                                }
                                if ($user->tambon) {
                                    $zipCode = null;
                                    if (is_object($user->tambon)) {
                                        $zipCode = $user->tambon->zip_code ?? null;
                                    } elseif (is_array($user->tambon)) {
                                        $zipCode = $user->tambon['รหัสไปรษณีย์'] ?? null;
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
                            @if ($user->active_status)
                                <span class="badge bg-success-subtle text-success">ใช้งาน</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">ระงับการใช้งาน</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-soft-info edit-user-btn" data-id="{{ $user->id }}"
                                    title="แก้ไข">
                                    <i class="ri-edit-2-line"></i>
                                </button>
                                <button class="btn btn-sm btn-soft-danger delete-user-btn"
                                    data-id="{{ $user->id }}" title="ลบ">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <div class="avatar-md mb-3 mx-auto">
                            <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                <i class="ri-user-search-line"></i>
                            </div>
                        </div>
                        <h5 class="text-muted">ไม่พบข้อมูลผู้ใช้งาน</h5>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

@if (isset($users) && method_exists($users, 'links'))
    <div class="d-flex justify-content-between align-items-center mt-3 px-2">
        <div class="text-muted">
            แสดง {{ $users->firstItem() ?? 0 }} ถึง {{ $users->lastItem() ?? 0 }} จากทั้งหมด
            {{ $users->total() ?? 0 }} รายการ
        </div>
        <div class="pagination-container">
            {!! $users->links() !!}
        </div>
    </div>
@endif
