@once('thailand-address-autocomplete-bundle')
<script src="{{ asset('assets/libs/@tarekraafat/autocomplete.js/autoComplete.min.js') }}"></script>
<script>
    (function() {
        const locationUrl = '{{ route('locations.thaiLocationInter') }}';

        function fillAddressFields(wrapper, selected) {
            const prefix = wrapper.dataset.idPrefix;
            const provinceField = wrapper.dataset.provinceField;
            const amphureField = wrapper.dataset.amphureField;
            const tambonField = wrapper.dataset.tambonField;

            wrapper.querySelector(`#${prefix}_subdistrict`).value = selected.tambon_name || '';
            wrapper.querySelector(`#${prefix}_district`).value = selected.amphure_name || '';
            wrapper.querySelector(`#${prefix}_province_display`).value = selected.province_name || '';
            wrapper.querySelector(`#${prefix}_zipcode`).value = selected.zip_code || '';
            wrapper.querySelector(`[name="${provinceField}"]`).value = selected.province_id || '';
            wrapper.querySelector(`[name="${amphureField}"]`).value = selected.amphure_id || '';
            wrapper.querySelector(`[name="${tambonField}"]`).value = selected.tambon_id || '';
        }

        function initThailandAddressAutocomplete(container) {
            if (typeof autoComplete === 'undefined') {
                return;
            }

            const root = container || document;

            root.querySelectorAll('.thailand-address-autocomplete').forEach(function(wrapper) {
                wrapper.querySelectorAll('.autocomplete-location').forEach(function(input) {
                    if (input.dataset.autocompleteReady === '1') {
                        return;
                    }

                    input.dataset.autocompleteReady = '1';

                    new autoComplete({
                        selector: function() {
                            return input;
                        },
                        data: {
                            src: async function() {
                                try {
                                    const response = await fetch(locationUrl);
                                    const data = await response.json();
                                    return data;
                                } catch (error) {
                                    console.error('Error fetching location data:', error);
                                    return [];
                                }
                            },
                            keys: ['address'],
                            cache: true,
                        },
                        threshold: 1,
                        resultItem: {
                            highlight: true,
                        },
                        events: {
                            input: {
                                selection: function(event) {
                                    fillAddressFields(wrapper, event.detail.selection.value);
                                },
                            },
                        },
                    });
                });
            });
        }

        function bindThailandAddressValidation(container) {
            const root = container || document;

            root.querySelectorAll('.thailand-address-autocomplete[data-validate-selection="1"]').forEach(function(wrapper) {
                const form = wrapper.closest('form');
                if (!form || form.dataset.thailandAddressValidationBound === '1') {
                    return;
                }

                form.dataset.thailandAddressValidationBound = '1';

                form.addEventListener('submit', function(event) {
                    const wrappers = form.querySelectorAll('.thailand-address-autocomplete[data-validate-selection="1"]');

                    for (const item of wrappers) {
                        const tambonField = item.dataset.tambonField;
                        const tambonInput = item.querySelector(`[name="${tambonField}"]`);

                        if (tambonInput && !tambonInput.value) {
                            event.preventDefault();
                            alert('กรุณาเลือกตำบล/อำเภอ/จังหวัดจากรายการที่แสดง');
                            return;
                        }
                    }
                });
            });
        }

        window.initThailandAddressAutocomplete = initThailandAddressAutocomplete;

        function bootThailandAddressAutocomplete(container) {
            initThailandAddressAutocomplete(container);
            bindThailandAddressValidation(container);
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                bootThailandAddressAutocomplete();
            });
        } else {
            bootThailandAddressAutocomplete();
        }
    })();
</script>
@endonce
