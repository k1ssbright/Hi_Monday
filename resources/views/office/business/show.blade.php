@extends('office.layout.office')

@section('title', 'รายละเอียดบริษัท และข้อมูลธุรกิจ | Hi Monday')

@section('style')
<style>
    .biz-card-main {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: none;
        padding: 24px;
    }

    .biz-logo-avatar-lg {
        width: 64px;
        height: 64px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.4rem;
    }

    .table-subbiz-custom {
        width: 100%;
        border-collapse: collapse;
    }

    .table-subbiz-custom th {
        color: #475569;
        font-size: 0.825rem;
        font-weight: 700;
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        background-color: #f8fafc;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-subbiz-custom td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .table-subbiz-custom tbody tr:nth-child(even) {
        background-color: #edf2f7;
    }

    .table-subbiz-custom tbody tr:hover {
        background-color: #cbd5e1;
    }

    .btn-back-custom {
        background-color: #f1f5f9;
        color: #475569;
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-back-custom:hover {
        background-color: #e2e8f0;
        color: #1e293b;
    }
</style>
@endsection

@section('content')
@php
    $pathSegments = explode('/', trim(request()->path(), '/'));
    $currentId = end($pathSegments);
    if (!in_array($currentId, ['AAA', 'BBB'])) {
        $currentId = 'AAA';
    }
    
    $companyDatabase = [
        'AAA' => [
            'code' => 'AAA',
            'name' => 'บริษัท เอบี กรุ๊ป จำกัด (AAA)',
            'type' => 'สำนักงานใหญ่',
            'badgeColor' => 'bg-primary',
            'subCount' => '3 รายการธุรกิจ',
            'tax' => '0105562012345',
            'phone' => '021234567',
            'items' => [
                ['code' => 'AAA-01', 'name' => 'บริษัท เอบี กรุ๊ป จำกัด (สำนักงานใหญ่)', 'tax' => '0105562012345', 'phone' => '021234567', 'type' => 'บริษัท จำกัด', 'status' => true],
                ['code' => 'AAA-02', 'name' => 'บริษัท เอบี กรุ๊ป จำกัด (สาขา 1 - เทคโนโลยี & โซลูชั่น)', 'tax' => '0105562012346', 'phone' => '021234568', 'type' => 'บริษัท จำกัด', 'status' => true],
                ['code' => 'AAA-03', 'name' => 'บริษัท เอบี กรุ๊ป จำกัด (สาขา 2 - ดิจิทัล ซัพพลาย)', 'tax' => '0105562012347', 'phone' => '021234569', 'type' => 'บริษัท จำกัด', 'status' => true],
            ]
        ],
        'BBB' => [
            'code' => 'BBB',
            'name' => 'บริษัท บีบี อินโนเวชั่น จำกัด (BBB)',
            'type' => 'องค์กรหลัก',
            'badgeColor' => 'bg-success',
            'subCount' => '2 รายการธุรกิจ',
            'tax' => '0105562012350',
            'phone' => '021234572',
            'items' => [
                ['code' => 'BBB-01', 'name' => 'บริษัท บีบี อินโนเวชั่น จำกัด (สำนักงานใหญ่)', 'tax' => '0105562012350', 'phone' => '021234572', 'type' => 'บริษัท จำกัด', 'status' => true],
                ['code' => 'BBB-02', 'name' => 'บริษัท บีบี อินโนเวชั่น จำกัด (สาขา 2)', 'tax' => '0105562012351', 'phone' => '021234573', 'type' => 'ห้างหุ้นส่วน', 'status' => false],
            ]
        ]
    ];

    $comp = $companyDatabase[$currentId] ?? $companyDatabase['AAA'];
@endphp

<div class="container-fluid">
    <!-- Top Breadcrumb -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('office.mybusiness') }}" class="btn-back-custom">
            <i class="ri-arrow-left-line"></i> ย้อนกลับไปรายการธุรกิจ
        </a>
        <div class="fs-13 text-muted">
            <span>ธุรกิจของฉัน</span> <i class="ri-arrow-right-s-line"></i> <span class="fw-semibold text-dark">ข้อมูลธุรกิจของ {{ $comp['code'] }}</span>
        </div>
    </div>

    <!-- Company Profile Header Card -->
    <div class="biz-card-main mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="biz-logo-avatar-lg {{ $comp['badgeColor'] }} text-white shadow-sm">{{ $comp['code'] }}</div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="fw-bold text-dark mb-0">{{ $comp['name'] }}</h4>
                        <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill"><i class="ri-checkbox-circle-fill me-1"></i> เปิดใช้งาน</span>
                    </div>
                    <p class="text-muted mb-0 fs-13 mt-1">{{ $comp['type'] }} • {{ $comp['subCount'] }}</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary rounded-3 px-3 py-2 fw-semibold fs-13">
                    <i class="ri-edit-2-line"></i> แก้ไขข้อมูลบริษัท
                </button>
            </div>
        </div>
    </div>

    <!-- Sub Businesses Table Card -->
    <div class="biz-card-main mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0 fs-16">
                <i class="ri-building-4-line text-primary me-2"></i> ข้อมูลธุรกิจ / สาขาย่อยทั้งหมดของบริษัทนี้
            </h5>
            <span class="text-muted fs-13">แสดง {{ count($comp['items']) }} รายการ</span>
        </div>

        <div class="table-responsive border rounded-3">
            <table class="table-subbiz-custom">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th style="width: 140px;">รหัสธุรกิจ</th>
                        <th>ชื่อธุรกิจ / สาขา</th>
                        <th>เลขประจำตัวผู้เสียภาษี</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>ประเภทธุรกิจ</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center" style="width: 120px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($comp['items'] as $index => $item)
                    <tr>
                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                        <td class="fw-bold text-primary">{{ $item['code'] }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="ri-git-commit-line text-primary fs-16"></i>
                                <span class="fw-semibold text-dark">{{ $item['name'] }}</span>
                            </div>
                        </td>
                        <td>{{ $item['tax'] }}</td>
                        <td>{{ $item['phone'] }}</td>
                        <td>{{ $item['type'] }}</td>
                        <td class="text-center">
                            @if($item['status'])
                                <span class="badge bg-success-subtle text-success px-2 py-1"><i class="ri-checkbox-circle-fill me-1"></i> เปิดใช้งาน</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="ri-close-circle-fill me-1"></i> ปิดใช้งาน</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-soft-info"><i class="ri-pencil-fill"></i> แก้ไข</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
