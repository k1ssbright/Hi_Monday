<?php

namespace App\Services;

use App\Models\Address\Amphures;
use App\Models\Address\Provinces;
use App\Models\Address\Tambons;
use App\Models\Partner\Partner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class EditableSalesDocumentService
{
    public function __construct(
        private DocumentPartyService $partyInfo,
        private DocumentTotalsService $totals,
    ) {}

    /**
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    public function payloadRules(array $extra = [], bool $withPayment = true, bool $withValidUntil = false): array
    {
        $rules = [
            'partner_id' => ['nullable', 'uuid'],
            'payment_unit_mode' => ['nullable', 'in:percent,baht'],
            'line' => ['required', 'array', 'min:1'],
            'line.*.description' => ['nullable', 'string'],
            'line.*.detail' => ['nullable', 'string'],
            'line.*.discount' => ['nullable', 'numeric', 'min:0'],
            'line.*.qty' => ['nullable', 'numeric', 'min:0.01'],
            'line.*.unit' => ['nullable', 'string', 'max:50'],
            'line.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'line.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'line.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'line.*.vat_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'line.*.product_service_item_id' => ['nullable'],
            'customer_contact_name' => ['nullable', 'string', 'max:255'],
            'customer_contact_phone' => ['nullable', 'string', 'max:50'],
            'customer_email' => ['nullable', 'string', 'max:255'],
            'customer_type' => ['nullable', 'integer', 'in:1,2,3'],
            'customer_tax_no' => ['nullable', 'string', 'max:13'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_status' => ['nullable', 'integer', 'in:0,1'],
            'customer_branch_no' => ['nullable', 'string', 'max:50'],
            'customer_branch_name' => ['nullable', 'string', 'max:255'],
            'customer_address' => ['nullable', 'string'],
            'customer_province_id' => ['nullable', 'integer', 'exists:provinces,id'],
            'customer_amphure_id' => ['nullable', 'integer', 'exists:amphures,id'],
            'customer_tambon_id' => ['nullable', 'integer', 'exists:tambons,id'],
            'customer_zip_code' => ['nullable', 'string', 'max:10'],
            'vat_type' => ['nullable', 'in:none,inclusive,exclusive'],
            'discount_type' => ['nullable', 'in:percent,baht'],
            'seller_name' => ['nullable', 'string', 'max:255'],
            'seller_phone' => ['nullable', 'string', 'max:50'],
            'seller_email' => ['nullable', 'string', 'max:255'],
        ];

        if ($withValidUntil) {
            $rules['valid_until'] = ['nullable', 'date', 'after_or_equal:issue_date'];
        }

        if ($withPayment) {
            $rules['payment'] = ['nullable', 'array'];
            $rules['payment.*.installment_no'] = ['nullable', 'integer', 'min:1'];
            $rules['payment.*.condition'] = ['nullable', 'string'];
            $rules['payment.*.due_days'] = ['nullable', 'integer', 'min:0'];
            $rules['payment.*.percent_amount'] = ['nullable', 'numeric', 'min:0', 'max:100'];
            $rules['payment.*.baht_amount'] = ['nullable', 'numeric', 'min:0'];
        }

        return array_merge($rules, $extra);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function defaultSeedLines(): array
    {
        return [[
            'product_service_item_id' => null,
            'description' => '',
            'detail' => '',
            'qty' => 1,
            'unit' => 'หน่วย',
            'unit_price' => 0,
            'discount' => 0,
            'vat_percent' => 7,
        ]];
    }

    /**
     * @return array<string, mixed>
     */
    public function formViewData(?Partner $partner, ?string $createByEmployeeBranchId): array
    {
        $customerParty = $partner
            ? $this->partyInfo->mergeWithOldInput($this->partyInfo->customerDefaults($partner))
            : [];
        $sellerParty = $this->partyInfo->mergeWithOldInput($this->partyInfo->sellerDefaults($createByEmployeeBranchId));
        $totalsDefaults = $this->partyInfo->mergeWithOldInput([
            'vat_type' => 'exclusive',
            'discount_type' => 'percent',
        ]);

        $provinceId = $customerParty['customer_province_id'] ?? null;
        $amphureId = $customerParty['customer_amphure_id'] ?? null;

        return [
            'partner' => $partner,
            'customerParty' => $customerParty,
            'sellerParty' => $sellerParty,
            'totalsDefaults' => $totalsDefaults,
            'provinces' => Provinces::query()->orderBy('name_th')->get(),
            'customerAmphures' => $provinceId
                ? Amphures::query()->where('province_id', $provinceId)->orderBy('name_th')->get()
                : collect(),
            'customerTambons' => $amphureId
                ? Tambons::query()->where('amphure_id', $amphureId)->orderBy('name_th')->get()
                : collect(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string|null>
     */
    public function partyFieldsFromRequest(array $data): array
    {
        $isNaturalPerson = (int) ($data['customer_type'] ?? 0) === 3;
        $isHeadOffice = ! $isNaturalPerson && (int) ($data['customer_status'] ?? 0) === 0;

        return [
            'customer_contact_name' => $data['customer_contact_name'] ?? null,
            'customer_contact_phone' => $data['customer_contact_phone'] ?? null,
            'customer_email' => $data['customer_email'] ?? null,
            'customer_type' => $data['customer_type'] ?? null,
            'customer_tax_no' => $data['customer_tax_no'] ?? null,
            'customer_name' => $data['customer_name'] ?? null,
            'customer_status' => $isNaturalPerson ? null : ($data['customer_status'] ?? null),
            'customer_branch_no' => ($isNaturalPerson || $isHeadOffice) ? null : ($data['customer_branch_no'] ?? null),
            'customer_branch_name' => ($isNaturalPerson || $isHeadOffice) ? null : ($data['customer_branch_name'] ?? null),
            'customer_address' => $data['customer_address'] ?? null,
            'customer_province_id' => $data['customer_province_id'] ?? null,
            'customer_amphure_id' => $data['customer_amphure_id'] ?? null,
            'customer_tambon_id' => $data['customer_tambon_id'] ?? null,
            'customer_zip_code' => $data['customer_zip_code'] ?? null,
            'seller_name' => $data['seller_name'] ?? null,
            'seller_phone' => $data['seller_phone'] ?? null,
            'seller_email' => $data['seller_email'] ?? null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function extractPartyFieldsFromRevision(?object $sourceRevision): array
    {
        if (! $sourceRevision) {
            return [];
        }

        return [
            'customer_contact_name' => $sourceRevision->customer_contact_name ?? null,
            'customer_contact_phone' => $sourceRevision->customer_contact_phone ?? null,
            'customer_email' => $sourceRevision->customer_email ?? null,
            'customer_type' => $sourceRevision->customer_type ?? null,
            'customer_tax_no' => $sourceRevision->customer_tax_no ?? null,
            'customer_name' => $sourceRevision->customer_name ?? null,
            'customer_status' => $sourceRevision->customer_status ?? null,
            'customer_branch_no' => $sourceRevision->customer_branch_no ?? null,
            'customer_branch_name' => $sourceRevision->customer_branch_name ?? null,
            'customer_address' => $sourceRevision->customer_address ?? null,
            'customer_province_id' => $sourceRevision->customer_province_id ?? null,
            'customer_amphure_id' => $sourceRevision->customer_amphure_id ?? null,
            'customer_tambon_id' => $sourceRevision->customer_tambon_id ?? null,
            'customer_zip_code' => $sourceRevision->customer_zip_code ?? null,
            'vat_type' => $sourceRevision->vat_type ?? 'exclusive',
            'seller_name' => $sourceRevision->seller_name ?? null,
            'seller_phone' => $sourceRevision->seller_phone ?? null,
            'seller_email' => $sourceRevision->seller_email ?? null,
        ];
    }


    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function totalsFieldsFromRequest(array $data): array
    {
        return [
            'vat_type' => $data['vat_type'] ?? 'exclusive',
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array<int, array<string, mixed>>
     */
    public function normalizeLines(array $lines, string $discountType = 'percent'): array
    {
        return collect($lines)->map(function (array $line) use ($discountType) {
            $name = trim((string) ($line['description'] ?? ''));
            $detail = trim((string) ($line['detail'] ?? ''));
            $line['description'] = $detail !== '' && $name !== ''
                ? $name."\n".$detail
                : ($name !== '' ? $name : $detail);

            $discount = $line['discount'] ?? null;
            if ($discount !== null && $discount !== '') {
                if ($discountType === 'baht') {
                    $line['discount_amount'] = $discount;
                } else {
                    $line['discount_percent'] = $discount;
                }
            }

            return $line;
        })->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @return array<int, array<string, mixed>>
     */
    public function filterLines(array $lines): array
    {
        $filtered = collect($lines)
            ->filter(fn ($line) => filled(trim((string) ($line['description'] ?? ''))))
            ->values()
            ->all();

        if ($filtered === []) {
            throw ValidationException::withMessages(['line' => 'กรุณาระบุรายการอย่างน้อย 1 รายการ']);
        }

        return $filtered;
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @param  array<string, mixed>  $data
     * @param  class-string<Model>  $lineModelClass
     */
    public function persistLines(
        Model $revision,
        array $lines,
        array $data,
        string $lineModelClass,
        string $revisionForeignKey,
        array $extraLineAttributes = [],
    ): void {
        $vatType = $data['vat_type'] ?? 'exclusive';
        $discountType = $data['discount_type'] ?? 'percent';

        foreach ($lines as $line) {
            $qty = (float) ($line['qty'] ?? 1);
            $unitPrice = (float) ($line['unit_price'] ?? 0);
            $vatPercent = isset($line['vat_percent']) ? (float) $line['vat_percent'] : 7.0;

            $lineTotals = $this->totals->calculateLine([
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'discount' => $line['discount'] ?? 0,
                'discount_percent' => $line['discount_percent'] ?? null,
                'discount_amount' => $line['discount_amount'] ?? null,
                'vat_percent' => $vatPercent,
            ], $vatType, $discountType);

            $lineModelClass::create(array_merge([
                $revisionForeignKey => $revision->getKey(),
                'product_service_item_id' => $line['product_service_item_id'] ?? null,
                'description' => $line['description'],
                'quantity' => $qty,
                'unit' => $line['unit'] ?? null,
                'unit_price' => $unitPrice,
                'discount_amount' => $lineTotals['discount_amount'],
                'vat_percent' => $vatPercent,
                'line_total' => $lineTotals['line_total'],
            ], $extraLineAttributes));
        }

        $documentTotals = $this->totals->calculate($lines, $vatType, false, null, $discountType);
        $revision->fill($this->totals->mapToRevisionTotals($documentTotals));
        $revision->vat_type = $vatType;
        $revision->save();
    }

    public function resolveOptionalPartner(string $businessId, ?string $partnerId): ?Partner
    {
        if (blank($partnerId)) {
            return null;
        }

        return Partner::query()
            ->where('business_id', $businessId)
            ->findOrFail($partnerId);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function seedLinesFromOldInput(): array
    {
        $seedLines = old('line');
        if (! is_array($seedLines)) {
            return $this->defaultSeedLines();
        }

        return $this->normalizeSeedLines($seedLines);
    }

    /**
     * @return array<string, mixed>
     */
    public function formViewDataFromRevision(?Partner $partner, ?string $createByEmployeeBranchId, object $revision): array
    {
        $customerParty = $this->partyInfo->mergeWithOldInput($this->customerPartyFromRevision($revision, $partner));
        $sellerParty = $this->partyInfo->mergeWithOldInput($this->sellerPartyFromRevision($revision, $createByEmployeeBranchId));
        $totalsDefaults = $this->partyInfo->mergeWithOldInput($this->totalsDefaultsFromRevision($revision));

        $provinceId = $customerParty['customer_province_id'] ?? null;
        $amphureId = $customerParty['customer_amphure_id'] ?? null;

        return [
            'partner' => $partner,
            'customerParty' => $customerParty,
            'sellerParty' => $sellerParty,
            'totalsDefaults' => $totalsDefaults,
            'provinces' => Provinces::query()->orderBy('name_th')->get(),
            'customerAmphures' => $provinceId
                ? Amphures::query()->where('province_id', $provinceId)->orderBy('name_th')->get()
                : collect(),
            'customerTambons' => $amphureId
                ? Tambons::query()->where('amphure_id', $amphureId)->orderBy('name_th')->get()
                : collect(),
        ];
    }

    /**
     * @return array<string, string|null>
     */
    public function customerPartyFromRevision(object $revision, ?Partner $partner = null): array
    {
        $snapshot = [
            'customer_contact_name' => $revision->customer_contact_name ?? null,
            'customer_contact_phone' => $revision->customer_contact_phone ?? null,
            'customer_email' => $revision->customer_email ?? null,
            'customer_type' => $revision->customer_type ?? null,
            'customer_tax_no' => $revision->customer_tax_no ?? null,
            'customer_name' => $revision->customer_name ?? null,
            'customer_status' => $revision->customer_status ?? null,
            'customer_branch_no' => $revision->customer_branch_no ?? null,
            'customer_branch_name' => $revision->customer_branch_name ?? null,
            'customer_address' => $revision->customer_address ?? null,
            'customer_province_id' => $revision->customer_province_id ?? null,
            'customer_amphure_id' => $revision->customer_amphure_id ?? null,
            'customer_tambon_id' => $revision->customer_tambon_id ?? null,
            'customer_zip_code' => $revision->customer_zip_code ?? null,
        ];

        if ($partner && blank($snapshot['customer_name'] ?? null) && blank($snapshot['customer_contact_name'] ?? null)) {
            $defaults = $this->partyInfo->customerDefaults($partner);
            foreach ($defaults as $key => $val) {
                if (blank($snapshot[$key] ?? null)) {
                    $snapshot[$key] = $val;
                }
            }
        }

        return $snapshot;
    }

    /**
     * @return array<string, string|null>
     */
    public function sellerPartyFromRevision(object $revision, ?string $createByEmployeeBranchId = null): array
    {
        $snapshot = [
            'seller_name' => $revision->seller_name ?? null,
            'seller_phone' => $revision->seller_phone ?? null,
            'seller_email' => $revision->seller_email ?? null,
        ];

        if (blank($snapshot['seller_name'] ?? null)) {
            $defaults = $this->partyInfo->sellerDefaults($createByEmployeeBranchId);
            foreach ($defaults as $key => $val) {
                if (blank($snapshot[$key] ?? null)) {
                    $snapshot[$key] = $val;
                }
            }
        }

        return $snapshot;
    }

    /**
     * @return array<string, string>
     */
    public function totalsDefaultsFromRevision(object $revision): array
    {
        return [
            'vat_type' => $revision->vat_type ?? 'exclusive',
            'discount_type' => 'percent',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function seedLinesFromRevision(object $revision): array
    {
        if (! method_exists($revision, 'lines')) {
            return $this->defaultSeedLines();
        }

        $revision->loadMissing('lines');

        if ($revision->lines->isEmpty()) {
            return $this->defaultSeedLines();
        }

        return $revision->lines->map(function ($line) {
            $description = (string) $line->description;
            $name = $description;
            $detail = '';
            if (str_contains($description, "\n")) {
                $parts = explode("\n", $description, 2);
                $name = $parts[0];
                $detail = $parts[1] ?? '';
            }

            return [
                'product_service_item_id' => $line->product_service_item_id,
                'description' => $name,
                'detail' => $detail,
                'qty' => $line->quantity,
                'unit' => $line->unit ?? 'หน่วย',
                'unit_price' => $line->unit_price,
                'discount' => $line->discount_amount ?? 0,
                'vat_percent' => $line->vat_percent ?? 7,
            ];
        })->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $lines
     * @param  array<string, mixed>  $data
     * @param  class-string<Model>  $lineModelClass
     */
    public function replaceLinesOnRevision(
        Model $revision,
        array $lines,
        array $data,
        string $lineModelClass,
        string $revisionForeignKey,
        array $extraLineAttributes = [],
    ): void {
        $lineModelClass::query()
            ->where($revisionForeignKey, $revision->getKey())
            ->delete();

        $this->persistLines(
            $revision,
            $lines,
            $data,
            $lineModelClass,
            $revisionForeignKey,
            $extraLineAttributes,
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $seedLines
     * @return array<int, array<string, mixed>>
     */
    private function normalizeSeedLines(array $seedLines): array
    {
        return collect($seedLines)->map(function (array $line) {
            if (! isset($line['detail']) && isset($line['description'])) {
                $text = (string) $line['description'];
                if (str_contains($text, "\n")) {
                    $parts = explode("\n", $text, 2);
                    $line['description'] = $parts[0];
                    $line['detail'] = $parts[1] ?? '';
                }
            }
            $line['discount'] = $line['discount'] ?? $line['discount_percent'] ?? $line['discount_amount'] ?? 0;

            return $line;
        })->all();
    }
}
