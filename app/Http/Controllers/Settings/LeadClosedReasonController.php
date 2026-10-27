<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Lead\LeadClosedReason;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadClosedReasonController extends Controller
{
    use ResolvesBusinessContext;

    public function index(): View
    {
        return view('office.settings.lead-closed-reasons.index', [
            'rows' => LeadClosedReason::query()
                ->where('business_id', $this->businessId())
                ->orderBy('sort')->orderBy('name')
                ->paginate(30),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['business_id'] = $this->businessId();
        $data['create_by'] = $this->createBy();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['allows_reopen'] = $request->boolean('allows_reopen');
        $data['sort'] = (int) ($data['sort'] ?? 0);
        LeadClosedReason::create($data);

        return back()->with('success', 'บันทึกเหตุผลปิด Lead เรียบร้อยแล้ว');
    }

    public function update(Request $request, LeadClosedReason $lead_closed_reason): RedirectResponse
    {
        $this->assertOwned($lead_closed_reason);
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['allows_reopen'] = $request->boolean('allows_reopen');
        $data['sort'] = (int) ($data['sort'] ?? 0);
        $lead_closed_reason->update($data);

        return back()->with('success', 'อัปเดตเหตุผลปิด Lead เรียบร้อยแล้ว');
    }

    public function destroy(LeadClosedReason $lead_closed_reason): RedirectResponse
    {
        $this->assertOwned($lead_closed_reason);
        $lead_closed_reason->delete();

        return back()->with('success', 'ลบเหตุผลปิด Lead เรียบร้อยแล้ว');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'applies_to' => ['required', 'in:lead,opportunity,both'],
            'sort' => ['nullable', 'integer'],
        ]);
    }

    private function assertOwned(LeadClosedReason $row): void
    {
        if ((string) $row->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
