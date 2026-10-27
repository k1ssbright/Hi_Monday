@extends('office.layout.office')

@section('title', 'เหตุผลปิด Lead | Hi Monday')

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', ['section' => 'การตั้งค่า', 'title' => 'เหตุผลปิด Lead'])
    @include('office.settings.partials.flash')

    <div class="card">
        <div class="card-header border-0">
            <div class="row g-4 align-items-center">
                <div class="col-sm">
                    <h5 class="card-title mb-0">
                        รายการเหตุผลปิด Lead
                        <span class="badge bg-secondary-subtle text-secondary fs-12 ms-1">{{ $rows->total() }}</span>
                    </h5>
                </div>
                <div class="col-sm-auto">
                    <div class="d-flex gap-2 flex-wrap">
                        <div class="search-box">
                            <input type="text" class="form-control search" id="reasonSearch" placeholder="ค้นหาเหตุผล, รหัส...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                        <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" data-bs-target="#showModal">
                            <i class="ri-add-line align-bottom me-1"></i> เพิ่มเหตุผล
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive table-card mb-1">
                <table class="table table-hover align-middle table-nowrap mb-0" id="reasonTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;" class="text-center">ลำดับ</th>
                            <th>ชื่อเหตุผล</th>
                            <th>รหัสอ้างอิง (Code)</th>
                            <th>ใช้กับ</th>
                            <th>เปิดใหม่ได้ (Reopen)</th>
                            <th>สถานะ</th>
                            <th style="width: 120px;" class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($rows as $row)
                        @php
                            $appliesMeta = match($row->applies_to) {
                                'lead'        => ['label' => 'Lead',              'badge' => 'bg-primary-subtle text-primary'],
                                'opportunity' => ['label' => 'Opportunity',       'badge' => 'bg-info-subtle text-info'],
                                'both'        => ['label' => 'Lead + Opportunity', 'badge' => 'bg-secondary-subtle text-secondary'],
                                default       => ['label' => ucfirst($row->applies_to), 'badge' => 'bg-light text-muted'],
                            };
                        @endphp
                        <tr data-reason-row>
                            <td class="text-center text-muted fw-medium">{{ $row->sort }}</td>
                            <td>
                                <span class="fw-semibold text-dark">{{ $row->name }}</span>
                            </td>
                            <td>
                                @if($row->code)
                                    <code class="text-muted fs-12">{{ $row->code }}</code>
                                @else
                                    <span class="text-muted fs-12">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $appliesMeta['badge'] }} fs-11">
                                    {{ $appliesMeta['label'] }}
                                </span>
                            </td>
                            <td>
                                @if ($row->allows_reopen)
                                    <span class="badge bg-success-subtle text-success fs-11">
                                        <i class="ri-check-line align-middle me-1"></i>เปิดใหม่ได้
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted fs-11">
                                        <i class="ri-close-line align-middle me-1"></i>ไม่ได้
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
                                        <form action="{{ route('office.settings.lead-closed-reasons.destroy', $row) }}" method="post" class="d-inline" onsubmit="return confirm('ยืนยันการลบเหตุผล「{{ $row->name }}」ใช่หรือไม่?');">
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
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0">
                                    <form action="{{ route('office.settings.lead-closed-reasons.update', $row) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-light p-3">
                                            <h5 class="modal-title" id="editModalLabel{{ $row->id }}">แก้ไขเหตุผลปิด Lead</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('office.settings.lead-closed-reasons._form', ['row' => $row])
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
                            <td colspan="7" class="text-center py-5">
                                <div class="avatar-md mx-auto mb-3">
                                    <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                        <i class="ri-forbid-line"></i>
                                    </div>
                                </div>
                                <h5 class="fs-14 mb-1">ยังไม่มีข้อมูลเหตุผลปิด Lead</h5>
                                <p class="text-muted fs-13 mb-3">คุณสามารถเพิ่มเหตุผลในการปิด Lead หรือ Opportunity เช่น งบประมาณไม่เพียงพอ, เลือกเจ้าอื่น หรือ ติดต่อไม่ได้</p>
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#showModal">
                                    <i class="ri-add-line align-bottom me-1"></i> เพิ่มเหตุผล
                                </button>
                            </td>
                        </tr>
                    @endforelse
                        <tr id="noReasonMatch" style="display: none;">
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="ri-search-line fs-18 align-middle me-1"></i> ไม่พบเหตุผลที่ตรงกับคำค้นหา
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <form action="{{ route('office.settings.lead-closed-reasons.store') }}" method="post">
                @csrf
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="showModalLabel">เพิ่มเหตุผลปิด Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('office.settings.lead-closed-reasons._form', ['row' => null])
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
        const searchInput = document.getElementById('reasonSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function (e) {
                const query = e.target.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#reasonTable tbody tr[data-reason-row]');
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

                const noMatch = document.getElementById('noReasonMatch');
                if (noMatch) {
                    noMatch.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
                }
            });
        }
    });
</script>
@endsection
