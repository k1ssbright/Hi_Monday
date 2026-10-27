<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Lead\LeadAssignmentRule;
use App\Models\Lead\LeadChannel;
use App\Models\Marketing\MarketingAd;
use App\Models\Address\Provinces;
use App\Models\Business\BusinessBranch;
use App\Models\Employee\EmployeeBranch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadAssignmentRuleController extends Controller
{
    use ResolvesBusinessContext;

    public function index(): View
    {
        $businessId = $this->businessId();

        return view('office.settings.lead-assignment-rules.index', [
            'rows' => LeadAssignmentRule::query()
                ->where('business_id', $businessId)
                ->with(['channel', 'assignBranch', 'assignEmployee.employee.user'])
                ->orderBy('priority')
                ->paginate(30),
            'channels' => LeadChannel::query()->where('business_id', $businessId)->where('is_active', true)->orderBy('name')->get(),
            'ads' => MarketingAd::query()->where('business_id', $businessId)->orderBy('name')->get(),
            'provinces' => Provinces::query()->orderBy('name_th')->get(),
            'branches' => BusinessBranch::query()->where('business_id', $businessId)->orderBy('business_branch_name_th')->get(),
            'employees' => EmployeeBranch::query()
                ->whereHas('branch', fn ($q) => $q->where('business_id', $businessId))
                ->with('employee.user')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['business_id'] = $this->businessId();
        $data['create_by'] = $this->createBy();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['priority'] = (int) ($data['priority'] ?? 100);
        LeadAssignmentRule::create($data);

        return back()->with('success', 'บันทึกกฎการมอบหมาย Lead เรียบร้อยแล้ว');
    }

    public function update(Request $request, LeadAssignmentRule $lead_assignment_rule): RedirectResponse
    {
        $this->assertOwned($lead_assignment_rule);
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['priority'] = (int) ($data['priority'] ?? 100);
        $lead_assignment_rule->update($data);

        return back()->with('success', 'อัปเดตกฎการมอบหมาย Lead เรียบร้อยแล้ว');
    }

    public function destroy(LeadAssignmentRule $lead_assignment_rule): RedirectResponse
    {
        $this->assertOwned($lead_assignment_rule);
        $lead_assignment_rule->delete();

        return back()->with('success', 'ลบกฎการมอบหมาย Lead เรียบร้อยแล้ว');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'priority' => ['nullable', 'integer'],
            'lead_channel_id' => ['nullable', 'integer'],
            'marketing_ad_id' => ['nullable', 'integer'],
            'province_id' => ['nullable', 'integer'],
            'assign_business_branch_id' => ['nullable', 'uuid'],
            'assign_employee_branch_id' => ['nullable', 'uuid'],
        ]);
    }

    private function assertOwned(LeadAssignmentRule $row): void
    {
        if ((string) $row->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
