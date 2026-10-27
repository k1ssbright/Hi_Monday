<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Lead\LeadGoal;
use App\Models\Lead\LeadSlaSetting;
use App\Models\Lead\LeadTierSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadPipelineSettingController extends Controller
{
    use ResolvesBusinessContext;

    public function edit(): View
    {
        $businessId = $this->businessId();
        $tier = LeadTierSetting::query()->firstOrNew(
            ['business_id' => $businessId],
            LeadTierSetting::DEFAULTS
        );
        if (! $tier->factors) {
            $tier->factors = LeadTierSetting::DEFAULTS['factors'];
        }

        return view('office.settings.lead-pipeline.index', [
            'tier' => $tier,
            'sla' => LeadSlaSetting::forBusiness($businessId),
            'goal' => LeadGoal::query()
                ->where('business_id', $businessId)
                ->where('fiscal_year', now()->year)
                ->first() ?? new LeadGoal(['fiscal_year' => now()->year]),
        ]);
    }

    public function updateTier(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'min_factors_required' => ['required', 'integer', 'min:1', 'max:5'],
            't1_min' => ['required', 'numeric'],
            't2_min' => ['required', 'numeric'],
            't1_pct' => ['required', 'integer', 'min:0', 'max:100'],
            't2_pct' => ['required', 'integer', 'min:0', 'max:100'],
            't3_pct' => ['required', 'integer', 'min:0', 'max:100'],
            'factors' => ['required', 'array'],
        ]);

        $factors = [];
        foreach (array_keys(LeadTierSetting::DEFAULTS['factors']) as $key) {
            $factors[$key] = [
                'enabled' => (bool) data_get($data['factors'], $key.'.enabled'),
                'weight' => (float) data_get($data['factors'], $key.'.weight', 1),
            ];
        }
        $data['factors'] = $factors;
        $data['create_by'] = $this->createBy();

        LeadTierSetting::query()->updateOrCreate(
            ['business_id' => $this->businessId()],
            $data
        );

        return back()->with('success', 'บันทึกสูตร Tier เรียบร้อยแล้ว');
    }

    public function updateSla(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_touch_minutes' => ['required', 'integer', 'min:1'],
            'qualify_within_days' => ['required', 'integer', 'min:1'],
            'nurture_review_days' => ['required', 'integer', 'min:1'],
        ]);
        $data['business_hours_only'] = $request->boolean('business_hours_only');
        $data['create_by'] = $this->createBy();

        LeadSlaSetting::query()->updateOrCreate(
            ['business_id' => $this->businessId()],
            $data
        );

        return back()->with('success', 'บันทึกการตั้งค่า SLA เรียบร้อยแล้ว');
    }

    public function updateGoal(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fiscal_year' => ['required', 'integer', 'min:2020'],
            'revenue_target' => ['required', 'numeric', 'min:0'],
            'avg_deal_value' => ['required', 'numeric', 'min:0'],
            'lead_to_opportunity_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'opportunity_win_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);
        $data['create_by'] = $this->createBy();

        LeadGoal::query()->updateOrCreate(
            ['business_id' => $this->businessId(), 'fiscal_year' => $data['fiscal_year']],
            $data
        );

        return back()->with('success', 'บันทึกเป้าหมาย Sales Funnel เรียบร้อยแล้ว');
    }
}
