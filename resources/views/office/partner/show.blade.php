@extends('office.layout.office')

@section('title', 'รายละเอียดคู่ค้า | Hi Monday')

@section('style')
<style>
    .biz-card-main {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        border: none;
        padding: 24px;
    }

    .biz-logo-avatar-lg {
        width: 64px;
        height: 64px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.4rem;
    }

    .table-subbiz-custom {
        width: 100%;
        border-collapse: collapse;
    }

    .table-subbiz-custom th {
        color: #475569;
        font-size: 0.825rem;
        font-weight: 700;
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        background-color: #f8fafc;
        vertical-align: middle;
        white-space: nowrap;
    }

    .table-subbiz-custom td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.875rem;
    }

    .table-subbiz-custom tbody tr {
        transition: background-color 0.15s ease;
    }

    .table-subbiz-custom tbody tr:nth-child(even) {
        background-color: #fafbfc;
    }

    .table-subbiz-custom tbody tr:hover {
        background-color: #f1f5f9;
    }

    .btn-back-custom {
        background-color: #f1f5f9;
        color: #475569;
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-back-custom:hover {
        background-color: #e2e8f0;
        color: #1e293b;
    }

    /* ===== Clean & Minimal Contact Modal ===== */
    .modal-contact-clean .modal-content {
        border: none;
        border-radius: 14px;
        box-shadow: 0 15px 35px rgba(15, 23, 42, 0.12);
        overflow: hidden;
    }

    .modal-contact-clean .table th {
        font-size: 0.775rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #64748b;
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 11px 18px;
    }

    .modal-contact-clean .table td {
        padding: 13px 18px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.85rem;
    }

    .modal-contact-clean .table tbody tr:last-child td {
        border-bottom: none;
    }

    .modal-contact-clean .table tbody tr:hover {
        background-color: #f8fafc;
    }

    .contact-link-simple {
        color: #334155;
        text-decoration: none;
        transition: color 0.15s ease;
    }

    .contact-link-simple:hover {
        color: #2563eb;
    }

    .contact-link-line:hover {
        color: #06c755;
    }

    /* ===== Premium Contact Card ===== */
    .contact-item {
        border-color: #f1f5f9 !important;
    }
    
    .contact-item:hover {
        border-color: #e2e8f0 !important;
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01) !important;
    }
    
    .contact-link-hover:hover {
        color: #2563eb !important;
    }
    
    .line-link-hover:hover {
        filter: brightness(0.9);
    }
    
    .primary-contact-indicator {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 4px;
        background: linear-gradient(180deg, #fbbf24 0%, #f59e0b 100%);
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }
</style>
@endsection

@section('content')
@php
    $role = $partner->role();
    $badgeColor = match ($role) {
        'vendor' => 'bg-warning',
        'both' => 'bg-primary',
        default => 'bg-info',
    };
    $initials = mb_strtoupper(mb_substr($partner->partner_code ?: $partner->partner_name_th, 0, 3));
@endphp

<div class="container-fluid">
    @include('office.settings.partials.flash')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('office.partners.index') }}" class="btn-back-custom">
            <i class="ri-arrow-left-line"></i> ย้อนกลับไปรายการคู่ค้า
        </a>
        <div class="fs-13 text-muted">
            <span>สมุดรายชื่อคู่ค้า</span> <i class="ri-arrow-right-s-line"></i> <span class="fw-semibold text-dark">ข้อมูลคู่ค้า {{ $partner->partner_code }}</span>
        </div>
    </div>

    <div class="biz-card-main mb-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="biz-logo-avatar-lg {{ $badgeColor }} text-white shadow-sm">{{ $initials }}</div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="fw-bold text-dark mb-0">{{ $partner->partner_name_th }}</h4>
                        @if ($partner->isActive())
                            <span class="badge bg-success-subtle text-success px-3 py-1 rounded-pill"><i class="ri-checkbox-circle-fill me-1"></i> เปิดใช้งาน</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger px-3 py-1 rounded-pill"><i class="ri-close-circle-fill me-1"></i> ปิดใช้งาน</span>
                        @endif
                    </div>
                    <p class="text-muted mb-0 fs-13 mt-1">{{ $partner->roleLabel() }} • {{ $partner->entityTypeLabel() }} • {{ $partner->branches->count() }} สาขา</p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary rounded-3 px-3 py-2 fw-semibold fs-13" data-bs-toggle="modal" data-bs-target="#editPartnerMasterModal">
                    <i class="ri-edit-2-line"></i> แก้ไขข้อมูลคู่ค้า
                </button>
            </div>
        </div>
    </div>

    <div class="biz-card-main mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold text-dark mb-0 fs-16">
                <i class="ri-contacts-book-line text-primary me-2"></i> ข้อมูลสาขา / สำนักงานทั้งหมดของคู่ค้านี้
            </h5>
            <span class="text-muted fs-13">แสดง {{ $partner->branches->count() }} รายการ</span>
        </div>

        <div class="table-responsive border rounded-3">
            <table class="table-subbiz-custom">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">#</th>
                        <th style="width: 120px;">รหัสสาขา</th>
                        <th>ชื่อสาขา / สำนักงาน</th>
                        <th>เลขประจำตัวผู้เสียภาษี</th>
                        <th>เบอร์โทรศัพท์</th>
                        <th>ประเภท</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center" style="width: 100px;">ผู้ติดต่อ</th>
                        <th class="text-center" style="width: 100px;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($partner->branches as $index => $item)
                    @php
                        $branchContacts = $item->assignedContacts();
                        if ($branchContacts->isEmpty() && (int) $item->partner_status === 0) {
                            $branchContacts = $partner->contacts;
                        }
                    @endphp
                    <tr>
                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                        <td class="fw-bold text-primary">{{ $item->partner_branch_no ?: '-' }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <i class="ri-git-commit-line text-primary fs-16"></i>
                                <span class="fw-semibold text-dark">{{ $item->partner_branch_name_th ?: '-' }}</span>
                            </div>
                        </td>
                        <td>{{ $partner->partner_tax_no ?: '-' }}</td>
                        <td>{{ $item->partner_tel ?: $item->partner_phone ?: '-' }}</td>
                        <td>{{ $item->roleLabel() }}</td>
                        <td class="text-center">
                            @if($item->active_status)
                                <span class="badge bg-success-subtle text-success px-2 py-1"><i class="ri-checkbox-circle-fill me-1"></i> เปิดใช้งาน</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger px-2 py-1"><i class="ri-close-circle-fill me-1"></i> ปิดใช้งาน</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($branchContacts->isNotEmpty())
                                <button type="button" class="btn btn-sm btn-soft-primary px-3" data-bs-toggle="modal" data-bs-target="#contactsModal-{{ $item->id }}">
                                    <i class="ri-eye-line me-1"></i>
                                </button>
                            @else
                                <span class="text-muted fs-13">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button"
                                    class="btn btn-sm btn-soft-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editBranchModal"
                                    data-action="{{ route('office.partners.branches.update', [$partner, $item]) }}"
                                    data-code="{{ $item->partner_branch_no }}"
                                    data-name="{{ $item->partner_branch_name_th }}"
                                    data-tax="{{ $partner->partner_tax_no }}"
                                    data-phone="{{ $item->partner_tel }}"
                                    data-role="{{ $item->role() }}"
                                    data-status="{{ $item->active_status ? '1' : '0' }}">
                                <i class="ri-pencil-fill"></i> แก้ไข
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">ยังไม่มีข้อมูลสาขา</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach($partner->branches as $item)
    @php
        $branchContacts = $item->assignedContacts();
        if ($branchContacts->isEmpty() && (int) $item->partner_status === 0) {
            $branchContacts = $partner->contacts;
        }
    @endphp
    <div class="modal fade modal-contact-clean" id="contactsModal-{{ $item->id }}" tabindex="-1" aria-labelledby="contactsModalLabel-{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 580px;">
            <div class="modal-content">
                <div class="modal-header py-3 px-4 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-contacts-line text-primary fs-18"></i>
                        <h6 class="modal-title fw-bold text-dark mb-0 fs-15" id="contactsModalLabel-{{ $item->id }}">
                            ผู้ติดต่อ - {{ $item->partner_branch_name_th }}
                        </h6>
                        <span class="badge bg-primary-subtle text-primary rounded-pill fs-11 px-2">{{ $branchContacts->count() }}</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-light bg-opacity-50">
                    @if($branchContacts->isNotEmpty())
                    <div class="d-flex flex-column gap-3">
                        @foreach($branchContacts as $contact)
                        @php
                            $initial = mb_substr($contact->firstname ?: '-', 0, 1);
                            $fullName = $contact->displayName();
                            $lineId = $contact->line_id;
                            $isPrimary = $loop->first;
                        @endphp
                        
                        <div class="contact-item p-3 border rounded-3 bg-white shadow-sm position-relative transition-all" style="transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);">
                            @if($isPrimary)
                                <div class="primary-contact-indicator"></div>
                            @endif
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 w-100 ps-2">
                                <!-- Avatar & Name -->
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-sm flex-shrink-0 shadow-sm rounded-circle">
                                        <span class="avatar-title rounded-circle fw-bold fs-15 {{ $isPrimary ? 'bg-warning text-white' : 'bg-primary-subtle text-primary' }}" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                                            {{ $initial }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <h6 class="fw-bold text-dark mb-0 fs-15">{{ $fullName }}</h6>
                                            @if($isPrimary)
                                                <span class="badge rounded-pill bg-warning-subtle text-warning px-2 py-1 border border-warning-subtle d-flex align-items-center gap-1 shadow-sm" style="font-size: 0.65rem;">
                                                    <i class="ri-star-fill"></i> ผู้ติดต่อหลัก
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-muted fs-12 d-flex align-items-center">
                                            <i class="ri-building-4-line me-1 opacity-75"></i> 
                                            ผู้ติดต่อประจำสาขา {{ $item->partner_branch_name_th }}
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Contact Info (Phone / Line) -->
                                <div class="d-flex gap-4 ms-auto bg-light rounded-3 p-2 px-3 border border-light-subtle">
                                    <!-- Phone -->
                                    <div class="d-flex flex-column pe-3 border-end">
                                        <span class="text-muted fs-11 text-uppercase fw-bold mb-1 letter-spacing-wide">เบอร์โทรศัพท์</span>
                                        @if($contact->phone)
                                            <a href="tel:{{ preg_replace('/[^0-9]/', '', $contact->phone) }}" class="text-dark fw-semibold fs-13 text-decoration-none contact-link-hover d-flex align-items-center transition-all">
                                                <i class="ri-phone-fill text-muted me-1 fs-14"></i>{{ $contact->phone }}
                                            </a>
                                        @else
                                            <span class="text-muted fs-13">-</span>
                                        @endif
                                    </div>
                                    <!-- LINE -->
                                    <div class="d-flex flex-column ps-1">
                                        <span class="text-muted fs-11 text-uppercase fw-bold mb-1 letter-spacing-wide">LINE ID</span>
                                        @if(!empty($lineId))
                                            <a href="https://line.me/ti/p/~{{ urlencode($lineId) }}" target="_blank" class="fw-semibold fs-13 text-decoration-none line-link-hover d-flex align-items-center transition-all" style="color: #06c755;">
                                                <i class="ri-line-fill me-1 fs-15"></i>{{ $lineId }}
                                            </a>
                                        @else
                                            <span class="text-muted fs-13">-</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="ri-user-line fs-24 d-block mb-1 text-muted opacity-50"></i>
                        <span class="fs-13">ไม่มีข้อมูลผู้ติดต่อในสาขานี้</span>
                    </div>
                    @endif
                </div>

                <div class="modal-footer py-2 px-4 border-top bg-light bg-opacity-50">
                    <button type="button" class="btn btn-sm btn-light border px-3" data-bs-dismiss="modal">ปิด</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @include('office.partner.partials.edit')
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-target="#editBranchModal"]').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('editBranchForm').action = button.dataset.action;
            document.getElementById('editBranchCode').value = button.dataset.code || '';
            document.getElementById('editBranchName').value = button.dataset.name || '';
            document.getElementById('editBranchTax').value = button.dataset.tax || '';
            document.getElementById('editBranchPhone').value = button.dataset.phone || '';
            document.getElementById('editBranchType').value = button.dataset.role || 'customer';
            document.getElementById('editBranchStatus').checked = button.dataset.status === '1';
        });
    });
});
</script>
@endsection
