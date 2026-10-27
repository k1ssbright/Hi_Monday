    <!-- JAVASCRIPT -->
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>
    <script src="{{ asset('plugins/select2-4.1.0-rc.0/dist/js/select2.min.js') }}"></script>

    <!-- SweetAlert2 JS -->
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            if ($('#business-select').length) {
                var $businessSelect = $('#business-select');

                $businessSelect.select2({
                    width: '250px',
                    minimumResultsForSearch: Infinity
                });

                $businessSelect.on('change', function() {
                    var switchUrl = $(this).find(':selected').data('switch-url');
                    if (switchUrl) {
                        window.location.href = switchUrl;
                    }
                });
            }
        });

        // Global SweetAlert2 handler for native onsubmit confirm()
        document.addEventListener('submit', function (e) {
            var form = e.target;
            if (!form) return;

            var onsubmitAttr = form.getAttribute('onsubmit');
            if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                e.preventDefault();
                e.stopImmediatePropagation();

                var match = onsubmitAttr.match(/confirm\(['"](.*?)['"]\)/);
                var message = match ? match[1] : 'คุณยืนยันที่จะดำเนินการนี้หรือไม่?';

                var isDanger = message.includes('ลบ') || message.includes('delete');
                var isWarning = message.includes('ยกเลิก') || message.includes('cancel');

                var iconType = isDanger ? 'error' : (isWarning ? 'warning' : 'question');
                var confirmBtnColor = isDanger ? '#ef4444' : (isWarning ? '#ea580c' : '#2563eb');
                var confirmBtnText = isDanger ? 'ยืนยันการลบ' : (isWarning ? 'ยืนยันการยกเลิก' : 'ยืนยันทำรายการ');

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: isDanger ? 'ยืนยันการลบข้อมูล?' : (isWarning ? 'ยืนยันการยกเลิกเอกสาร?' : 'ยืนยันทำรายการ?'),
                        text: message,
                        icon: iconType,
                        showCancelButton: true,
                        confirmButtonColor: confirmBtnColor,
                        cancelButtonColor: '#64748b',
                        confirmButtonText: '<i class="ri-check-line me-1"></i> ' + confirmBtnText,
                        cancelButtonText: 'ปิดหน้าต่าง',
                        buttonsStyling: true,
                        customClass: {
                            popup: 'rounded-4 shadow-lg border-0 p-4',
                            confirmButton: 'btn btn-danger px-4 py-2 rounded-3 fs-14 fw-semibold',
                            cancelButton: 'btn btn-light px-4 py-2 rounded-3 fs-14 fw-semibold me-2'
                        }
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            form.removeAttribute('onsubmit');
                            form.submit();
                        }
                    });
                } else if (confirm(message)) {
                    form.removeAttribute('onsubmit');
                    form.submit();
                }

                return false;
            }
        }, true);
    </script>

