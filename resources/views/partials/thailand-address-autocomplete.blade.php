@php
    use App\Models\Address\Tambons;

    $provinceName = $provinceName ?? 'province_id';
    $amphureName = $amphureName ?? 'amphure_id';
    $tambonName = $tambonName ?? 'tambon_id';
    $zipName = $zipName ?? null;
    $variant = $variant ?? 'public';
    $required = (bool) ($required ?? false);
    $idPrefix = $idPrefix ?? preg_replace('/_id$/', '', (string) $tambonName);

    $selectedProvinceId = old($provinceName, $selectedProvinceId ?? '');
    $selectedAmphureId = old($amphureName, $selectedAmphureId ?? '');
    $selectedTambonId = old($tambonName, $selectedTambonId ?? '');
    $selectedProvinceName = $selectedProvinceName ?? '';
    $selectedAmphureName = $selectedAmphureName ?? '';
    $selectedTambonName = $selectedTambonName ?? '';
    $selectedZipCode = old($zipName ?? 'zip_code', $selectedZipCode ?? '');

    if ($selectedTambonName === '' && $selectedTambonId) {
        $tambonModel = Tambons::query()
            ->with('amphure.province')
            ->find($selectedTambonId);

        if ($tambonModel) {
            $selectedTambonName = $tambonModel->name_th;
            $selectedAmphureName = $selectedAmphureName ?: ($tambonModel->amphure?->name_th ?? '');
            $selectedProvinceName = $selectedProvinceName ?: ($tambonModel->amphure?->province?->name_th ?? '');
            $selectedZipCode = $selectedZipCode ?: ($tambonModel->zip_code ?? '');
        }
    }

    $isOffice = $variant === 'office';
    $size = $size ?? 'md';
    $isOfficeSm = $isOffice && $size === 'sm';
    $labelClass = $labelClass ?? ($isOffice
        ? ($isOfficeSm ? 'form-label fs-13' : 'form-label')
        : 'form-label-custom');
    $inputClass = $inputClass ?? ($isOffice
        ? ($isOfficeSm ? 'form-control form-control-sm' : 'form-control')
        : 'custom-input');
    $errorWrapperClass = $isOffice ? 'text-danger fs-12' : 'field-error-msg';
    $colClass = $colClass ?? ($isOffice ? 'col-md-6' : 'col-md-6 col-12 form-group-custom');
    $rowClass = $rowClass ?? ($isOffice
        ? ($isOfficeSm ? 'row g-2' : 'row g-3')
        : 'row g-3');
    $requiredMark = $required ? ($isOffice ? ' <span class="text-danger">*</span>' : ' <span class="required-star">*</span>') : '';
    $fieldWrapperClass = '';
@endphp

<div class="{{ $rowClass }} thailand-address-autocomplete thailand-address-autocomplete--{{ $variant }}"
    data-id-prefix="{{ $idPrefix }}"
    data-province-field="{{ $provinceName }}"
    data-amphure-field="{{ $amphureName }}"
    data-tambon-field="{{ $tambonName }}"
    @if ($required) data-validate-selection="1" @endif>
    <input type="hidden" name="{{ $provinceName }}" id="{{ $provinceName }}" value="{{ $selectedProvinceId }}">
    <input type="hidden" name="{{ $amphureName }}" id="{{ $amphureName }}" value="{{ $selectedAmphureId }}">
    <input type="hidden" name="{{ $tambonName }}" id="{{ $tambonName }}" value="{{ $selectedTambonId }}">

    <div class="{{ $colClass }} {{ $fieldWrapperClass }}">
        <label class="{{ $labelClass }}" for="{{ $idPrefix }}_subdistrict">
            ตำบล/แขวง{!! $requiredMark !!}
        </label>
        <input type="text"
            id="{{ $idPrefix }}_subdistrict"
            class="{{ $inputClass }} autocomplete-location @error($tambonName) is-invalid @enderror"
            value="{{ $selectedTambonName }}"
            placeholder="พิมพ์ค้นหาตำบล/แขวง (ไทย/อังกฤษ)"
            autocomplete="off"
            @if ($required) required @endif>
        @error($tambonName)
            <div class="{{ $errorWrapperClass }}">
                @if (! $isOffice)<i class="ri-error-warning-line"></i>@endif
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="{{ $colClass }} {{ $fieldWrapperClass }}">
        <label class="{{ $labelClass }}" for="{{ $idPrefix }}_district">
            อำเภอ/เขต{!! $requiredMark !!}
        </label>
        <input type="text"
            id="{{ $idPrefix }}_district"
            class="{{ $inputClass }} autocomplete-location @error($amphureName) is-invalid @enderror"
            value="{{ $selectedAmphureName }}"
            placeholder="พิมพ์ค้นหาอำเภอ/เขต (ไทย/อังกฤษ)"
            autocomplete="off"
            @if ($required) required @endif>
        @error($amphureName)
            <div class="{{ $errorWrapperClass }}">
                @if (! $isOffice)<i class="ri-error-warning-line"></i>@endif
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="{{ $colClass }} {{ $fieldWrapperClass }}">
        <label class="{{ $labelClass }}" for="{{ $idPrefix }}_province_display">
            จังหวัด{!! $requiredMark !!}
        </label>
        <input type="text"
            id="{{ $idPrefix }}_province_display"
            class="{{ $inputClass }} autocomplete-location @error($provinceName) is-invalid @enderror"
            value="{{ $selectedProvinceName }}"
            placeholder="พิมพ์ค้นหาจังหวัด (ไทย/อังกฤษ)"
            autocomplete="off"
            @if ($required) required @endif>
        @error($provinceName)
            <div class="{{ $errorWrapperClass }}">
                @if (! $isOffice)<i class="ri-error-warning-line"></i>@endif
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="{{ $colClass }} {{ $fieldWrapperClass }}">
        <label class="{{ $labelClass }}" for="{{ $idPrefix }}_zipcode">
            รหัสไปรษณีย์{!! $requiredMark !!}
        </label>
        <input type="text"
            id="{{ $idPrefix }}_zipcode"
            @if ($zipName) name="{{ $zipName }}" @endif
            class="{{ $inputClass }} autocomplete-location @error($zipName) is-invalid @enderror"
            value="{{ $selectedZipCode }}"
            placeholder="พิมพ์ค้นหารหัสไปรษณีย์"
            autocomplete="off"
            maxlength="10"
            @if ($required) required @endif>
        @if ($zipName)
            @error($zipName)
                <div class="{{ $errorWrapperClass }}">
                    @if (! $isOffice)<i class="ri-error-warning-line"></i>@endif
                    {{ $message }}
                </div>
            @enderror
        @endif
    </div>
</div>
