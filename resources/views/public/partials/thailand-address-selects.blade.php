@include('partials.thailand-address-selects', [
    'provinces' => $provinces ?? collect(),
    'amphures' => $amphures ?? collect(),
    'tambons' => $tambons ?? collect(),
    'selectedProvinceId' => old('province_id', $selectedProvinceId ?? ''),
    'selectedAmphureId' => old('amphure_id', $selectedAmphureId ?? ''),
    'selectedTambonId' => old('tambon_id', $selectedTambonId ?? ''),
    'selectedZipCode' => $selectedZipCode ?? '',
    'variant' => 'public',
    'required' => true,
])
