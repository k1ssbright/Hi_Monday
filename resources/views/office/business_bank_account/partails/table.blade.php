<div class="table-responsive table-card mb-1">
    <table class="table table-hover table-centered align-middle table-nowrap mb-0" id="businessBankAccountTable">
        <thead class="table-light text-muted">
            <tr>
                <th scope="col" style="width: 50px;">ลำดับ</th>
                <th scope="col">ธุรกิจ</th>
                <th scope="col">ธนาคาร</th>
                <th scope="col">ชื่อบัญชี</th>
                <th scope="col">เลขที่บัญชี</th>
                <th scope="col">ประเภท</th>
                <th scope="col">บัญชีหลัก</th>
                <th scope="col">สถานะ</th>
                <th scope="col" class="text-center" style="width: 150px;">จัดการ</th>
            </tr>
        </thead>
        <tbody class="list">
            @php
                $accounts = $businessBankAccounts ?? $accounts ?? collect([]);
            @endphp
            @if (isset($accounts) && $accounts->count() > 0)
                @foreach ($accounts as $index => $account)
                    <tr>
                        <td>
                            @if (method_exists($accounts, 'currentPage'))
                                {{ ($accounts->currentPage() - 1) * $accounts->perPage() + $index + 1 }}
                            @else
                                {{ $index + 1 }}
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">
                                @if (!empty($account->business))
                                    {{ $account->business->business_name_th ?? '-' }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if (!empty($account->bank) && !empty($account->bank->logo))
                                    <img src="{{ asset('assets/images/banks/' . $account->bank->logo) }}" alt="bank logo" class="avatar-xs rounded me-2" onerror="this.style.display='none';">
                                @endif
                                <span>{{ $account->bank->name_th ?? $account->bank->name_en ?? '-' }}</span>
                            </div>
                        </td>
                        <td>
                            {{ $account->account_name }}
                        </td>
                        <td>
                            <code class="text-dark">{{ $account->account_no }}</code>
                        </td>
                        <td>
                            @if ($account->is_salary)
                                <span class="badge bg-info-subtle text-info">บัญชีเงินเดือน</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($account->default_status)
                                <span class="badge bg-primary text-uppercase">บัญชีหลัก</span>
                            @else
                                <span class="badge bg-light text-muted">สำรอง</span>
                            @endif
                        </td>
                        <td>
                            @if ($account->active_status)
                                <span class="badge bg-success-subtle text-success">ใช้งาน</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger">ไม่ใช้งาน</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-soft-info edit-business-bank-account-btn" data-id="{{ $account->id }}" title="แก้ไข">
                                    <i class="ri-edit-2-line"></i>
                                </button>
                                <button class="btn btn-sm btn-soft-danger delete-business-bank-account-btn" data-id="{{ $account->id }}" title="ลบ">
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
                                <i class="ri-bank-card-2-line"></i>
                            </div>
                        </div>
                        <h5 class="text-muted">ไม่พบข้อมูลบัญชีธนาคารธุรกิจ</h5>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

@if (isset($accounts) && method_exists($accounts, 'links'))
    <div class="d-flex justify-content-between align-items-center mt-3 px-2">
        <div class="text-muted">
            แสดง {{ $accounts->firstItem() ?? 0 }} ถึง {{ $accounts->lastItem() ?? 0 }} จากทั้งหมด {{ $accounts->total() ?? 0 }} รายการ
        </div>
        <div class="pagination-container">
            {!! $accounts->links() !!}
        </div>
    </div>
@endif
