<div class="table-responsive table-card mb-1">
    <table class="table table-hover table-centered align-middle table-nowrap mb-0" id="bankTable">
        <thead class="table-light text-muted">
            <tr>
                <th scope="col" style="width: 50px;">ลำดับ</th>
                <th scope="col">โลโก้</th>
                <th scope="col">ชื่อภาษาไทย</th>
                <th scope="col">ชื่อภาษาอังกฤษ</th>
                <th scope="col" class="text-center" style="width: 150px;">จัดการ</th>
            </tr>
        </thead>
        <tbody class="list">
            @php
                $bankCollection = $banks ?? $bankList ?? collect([]);
            @endphp
            @if (isset($bankCollection) && $bankCollection->count() > 0)
                @foreach ($bankCollection as $index => $bank)
                    <tr>
                        <td>
                            @if (method_exists($bankCollection, 'currentPage'))
                                {{ ($bankCollection->currentPage() - 1) * $bankCollection->perPage() + $index + 1 }}
                            @else
                                {{ $index + 1 }}
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if (!empty($bank->logo))
                                    <img src="{{ asset('assets/images/banks/' . $bank->logo) }}" 
                                         alt="{{ $bank->name_en }}" 
                                         class="avatar-xs rounded img-thumbnail"
                                         onerror="this.onerror=null;this.src='{{ asset('assets/images/banks/bank-dummy.png') }}';">
                                @else
                                    <div class="avatar-xs bg-light rounded d-flex align-items-center justify-content-center">
                                        <i class="ri-bank-line text-muted"></i>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $bank->name_th }}</span>
                        </td>
                        <td>
                            {{ $bank->name_en }}
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-soft-info edit-bank-btn" data-id="{{ $bank->id }}" title="แก้ไข">
                                    <i class="ri-edit-2-line"></i>
                                </button>
                                <button class="btn btn-sm btn-soft-danger delete-bank-btn" data-id="{{ $bank->id }}" title="ลบ">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <div class="avatar-md mb-3 mx-auto">
                            <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                <i class="ri-bank-line"></i>
                            </div>
                        </div>
                        <h5 class="text-muted">ไม่พบข้อมูลธนาคาร</h5>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

@if (isset($bankCollection) && method_exists($bankCollection, 'links'))
    <div class="d-flex justify-content-between align-items-center mt-3 px-2">
        <div class="text-muted">
            แสดง {{ $bankCollection->firstItem() ?? 0 }} ถึง {{ $bankCollection->lastItem() ?? 0 }} จากทั้งหมด {{ $bankCollection->total() ?? 0 }} รายการ
        </div>
        <div class="pagination-container">
            {!! $bankCollection->links() !!}
        </div>
    </div>
@endif
