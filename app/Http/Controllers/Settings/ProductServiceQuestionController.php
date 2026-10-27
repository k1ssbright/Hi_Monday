<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\ProductService\ProductServiceCategory;
use App\Models\ProductService\ProductServiceQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductServiceQuestionController extends Controller
{
    use ResolvesBusinessContext;

    public function index(Request $request): View
    {
        $businessId = $this->businessId();
        $query = ProductServiceQuestion::query()
            ->where('business_id', $businessId)
            ->with('category')
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($request->filled('purpose')) {
            $query->where('purpose', $request->string('purpose')->toString());
        }

        if ($request->filled('category_id')) {
            $query->where('product_service_category_id', $request->integer('category_id'));
        }

        return view('office.settings.product-service-questions.index', [
            'questions' => $query->paginate(20)->withQueryString(),
            'categories' => ProductServiceCategory::query()
                ->where('business_id', $businessId)
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
        $data['is_required'] = $request->boolean('is_required');
        $data['options'] = $this->parseOptions($request->input('options_text'));

        ProductServiceQuestion::create($data);

        return back()->with('success', 'บันทึกคำถามเรียบร้อยแล้ว');
    }

    public function update(Request $request, ProductServiceQuestion $product_service_question): RedirectResponse
    {
        $this->assertOwned($product_service_question);
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_required'] = $request->boolean('is_required');
        $data['options'] = $this->parseOptions($request->input('options_text'));
        $product_service_question->update($data);

        return back()->with('success', 'อัปเดตคำถามเรียบร้อยแล้ว');
    }

    public function destroy(ProductServiceQuestion $product_service_question): RedirectResponse
    {
        $this->assertOwned($product_service_question);
        $product_service_question->delete();

        return back()->with('success', 'ลบคำถามเรียบร้อยแล้ว');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'product_service_category_id' => ['required', 'integer'],
            'purpose' => ['required', 'in:intake,quality'],
            'label' => ['required', 'string', 'max:255'],
            'input_type' => ['required', 'in:text,number,select,checkbox,textarea,date'],
            'sort_order' => ['nullable', 'integer'],
        ]);
    }

    private function parseOptions(?string $text): ?array
    {
        if ($text === null || trim($text) === '') {
            return null;
        }

        $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $text))));

        return $lines === [] ? null : $lines;
    }

    private function assertOwned(ProductServiceQuestion $question): void
    {
        if ((string) $question->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
