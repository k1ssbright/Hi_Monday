<div class="table-responsive">
    <table class="table-biz-custom align-middle" id="partnerTable">
        <thead>
            <tr>
                <th class="text-center" style="width: 50px;">ลำดับ</th>
                <th style="width: 250px;">
                    ชื่อคู่ค้า / องค์กรหลัก <i class="ri-arrow-up-down-line sort-icon"></i>
                </th>
                <th style="width: 130px;">รหัสสาขา</th>
                <th>ชื่อสาขา / สำนักงาน</th>
                <th>เลขประจำตัวผู้เสียภาษี</th>
                <th>เบอร์โทรศัพท์</th>
                <th>ประเภท</th>
                <th class="text-center" style="width: 90px;">สถานะ</th>
                <th class="text-center" style="width: 100px;">จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($partners as $partnerIndex => $partner)
                @php
                    $branches = $partner->branches->isNotEmpty() ? $partner->branches : collect([null]);
                    $rowspan = max(1, $branches->count());
                    $role = $partner->role();
                    $roleLabel = $partner->roleLabel();
                    $badgeClass = match ($role) {
                        'vendor' => 'bg-warning-subtle text-warning',
                        'both' => 'bg-primary-subtle text-primary',
                        default => 'bg-info-subtle text-info',
                    };
                    $nameClass = match ($role) {
                        'vendor' => 'text-warning',
                        'both' => 'text-primary',
                        default => 'text-info',
                    };
                    $avatarClass = match ($role) {
                        'vendor' => 'bg-warning',
                        'both' => 'bg-primary',
                        default => 'bg-info',
                    };
                    $initials = mb_strtoupper(mb_substr($partner->partner_code ?: $partner->partner_name_th, 0, 3));
                @endphp
                @foreach ($branches as $branchIndex => $branch)
                    <tr @class(['table-group-header', 'border-top' => $partnerIndex > 0 && $branchIndex === 0])>
                        @if ($branchIndex === 0)
                            <td rowspan="{{ $rowspan }}" class="text-center text-muted fw-bold align-middle border-end bg-light-subtle">
                                {{ $partners->firstItem() + $partnerIndex }}
                            </td>
                            <td rowspan="{{ $rowspan }}" class="align-middle bg-light-subtle border-end">
                                <a href="{{ route('office.partners.show', $partner) }}" class="d-block text-decoration-none p-2 rounded-3 company-link-hover">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="biz-logo-avatar {{ $avatarClass }} text-white fw-bold shadow-sm" style="width: 44px; height: 44px; font-size: 1.1rem;">{{ $initials }}</div>
                                        <div>
                                            <div class="fw-bold {{ $nameClass }} fs-15">
                                                {{ $partner->partner_name_th }}
                                                @if ($partner->partner_code)
                                                    ({{ $partner->partner_code }})
                                                @endif
                                                <i class="ri-arrow-right-circle-line ms-1 fs-16"></i>
                                            </div>
                                            <small class="text-muted">{{ $roleLabel }} • {{ $partner->branches_count }} สาขา</small>
                                        </div>
                                    </div>
                                </a>
                            </td>
                        @endif
                        <td class="fw-bold text-dark">{{ $branch?->partner_branch_no ?: '-' }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="ri-git-commit-line {{ $branch?->roleIconClass() ?? 'text-info' }}"></i>
                                <span class="fw-semibold text-dark">{{ $branch?->partner_branch_name_th ?: '-' }}</span>
                            </div>
                        </td>
                        <td>{{ $partner->partner_tax_no ?: '-' }}</td>
                        <td>{{ $branch?->partner_tel ?: $branch?->partner_phone ?: '-' }}</td>
                        <td>
                            <span class="badge {{ $branch?->roleBadgeClass() ?? $badgeClass }}">
                                {{ $branch?->roleLabel() ?? $roleLabel }}
                            </span>
                        </td>
                        @if ($branchIndex === 0)
                            <td rowspan="{{ $rowspan }}" class="text-center align-middle border-start bg-light-subtle">
                                <form method="post" action="{{ route('office.partners.status', $partner) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <div class="form-check form-switch switch-navy d-inline-block mb-0">
                                        <input class="form-check-input status-toggle"
                                               type="checkbox"
                                               name="active_status"
                                               value="1"
                                               @checked($partner->isActive())
                                               onchange="this.form.submit()"
                                               title="สถานะ {{ $partner->partner_name_th }}">
                                    </div>
                                </form>
                            </td>
                            <td rowspan="{{ $rowspan }}" class="text-center align-middle border-start bg-light-subtle">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('office.partners.show', $partner) }}" class="btn-action-icon btn-action-view" title="ดูรายละเอียด {{ $partner->partner_name_th }}"><i class="ri-eye-fill"></i></a>
                                    <button type="button"
                                            class="btn-action-icon btn-action-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#partnerEditModal"
                                            data-id="{{ $partner->id }}"
                                            data-code="{{ $partner->partner_code }}"
                                            data-name="{{ $partner->partner_name_th }}"
                                            data-tax="{{ $partner->partner_tax_no }}"
                                            data-role="{{ $partner->role() }}"
                                            data-action="{{ route('office.partners.update', $partner) }}"
                                            title="แก้ไข {{ $partner->partner_name_th }}">
                                        <i class="ri-pencil-fill"></i>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                        <i class="ri-contacts-book-line fs-24 d-block mb-2 opacity-50"></i>
                        ยังไม่มีข้อมูลคู่ค้า
                        <div class="mt-3">
                            <a href="{{ route('office.partners.create') }}" class="btn btn-sm btn-primary">เพิ่มคู่ค้า</a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="d-flex justify-content-between align-items-center mt-4 pt-2 flex-wrap gap-3">
    <span class="text-muted fs-13">
        แสดง {{ $partners->firstItem() ?: 0 }}-{{ $partners->lastItem() ?: 0 }}
        จาก {{ $partners->total() }} คู่ค้า
    </span>
    {{ $partners->links() }}
</div>
