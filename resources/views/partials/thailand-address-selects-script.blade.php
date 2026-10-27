@php
    $amphureUrl = $amphureUrl ?? route('locations.amphures');
    $tambonUrl = $tambonUrl ?? route('locations.tambons');
@endphp
<script>
    (function() {
        const amphureUrl = '{{ $amphureUrl }}';
        const tambonUrl = '{{ $tambonUrl }}';

        function initThailandAddressSelects(container) {
            const root = container || document;
            root.querySelectorAll('.thailand-address-fields').forEach(function(wrapper) {
                const provinceSelect = wrapper.querySelector('.thailand-province-select');
                const amphureSelect = wrapper.querySelector('.thailand-amphure-select');
                const tambonSelect = wrapper.querySelector('.thailand-tambon-select');
                const zipInput = wrapper.querySelector('.thailand-zip-input');

                if (!provinceSelect || !amphureSelect || !tambonSelect || !zipInput) {
                    return;
                }

                function initSelect2(el) {
                    if (typeof $ === 'undefined' || !$.fn.select2) {
                        return;
                    }
                    const $el = $(el);
                    if ($el.hasClass('select2-hidden-accessible')) {
                        $el.select2('destroy');
                    }
                    $el.select2({
                        width: '100%',
                        placeholder: el.dataset.placeholder || '',
                        allowClear: true,
                    });
                }

                function fillSelect(select, items, placeholder, selectedId) {
                    select.innerHTML = '';
                    const empty = document.createElement('option');
                    empty.value = '';
                    empty.textContent = placeholder;
                    select.appendChild(empty);

                    items.forEach(function(item) {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.name_th;
                        if (item.zip_code) {
                            option.dataset.zip = item.zip_code;
                        }
                        if (String(selectedId) === String(item.id)) {
                            option.selected = true;
                        }
                        select.appendChild(option);
                    });

                    initSelect2(select);
                }

                function syncZipFromTambon() {
                    const option = tambonSelect.options[tambonSelect.selectedIndex];
                    zipInput.value = option?.dataset?.zip || '';
                }

                async function loadAmphures(provinceId, selectedAmphureId) {
                    fillSelect(amphureSelect, [], 'เลือกอำเภอ/เขต');
                    fillSelect(tambonSelect, [], 'เลือกตำบล/แขวง');
                    zipInput.value = '';
                    if (!provinceId) {
                        return;
                    }
                    const res = await fetch(`${amphureUrl}?province_id=${provinceId}`);
                    fillSelect(amphureSelect, await res.json(), 'เลือกอำเภอ/เขต', selectedAmphureId || '');
                }

                async function loadTambons(amphureId, selectedTambonId) {
                    fillSelect(tambonSelect, [], 'เลือกตำบล/แขวง');
                    zipInput.value = '';
                    if (!amphureId) {
                        return;
                    }
                    const res = await fetch(`${tambonUrl}?amphure_id=${amphureId}`);
                    fillSelect(tambonSelect, await res.json(), 'เลือกตำบล/แขวง', selectedTambonId || '');
                    syncZipFromTambon();
                }

                function onProvinceChange() {
                    loadAmphures(provinceSelect.value);
                }

                function onAmphureChange() {
                    loadTambons(amphureSelect.value);
                }

                initSelect2(provinceSelect);
                initSelect2(amphureSelect);
                initSelect2(tambonSelect);

                provinceSelect.addEventListener('change', onProvinceChange);
                amphureSelect.addEventListener('change', onAmphureChange);
                tambonSelect.addEventListener('change', syncZipFromTambon);

                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $(provinceSelect).on('change', onProvinceChange);
                    $(amphureSelect).on('change', onAmphureChange);
                    $(tambonSelect).on('change', syncZipFromTambon);
                }
            });
        }

        window.initThailandAddressSelects = initThailandAddressSelects;

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                initThailandAddressSelects();
            });
        } else {
            initThailandAddressSelects();
        }
    })();
</script>
