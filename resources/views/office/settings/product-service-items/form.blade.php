@extends('office.layout.office')

@section('title', ($item->exists ? 'แก้ไข' : 'เพิ่ม').' สินค้า/บริการ | Hi Monday')

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', [
        'title' => $item->exists ? 'แก้ไขสินค้า / บริการ' : 'เพิ่มสินค้า / บริการ',
        'parent' => 'สินค้า / บริการ',
        'parentUrl' => route('office.settings.product-service-items.index'),
    ])
    @include('office.settings.partials.flash')

    <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate
          action="{{ $item->exists ? route('office.settings.product-service-items.update', $item) : route('office.settings.product-service-items.store') }}">
        @csrf
        @if ($item->exists) @method('PUT') @endif

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="name">ชื่อ</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $item->name) }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ประเภท</label>
                                <select name="kind" class="form-select" required>
                                    <option value="product" @selected(old('kind', $item->kind) === 'product')>สินค้า</option>
                                    <option value="service" @selected(old('kind', $item->kind) === 'service')>บริการ</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">หมวด</label>
                                <select name="product_service_category_id" class="form-select">
                                    <option value="">— ไม่ระบุ —</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected((string) old('product_service_category_id', $item->product_service_category_id) === (string) $cat->id)>
                                            {{ $cat->name }} ({{ $cat->kind === 'service' ? 'บริการ' : 'สินค้า' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">รหัสอ้างอิง</label>
                                <input type="text" name="code" class="form-control" value="{{ old('code', $item->code) }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">รายละเอียด</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $item->description) }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">ข้อมูลเพิ่มเติม</h5></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">แบรนด์</label>
                                <input type="text" name="brand" class="form-control" value="{{ old('brand', $item->brand) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ผู้ผลิต</label>
                                <input type="text" name="manufacturer" class="form-control" value="{{ old('manufacturer', $item->manufacturer) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">รุ่น</label>
                                <input type="text" name="model" class="form-control" value="{{ old('model', $item->model) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">หน่วย</label>
                                <input type="text" name="unit" class="form-control" value="{{ old('unit', $item->unit) }}" placeholder="ชิ้น / งาน">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ราคาขาย</label>
                                <input type="number" step="0.01" name="default_price" class="form-control" value="{{ old('default_price', $item->default_price) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ราคาทุน</label>
                                <input type="number" step="0.01" name="buy_price" class="form-control" value="{{ old('buy_price', $item->buy_price) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">รูปภาพ</h5></div>
                    <div class="card-body">
                        <input class="form-control" type="file" name="image" accept="image/*">
                        @if ($item->image)
                            <img src="{{ asset('upload_file/'.$item->image) }}" class="img-thumbnail mt-3" alt="">
                        @endif
                    </div>
                </div>
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">สถานะ</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <select name="status" class="form-select">
                                <option value="active" @selected(old('status', $item->status ?: 'active') === 'active')>active</option>
                                <option value="inactive" @selected(old('status', $item->status) === 'inactive')>inactive</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $item->is_active ?? true))>
                            <label class="form-check-label" for="is_active">ใช้งาน</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="track_inventory" value="1" id="track_inventory" @checked(old('track_inventory', $item->track_inventory))>
                            <label class="form-check-label" for="track_inventory">ติดตามสต็อก</label>
                        </div>
                    </div>
                </div>
                <div class="text-end mb-4">
                    <a href="{{ route('office.settings.product-service-items.index') }}" class="btn btn-light">ยกเลิก</a>
                    <button type="submit" class="btn btn-success">บันทึก</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
