@extends('office.layout.office')

@section('title', 'แคมเปญโฆษณา | Hi Monday')

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', ['title' => 'แคมเปญโฆษณา'])
    @include('office.settings.partials.flash')

    <div class="card">
        <div class="card-header border-0">
            <div class="row g-4 align-items-center">
                <div class="col-sm-4">
                    <form method="get" class="search-box">
                        <input type="text" name="q" class="form-control search" value="{{ request('q') }}" placeholder="ค้นหาชื่อแคมเปญ...">
                        <i class="ri-search-line search-icon"></i>
                    </form>
                </div>
                <div class="col-sm-auto">
                    <form method="get">
                        <input type="hidden" name="q" value="{{ request('q') }}">
                        <select name="status" class="form-select" onchange="this.form.submit()">
                            <option value="">ทุกสถานะ</option>
                            @foreach (['active','paused','ended'] as $st)
                                <option value="{{ $st }}" @selected(request('status') === $st)>{{ $st }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
                <div class="col-sm-auto ms-auto">
                    <a href="{{ route('office.settings.marketing-ads.create') }}" class="btn btn-success add-btn">
                        <i class="ri-add-line align-bottom me-1"></i> สร้างแคมเปญ
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive table-card">
                <table class="table table-hover align-middle table-nowrap mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ชื่อแคมเปญ</th>
                            <th>สินค้า</th>
                            <th>งบ</th>
                            <th>ช่วงเวลา</th>
                            <th>หน่วยโฆษณา</th>
                            <th>สถานะ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ads as $row)
                            <tr>
                                <td>
                                    <a href="{{ route('office.settings.marketing-ads.show', $row) }}" class="fw-semibold link-primary">{{ $row->name }}</a>
                                </td>
                                <td>{{ $row->productServiceItem?->name ?? '-' }}</td>
                                <td>{{ $row->budget !== null ? number_format($row->budget, 2) : '-' }}</td>
                                <td>
                                    {{ $row->start_date?->format('d/m/Y') ?? '-' }}
                                    –
                                    {{ $row->end_date?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td>{{ $row->units_count }} / artwork {{ $row->artworks_count }}</td>
                                <td>
                                    @php
                                        $badge = match($row->status) {
                                            'paused' => 'bg-warning-subtle text-warning',
                                            'ended' => 'bg-secondary-subtle text-secondary',
                                            default => 'bg-success-subtle text-success',
                                        };
                                    @endphp
                                    <span class="badge {{ $badge }}">{{ $row->status }}</span>
                                </td>
                                <td>
                                    <a class="text-muted" href="{{ route('office.settings.marketing-ads.edit', $row) }}"><i class="ri-pencil-fill"></i></a>
                                    <form class="d-inline" action="{{ route('office.settings.marketing-ads.destroy', $row) }}" method="post" onsubmit="return confirm('ลบแคมเปญนี้?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-link p-0 text-muted" type="submit"><i class="ri-delete-bin-fill"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-5 text-muted">ยังไม่มีแคมเปญ</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $ads->links() }}
        </div>
    </div>
</div>
@endsection
