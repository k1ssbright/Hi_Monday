<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Marketing\MarketingAd;
use App\Models\Marketing\MarketingAdLog;
use App\Models\Marketing\MarketingArtwork;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingArtworkController extends Controller
{
    use ResolvesBusinessContext;

    public function store(Request $request, MarketingAd $marketing_ad): RedirectResponse
    {
        $this->assertOwned($marketing_ad);
        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'type' => ['required', 'string', 'max:50'],
        ]);

        $file = $request->file('file');
        $stored = uploadFileAddTime($file);

        MarketingArtwork::create([
            'marketing_ad_id' => $marketing_ad->id,
            'business_id' => $this->businessId(),
            'file_name' => $stored,
            'original_name' => $file->getClientOriginalName(),
            'type' => $request->string('type')->toString(),
            'create_by' => $this->createBy(),
        ]);

        MarketingAdLog::create([
            'marketing_ad_id' => $marketing_ad->id,
            'user_id' => Auth::id(),
            'action' => 'updated',
            'changes' => ['artwork_added' => $file->getClientOriginalName()],
            'create_by' => $this->createBy(),
        ]);

        return back()->with('success', 'อัปโหลด Artwork เรียบร้อยแล้ว');
    }

    public function destroy(MarketingAd $marketing_ad, MarketingArtwork $artwork): RedirectResponse
    {
        $this->assertOwned($marketing_ad);
        if ((int) $artwork->marketing_ad_id !== (int) $marketing_ad->id) {
            abort(404);
        }
        $artwork->delete();

        return back()->with('success', 'ลบ Artwork เรียบร้อยแล้ว');
    }

    private function assertOwned(MarketingAd $ad): void
    {
        if ((string) $ad->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
