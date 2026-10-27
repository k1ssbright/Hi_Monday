@php
    $status = strtolower((string) ($status ?? 'draft'));
    $tone = match ($status) {
        'issued', 'confirmed', 'accepted', 'approved', 'sent', 'closed',
        'qualified', 'converted', 'closed_won' => $status,
        'voided', 'cancelled', 'rejected', 'expired',
        'unqualified', 'duplicate', 'archived', 'closed_lost' => $status,
        'pending_approval', 'submitted', 'superseded', 'nurturing',
        'proposal', 'negotiation' => $status,
        'new', 'contacting', 'discovery', 'needs_analysis' => $status,
        default => 'draft',
    };
    $label = $label ?? match ($status) {
        'draft' => 'ร่าง (Draft)',
        'pending_approval' => 'รออนุมัติ',
        'approved' => 'อนุมัติแล้ว',
        'sent' => 'ส่งแล้ว',
        'accepted' => 'ตอบรับ',
        'rejected' => 'ปฏิเสธ',
        'expired' => 'หมดอายุ',
        'cancelled' => 'ยกเลิก',
        'superseded' => 'มีฉบับใหม่แทน',
        'confirmed' => 'ยืนยันแล้ว',
        'closed' => 'ปิดรายการ',
        'issued' => 'ออกแล้ว (Issued)',
        'voided' => 'ยกเลิก (Voided)',
        'submitted' => 'ส่งอนุมัติแล้ว',
        'new' => 'ใหม่',
        'contacting' => 'กำลังติดต่อ',
        'nurturing' => 'ดูแลต่อ',
        'qualified' => 'ผ่านเกณฑ์',
        'converted' => 'แปลงแล้ว',
        'unqualified' => 'ปิด / ไม่ผ่าน',
        'duplicate' => 'ซ้ำ',
        'archived' => 'คลัง',
        'discovery' => 'Discovery',
        'needs_analysis' => 'Needs Analysis',
        'proposal' => 'Proposal',
        'negotiation' => 'Negotiation',
        'closed_won' => 'Closed Won',
        'closed_lost' => 'Closed Lost',
        default => $status ?: '-',
    };
@endphp
<span class="status-badge-pill {{ $tone }}">
    <span class="status-dot {{ $tone }}"></span> {{ $label }}
</span>
