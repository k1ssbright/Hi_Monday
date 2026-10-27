<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\ProductService\ProductServiceCategory;
use App\Models\ProductService\ProductServiceItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductServiceItemController extends Controller
{
    use ResolvesBusinessContext;

    public function index(Request $request): View
    {
        $businessId = $this->businessId();
        $query = ProductServiceItem::query()
            ->where('business_id', $businessId)
            ->with('category')
            ->latest();

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('code', 'like', "%{$q}%")
                    ->orWhere('product_id', 'like', "%{$q}%")
                    ->orWhere('brand', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kind')) {
            $query->where('kind', $request->string('kind')->toString());
        }

        return view('office.settings.product-service-items.index', [
            'items' => $query->paginate(20)->withQueryString(),
            'categories' => ProductServiceCategory::query()
                ->where('business_id', $businessId)
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('office.settings.product-service-items.form', [
            'item' => new ProductServiceItem(['kind' => 'product', 'status' => 'active', 'is_active' => true]),
            'categories' => $this->categories(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        unset($data['image']);
        $data['business_id'] = $this->businessId();
        $data['create_by'] = $this->createBy();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['track_inventory'] = $request->boolean('track_inventory');
        $data['product_id'] = $this->nextProductId();

        if ($request->hasFile('image')) {
            $data['image'] = uploadFileAddTime($request->file('image'));
        }

        ProductServiceItem::create($data);

        return redirect()
            ->route('office.settings.product-service-items.index')
            ->with('success', 'บันทึกสินค้า/บริการเรียบร้อยแล้ว');
    }

    public function edit(ProductServiceItem $product_service_item): View
    {
        $this->assertOwned($product_service_item);

        return view('office.settings.product-service-items.form', [
            'item' => $product_service_item,
            'categories' => $this->categories(),
        ]);
    }

    public function update(Request $request, ProductServiceItem $product_service_item): RedirectResponse
    {
        $this->assertOwned($product_service_item);
        $data = $this->validated($request);
        unset($data['image']);
        $data['is_active'] = $request->boolean('is_active');
        $data['track_inventory'] = $request->boolean('track_inventory');

        if ($request->hasFile('image')) {
            $data['image'] = uploadFileAddTime($request->file('image'));
        }

        $product_service_item->update($data);

        return redirect()
            ->route('office.settings.product-service-items.index')
            ->with('success', 'อัปเดตสินค้า/บริการเรียบร้อยแล้ว');
    }

    public function destroy(ProductServiceItem $product_service_item): RedirectResponse
    {
        $this->assertOwned($product_service_item);
        $product_service_item->delete();

        return back()->with('success', 'ลบสินค้า/บริการเรียบร้อยแล้ว');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'product_service_category_id' => ['nullable', 'integer'],
            'kind' => ['required', 'in:product,service'],
            'code' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:255'],
            'default_price' => ['nullable', 'numeric'],
            'buy_price' => ['nullable', 'numeric'],
            'status' => ['required', 'in:active,inactive'],
            'image' => ['nullable', 'file', 'max:5120'],
        ]);
    }

    private function categories()
    {
        return ProductServiceCategory::query()
            ->where('business_id', $this->businessId())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function nextProductId(): string
    {
        $count = ProductServiceItem::withTrashed()
            ->where('business_id', $this->businessId())
            ->count() + 1;

        return 'P-'.str_pad((string) $count, 5, '0', STR_PAD_LEFT);
    }

    private function assertOwned(ProductServiceItem $item): void
    {
        if ((string) $item->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
