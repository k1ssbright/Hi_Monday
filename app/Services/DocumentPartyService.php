<?php

namespace App\Services;

use App\Models\Address\Amphures;
use App\Models\Address\Provinces;
use App\Models\Address\Tambons;
use App\Models\Employee\EmployeeBranch;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerBranch;
use App\Models\Partner\PartnerContact;
use App\Models\Sales\Quotation;
use App\Models\Sales\QuotationRevision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class DocumentPartyService
{
    public function partnerTypeLabel(?int $type): string
    {
        return match ((int) $type) {
            1 => 'บริษัท',
            2 => 'ห้างหุ้นส่วน',
            3 => 'บุคคลธรรมดา',
            default => '',
        };
    }

    public function formatContactName(?PartnerContact $contact): string
    {
        if (! $contact) {
            return '';
        }

        return trim(collect([
            $contact->other_prefix ?: $contact->prefix,
            $contact->firstname,
            $contact->surname,
        ])->filter()->implode(' '));
    }

    public function formatUserName(?User $user): string
    {
        if (! $user) {
            return '';
        }

        return trim(collect([
            $user->other_prefix ?: $user->prefix,
            $user->firstname,
            $user->surname,
        ])->filter()->implode(' '));
    }

    public function formatAddressLine(
        ?string $address,
        ?int $tambonId,
        ?int $amphureId,
        ?int $provinceId,
    ): string {
        $parts = [];

        if (filled(trim((string) $address))) {
            $parts[] = trim((string) $address);
        }

        $tambon = $tambonId ? Tambons::query()->find($tambonId) : null;
        $amphure = $amphureId ? Amphures::query()->find($amphureId) : null;
        $province = $provinceId ? Provinces::query()->find($provinceId) : null;

        if ($tambon) {
            $parts[] = $tambon->name_th;
        }
        if ($amphure) {
            $parts[] = $amphure->name_th;
        }
        if ($province) {
            $parts[] = $province->name_th;
        }
        if ($tambon?->zip_code) {
            $parts[] = $tambon->zip_code;
        }

        return implode(' ', $parts);
    }

    /**
     * @return array<string, string|int|null>
     */
    public function customerDefaults(
        Partner $partner,
        ?PartnerContact $contact = null,
        ?PartnerBranch $branch = null,
    ): array {
        $partner->loadMissing(['contacts', 'branches']);

        $contact ??= $partner->contacts->first();
        $branch ??= $partner->branches->first();

        return [
            'customer_contact_name' => $this->formatContactName($contact),
            'customer_contact_phone' => (string) ($contact?->phone ?: ''),
            'customer_email' => (string) ($contact?->email ?: ''),
            'customer_type' => $partner->partner_type,
            'customer_tax_no' => (string) ($partner->partner_tax_no ?: ''),
            'customer_name' => (string) ($partner->partner_name_th ?: ''),
            'customer_status' => $branch?->partner_status ?? 0,
            'customer_branch_no' => (string) ($branch?->partner_branch_no ?: ''),
            'customer_branch_name' => (string) ($branch?->partner_branch_name_th ?: ''),
            'customer_address' => (string) ($branch?->partner_address ?: ''),
            'customer_province_id' => $branch?->province_id,
            'customer_amphure_id' => $branch?->amphure_id,
            'customer_tambon_id' => $branch?->tambon_id,
            'customer_zip_code' => $branch?->tambon_id
                ? (string) (Tambons::query()->find($branch->tambon_id)?->zip_code ?: '')
                : '',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function sellerDefaults(?string $employeeBranchId): array
    {
        if (blank($employeeBranchId)) {
            return [
                'seller_name' => '',
                'seller_phone' => '',
                'seller_email' => '',
            ];
        }

        $employeeBranch = EmployeeBranch::query()
            ->with('employee.user')
            ->find($employeeBranchId);

        $user = $employeeBranch?->employee?->user;

        return [
            'seller_name' => $this->formatUserName($user),
            'seller_phone' => (string) ($user?->phone ?: ''),
            'seller_email' => (string) ($user?->email ?: ''),
        ];
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @return array<string, mixed>
     */
    public function mergeWithOldInput(array $defaults): array
    {
        foreach ($defaults as $key => $value) {
            $defaults[$key] = old($key, $value);
        }

        return $defaults;
    }

    /**
     * สร้าง Partner จาก snapshot บน revision เมื่อเอกสารยังไม่ผูกลูกค้าในระบบ
     */
    public function linkPartnerFromRevisionSnapshot(
        Model $document,
        object $revision,
        ?string $createBy = null,
    ): Partner {
        if ($document->partner_id) {
            return Partner::query()->findOrFail($document->partner_id);
        }

        $customerName = trim((string) $revision->customer_name);
        if ($customerName === '') {
            $customerName = trim((string) $revision->customer_contact_name);
        }

        if ($customerName === '') {
            throw ValidationException::withMessages([
                'customer_name' => 'กรุณาระบุชื่อลูกค้าในใบเสนอราคาก่อนดำเนินการต่อ',
            ]);
        }

        $partnerType = (int) ($revision->customer_type ?: 3);
        if (! in_array($partnerType, [1, 2, 3], true)) {
            $partnerType = 3;
        }

        $partner = Partner::create([
            'business_id' => $document->business_id,
            'partner_type' => $partnerType,
            'partner_tax_no' => $revision->customer_tax_no,
            'partner_vat_status' => 0,
            'partner_name_th' => $customerName,
            'create_by' => $createBy,
        ]);

        $branchStatus = (int) ($revision->customer_status ?? 0);
        $isNaturalPerson = $partnerType === 3;

        $branch = PartnerBranch::create([
            'partner_id' => $partner->id,
            'partner_status' => $isNaturalPerson ? 0 : $branchStatus,
            'partner_branch_no' => $isNaturalPerson || $branchStatus === 0 ? null : $revision->customer_branch_no,
            'partner_branch_name_th' => $isNaturalPerson || $branchStatus === 0 ? null : $revision->customer_branch_name,
            'partner_address' => $revision->customer_address,
            'province_id' => $revision->customer_province_id,
            'amphure_id' => $revision->customer_amphure_id,
            'tambon_id' => $revision->customer_tambon_id,
            'create_by' => $createBy,
        ]);

        $contactName = trim((string) $revision->customer_contact_name);
        $contact = PartnerContact::create([
            'partner_id' => $partner->id,
            'email' => filled($revision->customer_email) ? $revision->customer_email : 'noreply@oneclick.local',
            'prefix' => 'นาย',
            'firstname' => $contactName !== '' ? $contactName : $customerName,
            'surname' => '-',
            'phone' => $revision->customer_contact_phone,
            'create_by' => $createBy,
        ]);

        $document->forceFill([
            'partner_id' => $partner->id,
            'partner_branch_id' => $branch->id,
            'partner_contact_id' => $contact->id,
        ])->save();

        return $partner;
    }
}
