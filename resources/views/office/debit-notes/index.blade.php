@extends('office.layout.office')

@section('title', 'จัดการใบเพิ่มหนี้ (Debit Notes) | Hi Monday')

@section('style')
<style>
    /* Top Hero Banner */
    .hero-banner-card {
        background: #1e3a8a;
        border-radius: 20px;
        padding: 28px 32px;
        color: #ffffff;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(37, 99, 235, 0.25);
        margin-bottom: 24px;
    }
    .hero-banner-card::before {
        content: "";
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.18) 0%, rgba(255, 255, 255, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .hero-banner-card::after {
        content: "";
        position: absolute;
        bottom: -40px;
        left: 25%;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(129, 140, 248, 0.2) 0%, rgba(129, 140, 248, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.3px;
        margin-bottom: 12px;
    }
    .btn-create-glow {
        background: #ffffff;
        color: #1d4ed8;
        font-weight: 700;
        font-size: 14px;
        border: none;
        border-radius: 12px;
        padding: 12px 24px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
    }
    .btn-create-glow:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.28);
        background: #f8fafc;
        color: #1e40af;
    }

    /* Stat Cards */
    .stat-card-wow {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 18px;
        padding: 22px 24px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.04);
    }
    .stat-card-wow:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.09);
        border-color: #cbd5e1;
    }
    .stat-card-wow::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        opacity: 0.9;
    }
    .stat-card-wow.primary::before { background: linear-gradient(90deg, #2563eb, #6366f1); }
    .stat-card-wow.success::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-card-wow.warning::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .stat-card-wow.info::before { background: linear-gradient(90deg, #6366f1, #a855f7); }

    .stat-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
        transition: transform 0.3s ease;
    }
    .stat-card-wow:hover .stat-icon-wrap {
        transform: scale(1.1) rotate(4deg);
    }
    .stat-icon-wrap.primary { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); color: #2563eb; }
    .stat-icon-wrap.success { background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); color: #059669; }
    .stat-icon-wrap.warning { background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); color: #d97706; }
    .stat-icon-wrap.info { background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); color: #4f46e5; }

    /* Filter Toolbar Card */
    .filter-card-wow {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 6px 24px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }
    .toolbar-container {
        padding: 16px 20px;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
    }
    .search-input-wow {
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        height: 44px;
        font-size: 13.5px;
        padding-left: 12px;
        transition: all 0.2s ease;
    }
    .search-input-wow:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        background: #ffffff;
    }
    .select-wow {
        border-radius: 12px;
        border: 1.5px solid #e2e8f0;
        height: 44px;
        font-size: 13.5px;
        padding-left: 14px;
        font-weight: 500;
        color: #334155;
        transition: all 0.2s ease;
    }
    .select-wow:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }
    .btn-filter-wow {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        border: none;
        border-radius: 12px;
        height: 44px;
        padding: 0 20px;
        font-size: 13.5px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.22);
        transition: all 0.2s ease;
        white-space: nowrap;
    }
    .btn-filter-wow:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.32);
        color: #ffffff;
    }

    /* Table Styling */
    .table-wow {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .table-wow thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 18px;
        border-bottom: 1.5px solid #e2e8f0;
        white-space: nowrap;
    }
    .table-wow tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-wow tbody tr:hover {
        background-color: #f8fafc;
    }
    .table-wow td {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    /* Customer Info Block */
    .cust-info-box {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .cust-avatar-box {
        width: 40px;
        height: 40px;
        min-width: 40px;
        border-radius: 10px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        border: 1px solid #dbeafe;
        flex-shrink: 0;
    }
    .cust-text-wrap {
        min-width: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .cust-name-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 13.5px;
        line-height: 1.35;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cust-sub-desc {
        color: #64748b;
        font-size: 11.5px;
        line-height: 1.3;
        margin-top: 2px;
    }

    /* Document Tag */
    .doc-pill-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        color: #1d4ed8;
        font-weight: 700;
        font-size: 13px;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .doc-pill-tag:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.22);
    }
    .doc-pill-tag:hover i {
        color: #ffffff !important;
    }

    /* Status Pill Badges */
    .status-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }
    .status-badge-pill.issued {
        background-color: #ecfdf5;
        color: #059669;
        border: 1px solid #a7f3d0;
    }
    .status-badge-pill.draft {
        background-color: #fffbeb;
        color: #d97706;
        border: 1px solid #fde68a;
    }
    .status-badge-pill.voided {
        background-color: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .status-dot.issued { background-color: #10b981; }
    .status-dot.draft { background-color: #f59e0b; }
    .status-dot.voided { background-color: #ef4444; }

    /* Action Buttons */
    .action-btn-circle {
        width: 36px;
        height: 36px;
        padding: 0;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        font-size: 16px;
        line-height: 1;
        cursor: pointer;
        appearance: none;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .action-btn-circle:hover {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.15);
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-2">
    <!-- Top Hero Banner Card -->
    <div class="hero-banner-card">
        <div class="row align-items-center position-relative" style="z-index: 2;">
            <div class="col-lg-8 col-md-7">
                <div class="hero-badge">
                    <i class="ri-file-add-line text-warning"></i>
                    DEBIT NOTES SYSTEM
                </div>
                <h2 class="fw-bolder mb-2 text-white fs-26 d-flex align-items-center gap-2">
                    จัดการใบเพิ่มหนี้ (Debit Notes / DBN)
                </h2>
                <p class="mb-0 text-white-50 fs-14" style="max-width: 600px;">
                    ออกใบเพิ่มหนี้ ติดตามสถานะเอกสาร บริหารจัดการการปรับเพิ่มวงเงินและมูลค่าภาษีอย่างเป็นระบบใน Hi Monday
                </p>
            </div>
            <div class="col-lg-4 col-md-5 text-md-end mt-4 mt-md-0">
                <a href="{{ route('office.debit-notes.create') }}" class="btn-create-glow">
                    <i class="ri-add-circle-fill fs-20 text-primary"></i>
                    <span>สร้างใบเพิ่มหนี้ใหม่</span>
                </a>
            </div>
        </div>
    </div>

    @include('office.settings.partials.flash')

    <!-- Overview Stats Grid -->
    @php
        $totalCount = $debitNotes->total();
        $draftCount = $debitNotes->filter(fn($d) => ($d->currentRevision?->status ?? '') === 'draft')->count();
        $issuedCount = $debitNotes->filter(fn($d) => ($d->currentRevision?->status ?? '') === 'issued')->count();
        $sumAmount = $debitNotes->sum(fn($d) => (float)($d->currentRevision?->total_amount ?? 0));
    @endphp
    <div class="row g-3.5 mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase tracking-wider">รายการทั้งหมด</span>
                        <h3 class="fw-bolder text-dark mb-1 fs-26 mt-1">{{ number_format($totalCount) }} <span class="fs-13 fw-normal text-muted">ฉบับ</span></h3>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Total Records</span>
                    </div>
                    <div class="stat-icon-wrap primary">
                        <i class="ri-file-copy-2-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase tracking-wider">ออกเอกสารแล้ว</span>
                        <h3 class="fw-bolder text-success mb-1 fs-26 mt-1">{{ number_format($issuedCount) }} <span class="fs-13 fw-normal text-muted">ฉบับ</span></h3>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Status Issued</span>
                    </div>
                    <div class="stat-icon-wrap success">
                        <i class="ri-checkbox-circle-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase tracking-wider">ร่างเอกสาร</span>
                        <h3 class="fw-bolder text-warning mb-1 fs-26 mt-1">{{ number_format($draftCount) }} <span class="fs-13 fw-normal text-muted">ฉบับ</span></h3>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Draft Status</span>
                    </div>
                    <div class="stat-icon-wrap warning">
                        <i class="ri-edit-box-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="stat-card-wow info">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-secondary fs-12 fw-bold text-uppercase tracking-wider">มูลค่ารวมใบเพิ่มหนี้</span>
                        <h3 class="fw-bolder text-primary mb-1 fs-22 mt-1">฿{{ number_format($sumAmount, 2) }}</h3>
                        <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill fs-11 fw-semibold px-2.5 py-0.5">Grand Total</span>
                    </div>
                    <div class="stat-icon-wrap info">
                        <i class="ri-money-dollar-circle-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card Container -->
    <div class="filter-card-wow mb-4">
        <!-- Search & Filter Controls (Clean Unified Toolbar) -->
        <div class="toolbar-container">
            <form method="get" action="{{ route('office.debit-notes.index') }}">
                <div class="row g-2.5 align-items-center">
                    <div class="col-lg-5 col-md-6 col-12">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted rounded-start-3" style="border: 1.5px solid #e2e8f0; border-right: none;">
                                <i class="ri-search-2-line fs-16 text-primary"></i>
                            </span>
                            <input type="text" name="q" class="form-control search-input-wow border-start-0 rounded-end-3 ps-1" value="{{ request('q') }}" placeholder="ค้นหาเลขที่ DBN, ลูกค้า, หรือ Tax Invoice...">
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-4 col-12">
                        <select name="status" class="form-select select-wow" onchange="this.form.submit()">
                            <option value="">-- ทุกสถานะเอกสาร --</option>
                            <option value="draft" @selected(request('status') === 'draft')>ร่างเอกสาร (Draft)</option>
                            <option value="issued" @selected(request('status') === 'issued')>ออกเอกสารแล้ว (Issued)</option>
                            <option value="voided" @selected(request('status') === 'voided')>ยกเลิก (Voided)</option>
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-2 col-12 d-flex gap-2 align-items-center justify-content-lg-start">
                        <button type="submit" class="btn-filter-wow">
                            <i class="ri-filter-3-line fs-15"></i>
                            <span>กรองข้อมูล</span>
                        </button>
                        @if(request('q') || request('status'))
                            <a href="{{ route('office.debit-notes.index') }}" class="btn btn-light border rounded-3 px-3 d-inline-flex align-items-center gap-1 fs-13 text-secondary font-medium" style="height: 44px;">
                                <i class="ri-refresh-line"></i> ล้าง
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Responsive -->
        <div class="table-responsive">
            <table class="table table-wow align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 180px;" class="ps-4">เลขที่เอกสาร</th>
                        <th style="min-width: 240px;">ลูกค้า / องค์กร</th>
                        <th style="min-width: 160px;">Tax Invoice อ้างอิง</th>
                        <th style="min-width: 150px;" class="text-center">สถานะ</th>
                        <th style="min-width: 150px;" class="text-end">ยอดเงินรวม</th>
                        <th style="min-width: 130px;">วันที่ออก</th>
                        <th style="min-width: 120px;" class="text-center pe-4">จัดการ</th>
                    </tr>
                </thead>
                <tbody class="fs-13">
                    @forelse ($debitNotes as $debitNote)
                        @php
                            $revision = $debitNote->currentRevision;
                            $status = strtolower($revision?->status ?: 'draft');
                            $customerName = $debitNote->partner?->partner_name_th ?: ($revision?->customer_name ?: 'ไม่ระบุชื่อลูกค้า');
                        @endphp
                        <tr>
                            <td class="ps-4 text-nowrap">
                                <a href="{{ route('office.debit-notes.show', $debitNote) }}" class="doc-pill-tag">
                                    <i class="ri-file-add-line text-primary fs-15"></i>
                                    <span>{{ $debitNote->displayNo() }}</span>
                                </a>
                            </td>
                            <td>
                                <div class="cust-info-box">
                                    <div class="cust-avatar-box">
                                        <i class="ri-building-line"></i>
                                    </div>
                                    <div class="cust-text-wrap">
                                        <div class="cust-name-title">
                                            {{ $customerName }}
                                        </div>
                                        <div class="cust-sub-desc">
                                            {{ $debitNote->partner?->customer_tax_no ? 'Tax ID: '.$debitNote->partner->customer_tax_no : 'องค์กรลูกค้า' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-nowrap">
                                @if($debitNote->taxInvoice)
                                    <a href="{{ route('office.tax-invoices.show', $debitNote->taxInvoice) }}" class="badge bg-light text-secondary border font-monospace text-decoration-none fs-12 px-2.5 py-1.5 rounded-2">
                                        <i class="ri-file-paper-2-line me-1 text-primary"></i>{{ $debitNote->taxInvoice->displayNo() }}
                                    </a>
                                @else
                                    <span class="text-muted fs-13">-</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                @if($status === 'issued')
                                    <span class="status-badge-pill issued">
                                        <span class="status-dot issued"></span> ออกแล้ว (Issued)
                                    </span>
                                @elseif($status === 'voided')
                                    <span class="status-badge-pill voided">
                                        <span class="status-dot voided"></span> ยกเลิก (Voided)
                                    </span>
                                @else
                                    <span class="status-badge-pill draft">
                                        <span class="status-dot draft"></span> ร่าง (Draft)
                                    </span>
                                @endif
                            </td>
                            <td class="text-end fw-bolder text-dark font-monospace text-nowrap fs-14">
                                {{ $revision ? number_format((float) $revision->total_amount, 2) : '0.00' }} <span class="fs-11 fw-normal text-muted">บาท</span>
                            </td>
                            <td class="text-nowrap">
                                <span class="text-secondary fs-13 d-inline-flex align-items-center gap-1">
                                    <i class="ri-calendar-line text-primary"></i>
                                    <span>{{ $revision?->issue_date?->format('d/m/Y') ?: '-' }}</span>
                                </span>
                            </td>
                            <td class="text-center pe-4 text-nowrap">
                                <div class="d-flex justify-content-center align-items-center gap-1.5">
                                    <a href="{{ route('office.debit-notes.show', $debitNote) }}" class="action-btn-circle" title="ดูรายละเอียด">
                                        <i class="ri-eye-line text-primary"></i>
                                    </a>
                                    @include('office.partials.document-crud-index-actions', [
                                        'revision' => $revision,
                                        'editRoute' => route('office.debit-notes.edit', $debitNote),
                                        'destroyRoute' => route('office.debit-notes.destroy', $debitNote),
                                        'rejectStatuses' => [],
                                    ])
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 bg-white">
                                <div class="py-4">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                        <i class="ri-inbox-archive-line fs-30"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark fs-16 mb-1">ยังไม่มีรายการใบเพิ่มหนี้</h5>
                                    <p class="fs-13 text-muted mb-3" style="max-width: 400px; margin: 0 auto;">คุณยังไม่ได้สร้างใบเพิ่มหนี้ในระบบ หรือไม่พบข้อมูลตามเงื่อนไขที่ค้นหา</p>
                                    <a href="{{ route('office.debit-notes.create') }}" class="btn-create-glow" style="display: inline-flex; border: 1px solid #2563eb; color: #2563eb;">
                                        <i class="ri-add-line fs-18"></i> เริ่มสร้างใบเพิ่มหนี้ฉบับแรก
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Table Footer Pagination -->
        @if($debitNotes->hasPages())
            <div class="card-footer bg-white border-top p-3.5 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted fs-13 font-medium">
                    แสดง {{ $debitNotes->firstItem() }} ถึง {{ $debitNotes->lastItem() }} จากทั้งหมด <strong class="text-dark">{{ $debitNotes->total() }}</strong> รายการ
                </div>
                <div>
                    {{ $debitNotes->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection