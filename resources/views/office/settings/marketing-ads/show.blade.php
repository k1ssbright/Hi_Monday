@extends('office.layout.office')

@section('title', $ad->name.' | Hi Monday')

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', [
        'title' => $ad->name,
        'parent' => 'แคมเปญโฆษณา',
        'parentUrl' => route('office.settings.marketing-ads.index'),
    ])
    @include('office.settings.partials.flash')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-1">{{ $ad->name }}</h5>
                            <p class="text-muted mb-0">
                                สินค้า: {{ $ad->productServiceItem?->name ?? '-' }}
                                · งบ {{ $ad->budget !== null ? number_format($ad->budget, 2) : '-' }}
                                · {{ $ad->start_date?->format('d/m/Y') ?? '-' }} – {{ $ad->end_date?->format('d/m/Y') ?? '-' }}
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-success-subtle text-success fs-12 me-2">{{ $ad->status }}</span>
                            <a href="{{ route('office.settings.marketing-ads.edit', $ad) }}" class="btn btn-soft-primary btn-sm">แก้ไขแคมเปญ</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#units" role="tab">หน่วยโฆษณา</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#artworks" role="tab">Artwork</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#logs" role="tab">ประวัติ</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="units" role="tabpanel">
                            <div class="d-flex justify-content-end mb-3">
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#unitModal">
                                    <i class="ri-add-line align-bottom me-1"></i> เพิ่มหน่วยโฆษณา
                                </button>
                            </div>
                            <div class="table-responsive table-card">
                                <table class="table table-nowrap align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ชื่อ</th>
                                            <th>แพลตฟอร์ม</th>
                                            <th>วัตถุประสงค์</th>
                                            <th>External ID</th>
                                            <th>งบ / ใช้ไป</th>
                                            <th>สถานะ</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($ad->units as $unit)
                                            <tr>
                                                <td class="fw-semibold">{{ $unit->name }}</td>
                                                <td>{{ $unit->platform ?: '-' }}</td>
                                                <td>{{ $unit->objective ?: '-' }}</td>
                                                <td>{{ $unit->external_id ?: '-' }}</td>
                                                <td>{{ number_format($unit->budget, 2) }} / {{ number_format($unit->spend, 2) }}</td>
                                                <td><span class="badge bg-light text-body">{{ $unit->status }}</span></td>
                                                <td>
                                                    <a href="#editUnit{{ $unit->id }}" data-bs-toggle="modal" class="text-muted"><i class="ri-pencil-fill"></i></a>
                                                    <form class="d-inline" action="{{ route('office.settings.marketing-ads.units.destroy', [$ad, $unit]) }}" method="post" onsubmit="return confirm('ลบหน่วยนี้?');">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-link p-0 text-muted" type="submit"><i class="ri-delete-bin-fill"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="editUnit{{ $unit->id }}" tabindex="-1">
                                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <form action="{{ route('office.settings.marketing-ads.units.update', [$ad, $unit]) }}" method="post">
                                                            @csrf @method('PUT')
                                                            <div class="modal-header bg-light p-3"><h5 class="modal-title">แก้ไขหน่วยโฆษณา</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                                            <div class="modal-body">@include('office.settings.marketing-ads._unit-form', ['unit' => $unit, 'platforms' => $platforms, 'objectives' => $objectives])</div>
                                                            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button><button class="btn btn-success">บันทึก</button></div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <tr><td colspan="7" class="text-center py-4 text-muted">ยังไม่มีหน่วยโฆษณา</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="artworks" role="tabpanel">
                            <form action="{{ route('office.settings.marketing-ads.artworks.store', $ad) }}" method="post" enctype="multipart/form-data" class="row g-3 align-items-end mb-4">
                                @csrf
                                <div class="col-md-5">
                                    <label class="form-label">ไฟล์</label>
                                    <input type="file" name="file" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">ประเภท / แพลตฟอร์ม</label>
                                    <select name="type" class="form-select">
                                        @foreach (['facebook','instagram','tiktok','line','youtube','other'] as $type)
                                            <option value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-success w-100" type="submit">อัปโหลด</button>
                                </div>
                            </form>
                            <div class="row">
                                @forelse ($ad->artworks as $art)
                                    <div class="col-md-3 mb-3">
                                        <div class="card ribbon-box border mb-0">
                                            <div class="card-body">
                                                <span class="ribbon ribbon-primary ribbon-shape">{{ $art->type }}</span>
                                                <p class="mb-2 text-truncate" title="{{ $art->original_name }}">{{ $art->original_name }}</p>
                                                <a href="{{ asset('upload_file/'.$art->file_name) }}" target="_blank" class="btn btn-soft-info btn-sm">เปิดไฟล์</a>
                                                <form class="d-inline" action="{{ route('office.settings.marketing-ads.artworks.destroy', [$ad, $art]) }}" method="post" onsubmit="return confirm('ลบไฟล์นี้?');">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-soft-danger btn-sm" type="submit">ลบ</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted">ยังไม่มี artwork</p>
                                @endforelse
                            </div>
                        </div>
                        <div class="tab-pane" id="logs" role="tabpanel">
                            <div class="table-responsive table-card">
                                <table class="table table-nowrap align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>เวลา</th>
                                            <th>การกระทำ</th>
                                            <th>รายละเอียด</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($ad->logs->sortByDesc('id') as $log)
                                            <tr>
                                                <td>{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                                                <td><span class="badge bg-primary-subtle text-primary">{{ $log->action }}</span></td>
                                                <td class="text-muted fs-12"><code>{{ \Illuminate\Support\Str::limit(json_encode($log->changes, JSON_UNESCAPED_UNICODE), 180) }}</code></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-center py-4 text-muted">ยังไม่มีประวัติ</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="unitModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('office.settings.marketing-ads.units.store', $ad) }}" method="post">
                @csrf
                <div class="modal-header bg-light p-3"><h5 class="modal-title">เพิ่มหน่วยโฆษณา</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">@include('office.settings.marketing-ads._unit-form', ['unit' => null, 'platforms' => $platforms, 'objectives' => $objectives])</div>
                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button><button class="btn btn-success">บันทึก</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
