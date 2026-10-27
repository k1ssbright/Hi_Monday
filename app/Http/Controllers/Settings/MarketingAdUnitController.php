<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Marketing\MarketingAd;
use App\Models\Marketing\MarketingAdLog;
use App\Models\Marketing\MarketingAdUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingAdUnitController extends Controller
{
    use ResolvesBusinessContext;

    public function store(Request $request, MarketingAd $marketing_ad): RedirectResponse
    {
        $this->assertOwned($marketing_ad);
        $data = $this->validated($request);
        $data['marketing_ad_id'] = $marketing_ad->id;
        $data['business_id'] = $this->businessId();
        $data['create_by'] = $this->createBy();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['meta'] = $this->parseMeta($request->input('meta_json'));

        MarketingAdUnit::create($data);
        $this->log($marketing_ad, 'updated', ['unit_created' => $data['name']]);

        return back()->with('success', 'เพิ่มหน่วยโฆษณาเรียบร้อยแล้ว');
    }

    public function update(Request $request, MarketingAd $marketing_ad, MarketingAdUnit $unit): RedirectResponse
    {
        $this->assertOwned($marketing_ad);
        if ((int) $unit->marketing_ad_id !== (int) $marketing_ad->id) {
            abort(404);
        }

        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['meta'] = $this->parseMeta($request->input('meta_json'));
        $unit->update($data);
        $this->log($marketing_ad, 'updated', ['unit_updated' => $unit->name]);

        return back()->with('success', 'อัปเดตหน่วยโฆษณาเรียบร้อยแล้ว');
    }

    public function destroy(MarketingAd $marketing_ad, MarketingAdUnit $unit): RedirectResponse
    {
        $this->assertOwned($marketing_ad);
        if ((int) $unit->marketing_ad_id !== (int) $marketing_ad->id) {
            abort(404);
        }
        $unit->delete();
        $this->log($marketing_ad, 'updated', ['unit_deleted' => $unit->name]);

        return back()->with('success', 'ลบหน่วยโฆษณาเรียบร้อยแล้ว');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:255'],
            'objective' => ['nullable', 'string', 'max:255'],
            'external_id' => ['nullable', 'string', 'max:255'],
            'budget' => ['nullable', 'numeric'],
            'spend' => ['nullable', 'numeric'],
            'status' => ['required', 'in:draft,active,paused,ended'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function parseMeta(?string $json): ?array
    {
        if ($json === null || trim($json) === '') {
            return null;
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function log(MarketingAd $ad, string $action, ?array $changes): void
    {
        MarketingAdLog::create([
            'marketing_ad_id' => $ad->id,
            'user_id' => Auth::id(),
            'action' => $action,
            'changes' => $changes,
            'create_by' => $this->createBy(),
        ]);
    }

    private function assertOwned(MarketingAd $ad): void
    {
        if ((string) $ad->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
