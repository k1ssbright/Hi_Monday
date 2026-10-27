@extends('office.layout.office')

@section('title', 'ช่องทางรับ Lead | Hi Monday')

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', ['section' => 'การตั้งค่า', 'title' => 'ช่องทางรับ Lead'])
    @include('office.settings.partials.flash')

    <div class="card">
        <div class="card-header border-0">
            <div class="row g-4 align-items-center">
                <div class="col-sm">
                    <h5 class="card-title mb-0">
                        รายการช่องทางรับ Lead
                        <span class="badge bg-secondary-subtle text-secondary fs-12 ms-1">{{ $rows->total() }}</span>
                    </h5>
                </div>
                <div class="col-sm-auto">
                    <div class="d-flex gap-2 flex-wrap">
                        <div class="search-box">
                            <input type="text" class="form-control search" id="channelSearch" placeholder="ค้นหาช่องทาง...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                        <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" data-bs-target="#showModal">
                            <i class="ri-add-line align-bottom me-1"></i> เพิ่มช่องทาง
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive table-card mb-1">
                <table class="table table-hover align-middle table-nowrap mb-0" id="channelTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;" class="text-center">ลำดับ</th>
                            <th>ชื่อช่องทาง</th>
                            <th>รหัสอ้างอิง (Code)</th>
                            <th>กลุ่มช่องทาง</th>
                            <th>สถานะ</th>
                            <th style="width: 120px;" class="text-end">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse ($rows as $row)
                        @php
                            $kindMeta = match($row->kind) {
                                'inbound'  => ['label' => 'Inbound',  'badge' => 'bg-success-subtle text-success'],
                                'paid'     => ['label' => 'Paid Ads', 'badge' => 'bg-warning-subtle text-warning'],
                                'owned'    => ['label' => 'Owned',    'badge' => 'bg-info-subtle text-info'],
                                'outbound' => ['label' => 'Outbound', 'badge' => 'bg-primary-subtle text-primary'],
                                'partner'  => ['label' => 'Partner',  'badge' => 'bg-secondary-subtle text-secondary'],
                                'offline'  => ['label' => 'Offline',  'badge' => 'bg-dark-subtle text-dark'],
                                default    => ['label' => ucfirst($row->kind), 'badge' => 'bg-light text-muted'],
                            };
                        @endphp
                        <tr data-channel-row>
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
                                <span class="badge {{ $kindMeta['badge'] }} fs-11">
                                    {{ $kindMeta['label'] }}
                                </span>
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
                                        <form action="{{ route('office.settings.lead-channels.destroy', $row) }}" method="post" class="d-inline" onsubmit="return confirm('ยืนยันการลบช่องทาง「{{ $row->name }}」ใช่หรือไม่?');">
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
                                    <form action="{{ route('office.settings.lead-channels.update', $row) }}" method="post">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-light p-3">
                                            <h5 class="modal-title" id="editModalLabel{{ $row->id }}">แก้ไขช่องทางรับ Lead</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('office.settings.lead-channels._form', ['row' => $row])
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
                                        <i class="ri-route-line"></i>
                                    </div>
                                </div>
                                <h5 class="fs-14 mb-1">ยังไม่มีข้อมูลช่องทางรับ Lead</h5>
                                <p class="text-muted fs-13 mb-3">คุณสามารถเพิ่มช่องทางเพื่อระบุแหล่งที่มาของลูกค้า เช่น Facebook Ads, LINE OA หรือ หน้าร้าน</p>
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#showModal">
                                    <i class="ri-add-line align-bottom me-1"></i> เพิ่มช่องทาง
                                </button>
                            </td>
                        </tr>
                    @endforelse
                        <tr id="noChannelMatch" style="display: none;">
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="ri-search-line fs-18 align-middle me-1"></i> ไม่พบช่องทางที่ตรงกับคำค้นหา
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
            <form action="{{ route('office.settings.lead-channels.store') }}" method="post">
                @csrf
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="showModalLabel">เพิ่มช่องทางรับ Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('office.settings.lead-channels._form', ['row' => null])
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
        const searchInput = document.getElementById('channelSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function (e) {
                const query = e.target.value.toLowerCase().trim();
                const rows = document.querySelectorAll('#channelTable tbody tr[data-channel-row]');
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

                const noMatch = document.getElementById('noChannelMatch');
                if (noMatch) {
                    noMatch.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
                }
            });
        }
    });
</script>
@endsection
