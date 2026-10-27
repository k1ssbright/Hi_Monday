@extends('office.layout.office')

@section('title', 'กฎการมอบหมาย Lead | Hi Monday')

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', ['section' => 'การตั้งค่า', 'title' => 'กฎการมอบหมาย Lead'])
    @include('office.settings.partials.flash')

    <div class="card">
        <div class="card-header border-0">
            <div class="row g-4 align-items-center">
                <div class="col-sm">
                    <h5 class="card-title mb-0">
                        รายการกฎการมอบหมาย Lead
                        <span class="badge bg-secondary-subtle text-secondary fs-12 ms-1">{{ $rows->total() }}</span>
                    </h5>
                </div>
                <div class="col-sm-auto">
                    <div class="d-flex gap-2 flex-wrap">
                        <div class="search-box">
                            <input type="text" class="form-control search" id="ruleSearch" placeholder="ค้นหากฎ, ช่องทาง, ผู้รับผิดชอบ...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                        <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" data-bs-target="#showModal">
                            <i class="ri-add-line align-bottom me-1"></i> เพิ่มกฎการมอบหมาย
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-top border-bottom py-2 px-3 bg-light-subtle text-muted fs-12">
            <i class="ri-information-line align-middle me-1 text-primary"></i>
            ระบบจะตรวจสอบเงื่อนไขและมอบหมาย Lead ตามลำดับความสำคัญ (ค่าตัวเลขน้อยกว่าจะถูกนำมาใช้ก่อน)
        </div>

        <div class="card-body">
            <div class="table-responsive table-card mb-1">
                <table class="table table-hover align-middle table-nowrap mb-0" id="ruleTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;" class="text-center">Priority</th>
                            <th>ชื่อกฎการมอบหมาย</th>
                            <th>เงื่อนไขที่กำหนด</th>
                            <th>มอบหมายให้</th>
                            <th>สถานะ</th>
                            <th style="width: 120px;" class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($rows as $row)
                        @php
                            $pv = $row->province_id ? $provinces->firstWhere('id', $row->province_id) : null;
                        @endphp
                        <tr data-rule-row>
                            <td class="text-center">
                                <span class="badge bg-light text-body border fs-12">{{ $row->priority }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $row->name }}</span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge bg-light text-muted border fs-11">
                                        <i class="ri-route-line align-middle me-1 text-primary"></i>{{ $row->channel?->name ?? 'ทุกช่องทาง' }}
                                    </span>
                                    @if ($pv)
                                        <span class="badge bg-light text-muted border fs-11">
                                            <i class="ri-map-pin-line align-middle me-1 text-danger"></i>{{ $pv->name_th }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if ($row->assignEmployee)
                                    <span class="fw-medium text-dark">
                                        <i class="ri-user-line text-primary align-middle me-1"></i>{{ $row->assignEmployee->displayName() }}
                                    </span>
                                @elseif ($row->assignBranch)
                                    <span class="text-muted">
                                        <i class="ri-building-line text-info align-middle me-1"></i>{{ $row->assignBranch->business_branch_name_th }}
                                    </span>
                                @else
                                    <span class="text-muted fs-12">
                                        <i class="ri-team-line text-secondary align-middle me-1"></i>คิวส่วนกลางของสาขา
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if ($row->is_active)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="ri-checkbox-circle-line align-middle me-1"></i>เปิดใช้งาน
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="ri-close-circle-line align-middle me-1"></i>ปิดใช้งาน
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <ul class="list-inline hstack gap-2 justify-content-end mb-0">
                                    <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="แก้ไข">
                                        <a href="#editModal{{ $row->id }}" data-bs-toggle="modal" class="btn btn-sm btn-soft-primary">
                                            <i class="ri-pencil-fill"></i>
                                        </a>
                                    </li>
                                    <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="ลบ">
                                        <form action="{{ route('office.settings.lead-assignment-rules.destroy', $row) }}" method="post" class="d-inline" onsubmit="return confirm('ยืนยันการลบกฎ「{{ $row->name }}」ใช่หรือไม่?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-soft-danger">
                                                <i class="ri-delete-bin-fill"></i>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </td>
                        </tr>

                        {{-- Edit Modal --}}
                        <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $row->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0">
                                    <form action="{{ route('office.settings.lead-assignment-rules.update', $row) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-light p-3">
                                            <h5 class="modal-title" id="editModalLabel{{ $row->id }}">แก้ไขกฎการมอบหมาย Lead</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('office.settings.lead-assignment-rules._form', ['row' => $row])
                                        </div>
                                        <div class="modal-footer">
                                            <div class="hstack gap-2 justify-content-end">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="ri-save-line align-bottom me-1"></i> บันทึกการเปลี่ยนแปลง
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="avatar-md mx-auto mb-3">
                                    <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                        <i class="ri-user-shared-line"></i>
                                    </div>
                                </div>
                                <h5 class="fs-14 mb-1">ยังไม่มีข้อมูลกฎการมอบหมาย Lead</h5>
                                <p class="text-muted fs-13 mb-3">สร้างกฎเพื่อช่วยส่งต่อและกระจาย Lead ให้แก่พนักงานหรือสาขาโดยอัตโนมัติตามเงื่อนไขที่กำหนด</p>
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#showModal">
                                    <i class="ri-add-line align-bottom me-1"></i> เพิ่มกฎการมอบหมาย
                                </button>
                            </td>
                        </tr>
                    @endforelse
                        <tr id="noRuleMatch" style="display: none;">
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="ri-search-line fs-18 align-middle me-1"></i> ไม่พบกฎที่ตรงกับคำค้นหา
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if ($rows->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $rows->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="showModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <form action="{{ route('office.settings.lead-assignment-rules.store') }}" method="post">
                @csrf
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="showModalLabel">เพิ่มกฎการมอบหมาย Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('office.settings.lead-assignment-rules._form', ['row' => null])
                </div>
                <div class="modal-footer">
                    <div class="hstack gap-2 justify-content-end">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-success">
                            <i class="ri-save-line align-bottom me-1"></i> บันทึก
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('ruleSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function (e) {
                const query = e.target.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#ruleTable tbody tr[data-rule-row]');
                let visibleCount = 0;

                rows.forEach(function (row) {
                    const content = row.textContent.toLowerCase();
                    if (!query || content.includes(query)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                const noMatch = document.getElementById('noRuleMatch');
                if (noMatch) {
                    noMatch.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
                }
            });
        }
    });

    $(document).ready(function() {
        // Initialize Select2 for Create Modal when shown
        $('#showModal').on('shown.bs.modal', function () {
            $(this).find('.select2-init').select2({
                dropdownParent: $('#showModal'),
                width: '100%'
            });
        });

        // Initialize Select2 for Edit Modals when shown
        $('[id^=editModal]').on('shown.bs.modal', function () {
            var modalId = $(this).attr('id');
            $(this).find('.select2-init').select2({
                dropdownParent: $('#' + modalId),
                width: '100%'
            });
        });
    });
</script>
@endsection
