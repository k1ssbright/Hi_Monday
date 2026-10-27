@extends('office.layout.office')

@section('title', 'แพลตฟอร์มการตลาด | Hi Monday')

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', ['title' => 'แพลตฟอร์มการตลาด'])
    @include('office.settings.partials.flash')

    <div class="card">
        <div class="card-header border-0">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" data-bs-target="#showModal">
                    <i class="ri-add-line align-bottom me-1"></i> เพิ่มแพลตฟอร์ม
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive table-card">
                <table class="table table-hover align-middle table-nowrap mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:80px;">ลำดับ</th>
                            <th>ชื่อ</th>
                            <th>สถานะ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($platforms as $row)
                            <tr>
                                <td>{{ $row->sort }}</td>
                                <td class="fw-semibold">{{ $row->name }}</td>
                                <td>
                                    @if ($row->is_active)
                                        <span class="badge bg-success-subtle text-success">ใช้งาน</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">ปิด</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="#editModal{{ $row->id }}" data-bs-toggle="modal" class="text-muted"><i class="ri-pencil-fill"></i></a>
                                    <form class="d-inline" action="{{ route('office.settings.marketing-platforms.destroy', $row) }}" method="post" onsubmit="return confirm('ลบแพลตฟอร์มนี้?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-link p-0 text-muted" type="submit"><i class="ri-delete-bin-fill"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('office.settings.marketing-platforms.update', $row) }}" method="post">
                                            @csrf @method('PUT')
                                            <div class="modal-header bg-light p-3"><h5 class="modal-title">แก้ไขแพลตฟอร์ม</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <div class="modal-body">@include('office.settings.marketing-platforms._form', ['row' => $row])</div>
                                            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button><button class="btn btn-success">บันทึก</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">ยังไม่มีแพลตฟอร์ม</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $platforms->links() }}
        </div>
    </div>
</div>
<div class="modal fade" id="showModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('office.settings.marketing-platforms.store') }}" method="post">
                @csrf
                <div class="modal-header bg-light p-3"><h5 class="modal-title">เพิ่มแพลตฟอร์ม</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">@include('office.settings.marketing-platforms._form', ['row' => null])</div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button><button class="btn btn-success">บันทึก</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
