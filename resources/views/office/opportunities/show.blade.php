@extends('office.layout.office')
@section('title', ($opportunity->opportunity_no ?: 'Opportunity Record') . ' | Hi Monday')

@section('style')
    <style>
        /* Card Main */
        .emp-card-main {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
            border: 1px solid #e2e8f0;
            padding: 24px;
            margin-bottom: 24px;
        }

        .section-header-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .section-header-title i {
            color: #2F53B4;
            font-size: 1.25rem;
        }

        /* Buttons */
        .btn-add-emp {
            background: linear-gradient(135deg, #2F53B4 0%, #1E3A8A 100%);
            color: #ffffff !important;
            border: none;
            border-radius: 10px;
            padding: 10px 24px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 14px rgba(47, 83, 180, 0.28);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            cursor: pointer;
            height: 42px;
            white-space: nowrap;
        }

        .btn-add-emp:hover {
            background: linear-gradient(135deg, #254394 0%, #172E6E 100%);
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(47, 83, 180, 0.38);
        }

        .btn-sec-emp {
            background-color: #ffffff;
            color: #2F53B4 !important;
            border: 1.5px solid #2F53B4;
            border-radius: 10px;
            padding: 10px 22px;
            font-size: 0.875rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 6px rgba(47, 83, 180, 0.08);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            height: 42px;
            white-space: nowrap;
        }

        .btn-sec-emp:hover {
            background-color: #EFF6FF;
            color: #1E3A8A !important;
            border-color: #1E3A8A;
            transform: translateY(-2px);
        }

        /* Detail Label & Values */
        .info-label {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .info-value {
            font-size: 0.9rem;
            color: #0f172a;
            font-weight: 600;
            word-break: break-word;
        }

        .emp-code-badge {
            font-family: 'SFMono-Regular', Consolas, Monaco, monospace;
            font-size: 0.825rem;
            font-weight: 600;
            color: #2F53B4;
            background: #EFF6FF;
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid #DBEAFE;
            display: inline-flex;
            align-items: center;
            letter-spacing: 0.02em;
            transition: all 0.2s ease;
        }

        .emp-code-badge:hover {
            background: #DBEAFE;
            color: #1E3A8A;
        }

        /* Status Badges */
        .badge-status-active {
            background-color: #ECFDF5 !important;
            color: #059669 !important;
            border: 1px solid #A7F3D0 !important;
            font-size: 0.78rem !important;
            font-weight: 600 !important;
            padding: 5px 12px !important;
            border-radius: 20px !important;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-status-inactive {
            background-color: #FEF2F2 !important;
            color: #DC2626 !important;
            border: 1px solid #FCA5A5 !important;
            font-size: 0.78rem !important;
            font-weight: 600 !important;
            padding: 5px 12px !important;
            border-radius: 20px !important;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-status-warning {
            background-color: #FFFBEB !important;
            color: #D97706 !important;
            border: 1px solid #FDE68A !important;
            font-size: 0.78rem !important;
            font-weight: 600 !important;
            padding: 5px 12px !important;
            border-radius: 20px !important;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-status-blue {
            background-color: #EFF6FF !important;
            color: #2563EB !important;
            border: 1px solid #BFDBFE !important;
            font-size: 0.78rem !important;
            font-weight: 600 !important;
            padding: 5px 12px !important;
            border-radius: 20px !important;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Table Responsive Container */
        .table-responsive-emp {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        }

        /* Table Styles */
        .table-emp-custom {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .table-emp-custom th {
            color: #475569;
            font-size: 0.8125rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 14px 18px;
            border-bottom: 2px solid #e2e8f0;
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            vertical-align: middle;
            white-space: nowrap;
        }

        .table-emp-custom td {
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 0.875rem;
            color: #334155;
            transition: background-color 0.15s ease;
        }

        .table-emp-custom tbody tr {
            transition: all 0.15s ease;
        }

        .table-emp-custom tbody tr:hover {
            background-color: #f8fafc;
        }

        .table-emp-custom tbody tr:last-child td {
            border-bottom: none;
        }

        .nav-tabs-custom { border-bottom: 1px solid #e2e8f0; }
        .nav-tabs-custom .nav-link {
            border: 0;
            color: #64748b;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.75rem 1rem;
            border-bottom: 2px solid transparent;
        }
        .nav-tabs-custom .nav-link.active {
            color: #2563eb;
            background: transparent;
            border-bottom-color: #2563eb;
        }
        .nav-tabs-custom .nav-link:hover { color: #1d4ed8; }
    </style>
    @include('office.partials.sales-journey-styles')
@endsection

@section('content')
    @php
        $stageBadgeClass = match ($opportunity->stage) {
            'closed_won' => 'badge-status-active',
            'closed_lost' => 'badge-status-inactive',
            'proposal', 'negotiation' => 'badge-status-warning',
            default => 'badge-status-blue',
        };
    @endphp

    <div class="container-fluid">
        @include('office.settings.partials.page-title', [
            'section' => 'งานขาย',
            'parent' => 'Opportunities',
            'parentUrl' => route('office.opportunities.index'),
            'title' => $opportunity->opportunity_no ?: 'Opportunity',
        ])
        @include('office.settings.partials.flash')

        <!-- Page Header & Action Bar -->
        <div class="emp-card-main mb-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="fw-bold text-dark mb-0 fs-20">{{ $opportunity->opportunity_no ?: 'Opportunity' }}</h4>
                        <span class="{{ $stageBadgeClass }}">
                            {{ $opportunity->stageLabel() }}
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="{{ route('office.opportunities.edit', $opportunity) }}" class="btn-sec-emp">
                        <i class="ri-edit-2-line fs-16"></i>
                        <span>แก้ไข Opportunity</span>
                    </a>
                    <a href="{{ route('office.opportunities.quotations.create', $opportunity) }}" class="btn-add-emp">
                        <i class="ri-file-add-line fs-16"></i>
                        <span>สร้าง Quotation</span>
                    </a>
                    <a href="{{ route('office.opportunities.index') }}" class="btn-sec-emp">
                        <i class="ri-arrow-left-line fs-16"></i>
                        <span>กลับหน้ารายการ Opportunity</span>
                    </a>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs nav-tabs-custom mb-3" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab-opp-detail" role="tab">
                    <i class="ri-file-text-line me-1"></i> รายละเอียด
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab-opp-journey" role="tab">
                    <i class="ri-git-branch-line me-1"></i> ไทม์ไลน์งานขาย
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="tab-opp-detail" role="tabpanel">
        <div class="row g-4">
            <!-- Left Column: Primary Details -->
            <div class="col-lg-4">
                <!-- Card 1: ข้อมูลหลัก -->
                <div class="emp-card-main">
                    <div class="section-header-title">
                        <i class="ri-briefcase-line"></i>
                        <span>ข้อมูลหลัก</span>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div>
                            <div class="info-label"><i class="ri-file-text-line"></i> ชื่อดีล</div>
                            <div class="info-value">{{ $opportunity->name }}</div>
                        </div>

                        <div>
                            <div class="info-label"><i class="ri-building-4-line"></i> ลูกค้า (Partner)</div>
                            <div class="info-value">
                                @if ($opportunity->partner?->partner_name_th)
                                    <span class="text-dark">{{ $opportunity->partner->partner_name_th }}</span>
                                @else
                                    <span class="text-muted fw-normal">-</span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="info-label"><i class="ri-user-line"></i> ผู้ติดต่อ</div>
                            <div class="info-value">
                                @php
                                    $contactName = $opportunity->partnerContact?->displayName();
                                    if (!$contactName && $opportunity->partnerContact) {
                                        $contactName = trim($opportunity->partnerContact->firstname . ' ' . $opportunity->partnerContact->surname);
                                    }
                                @endphp
                                {{ $contactName ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="info-label"><i class="ri-coin-line"></i> มูลค่าประมาณการ</div>
                            <div class="info-value text-primary fs-15 fw-bold">
                                ฿{{ number_format((float) $opportunity->estimated_value, 2) }} {{ $opportunity->currency }}
                            </div>
                        </div>

                        <div>
                            <div class="info-label"><i class="ri-calendar-event-line"></i> วันที่คาดว่าจะปิด</div>
                            <div class="info-value">
                                {{ $opportunity->expected_close_date?->format('d/m/Y') ?: '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="info-label"><i class="ri-user-star-line"></i> ผู้รับผิดชอบดีล</div>
                            <div class="info-value">
                                {{ $opportunity->owner?->displayName() ?: '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: ที่มาจาก Lead -->
                <div class="emp-card-main">
                    <div class="section-header-title">
                        <i class="ri-user-search-line"></i>
                        <span>ที่มาจาก Lead</span>
                    </div>

                    @if ($opportunity->convertedFromLead)
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('office.leads.show', $opportunity->convertedFromLead) }}"
                                class="emp-code-badge text-decoration-none">
                                <i class="ri-link-m me-1"></i>
                                <span>{{ $opportunity->convertedFromLead->registration_no ?: 'LEAD-' . str_pad($opportunity->convertedFromLead->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </a>
                        </div>
                    @else
                        <p class="text-muted fs-13 mb-0">เปิดดีลตรง (ไม่มี Lead ต้นทาง)</p>
                    @endif
                </div>
            </div>

            <!-- Right Column: Line Items & Quotations -->
            <div class="col-lg-8">
                <!-- Card 1: รายการ Opportunity -->
                <div class="emp-card-main">
                    <div class="section-header-title">
                        <i class="ri-shopping-bag-3-line"></i>
                        <span>รายการ Opportunity</span>
                    </div>

                    <div class="table-responsive-emp">
                        <table class="table-emp-custom">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 40%;">รายการ</th>
                                    <th scope="col" class="text-center" style="width: 20%;">จำนวน</th>
                                    <th scope="col" class="text-end" style="width: 20%;">ราคา/หน่วย</th>
                                    <th scope="col" class="text-end" style="width: 20%;">รวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($opportunity->lines as $line)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark fs-14">{{ $line->item?->name ?: 'รายการกำหนดเอง' }}</div>
                                            @if ($line->description)
                                                <div class="text-muted fs-12 mt-1">{{ $line->description }}</div>
                                            @endif
                                        </td>
                                        <td class="text-center fw-semibold text-dark">
                                            {{ number_format((float) $line->quantity, 2) }}
                                        </td>
                                        <td class="text-end fw-semibold text-dark">
                                            ฿{{ number_format((float) $line->unit_price_estimate, 2) }}
                                        </td>
                                        <td class="text-end fw-bold text-dark fs-14">
                                            ฿{{ number_format((float) $line->line_total_estimate, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted fs-13">
                                            <i class="ri-inbox-line fs-20 d-block mb-1 text-muted"></i>
                                            ยังไม่มีรายการสินค้า/บริการใน Opportunity นี้
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Card 2: Quotations (ใบเสนอราคา) -->
                <div class="emp-card-main">
                    <div class="section-header-title">
                        <i class="ri-file-text-line"></i>
                        <span>Quotations (ใบเสนอราคา)</span>
                    </div>

                    <div class="table-responsive-emp">
                        <table class="table-emp-custom">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 40%;">เลขที่ใบเสนอราคา</th>
                                    <th scope="col" class="text-center" style="width: 20%;">สถานะ</th>
                                    <th scope="col" class="text-end" style="width: 20%;">ยอดรวม</th>
                                    <th scope="col" class="text-end" style="width: 20%;">วันที่ออก</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($opportunity->quotations as $qt)
                                    @php($revision = $qt->currentRevision)
                                    <tr>
                                        <td>
                                            <a href="{{ route('office.quotations.show', $qt) }}"
                                                class="emp-code-badge text-decoration-none">
                                                <i class="ri-file-list-3-line me-1"></i>{{ $qt->displayNo() }}
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-status-blue">
                                                {{ $revision?->status ?: '-' }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-dark fs-14">
                                            {{ $revision ? '฿' . number_format((float) $revision->total_amount, 2) : '-' }}
                                        </td>
                                        <td class="text-end">
                                            <span class="fs-13 text-muted">
                                                {{ $revision?->issue_date?->format('d/m/Y') ?: '-' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted fs-13">
                                            <i class="ri-file-search-line fs-20 d-block mb-1 text-muted"></i>
                                            ยังไม่มีใบเสนอราคาสำหรับ Opportunity นี้
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
            </div>{{-- /tab-opp-detail --}}

            <div class="tab-pane" id="tab-opp-journey" role="tabpanel">
                @include('office.partials.sales-journey-timeline', ['journey' => $salesJourney ?? []])
            </div>
        </div>{{-- /tab-content --}}
    </div>
@endsection
