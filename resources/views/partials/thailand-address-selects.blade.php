@php
    $provinces = $provinces ?? collect();
    $amphures = $amphures ?? collect();
    $tambons = $tambons ?? collect();
    $provinceName = $provinceName ?? 'province_id';
    $amphureName = $amphureName ?? 'amphure_id';
    $tambonName = $tambonName ?? 'tambon_id';
    $zipName = $zipName ?? null;
    $zipReadonly = $zipReadonly ?? ($zipName === null);
    $variant = $variant ?? 'public';
    $required = (bool) ($required ?? false);
    $rowClass = $rowClass ?? ($variant === 'office' ? 'row g-2' : 'row g-3');

    $selectedProvinceId = old($provinceName, $selectedProvinceId ?? '');
    $selectedAmphureId = old($amphureName, $selectedAmphureId ?? '');
    $selectedTambonId = old($tambonName, $selectedTambonId ?? '');
    $selectedZipCode = old($zipName ?? 'zip_code', $selectedZipCode ?? '');

    if ($selectedZipCode === '' && $selectedTambonId && $tambons->isNotEmpty()) {
        $selectedZipCode = $tambons->firstWhere('id', (int) $selectedTambonId)?->zip_code ?? '';
    }

    $isOffice = $variant === 'office';
    $labelClass = $isOffice ? 'form-label fs-13' : 'form-label-custom';
    $selectClass = $isOffice ? 'form-select form-select-sm' : 'custom-input';
    $inputClass = $isOffice ? 'form-control form-control-sm' : 'custom-input';
    $errorWrapperClass = $isOffice ? 'text-danger fs-12' : 'field-error-msg';
    $colClass = $colClass ?? ($isOffice ? 'col-md-6' : 'col-md-6 col-12 form-group-custom');
    $requiredMark = $required ? ($isOffice ? ' <span class="text-danger">*</span>' : ' <span class="required-star">*</span>') : '';
@endphp

<div class="{{ $rowClass }} thailand-address-fields">
    <div class="{{ $colClass }}">
        <label class="{{ $labelClass }}" for="{{ $provinceName }}">
            จังหวัด{!! $requiredMark !!}
        </label>
        <select name="{{ $provinceName }}" id="{{ $provinceName }}"
            class="{{ $selectClass }} thailand-province-select @error($provinceName) is-invalid @enderror"
            data-placeholder="เลือกจังหวัด" @if($required) required @endif>
            <option value="">เลือกจังหวัด</option>
            @foreach ($provinces as $province)
                <option value="{{ $province->id }}" @selected((string) $selectedProvinceId === (string) $province->id)>
                    {{ $province->name_th }}
                </option>
            @endforeach
        </select>
        @error($provinceName)
            <div class="{{ $errorWrapperClass }}">
                @if (! $isOffice)<i class="ri-error-warning-line"></i>@endif
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="{{ $colClass }}">
        <label class="{{ $labelClass }}" for="{{ $amphureName }}">
            อำเภอ/เขต{!! $requiredMark !!}
        </label>
        <select name="{{ $amphureName }}" id="{{ $amphureName }}"
            class="{{ $selectClass }} thailand-amphure-select @error($amphureName) is-invalid @enderror"
            data-placeholder="เลือกอำเภอ/เขต" @if($required) required @endif>
            <option value="">เลือกอำเภอ/เขต</option>
            @foreach ($amphures as $amphure)
                <option value="{{ $amphure->id }}" @selected((string) $selectedAmphureId === (string) $amphure->id)>
                    {{ $amphure->name_th }}
                </option>
            @endforeach
        </select>
        @error($amphureName)
            <div class="{{ $errorWrapperClass }}">
                @if (! $isOffice)<i class="ri-error-warning-line"></i>@endif
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="{{ $colClass }}">
        <label class="{{ $labelClass }}" for="{{ $tambonName }}">
            ตำบล/แขวง{!! $requiredMark !!}
        </label>
        <select name="{{ $tambonName }}" id="{{ $tambonName }}"
            class="{{ $selectClass }} thailand-tambon-select @error($tambonName) is-invalid @enderror"
            data-placeholder="เลือกตำบล/แขวง" @if($required) required @endif>
            <option value="">เลือกตำบล/แขวง</option>
            @foreach ($tambons as $tambon)
                <option value="{{ $tambon->id }}" data-zip="{{ $tambon->zip_code }}"
                    @selected((string) $selectedTambonId === (string) $tambon->id)>
                    {{ $tambon->name_th }}
                </option>
            @endforeach
        </select>
        @error($tambonName)
            <div class="{{ $errorWrapperClass }}">
                @if (! $isOffice)<i class="ri-error-warning-line"></i>@endif
                {{ $message }}
            </div>
        @enderror
    </div>

    <div class="{{ $colClass }}">
        <label class="{{ $labelClass }}" for="{{ $zipName ?? ($provinceName.'_zip_display') }}">
            รหัสไปรษณีย์{!! $requiredMark !!}
        </label>
        <input type="text"
            @if ($zipName) name="{{ $zipName }}" id="{{ $zipName }}" @else id="{{ $provinceName }}_zip_display" @endif
            class="{{ $inputClass }} thailand-zip-input @error($zipName) is-invalid @enderror"
            value="{{ $selectedZipCode }}"
            maxlength="10"
            @if ($zipReadonly) readonly @endif
            placeholder="เลือกตำบล/แขวงเพื่อแสดงรหัสไปรษณีย์">
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
