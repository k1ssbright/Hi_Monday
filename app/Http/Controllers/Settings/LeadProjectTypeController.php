<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Lead\LeadProjectType;
use App\Models\ProductService\ProductServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadProjectTypeController extends Controller
{
    use ResolvesBusinessContext;

    public function index(): View
    {
        $businessId = $this->businessId();

        return view('office.settings.lead-project-types.index', [
            'rows' => LeadProjectType::query()
                ->where('business_id', $businessId)
                ->with('category')
                ->orderBy('sort')->orderBy('name')
                ->paginate(30),
            'categories' => ProductServiceCategory::query()
                ->where('business_id', $businessId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['business_id'] = $this->businessId();
        $data['create_by'] = $this->createBy();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort'] = (int) ($data['sort'] ?? 0);
        LeadProjectType::create($data);

        return back()->with('success', 'บันทึกประเภทงาน Lead เรียบร้อยแล้ว');
    }

    public function update(Request $request, LeadProjectType $lead_project_type): RedirectResponse
    {
        $this->assertOwned($lead_project_type);
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['sort'] = (int) ($data['sort'] ?? 0);
        $lead_project_type->update($data);

        return back()->with('success', 'อัปเดตประเภทงาน Lead เรียบร้อยแล้ว');
    }

    public function destroy(LeadProjectType $lead_project_type): RedirectResponse
    {
        $this->assertOwned($lead_project_type);
        $lead_project_type->delete();

        return back()->with('success', 'ลบประเภทงาน Lead เรียบร้อยแล้ว');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'product_service_category_id' => ['nullable', 'integer'],
            'sort' => ['nullable', 'integer'],
        ]);
    }

    private function assertOwned(LeadProjectType $row): void
    {
        if ((string) $row->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
