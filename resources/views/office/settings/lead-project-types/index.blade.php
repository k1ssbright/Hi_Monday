@extends('office.layout.office')

@section('title', 'ประเภทงาน Lead | Hi Monday')

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', ['section' => 'การตั้งค่า', 'title' => 'ประเภทงาน Lead'])
    @include('office.settings.partials.flash')

    <div class="card">
        <div class="card-header border-0">
            <div class="row g-4 align-items-center">
                <div class="col-sm">
                    <h5 class="card-title mb-0">
                        รายการประเภทงาน Lead
                        <span class="badge bg-secondary-subtle text-secondary fs-12 ms-1">{{ $rows->total() }}</span>
                    </h5>
                </div>
                <div class="col-sm-auto">
                    <div class="d-flex gap-2 flex-wrap">
                        <div class="search-box">
                            <input type="text" class="form-control search" id="projectTypeSearch" placeholder="ค้นหาประเภทงาน, รหัส...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                        <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" data-bs-target="#showModal">
                            <i class="ri-add-line align-bottom me-1"></i> เพิ่มประเภทงาน
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive table-card mb-1">
                <table class="table table-hover align-middle table-nowrap mb-0" id="projectTypeTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;" class="text-center">ลำดับ</th>
                            <th>ชื่อประเภทงาน</th>
                            <th>รหัสอ้างอิง (Code)</th>
                            <th>หมวดสินค้าแนะนำ</th>
                            <th>สถานะ</th>
                            <th style="width: 120px;" class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($rows as $row)
                        <tr data-project-type-row>
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
                                @if ($row->category)
                                    <span class="badge bg-info-subtle text-info fs-11">
                                        <i class="ri-price-tag-3-line align-middle me-1"></i>{{ $row->category->name }}
                                    </span>
                                @else
                                    <span class="text-muted fs-12">—</span>
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
                                        <form action="{{ route('office.settings.lead-project-types.destroy', $row) }}" method="post" class="d-inline" onsubmit="return confirm('ยืนยันการลบประเภทงาน「{{ $row->name }}」ใช่หรือไม่?');">
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
                                    <form action="{{ route('office.settings.lead-project-types.update', $row) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-light p-3">
                                            <h5 class="modal-title" id="editModalLabel{{ $row->id }}">แก้ไขประเภทงาน Lead</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('office.settings.lead-project-types._form', ['row' => $row, 'categories' => $categories])
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
                                        <i class="ri-briefcase-line"></i>
                                    </div>
                                </div>
                                <h5 class="fs-14 mb-1">ยังไม่มีข้อมูลประเภทงาน Lead</h5>
                                <p class="text-muted fs-13 mb-3">คุณสามารถเพิ่มประเภทงานสำหรับจัดกลุ่ม Lead เช่น งานติดตั้ง, งานบริการ หรือ ออกแบบภายใน</p>
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#showModal">
                                    <i class="ri-add-line align-bottom me-1"></i> เพิ่มประเภทงาน
                                </button>
                            </td>
                        </tr>
                    @endforelse
                        <tr id="noProjectTypeMatch" style="display: none;">
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="ri-search-line fs-18 align-middle me-1"></i> ไม่พบประเภทงานที่ตรงกับคำค้นหา
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
            <form action="{{ route('office.settings.lead-project-types.store') }}" method="post">
                @csrf
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="showModalLabel">เพิ่มประเภทงาน Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('office.settings.lead-project-types._form', ['row' => null, 'categories' => $categories])
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
        const searchInput = document.getElementById('projectTypeSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function (e) {
                const query = e.target.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#projectTypeTable tbody tr[data-project-type-row]');
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

                const noMatch = document.getElementById('noProjectTypeMatch');
                if (noMatch) {
                    noMatch.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
                }
            });
        }
    });
</script>
@endsection
