<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Lead\LeadChannel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadChannelController extends Controller
{
    use ResolvesBusinessContext;

    public function index(): View
    {
        return view('office.settings.lead-channels.index', [
            'rows' => LeadChannel::query()
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
        $data['sort'] = (int) ($data['sort'] ?? 0);
        LeadChannel::create($data);

        return back()->with('success', 'บันทึกช่องทางรับ Lead เรียบร้อยแล้ว');
    }

    public function update(Request $request, LeadChannel $lead_channel): RedirectResponse
    {
        $this->assertOwned($lead_channel);
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort'] = (int) ($data['sort'] ?? 0);
        $lead_channel->update($data);

        return back()->with('success', 'อัปเดตช่องทางรับ Lead เรียบร้อยแล้ว');
    }

    public function destroy(LeadChannel $lead_channel): RedirectResponse
    {
        $this->assertOwned($lead_channel);
        $lead_channel->delete();

        return back()->with('success', 'ลบช่องทางรับ Lead เรียบร้อยแล้ว');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'kind' => ['required', 'in:'.implode(',', LeadChannel::KINDS)],
            'sort' => ['nullable', 'integer'],
        ]);
    }

    private function assertOwned(LeadChannel $row): void
    {
        if ((string) $row->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
