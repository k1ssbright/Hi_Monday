@extends('office.layout.office')

@section('title', 'Lead Scoring / SLA / เป้าหมาย | Hi Monday')

@section('content')
<div class="container-fluid">
    @include('office.settings.partials.page-title', ['section' => 'การตั้งค่า', 'title' => 'กฎคะแนน Lead / SLA / เป้าหมาย'])
    @include('office.settings.partials.flash')

    <div class="row">
        {{-- ========== LEFT COLUMN ========== --}}
        <div class="col-lg-6">

            {{-- Tier (Lead Scoring) --}}
            <form method="post" action="{{ route('office.settings.lead-pipeline.tier') }}">
                @csrf @method('PUT')
                <div class="card">
                    <div class="card-header border-0">
                        <div class="row g-4 align-items-center">
                            <div class="col-sm">
                                <h5 class="card-title mb-0">
                                    <i class="ri-bar-chart-box-line text-primary align-middle me-1"></i>
                                    สูตรคำนวณ Tier (Lead Scoring)
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted fs-13 mb-3">กำหนดปัจจัยและน้ำหนักที่ใช้คำนวณ Tier ของ Lead แต่ละราย</p>

                        <div class="table-responsive table-card mb-3">
                            <table class="table table-hover align-middle table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 60px;">เปิดใช้</th>
                                        <th>ปัจจัย</th>
                                        <th style="width: 100px;" class="text-center">น้ำหนัก</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (\App\Models\Lead\Lead::QUALITY_FACTORS + ['timeline' => 'ระยะเวลา (ยังไม่ใช้บน Lead)'] as $key => $label)
                                        @php $f = $tier->factors[$key] ?? ['enabled' => $key !== 'timeline', 'weight' => 1]; @endphp
                                        <tr>
                                            <td class="text-center">
                                                <div class="form-check form-switch form-switch-md mb-0 d-flex justify-content-center">
                                                    <input class="form-check-input"
                                                           type="checkbox"
                                                           name="factors[{{ $key }}][enabled]"
                                                           value="1"
                                                           @checked(!empty($f['enabled']))>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-medium text-dark">{{ $label }}</span>
                                            </td>
                                            <td>
                                                <input type="number"
                                                       step="0.1"
                                                       name="factors[{{ $key }}][weight]"
                                                       class="form-control form-control-sm text-center"
                                                       value="{{ $f['weight'] ?? 1 }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="border-top pt-3">
                            <h6 class="fs-13 text-muted text-uppercase fw-semibold mb-2">เกณฑ์การแบ่ง Tier</h6>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="form-label" for="min_factors_required">ตอบปัจจัยขั้นต่ำ</label>
                                    <input type="number" name="min_factors_required" id="min_factors_required" class="form-control" value="{{ $tier->min_factors_required ?? 1 }}">
                                    <div class="form-text text-muted">จำนวนปัจจัยที่ต้องตอบอย่างน้อย</div>
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="t1_min">T1 ขั้นต่ำ (คะแนน)</label>
                                    <input type="number" step="0.01" name="t1_min" id="t1_min" class="form-control" value="{{ $tier->t1_min ?? 3.5 }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="t2_min">T2 ขั้นต่ำ (คะแนน)</label>
                                    <input type="number" step="0.01" name="t2_min" id="t2_min" class="form-control" value="{{ $tier->t2_min ?? 2.5 }}">
                                </div>
                                <div class="col-6">&nbsp;</div>
                                <div class="col-4">
                                    <label class="form-label" for="t1_pct">T1 %</label>
                                    <div class="input-group">
                                        <input type="number" name="t1_pct" id="t1_pct" class="form-control" value="{{ $tier->t1_pct ?? 75 }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="form-label" for="t2_pct">T2 %</label>
                                    <div class="input-group">
                                        <input type="number" name="t2_pct" id="t2_pct" class="form-control" value="{{ $tier->t2_pct ?? 50 }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <label class="form-label" for="t3_pct">T3 %</label>
                                    <div class="input-group">
                                        <input type="number" name="t3_pct" id="t3_pct" class="form-control" value="{{ $tier->t3_pct ?? 25 }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="ri-save-line align-bottom me-1"></i> บันทึกสูตรการให้คะแนน
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>

        {{-- ========== RIGHT COLUMN ========== --}}
        <div class="col-lg-6">

            {{-- SLA --}}
            <form method="post" action="{{ route('office.settings.lead-pipeline.sla') }}">
                @csrf @method('PUT')
                <div class="card">
                    <div class="card-header border-0">
                        <div class="row g-4 align-items-center">
                            <div class="col-sm">
                                <h5 class="card-title mb-0">
                                    <i class="ri-timer-line text-warning align-middle me-1"></i>
                                    SLA (ข้อตกลงระดับการบริการ)
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted fs-13 mb-3">กำหนดเกณฑ์เวลาที่พนักงานต้องดำเนินการกับ Lead ในแต่ละขั้นตอน</p>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="first_touch_minutes">
                                    นาทีถึงการติดต่อครั้งแรก <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="first_touch_minutes" id="first_touch_minutes" class="form-control" value="{{ $sla->first_touch_minutes }}" min="1">
                                    <span class="input-group-text">นาที</span>
                                </div>
                                <div class="form-text text-muted">ระยะเวลาที่ต้องติดต่อกลับลูกค้าหลังได้รับ Lead</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="qualify_within_days">
                                    จำนวนวันสำหรับ Qualify / Unqualify <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="qualify_within_days" id="qualify_within_days" class="form-control" value="{{ $sla->qualify_within_days }}" min="1">
                                    <span class="input-group-text">วัน</span>
                                </div>
                                <div class="form-text text-muted">ระยะเวลาที่ต้องตัดสินใจว่า Lead คุณภาพดีหรือไม่</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="nurture_review_days">
                                    รอบทบทวน Nurture <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" name="nurture_review_days" id="nurture_review_days" class="form-control" value="{{ $sla->nurture_review_days }}" min="1">
                                    <span class="input-group-text">วัน</span>
                                </div>
                                <div class="form-text text-muted">ระยะเวลาทบทวน Lead ที่อยู่ในสถานะ Nurturing</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label d-block">การนับเวลา</label>
                                <div class="form-check form-switch form-switch-md mt-1">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           role="switch"
                                           name="business_hours_only"
                                           value="1"
                                           id="business_hours_only"
                                           @checked($sla->business_hours_only)>
                                    <label class="form-check-label ms-1" for="business_hours_only">
                                        นับเฉพาะเวลาทำการ (จันทร์ – ศุกร์)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="ri-save-line align-bottom me-1"></i> บันทึก SLA
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Sales Funnel Target --}}
            <form method="post" action="{{ route('office.settings.lead-pipeline.goal') }}">
                @csrf @method('PUT')
                <div class="card">
                    <div class="card-header border-0">
                        <div class="row g-4 align-items-center">
                            <div class="col-sm">
                                <h5 class="card-title mb-0">
                                    <i class="ri-funds-line text-success align-middle me-1"></i>
                                    เป้ากรวยการขาย (Sales Funnel Target)
                                    <span class="badge bg-primary-subtle text-primary fs-12 ms-1">{{ $goal->fiscal_year ?? now()->year }}</span>
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted fs-13 mb-3">กำหนดเป้าหมายรายได้และอัตราการแปลง Lead เพื่อคำนวณจำนวน Lead ที่ต้องการ</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="fiscal_year">ปีงบประมาณ <span class="text-danger">*</span></label>
                                <input type="number" name="fiscal_year" id="fiscal_year" class="form-control" value="{{ $goal->fiscal_year ?? now()->year }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="revenue_target">รายได้เป้า (บาท) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="revenue_target" id="revenue_target" class="form-control" value="{{ $goal->revenue_target ?? 0 }}">
                                    <span class="input-group-text">฿</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="avg_deal_value">มูลค่าเฉลี่ยต่อ Opportunity <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="avg_deal_value" id="avg_deal_value" class="form-control" value="{{ $goal->avg_deal_value ?? 0 }}">
                                    <span class="input-group-text">฿</span>
                                </div>
                            </div>
                            <div class="col-md-6">&nbsp;</div>
                            <div class="col-md-6">
                                <label class="form-label" for="lead_to_opportunity_rate">% Lead → Opportunity <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="lead_to_opportunity_rate" id="lead_to_opportunity_rate" class="form-control" value="{{ $goal->lead_to_opportunity_rate ?? 0 }}">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="opportunity_win_rate">% Win Rate <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="opportunity_win_rate" id="opportunity_win_rate" class="form-control" value="{{ $goal->opportunity_win_rate ?? 0 }}">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>

                        @if ($goal->exists && $goal->requiredLeads())
                            <div class="alert alert-info d-flex align-items-center gap-2 mt-3 mb-0">
                                <i class="ri-lightbulb-line fs-18"></i>
                                <div>
                                    จากเป้าหมายที่ตั้งไว้ ต้องมี Lead ประมาณ
                                    <strong>{{ number_format($goal->requiredLeads()) }}</strong> รายการต่อปี
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="card-footer">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="submit" class="btn btn-success">
                                <i class="ri-save-line align-bottom me-1"></i> บันทึกเป้าหมาย
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
