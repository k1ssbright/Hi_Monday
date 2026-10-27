@extends('office.layout.office')

@section('title', 'คำถามรับ Lead | Hi Monday')

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', ['title' => 'คำถามตามหมวดสินค้า'])
    @include('office.settings.partials.flash')

    <div class="card">
        <div class="card-header border-0">
            <div class="row g-3 align-items-center">
                <div class="col-sm-auto">
                    <form method="get" class="hstack gap-2">
                        <select name="purpose" class="form-select" onchange="this.form.submit()">
                            <option value="">ทุกวัตถุประสงค์</option>
                            <option value="intake" @selected(request('purpose') === 'intake')>Intake</option>
                            <option value="quality" @selected(request('purpose') === 'quality')>Quality</option>
                        </select>
                        <select name="category_id" class="form-select" onchange="this.form.submit()">
                            <option value="">ทุกหมวด</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected((string) request('category_id') === (string) $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="col-sm-auto ms-auto">
                    <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" data-bs-target="#showModal">
                        <i class="ri-add-line align-bottom me-1"></i> เพิ่มคำถาม
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive table-card">
                <table class="table table-hover align-middle table-nowrap mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ลำดับ</th>
                            <th>หมวด</th>
                            <th>คำถาม</th>
                            <th>ประเภท</th>
                            <th>Purpose</th>
                            <th>บังคับ</th>
                            <th>สถานะ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($questions as $row)
                            <tr>
                                <td>{{ $row->sort_order }}</td>
                                <td>{{ $row->category?->name }}</td>
                                <td class="fw-semibold">{{ $row->label }}</td>
                                <td><span class="badge bg-light text-body">{{ $row->input_type }}</span></td>
                                <td>{{ $row->purpose }}</td>
                                <td>{{ $row->is_required ? 'ใช่' : 'ไม่' }}</td>
                                <td>
                                    @if ($row->is_active)
                                        <span class="badge bg-success-subtle text-success">ใช้งาน</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">ปิด</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="#editModal{{ $row->id }}" data-bs-toggle="modal" class="text-muted"><i class="ri-pencil-fill"></i></a>
                                    <form class="d-inline" action="{{ route('office.settings.product-service-questions.destroy', $row) }}" method="post" onsubmit="return confirm('ลบคำถามนี้?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-link p-0 text-muted" type="submit"><i class="ri-delete-bin-fill"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('office.settings.product-service-questions.update', $row) }}" method="post">
                                            @csrf @method('PUT')
                                            <div class="modal-header bg-light p-3"><h5 class="modal-title">แก้ไขคำถาม</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <div class="modal-body">@include('office.settings.product-service-questions._form', ['row' => $row, 'categories' => $categories])</div>
                                            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button><button class="btn btn-success" type="submit">บันทึก</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr><td colspan="8" class="text-center py-5 text-muted">ยังไม่มีคำถาม</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $questions->links() }}
        </div>
    </div>
</div>

<div class="modal fade" id="showModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('office.settings.product-service-questions.store') }}" method="post">
                @csrf
                <div class="modal-header bg-light p-3"><h5 class="modal-title">เพิ่มคำถาม</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">@include('office.settings.product-service-questions._form', ['row' => null, 'categories' => $categories])</div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button><button class="btn btn-success" type="submit">บันทึก</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
