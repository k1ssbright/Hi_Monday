@extends('office.layout.office')

@section('title', 'หมวดสินค้า/บริการ | Hi Monday')

@section('style')
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', ['title' => 'หมวดสินค้า / บริการ'])
    @include('office.settings.partials.flash')

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="categoryList">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm-4">
                            <form method="get" class="search-box">
                                <input type="text" name="q" class="form-control search" value="{{ request('q') }}" placeholder="ค้นหาชื่อหรือรหัส...">
                                <i class="ri-search-line search-icon"></i>
                            </form>
                        </div>
                        <div class="col-sm-auto">
                            <form method="get" class="hstack gap-2">
                                <input type="hidden" name="q" value="{{ request('q') }}">
                                <select name="kind" class="form-select" onchange="this.form.submit()">
                                    <option value="">ทุกประเภท</option>
                                    <option value="product" @selected(request('kind') === 'product')>สินค้า</option>
                                    <option value="service" @selected(request('kind') === 'service')>บริการ</option>
                                </select>
                            </form>
                        </div>
                        <div class="col-sm-auto ms-auto">
                            <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" data-bs-target="#showModal">
                                <i class="ri-add-line align-bottom me-1"></i> เพิ่มหมวด
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card mb-1">
                        <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th>ประเภท</th>
                                    <th>รหัส</th>
                                    <th>ชื่อ</th>
                                    <th>รายการ</th>
                                    <th>คำถาม</th>
                                    <th>สถานะ</th>
                                    <th class="text-center" style="width: 120px;">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse ($categories as $row)
                                    <tr>
                                        <td>
                                            @if ($row->kind === 'service')
                                                <span class="badge bg-info-subtle text-info">บริการ</span>
                                            @else
                                                <span class="badge bg-primary-subtle text-primary">สินค้า</span>
                                            @endif
                                        </td>
                                        <td>{{ $row->code ?: '-' }}</td>
                                        <td>
                                            <span class="fw-semibold">{{ $row->name }}</span>
                                            @if ($row->description)
                                                <div class="text-muted fs-12">{{ \Illuminate\Support\Str::limit($row->description, 60) }}</div>
                                            @endif
                                        </td>
                                        <td>{{ $row->items_count }}</td>
                                        <td>{{ $row->questions_count }}</td>
                                        <td>
                                            @if ($row->is_active)
                                                <span class="badge bg-success-subtle text-success">ใช้งาน</span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger">ปิด</span>
                                            @endif
                                        </td>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0 justify-content-center">
                                                <li class="list-inline-item">
                                                    <a class="edit-item-btn text-muted" href="#editModal{{ $row->id }}" data-bs-toggle="modal"><i class="ri-pencil-fill align-bottom"></i></a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <form action="{{ route('office.settings.product-service-categories.destroy', $row) }}" method="post" onsubmit="return confirm('ลบหมวดนี้?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-link p-0 text-muted"><i class="ri-delete-bin-fill align-bottom"></i></button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                    <div class="modal fade" id="editModal{{ $row->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <form action="{{ route('office.settings.product-service-categories.update', $row) }}" method="post">
                                                    @csrf @method('PUT')
                                                    <div class="modal-header bg-light p-3">
                                                        <h5 class="modal-title">แก้ไขหมวด</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @include('office.settings.product-service-categories._form', ['row' => $row])
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                                                        <button type="submit" class="btn btn-success">บันทึก</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="avatar-md mb-3 mx-auto">
                                                <div class="avatar-title bg-light text-primary rounded-circle fs-24">
                                                    <i class="ri-folder-line"></i>
                                                </div>
                                            </div>
                                            <h5 class="text-muted">ยังไม่มีหมวดสินค้า/บริการ</h5>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="showModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('office.settings.product-service-categories.store') }}" method="post">
                @csrf
                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title">เพิ่มหมวด</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('office.settings.product-service-categories._form', ['row' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
