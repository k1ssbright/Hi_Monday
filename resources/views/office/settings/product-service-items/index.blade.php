@extends('office.layout.office')

@section('title', 'สินค้า/บริการ | Hi Monday')

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', ['title' => 'สินค้า / บริการ'])
    @include('office.settings.partials.flash')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">
                        <div class="col-sm-4">
                            <form method="get" class="search-box">
                                <input type="text" name="q" class="form-control search" value="{{ request('q') }}" placeholder="ค้นหาชื่อ รหัส แบรนด์...">
                                <i class="ri-search-line search-icon"></i>
                            </form>
                        </div>
                        <div class="col-sm-auto">
                            <form method="get">
                                <input type="hidden" name="q" value="{{ request('q') }}">
                                <select name="kind" class="form-select" onchange="this.form.submit()">
                                    <option value="">ทุกประเภท</option>
                                    <option value="product" @selected(request('kind') === 'product')>สินค้า</option>
                                    <option value="service" @selected(request('kind') === 'service')>บริการ</option>
                                </select>
                            </form>
                        </div>
                        <div class="col-sm-auto ms-auto">
                            <a href="{{ route('office.settings.product-service-items.create') }}" class="btn btn-success add-btn">
                                <i class="ri-add-line align-bottom me-1"></i> เพิ่มรายการ
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive table-card mb-1">
                        <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th>รหัสระบบ</th>
                                    <th>ชื่อ</th>
                                    <th>หมวด</th>
                                    <th>ราคาขาย</th>
                                    <th>สถานะ</th>
                                    <th class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $row)
                                    <tr>
                                        <td><span class="fw-medium text-primary">{{ $row->product_id }}</span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if ($row->image)
                                                    <img src="{{ asset('upload_file/'.$row->image) }}" alt="" class="avatar-xs rounded me-2">
                                                @else
                                                    <div class="avatar-xs bg-light rounded d-flex align-items-center justify-content-center me-2">
                                                        <i class="ri-shopping-bag-line text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-semibold">{{ $row->name }}</div>
                                                    <div class="text-muted fs-12">{{ $row->kind === 'service' ? 'บริการ' : 'สินค้า' }} {{ $row->code ? '· '.$row->code : '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $row->category?->name ?? '-' }}</td>
                                        <td>{{ $row->default_price !== null ? number_format($row->default_price, 2) : '-' }}</td>
                                        <td>
                                            @if ($row->status === 'active' && $row->is_active)
                                                <span class="badge bg-success-subtle text-success">ใช้งาน</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">ปิด</span>
                                            @endif
                                        </td>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0 justify-content-center">
                                                <li class="list-inline-item">
                                                    <a class="text-muted" href="{{ route('office.settings.product-service-items.edit', $row) }}"><i class="ri-pencil-fill"></i></a>
                                                </li>
                                                <li class="list-inline-item">
                                                    <form action="{{ route('office.settings.product-service-items.destroy', $row) }}" method="post" onsubmit="return confirm('ลบรายการนี้?');">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-link p-0 text-muted" type="submit"><i class="ri-delete-bin-fill"></i></button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">ยังไม่มีสินค้า/บริการ</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
