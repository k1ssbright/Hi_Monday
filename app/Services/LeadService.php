<?php

namespace App\Services;

use App\Models\Lead\Lead;
use App\Models\Lead\LeadAssignment;
use App\Models\Lead\LeadAssignmentRule;
use App\Models\Lead\LeadInboundTouch;
use App\Models\Lead\LeadStatusLog;
use Illuminate\Support\Carbon;

class LeadService
{
    public function allocateRegistrationNo(Lead $lead): void
    {
        if (filled($lead->registration_no)) {
            return;
        }

        $year = (int) ($lead->fiscal_year ?: now()->year);
        $prefix = 'LD-'.$year.'-';
        $last = Lead::query()
            ->withTrashed()
            ->where('business_id', $lead->business_id)
            ->where('registration_no', 'like', $prefix.'%')
            ->orderByDesc('registration_no')
            ->value('registration_no');

        $seq = 1;
        if (is_string($last) && preg_match('/(\d+)$/', $last, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        $lead->registration_no = $prefix.str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
        $lead->fiscal_year = $year;
        if (! $lead->registration_date) {
            $lead->registration_date = now()->toDateString();
        }
    }

    public function logStatus(Lead $lead, ?string $from, string $to, ?string $changedBy, ?int $reasonId = null, ?string $note = null): void
    {
        LeadStatusLog::create([
            'lead_id' => $lead->id,
            'from_status' => $from,
            'to_status' => $to,
            'lead_closed_reason_id' => $reasonId,
            'note' => $note,
            'changed_by' => $changedBy,
            'created_at' => now(),
        ]);
    }

    public function logAssignment(
        Lead $lead,
        ?string $fromAssigned,
        ?string $toAssigned,
        ?string $fromBranch,
        ?string $toBranch,
        string $reason,
        ?string $changedBy,
        ?string $note = null,
        ?int $ruleId = null,
    ): void {
        LeadAssignment::create([
            'lead_id' => $lead->id,
            'from_assigned_to' => $fromAssigned,
            'to_assigned_to' => $toAssigned,
            'from_business_branch_id' => $fromBranch,
            'to_business_branch_id' => $toBranch,
            'reason' => $reason,
            'note' => $note,
            'lead_assignment_rule_id' => $ruleId,
            'changed_by' => $changedBy,
            'created_at' => now(),
        ]);
    }

    public function applyMatchingRule(Lead $lead): ?LeadAssignmentRule
    {
        $rules = LeadAssignmentRule::query()
            ->where('business_id', $lead->business_id)
            ->where('is_active', true)
            ->orderBy('priority')
            ->orderBy('id')
            ->get();

        foreach ($rules as $rule) {
            if ($rule->lead_channel_id && (string) $rule->lead_channel_id !== (string) $lead->lead_channel_id) {
                continue;
            }
            if ($rule->marketing_ad_id && (string) $rule->marketing_ad_id !== (string) $lead->marketing_ad_id) {
                continue;
            }
            if ($rule->province_id && (string) $rule->province_id !== (string) $lead->province_id) {
                continue;
            }

            if ($rule->assign_business_branch_id) {
                $lead->business_branch_id = $rule->assign_business_branch_id;
            }
            if ($rule->assign_employee_branch_id) {
                $lead->assigned_to = $rule->assign_employee_branch_id;
            }

            return $rule;
        }

        return null;
    }

    public function recordFirstTouch(Lead $lead, ?string $createBy): void
    {
        LeadInboundTouch::create([
            'lead_id' => $lead->id,
            'lead_channel_id' => $lead->lead_channel_id,
            'marketing_ad_id' => $lead->marketing_ad_id,
            'marketing_ad_unit_id' => $lead->marketing_ad_unit_id,
            'contacted_at' => $lead->registration_date
                ? Carbon::parse($lead->registration_date)->startOfDay()
                : now(),
            'create_by' => $createBy,
            'created_at' => now(),
        ]);
    }
}
