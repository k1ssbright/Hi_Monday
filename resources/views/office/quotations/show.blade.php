@extends('office.layout.office')
@section('title', ($quotation->displayNo() ?: 'Quotation Record').' | Hi Monday')

@section('style')
@include('office.partials.document-form-styles')
@include('office.partials.sales-journey-styles')
<link href="{{ asset('assets/libs/dropzone/dropzone.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
@php
    $revision = $quotation->currentRevision;
    $relatedItems = [];
    if ($quotation->opportunity) {
        $relatedItems[] = [
            'url' => route('office.opportunities.show', $quotation->opportunity),
            'label' => 'Opportunity: '.$quotation->opportunity->opportunity_no,
            'badge' => 'OPP',
            'badge_class' => 'bg-info-subtle text-info',
        ];
    }
    foreach ($quotation->salesOrders as $so) {
        $relatedItems[] = [
            'url' => route('office.sales-orders.show', $so),
            'label' => $so->displayNo(),
            'badge' => $so->currentRevision?->status ?: 'SO',
            'badge_class' => 'bg-primary-subtle text-primary',
        ];
    }
@endphp
<div class="container-fluid document-form">
    @include('office.settings.partials.page-title', [
        'section' => 'งานขาย',
        'parent' => 'Quotations',
        'parentUrl' => route('office.quotations.index'),
        'title' => $quotation->displayNo(),
    ])
    @include('office.settings.partials.flash')

    <x-document-show-with-journey :journey="$salesJourney">
    @if ($revision && in_array($revision->status, ['sent', 'approved'], true))
        <div class="row justify-content-center mb-3">
            <div class="col-12 col-xl-10">
                <div class="emp-card-main document-form">

                    {{-- Accept Form --}}
                    <div class="section-header-title">
                        <i class="ri-checkbox-circle-line"></i>
                        ยืนยันการตอบรับ Quotation
                    </div>
                    <p class="text-muted fs-13 mb-4">เมื่อยืนยัน ระบบจะสร้าง Sales Order สถานะ <strong class="text-success">confirmed</strong> ให้อัตโนมัติ (แนบเอกสารไม่บังคับ)</p>

                    <form method="post" action="{{ route('office.quotations.accept', $quotation) }}" enctype="multipart/form-data" id="quotation-accept-form">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">เลขที่ PO ลูกค้า</label>
                                <input type="text" name="customer_po_no" class="form-control" value="{{ old('customer_po_no') }}" placeholder="ไม่บังคับ">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">เอกสาร PO</label>
                                <input type="file" name="po_file" class="form-control" accept=".pdf,.png,.jpg,.jpeg,.doc,.docx,.xls,.xlsx,.zip">
                                <div class="form-text">ไม่บังคับ · ไฟล์เดียว</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">หมายเหตุการตอบรับ</label>
                                <input type="text" name="accepted_note" class="form-control" value="{{ old('accepted_note') }}" placeholder="ไม่บังคับ">
                            </div>
                            <div class="col-12">
                                <label class="form-label">เอกสารเพิ่มเติม</label>
                                <div class="dropzone" id="quotation-extra-dropzone" style="border:2px dashed #cbd5e1; border-radius:12px; background:#f8fafc; min-height:140px;">
                                    <div class="dz-message needsclick">
                                        <div class="mb-2"><i class="ri-upload-cloud-2-line" style="font-size:2.5rem; color:#94a3b8;"></i></div>
                                        <h6 class="fw-semibold text-muted mb-1">ลากวางไฟล์ หรือคลิกเพื่อเลือก</h6>
                                        <p class="text-muted fs-13 mb-0">เลือกได้หลายไฟล์ · ไม่บังคับ</p>
                                    </div>
                                </div>
                                <input type="file" name="extra_files[]" id="quotation-extra-files" class="d-none" multiple>
                                <ul class="list-unstyled mb-0 mt-2" id="dropzone-preview">
                                    <li class="mt-2" id="dropzone-preview-list">
                                        <div class="border rounded-3" style="border-color:#e2e8f0 !important;">
                                            <div class="d-flex p-2 align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar-sm bg-light rounded-2">
                                                        <img data-dz-thumbnail class="img-fluid rounded-2 d-block" src="{{ asset('assets/images/new-document.png') }}" alt="" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="pt-1">
                                                        <h6 class="fs-13 mb-1 fw-semibold text-dark" data-dz-name>&nbsp;</h6>
                                                        <p class="fs-12 text-muted mb-0" data-dz-size></p>
                                                        <strong class="error text-danger fs-12" data-dz-errormessage></strong>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0 ms-3">
                                                    <button type="button" data-dz-remove class="btn-action-item btn-action-delete"><i class="ri-delete-bin-line"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-emp btn-emp-success">
                                    <i class="ri-checkbox-circle-line"></i> ยืนยันการตอบรับ และสร้าง SO
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Divider --}}
                    <hr style="border-color:#e2e8f0; margin: 28px 0;">

                    {{-- Reject Form --}}
                    <div class="section-header-title" style="border-bottom-color:#FEE2E2; color:#DC2626;">
                        <i class="ri-close-circle-line" style="color:#DC2626;"></i>
                        ปฏิเสธ Quotation
                    </div>
                    <form method="post" action="{{ route('office.quotations.reject', $quotation) }}" class="row g-3 align-items-end">
                        @csrf
                        <div class="col-md-8">
                            <label class="form-label">เหตุผลปฏิเสธ</label>
                            <input type="text" name="rejected_note" class="form-control" placeholder="ระบุเหตุผล" required>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn-emp btn-emp-danger w-100">
                                <i class="ri-close-circle-line"></i> บันทึกการปฏิเสธ
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endif


    <x-document-show-layout
        :revision="$revision"
        :meta-fields="[
            ['label' => 'วันที่ออก', 'value' => $revision?->issue_date?->format('d/m/Y')],
            ['label' => 'วันยืนราคา', 'value' => $revision?->valid_until?->format('d/m/Y')],
            ['label' => 'เลขที่ PO ลูกค้า', 'value' => $revision?->customer_po_no],
            ['label' => 'สกุลเงิน', 'value' => $revision?->currency ?: 'THB', 'class' => 'col-sm-3'],
        ]"
        :show-payment-terms="true"
        :payment-terms="$revision?->paymentTerms ?? collect()"
        :payment-mode="$revision?->payment_unit_mode ?? 'percent'"
        :attachments="$revision?->attachments ?? collect()"
    >
        @if ($relatedItems !== [])
            <x-slot:related>
                @include('office.partials.document-related-links', ['items' => $relatedItems])
            </x-slot:related>
        @endif

        <x-slot:actionbar>
            <x-document-show-actionbar
                :document-no="$quotation->displayNo()"
                :status="$revision?->status"
                :revision-no="$revision?->revision_no"
                :back-url="route('office.quotations.index')"
            >
                <x-slot:actions>
                    @if ($revision && in_array($revision->status, ['draft', 'rejected'], true))
                        <a href="{{ route('office.quotations.edit', $quotation) }}" class="btn-emp btn-emp-info">
                            <i class="ri-pencil-line"></i> แก้ไข
                        </a>
                    @endif
                    @if ($revision && $revision->status === 'draft')
                        <form method="post" action="{{ route('office.quotations.destroy', $quotation) }}" class="d-inline" onsubmit="return confirm('ยืนยันการลบ Quotation นี้?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-emp btn-emp-danger"><i class="ri-delete-bin-line"></i> ลบ</button>
                        </form>
                    @endif
                    @if ($revision && in_array($revision->status, ['draft', 'sent', 'approved'], true) && $quotation->salesOrders->isEmpty())
                        <form method="post" action="{{ route('office.quotations.cancel', $quotation) }}" class="d-inline" onsubmit="return confirm('ยืนยันการยกเลิก Quotation นี้?');">
                            @csrf
                            <button type="submit" class="btn-emp btn-emp-warning"><i class="ri-close-circle-line"></i> ยกเลิก</button>
                        </form>
                    @endif
                    @if ($revision && in_array($revision->status, ['draft', 'approved'], true))
                        <form method="post" action="{{ route('office.quotations.send', $quotation) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn-emp btn-emp-success"><i class="ri-send-plane-line"></i> ส่ง Quotation</button>
                        </form>
                    @endif
                    @if ($quotation->canCreateSalesOrder())
                        <a href="{{ route('office.quotations.sales-orders.create', $quotation) }}" class="btn-emp btn-emp-primary">
                            <i class="ri-file-list-3-line"></i> สร้าง Sales Order
                        </a>
                    @endif
                </x-slot:actions>
            </x-document-show-actionbar>
        </x-slot:actionbar>
    </x-document-show-layout>
    </x-document-show-with-journey>
</div>
@endsection

@section('script')
<script src="{{ asset('assets/libs/dropzone/dropzone-min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var zoneEl = document.querySelector('#quotation-extra-dropzone');
    if (!zoneEl || typeof Dropzone === 'undefined') return;
    Dropzone.autoDiscover = false;
    var previewNode = document.querySelector('#dropzone-preview-list');
    previewNode.id = '';
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);
    var fileInput = document.getElementById('quotation-extra-files');
    var syncInput = function (dropzone) {
        var dt = new DataTransfer();
        dropzone.files.forEach(function (file) {
            if (file instanceof File) dt.items.add(file);
        });
        fileInput.files = dt.files;
    };
    var dropzone = new Dropzone('#quotation-extra-dropzone', {
        url: '/',
        autoProcessQueue: false,
        uploadMultiple: true,
        parallelUploads: 20,
        maxFilesize: 10,
        previewTemplate: previewTemplate,
        previewsContainer: '#dropzone-preview',
        clickable: '#quotation-extra-dropzone',
    });
    dropzone.on('addedfile', function () { syncInput(dropzone); });
    dropzone.on('removedfile', function () { syncInput(dropzone); });
});
</script>
@endsection
