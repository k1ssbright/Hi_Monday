@extends('office.layout.office')

@section('title', ($ad->exists ? 'แก้ไข' : 'สร้าง').' แคมเปญ | Hi Monday')

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', [
        'title' => $ad->exists ? 'แก้ไขแคมเปญ' : 'สร้างแคมเปญ',
        'parent' => 'แคมเปญโฆษณา',
        'parentUrl' => route('office.settings.marketing-ads.index'),
    ])
    @include('office.settings.partials.flash')

    <form method="post" class="needs-validation" novalidate
          action="{{ $ad->exists ? route('office.settings.marketing-ads.update', $ad) : route('office.settings.marketing-ads.store') }}">
        @csrf
        @if ($ad->exists) @method('PUT') @endif
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">ชื่อแคมเปญ</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $ad->name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">สินค้า / บริการที่โปรโมต</label>
                            <select name="product_service_item_id" class="form-select">
                                <option value="">— ไม่ระบุ —</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" @selected((string) old('product_service_item_id', $ad->product_service_item_id) === (string) $item->id)>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea name="notes" class="form-control" rows="4">{{ old('notes', $ad->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">งบและช่วงเวลา</h5></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">งบประมาณ</label>
                            <input type="number" step="0.01" name="budget" class="form-control" value="{{ old('budget', $ad->budget) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">สถานะ</label>
                            <select name="status" class="form-select">
                                @foreach (['active','paused','ended'] as $st)
                                    <option value="{{ $st }}" @selected(old('status', $ad->status ?: 'active') === $st)>{{ $st }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">เริ่ม</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $ad->start_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">สิ้นสุด</label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $ad->end_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $ad->is_active ?? true))>
                            <label class="form-check-label" for="is_active">ใช้งาน</label>
                        </div>
                    </div>
                </div>
                <div class="text-end mb-4">
                    <a href="{{ route('office.settings.marketing-ads.index') }}" class="btn btn-light">ยกเลิก</a>
                    <button class="btn btn-success" type="submit">บันทึก</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
