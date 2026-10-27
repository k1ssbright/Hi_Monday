<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Marketing\MarketingAd;
use App\Models\Marketing\MarketingAdLog;
use App\Models\Marketing\MarketingObjective;
use App\Models\Marketing\MarketingPlatform;
use App\Models\ProductService\ProductServiceItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MarketingAdController extends Controller
{
    use ResolvesBusinessContext;

    public function index(Request $request): View
    {
        $businessId = $this->businessId();
        $query = MarketingAd::query()
            ->where('business_id', $businessId)
            ->with('productServiceItem')
            ->withCount(['units', 'artworks'])
            ->latest();

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where('name', 'like', "%{$q}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return view('office.settings.marketing-ads.index', [
            'ads' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return $this->formView(new MarketingAd([
            'status' => 'active',
            'is_active' => true,
        ]));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['business_id'] = $this->businessId();
        $data['create_by'] = $this->createBy();
        $data['is_active'] = $request->boolean('is_active', true);

        $ad = MarketingAd::create($data);
        $this->log($ad, 'created', $data);

        return redirect()
            ->route('office.settings.marketing-ads.show', $ad)
            ->with('success', 'สร้างแคมเปญเรียบร้อยแล้ว');
    }

    public function show(MarketingAd $marketing_ad): View
    {
        $this->assertOwned($marketing_ad);
        $marketing_ad->load(['productServiceItem', 'units', 'artworks', 'logs']);

        return view('office.settings.marketing-ads.show', [
            'ad' => $marketing_ad,
            'platforms' => MarketingPlatform::query()
                ->where('business_id', $this->businessId())
                ->where('is_active', true)
                ->orderBy('sort')
                ->get(),
            'objectives' => MarketingObjective::query()
                ->where('business_id', $this->businessId())
                ->where('is_active', true)
                ->orderBy('sort')
                ->get(),
        ]);
    }

    public function edit(MarketingAd $marketing_ad): View
    {
        $this->assertOwned($marketing_ad);

        return $this->formView($marketing_ad);
    }

    public function update(Request $request, MarketingAd $marketing_ad): RedirectResponse
    {
        $this->assertOwned($marketing_ad);
        $before = $marketing_ad->only(['name', 'budget', 'status', 'start_date', 'end_date', 'is_active', 'product_service_item_id', 'notes']);
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $marketing_ad->update($data);
        $this->log($marketing_ad, 'updated', ['before' => $before, 'after' => $data]);

        return redirect()
            ->route('office.settings.marketing-ads.show', $marketing_ad)
            ->with('success', 'อัปเดตแคมเปญเรียบร้อยแล้ว');
    }

    public function destroy(MarketingAd $marketing_ad): RedirectResponse
    {
        $this->assertOwned($marketing_ad);
        $this->log($marketing_ad, 'archived', ['name' => $marketing_ad->name]);
        $marketing_ad->delete();

        return redirect()
            ->route('office.settings.marketing-ads.index')
            ->with('success', 'ลบแคมเปญเรียบร้อยแล้ว');
    }

    private function formView(MarketingAd $ad): View
    {
        return view('office.settings.marketing-ads.form', [
            'ad' => $ad,
            'items' => ProductServiceItem::query()
                ->where('business_id', $this->businessId())
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'product_service_item_id' => ['nullable', 'integer'],
            'budget' => ['nullable', 'numeric'],
            'status' => ['required', 'in:active,paused,ended'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string'],
        ]);
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
