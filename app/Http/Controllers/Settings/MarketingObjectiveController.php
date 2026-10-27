<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Marketing\MarketingObjective;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketingObjectiveController extends Controller
{
    use ResolvesBusinessContext;

    public function index(): View
    {
        return view('office.settings.marketing-objectives.index', [
            'objectives' => MarketingObjective::query()
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

        MarketingObjective::create($data);

        return back()->with('success', 'บันทึกวัตถุประสงค์เรียบร้อยแล้ว');
    }

    public function update(Request $request, MarketingObjective $marketing_objective): RedirectResponse
    {
        $this->assertOwned($marketing_objective);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sort' => ['nullable', 'integer'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort'] = (int) ($data['sort'] ?? 0);
        $marketing_objective->update($data);

        return back()->with('success', 'อัปเดตวัตถุประสงค์เรียบร้อยแล้ว');
    }

    public function destroy(MarketingObjective $marketing_objective): RedirectResponse
    {
        $this->assertOwned($marketing_objective);
        $marketing_objective->delete();

        return back()->with('success', 'ลบวัตถุประสงค์เรียบร้อยแล้ว');
    }

    private function assertOwned(MarketingObjective $objective): void
    {
        if ((string) $objective->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
