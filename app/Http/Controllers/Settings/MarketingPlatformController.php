<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Marketing\MarketingPlatform;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingPlatformController extends Controller
{
    use ResolvesBusinessContext;

    public function index(): View
    {
        return view('office.settings.marketing-platforms.index', [
            'platforms' => MarketingPlatform::query()
                ->where('business_id', $this->businessId())
                ->orderBy('sort')
                ->orderBy('name')
                ->paginate(30),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sort' => ['nullable', 'integer'],
        ]);
        $data['business_id'] = $this->businessId();
        $data['create_by'] = $this->createBy();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort'] = (int) ($data['sort'] ?? 0);

        MarketingPlatform::create($data);

        return back()->with('success', 'บันทึกแพลตฟอร์มเรียบร้อยแล้ว');
    }

    public function update(Request $request, MarketingPlatform $marketing_platform): RedirectResponse
    {
        $this->assertOwned($marketing_platform);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sort' => ['nullable', 'integer'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort'] = (int) ($data['sort'] ?? 0);
        $marketing_platform->update($data);

        return back()->with('success', 'อัปเดตแพลตฟอร์มเรียบร้อยแล้ว');
    }

    public function destroy(MarketingPlatform $marketing_platform): RedirectResponse
    {
        $this->assertOwned($marketing_platform);
        $marketing_platform->delete();

        return back()->with('success', 'ลบแพลตฟอร์มเรียบร้อยแล้ว');
    }

    private function assertOwned(MarketingPlatform $platform): void
    {
        if ((string) $platform->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
