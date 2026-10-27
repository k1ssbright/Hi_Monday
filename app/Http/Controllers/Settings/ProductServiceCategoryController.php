<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\ProductService\ProductServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductServiceCategoryController extends Controller
{
    use ResolvesBusinessContext;

    public function index(Request $request): View
    {
        $businessId = $this->businessId();
        $query = ProductServiceCategory::query()
            ->where('business_id', $businessId)
            ->withCount(['items', 'questions'])
            ->orderBy('kind')
            ->orderBy('name');

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kind')) {
            $query->where('kind', $request->string('kind')->toString());
        }

        return view('office.settings.product-service-categories.index', [
            'categories' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['business_id'] = $this->businessId();
        $data['create_by'] = $this->createBy();
        $data['is_active'] = $request->boolean('is_active', true);

        ProductServiceCategory::create($data);

        return back()->with('success', 'บันทึกหมวดสินค้า/บริการเรียบร้อยแล้ว');
    }

    public function update(Request $request, ProductServiceCategory $product_service_category): RedirectResponse
    {
        $this->assertOwned($product_service_category);
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $product_service_category->update($data);

        return back()->with('success', 'อัปเดตหมวดสินค้า/บริการเรียบร้อยแล้ว');
    }

    public function destroy(ProductServiceCategory $product_service_category): RedirectResponse
    {
        $this->assertOwned($product_service_category);
        $product_service_category->delete();

        return back()->with('success', 'ลบหมวดสินค้า/บริการเรียบร้อยแล้ว');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kind' => ['required', 'in:product,service'],
            'code' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }

    private function assertOwned(ProductServiceCategory $category): void
    {
        if ((string) $category->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
