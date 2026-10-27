<?php

namespace App\Services;

use App\Models\Partner\Partner;
use App\Models\Partner\PartnerBranch;
use App\Models\Partner\PartnerContact;
use App\Models\Partner\PartnerContactBranch;
use Illuminate\Support\Facades\DB;

class PartnerService
{
    public function create(string $businessId, ?string $createBy, array $data): Partner
    {
        return DB::transaction(function () use ($businessId, $createBy, $data) {
            $partner = Partner::create([
                'business_id' => $businessId,
                'partner_type' => (int) $data['partner_type'],
                'partner_tax_no' => $this->digits($data['partner_tax_no'] ?? null),
                'partner_vat_status' => (int) $data['partner_vat_status'],
                'partner_code' => trim((string) $data['partner_code']),
                'partner_name_th' => trim((string) $data['partner_name_th']),
                'create_by' => $createBy,
            ]);

            $roleFlags = PartnerBranch::roleFlags($data['partner_role'] ?? 'customer');
            $branchNo = $this->branchNo($data['partner_branch_no'] ?? null);
            $isHq = $branchNo === '00000';

            $branch = PartnerBranch::create([
                'partner_id' => $partner->id,
                'partner_status' => $isHq ? 0 : 1,
                'partner_branch_no' => $branchNo,
                'partner_branch_name_th' => trim((string) $data['partner_branch_name_th']),
                'partner_address' => trim((string) ($data['partner_address'] ?? '')),
                'partner_tel' => trim((string) ($data['partner_tel'] ?? '')),
                'partner_phone' => trim((string) ($data['partner_phone'] ?? '')) ?: null,
                'partner_fax' => trim((string) ($data['partner_fax'] ?? '')) ?: null,
                'is_customer' => $roleFlags['is_customer'],
                'is_vendor' => $roleFlags['is_vendor'],
                'active_status' => true,
                'create_by' => $createBy,
            ]);

            foreach ($this->usableContacts($data['contacts'] ?? []) as $contactData) {
                $contact = PartnerContact::create([
                    ...$this->contactPayload($contactData),
                    'partner_id' => $partner->id,
                    'create_by' => $createBy,
                ]);

                PartnerContactBranch::create([
                    'partner_contact_id' => $contact->id,
                    'partner_branch_id' => $branch->id,
                    'status' => true,
                    'create_by' => $createBy,
                ]);
            }

            return $partner->fresh(['branches', 'contacts']);
        });
    }

    public function updatePartner(Partner $partner, array $data): Partner
    {
        return DB::transaction(function () use ($partner, $data) {
            $partner->update([
                'partner_code' => trim((string) $data['partner_code']),
                'partner_name_th' => trim((string) $data['partner_name_th']),
                'partner_tax_no' => $this->digits($data['partner_tax_no'] ?? $partner->partner_tax_no),
            ]);

            if (isset($data['partner_role'])) {
                $flags = PartnerBranch::roleFlags($data['partner_role']);
                $partner->branches()->update($flags);
            }

            if (array_key_exists('active_status', $data)) {
                $partner->branches()->update([
                    'active_status' => (bool) $data['active_status'],
                ]);
            }

            return $partner->fresh(['branches', 'contacts']);
        });
    }

    public function updateBranch(Partner $partner, PartnerBranch $branch, array $data): PartnerBranch
    {
        return DB::transaction(function () use ($partner, $branch, $data) {
            $branchNo = $this->branchNo($data['partner_branch_no'] ?? $branch->partner_branch_no);
            $flags = PartnerBranch::roleFlags($data['partner_role'] ?? $branch->role());

            $branch->update([
                'partner_branch_no' => $branchNo,
                'partner_branch_name_th' => trim((string) ($data['partner_branch_name_th'] ?? $branch->partner_branch_name_th)),
                'partner_tel' => trim((string) ($data['partner_tel'] ?? '')) ?: $branch->partner_tel,
                'partner_phone' => array_key_exists('partner_phone', $data)
                    ? (trim((string) $data['partner_phone']) ?: null)
                    : $branch->partner_phone,
                'partner_status' => $branchNo === '00000' ? 0 : 1,
                'is_customer' => $flags['is_customer'],
                'is_vendor' => $flags['is_vendor'],
                'active_status' => array_key_exists('active_status', $data)
                    ? (bool) $data['active_status']
                    : $branch->active_status,
            ]);

            if (isset($data['partner_tax_no'])) {
                $partner->update([
                    'partner_tax_no' => $this->digits($data['partner_tax_no']),
                ]);
            }

            return $branch->fresh();
        });
    }

    public function toggleActive(Partner $partner, bool $active): Partner
    {
        $partner->branches()->update(['active_status' => $active]);

        return $partner->fresh(['branches']);
    }

    private function usableContacts(array $contacts): array
    {
        return array_values(array_filter($contacts, function ($contact): bool {
            if (! is_array($contact)) {
                return false;
            }

            return filled($contact['firstname'] ?? null) || filled($contact['surname'] ?? null);
        }));
    }

    private function contactPayload(array $contact): array
    {
        $prefix = (string) ($contact['prefix'] ?? 'นาย');
        $otherPrefix = trim((string) ($contact['other_prefix'] ?? ''));

        if ($prefix !== 'อื่นๆ') {
            $otherPrefix = '';
        }

        $email = trim((string) ($contact['email'] ?? ''));

        return [
            'email' => $email !== '' ? $email : null,
            'prefix' => $prefix,
            'other_prefix' => $otherPrefix !== '' ? $otherPrefix : null,
            'firstname' => trim((string) ($contact['firstname'] ?? '')),
            'surname' => trim((string) ($contact['surname'] ?? '')),
            'phone' => trim((string) ($contact['phone'] ?? '')) ?: null,
            'line_id' => trim((string) ($contact['line_id'] ?? '')) ?: null,
        ];
    }

    private function branchNo(?string $value): string
    {
        $digits = substr($this->digits($value) ?? '', 0, 5);

        return str_pad($digits !== '' ? $digits : '00000', 5, '0', STR_PAD_LEFT);
    }

    private function digits(?string $value): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);

        return $digits !== '' ? $digits : null;
    }
}
