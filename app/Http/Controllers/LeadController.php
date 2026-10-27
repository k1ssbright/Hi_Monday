<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Settings\Concerns\ResolvesBusinessContext;
use App\Models\Address\Amphures;
use App\Models\Address\Provinces;
use App\Models\Address\Tambons;
use App\Models\Business\BusinessBranch;
use App\Models\Employee\EmployeeBranch;
use App\Models\Lead\Lead;
use App\Models\Lead\LeadActivity;
use App\Models\Lead\LeadChannel;
use App\Models\Lead\LeadClosedReason;
use App\Models\Lead\LeadContact;
use App\Models\Lead\LeadProjectType;
use App\Models\Marketing\MarketingAd;
use App\Models\Partner\Partner;
use App\Models\Partner\PartnerBranch;
use App\Models\Partner\PartnerContact;
use App\Models\ProductService\ProductServiceCategory;
use App\Models\ProductService\ProductServiceItem;
use App\Models\ProductService\ProductServiceQuestion;
use App\Models\Sales\Opportunity;
use App\Models\Sales\OpportunityLine;
use App\Services\LeadService;
use App\Services\SalesFlowService;
use App\Services\SalesJourneyPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LeadController extends Controller
{
    use ResolvesBusinessContext;

    public function __construct(
        private LeadService $leads,
        private SalesFlowService $salesFlow,
    ) {}

    public function index(Request $request): View
    {
        $businessId = $this->businessId();
        $query = Lead::query()
            ->where('business_id', $businessId)
            ->with(['primaryContact', 'channel', 'assignedTo.employee.user', 'branch'])
            ->latest();

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q) {
                $builder->where('registration_no', 'like', "%{$q}%")
                    ->orWhere('organization_name', 'like', "%{$q}%")
                    ->orWhere('project_name', 'like', "%{$q}%")
                    ->orWhereHas('contacts', function ($c) use ($q) {
                        $c->where('firstname', 'like', "%{$q}%")
                            ->orWhere('surname', 'like', "%{$q}%")
                            ->orWhere('phone', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return view('office.leads.index', [
            'leads' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('office.leads.form', array_merge(
            $this->formLookups(),
            $this->locationCascadeLookups(),
            [
                'lead' => new Lead(['status' => 'draft', 'registration_date' => now()->toDateString()]),
            ],
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validatedLead($request);
        $payload['is_high_focus'] = $request->boolean('is_high_focus');
        $contact = $this->validatedPrimaryContact($request);

        $lead = DB::transaction(function () use ($payload, $contact, $request) {
            $actor = $this->createBy();
            $lead = new Lead($payload);
            $lead->business_id = $this->businessId();
            $lead->create_by = $actor;
            $lead->created_by = $actor;
            $lead->status = $request->input('status', 'draft');
            $this->prepareLead($lead, $actor, $request->boolean('skip_rules'));
            $lead->save();

            $this->leads->logStatus($lead, null, $lead->status, $actor);
            $this->leads->logAssignment($lead, null, $lead->assigned_to, null, $lead->business_branch_id, $lead->assigned_to ? 'manual' : 'created', $actor);
            $this->leads->recordFirstTouch($lead, $actor);

            LeadContact::create(array_merge($contact, [
                'business_id' => $lead->business_id,
                'lead_id' => $lead->id,
                'is_primary' => true,
                'create_by' => $actor,
            ]));

            $this->syncIntake($lead, $request);

            return $lead;
        });

        return redirect()->route('office.leads.show', $lead)->with('success', 'สร้าง Lead เรียบร้อยแล้ว');
    }

    public function show(Lead $lead): View
    {
        $this->assertOwned($lead);
        $lead->load([
            'contacts', 'channel', 'branch', 'assignedTo.employee.user', 'projectType',
            'category', 'item', 'marketingAd', 'adUnit', 'closedReason', 'province',
            'activities.performer.employee.user', 'statusLogs.changedBy.employee.user', 'statusLogs.reason',
            'assignments.toAssignee.employee.user', 'inboundTouches.channel', 'intakeAnswers',
        ]);

        $salesJourney = app(SalesJourneyPresenter::class)->for($lead);

        return view('office.leads.show', array_merge($this->formLookups(), [
            'lead' => $lead,
            'salesJourney' => $salesJourney,
        ]));
    }

    public function edit(Lead $lead): View
    {
        $this->assertOwned($lead);
        if ($lead->isLocked()) {
            abort(422, 'Lead นี้ถูกล็อกแล้ว กรุณาแก้ไขผ่าน Partner หรือ Opportunity');
        }
        $lead->load(['primaryContact', 'intakeAnswers']);

        return view('office.leads.form', array_merge(
            $this->formLookups(),
            $this->locationCascadeLookups($lead),
            compact('lead'),
        ));
    }

    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $this->assertOwned($lead);
        if ($lead->isLocked()) {
            abort(422, 'Lead นี้ถูกล็อกแล้ว');
        }

        $payload = $this->validatedLead($request);
        $payload['is_high_focus'] = $request->boolean('is_high_focus');
        $contact = $this->validatedPrimaryContact($request);
        $fromStatus = $lead->status;
        $fromAssigned = $lead->assigned_to;
        $fromBranch = $lead->business_branch_id;

        DB::transaction(function () use ($lead, $payload, $contact, $request, $fromStatus, $fromAssigned, $fromBranch) {
            $actor = $this->createBy();
            $lead->fill($payload);
            $newStatus = $request->input('status', $lead->status);
            if (! in_array($newStatus, Lead::STATUSES, true)) {
                $newStatus = $lead->status;
            }
            $lead->status = $newStatus;
            $this->prepareLead($lead, $actor, true);
            $lead->save();

            if ($fromStatus !== $lead->status) {
                $this->leads->logStatus($lead, $fromStatus, $lead->status, $actor);
            }
            if ($fromAssigned !== $lead->assigned_to || $fromBranch !== $lead->business_branch_id) {
                $this->leads->logAssignment($lead, $fromAssigned, $lead->assigned_to, $fromBranch, $lead->business_branch_id, 'manual', $actor);
            }

            $primary = $lead->primaryContact ?: new LeadContact(['lead_id' => $lead->id, 'business_id' => $lead->business_id, 'is_primary' => true]);
            $primary->fill($contact);
            $primary->is_primary = true;
            $primary->business_id = $lead->business_id;
            $primary->lead_id = $lead->id;
            $primary->save();

            $this->syncIntake($lead, $request);
        });

        return redirect()->route('office.leads.show', $lead)->with('success', 'บันทึกข้อมูล Lead เรียบร้อยแล้ว');
    }

    public function destroy(Lead $lead): RedirectResponse
    {
        $this->assertOwned($lead);
        $lead->delete();

        return redirect()->route('office.leads.index')->with('success', 'ลบข้อมูล Lead เรียบร้อยแล้ว');
    }

    public function changeStatus(Request $request, Lead $lead): RedirectResponse
    {
        $this->assertOwned($lead);
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', Lead::STATUSES)],
            'lead_closed_reason_id' => ['nullable', 'integer'],
            'closed_note' => ['nullable', 'string'],
            'nurture_review_at' => ['nullable', 'date'],
            'duplicate_of_lead_id' => ['nullable', 'uuid'],
        ]);

        $to = $data['status'];
        if ($to === 'converted') {
            throw ValidationException::withMessages(['status' => 'ยังไม่สามารถ Convert ได้ในสถานะนี้']);
        }
        if ($to === 'unqualified' && empty($data['lead_closed_reason_id'])) {
            throw ValidationException::withMessages(['lead_closed_reason_id' => 'กรุณาเลือกเหตุผลปิด Lead']);
        }

        $from = $lead->status;
        $lead->status = $to;
        $lead->lead_closed_reason_id = $data['lead_closed_reason_id'] ?? $lead->lead_closed_reason_id;
        $lead->closed_note = $data['closed_note'] ?? $lead->closed_note;
        $lead->nurture_review_at = $data['nurture_review_at'] ?? $lead->nurture_review_at;
        $lead->duplicate_of_lead_id = $data['duplicate_of_lead_id'] ?? $lead->duplicate_of_lead_id;
        if (in_array($to, ['unqualified', 'duplicate', 'archived'], true)) {
            $lead->closed_at = now();
        }
        if ($to === 'new' && $from === 'draft') {
            $this->leads->allocateRegistrationNo($lead);
        }
        $lead->save();
        $this->leads->logStatus($lead, $from, $to, $this->createBy(), $lead->lead_closed_reason_id, $lead->closed_note);

        return back()->with('success', 'ปรับสถานะ Lead เรียบร้อยแล้ว');
    }

    public function assign(Request $request, Lead $lead): RedirectResponse
    {
        $this->assertOwned($lead);
        $data = $request->validate([
            'assigned_to' => ['nullable', 'uuid'],
            'business_branch_id' => ['nullable', 'uuid'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);
        $fromA = $lead->assigned_to;
        $fromB = $lead->business_branch_id;
        $lead->assigned_to = $data['assigned_to'] ?? null;
        if (! empty($data['business_branch_id'])) {
            $lead->business_branch_id = $data['business_branch_id'];
        }
        $lead->save();
        $this->leads->logAssignment($lead, $fromA, $lead->assigned_to, $fromB, $lead->business_branch_id, 'manual', $this->createBy(), $data['note'] ?? null);

        return back()->with('success', 'มอบหมายผู้รับผิดชอบเรียบร้อยแล้ว');
    }

    public function storeContact(Request $request, Lead $lead): RedirectResponse
    {
        $this->assertOwned($lead);
        $data = $request->validate([
            'prefix' => ['nullable', 'in:'.implode(',', LeadContact::PREFIXES)],
            'firstname' => ['required', 'string', 'max:255'],
            'surname' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'line_id' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
        ], [
            'firstname.required' => 'กรุณากรอกชื่อผู้ติดต่อ',
            'firstname.string' => 'ชื่อผู้ติดต่อต้องเป็นข้อความ',
            'firstname.max' => 'ชื่อผู้ติดต่อต้องยาวไม่เกิน 255 ตัวอักษร',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
        ]);
        if (empty($data['phone']) && empty($data['email']) && empty($data['line_id'])) {
            throw ValidationException::withMessages(['phone' => 'กรุณาระบุช่องทางติดต่ออย่างน้อย 1 รายการ (เบอร์โทรศัพท์, อีเมล หรือ Line)']);
        }
        $makePrimary = $request->boolean('is_primary') || $lead->contacts()->doesntExist();
        if ($makePrimary) {
            $lead->contacts()->update(['is_primary' => false]);
        }
        LeadContact::create($data + [
            'business_id' => $lead->business_id,
            'lead_id' => $lead->id,
            'is_primary' => $makePrimary,
            'create_by' => $this->createBy(),
        ]);

        return back()->with('success', 'เพิ่มข้อมูลผู้ติดต่อเรียบร้อยแล้ว');
    }

    public function storeActivity(Request $request, Lead $lead): RedirectResponse
    {
        $this->assertOwned($lead);
        $data = $request->validate([
            'type' => ['required', 'in:'.implode(',', LeadActivity::TYPES)],
            'direction' => ['nullable', 'in:inbound,outbound'],
            'occurred_at' => ['required', 'date'],
            'due_at' => ['nullable', 'date'],
            'summary' => ['required', 'string'],
            'outcome' => ['nullable', 'string', 'max:255'],
            'next_action' => ['nullable', 'string'],
            'next_action_at' => ['nullable', 'date'],
            'lead_contact_id' => ['nullable', 'uuid'],
        ], [
            'type.required' => 'กรุณาเลือกประเภทกิจกรรม',
            'occurred_at.required' => 'กรุณาระบุวัน/เวลาที่เกิดขึ้น',
            'summary.required' => 'กรุณากรอกสรุปรายละเอียดกิจกรรม',
        ]);
        $actor = $this->createBy();
        $activity = LeadActivity::create($data + [
            'business_id' => $lead->business_id,
            'lead_id' => $lead->id,
            'performed_by' => $actor,
            'create_by' => $actor,
        ]);
        if (! $lead->first_activity_at && in_array($activity->type, LeadActivity::CONTACT_TYPES, true)) {
            $lead->first_activity_at = $activity->occurred_at;
            if ($lead->status === 'new') {
                $from = $lead->status;
                $lead->status = 'contacting';
                $this->leads->logStatus($lead, $from, 'contacting', $actor, null, 'activity');
            }
            $lead->save();
        }

        return back()->with('success', 'บันทึกกิจกรรมเรียบร้อยแล้ว');
    }

    public function updateActivity(Request $request, Lead $lead, LeadActivity $activity): RedirectResponse
    {
        $this->assertOwned($lead);
        if ($activity->lead_id !== $lead->id) {
            abort(404);
        }

        $data = $request->validate([
            'type' => ['required', 'in:'.implode(',', LeadActivity::TYPES)],
            'direction' => ['nullable', 'in:inbound,outbound'],
            'occurred_at' => ['required', 'date'],
            'due_at' => ['nullable', 'date'],
            'summary' => ['required', 'string'],
            'outcome' => ['nullable', 'string', 'max:255'],
            'next_action' => ['nullable', 'string'],
            'next_action_at' => ['nullable', 'date'],
            'lead_contact_id' => ['nullable', 'uuid'],
        ]);

        $activity->update($data);

        return back()->with('success', 'แก้ไขกิจกรรมเรียบร้อยแล้ว');
    }

    public function destroyActivity(Lead $lead, LeadActivity $activity): RedirectResponse
    {
        $this->assertOwned($lead);
        if ($activity->lead_id !== $lead->id) {
            abort(404);
        }

        $activity->delete();

        return back()->with('success', 'ลบกิจกรรมเรียบร้อยแล้ว');
    }

    public function convert(Request $request, Lead $lead): RedirectResponse
    {
        $this->assertOwned($lead);
        if ($lead->status === 'converted' && $lead->converted_opportunity_id) {
            return redirect()
                ->route('office.opportunities.show', $lead->converted_opportunity_id)
                ->with('success', 'Lead นี้ถูกแปลงเป็น Opportunity เรียบร้อยแล้ว');
        }
        if (! in_array($lead->status, ['qualified', 'contacting', 'new', 'nurturing'], true)) {
            throw ValidationException::withMessages(['status' => 'Lead ต้องอยู่ในสถานะที่สามารถแปลงเป็น Opportunity ได้']);
        }

        $data = $request->validate([
            'partner_id' => ['nullable', 'uuid'],
            'partner_name_th' => ['nullable', 'string', 'max:255'],
            'partner_type' => ['nullable', 'integer', 'in:1,2,3'],
            'partner_tax_no' => ['nullable', 'string', 'max:13'],
            'opportunity_name' => ['nullable', 'string', 'max:255'],
            'expected_close_date' => ['nullable', 'date'],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        $opportunity = DB::transaction(function () use ($lead, $data) {
            $businessId = (string) $lead->business_id;
            $actor = $this->createBy();

            $partner = null;
            if (! empty($data['partner_id'])) {
                $partner = Partner::query()
                    ->where('business_id', $businessId)
                    ->find($data['partner_id']);
            }
            if (! $partner) {
                $partnerName = $data['partner_name_th'] ?: $lead->organization_name ?: ($lead->primaryContact?->displayName() ?? 'ลูกค้าใหม่');
                $partner = Partner::create([
                    'business_id' => $businessId,
                    'partner_type' => (int) ($data['partner_type'] ?? $lead->organization_type ?? 3),
                    'partner_tax_no' => $data['partner_tax_no'] ?? $lead->tax_no,
                    'partner_vat_status' => 0,
                    'partner_name_th' => $partnerName,
                    'create_by' => $actor,
                ]);
            }

            $partnerBranch = $partner->branches()->first();
            if (! $partnerBranch) {
                $partnerBranch = PartnerBranch::create([
                    'partner_id' => $partner->id,
                    'partner_status' => 0,
                    'partner_branch_no' => '00000',
                    'partner_branch_name_th' => 'สำนักงานใหญ่',
                    'partner_address' => $lead->address,
                    'province_id' => $lead->province_id,
                    'amphure_id' => $lead->amphure_id,
                    'tambon_id' => $lead->tambon_id,
                    'create_by' => $actor,
                ]);
            }

            $primaryContact = $lead->primaryContact;
            $partnerContact = null;
            if ($primaryContact) {
                $partnerContact = $partner->contacts()
                    ->where(function ($query) use ($primaryContact): void {
                        if ($primaryContact->email) {
                            $query->orWhere('email', $primaryContact->email);
                        }
                        if ($primaryContact->phone) {
                            $query->orWhere('phone', $primaryContact->phone);
                        }
                    })
                    ->first();

                if (! $partnerContact) {
                    $partnerContact = PartnerContact::create([
                        'partner_id' => $partner->id,
                        'email' => $primaryContact->email ?: null,
                        'prefix' => $primaryContact->prefix ?: 'นาย',
                        'firstname' => $primaryContact->firstname,
                        'surname' => $primaryContact->surname ?: '-',
                        'phone' => $primaryContact->phone,
                        'line_id' => $primaryContact->line_id,
                        'address' => $lead->address,
                        'province_id' => $lead->province_id,
                        'amphure_id' => $lead->amphure_id,
                        'tambon_id' => $lead->tambon_id,
                        'create_by' => $actor,
                    ]);
                }
            }

            $opportunity = new Opportunity([
                'business_id' => $businessId,
                'business_branch_id' => $lead->business_branch_id,
                'name' => $data['opportunity_name']
                    ?: $lead->project_name
                    ?: 'Opportunity สำหรับ '.$partner->partner_name_th,
                'stage' => 'discovery',
                'probability' => 10,
                'expected_close_date' => $data['expected_close_date'] ?? now()->addDays(30)->toDateString(),
                'currency' => 'THB',
                'estimated_value' => $data['estimated_value'] ?? 0,
                'partner_id' => $partner->id,
                'partner_branch_id' => $partnerBranch->id,
                'partner_contact_id' => $partnerContact?->id,
                'owner_employee_branch_id' => $lead->assigned_to ?: $actor,
                'converted_from_lead_id' => $lead->id,
                'lead_channel_id' => $lead->lead_channel_id,
                'marketing_ad_id' => $lead->marketing_ad_id,
                'marketing_ad_unit_id' => $lead->marketing_ad_unit_id,
                'create_by' => $actor,
            ]);
            $this->salesFlow->allocateOpportunityNo($opportunity);
            $opportunity->save();

            OpportunityLine::create([
                'business_id' => $businessId,
                'opportunity_id' => $opportunity->id,
                'product_service_item_id' => $lead->product_service_item_id,
                'description' => $lead->project_scope ?: ($lead->project_name ?: 'ความต้องการจาก Lead '.$lead->registration_no),
                'quantity' => 1,
                'unit' => 'งาน',
                'unit_price_estimate' => (float) ($data['estimated_value'] ?? 0),
                'line_total_estimate' => (float) ($data['estimated_value'] ?? 0),
                'create_by' => $actor,
            ]);

            $fromStatus = $lead->status;
            $lead->status = 'converted';
            $lead->converted_at = now();
            $lead->converted_opportunity_id = $opportunity->id;
            $lead->matched_partner_id = $partner->id;
            $lead->save();
            $this->leads->logStatus($lead, $fromStatus, 'converted', $actor, null, 'convert');

            return $opportunity;
        });

        return redirect()
            ->route('office.opportunities.show', $opportunity)
            ->with('success', 'แปลง Lead เป็น Opportunity เรียบร้อยแล้ว');
    }

    private function prepareLead(Lead $lead, ?string $actor, bool $skipRules): void
    {
        if ($lead->status !== 'draft') {
            if (! $lead->business_branch_id) {
                throw ValidationException::withMessages(['business_branch_id' => 'กรุณาระบุสาขาก่อนเปลี่ยนสถานะจากร่าง']);
            }
            if (! $lead->lead_channel_id) {
                throw ValidationException::withMessages(['lead_channel_id' => 'กรุณาระบุช่องทางรับ Lead ก่อนเปลี่ยนสถานะจากร่าง']);
            }
            $this->leads->allocateRegistrationNo($lead);
        }
        if (! $skipRules && ! $lead->assigned_to) {
            $rule = $this->leads->applyMatchingRule($lead);
            if ($rule) {
                // assignment log is written by caller with reason created; fine for first save
            }
        }
        $lead->recomputeTier();
    }

    private function syncIntake(Lead $lead, Request $request): void
    {
        $answers = $request->input('intake', []);
        if (! is_array($answers)) {
            return;
        }
        $lead->intakeAnswers()->delete();
        foreach ($answers as $questionId => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $question = ProductServiceQuestion::query()->find($questionId);
            $lead->intakeAnswers()->create([
                'product_service_question_id' => $question?->id,
                'label' => $question?->label ?? (string) $questionId,
                'value' => is_array($value) ? implode(', ', $value) : (string) $value,
            ]);
        }
    }

    private function validatedLead(Request $request): array
    {
        return $request->validate([
            'business_branch_id' => ['nullable', 'uuid'],
            'registration_date' => ['nullable', 'date'],
            'lead_channel_id' => ['nullable', 'integer'],
            'marketing_ad_id' => ['nullable', 'integer'],
            'marketing_ad_unit_id' => ['nullable', 'integer'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'lead_project_type_id' => ['nullable', 'integer'],
            'product_service_category_id' => ['nullable', 'integer'],
            'product_service_item_id' => ['nullable', 'integer'],
            'organization_name' => ['nullable', 'string', 'max:255'],
            'organization_type' => ['nullable', 'integer', 'in:1,2,3'],
            'tax_no' => ['nullable', 'string', 'max:13'],
            'address' => ['nullable', 'string'],
            'province_id' => ['nullable', 'integer', 'exists:provinces,id'],
            'amphure_id' => ['nullable', 'integer', 'exists:amphures,id'],
            'tambon_id' => ['nullable', 'integer', 'exists:tambons,id'],
            'project_name' => ['nullable', 'string', 'max:255'],
            'project_scope' => ['nullable', 'string'],
            'quality_demand' => ['nullable', 'integer', 'min:1', 'max:4'],
            'quality_competition' => ['nullable', 'integer', 'min:1', 'max:4'],
            'quality_authority' => ['nullable', 'integer', 'min:1', 'max:4'],
            'quality_budget_fit' => ['nullable', 'integer', 'min:1', 'max:4'],
            'is_high_focus' => ['sometimes', 'boolean'],
            'assigned_to' => ['nullable', 'uuid'],
            'status' => ['nullable', 'in:'.implode(',', Lead::STATUSES)],
        ]);
    }

    private function validatedPrimaryContact(Request $request): array
    {
        $data = $request->validate([
            'contact_prefix' => ['nullable', 'in:'.implode(',', LeadContact::PREFIXES)],
            'contact_firstname' => ['required', 'string', 'max:255'],
            'contact_surname' => ['nullable', 'string', 'max:255'],
            'contact_position' => ['nullable', 'string', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email'],
            'contact_line_id' => ['nullable', 'string', 'max:100'],
        ], [
            'contact_firstname.required' => 'กรุณากรอกชื่อผู้ติดต่อหลัก',
            'contact_firstname.string' => 'ชื่อผู้ติดต่อหลักต้องเป็นข้อความ',
            'contact_firstname.max' => 'ชื่อผู้ติดต่อหลักต้องยาวไม่เกิน 255 ตัวอักษร',
            'contact_email.email' => 'รูปแบบอีเมลผู้ติดต่อหลักไม่ถูกต้อง',
        ]);
        $mapped = [
            'prefix' => $data['contact_prefix'] ?? null,
            'firstname' => $data['contact_firstname'],
            'surname' => $data['contact_surname'] ?? null,
            'position' => $data['contact_position'] ?? null,
            'phone' => $data['contact_phone'] ?? null,
            'email' => $data['contact_email'] ?? null,
            'line_id' => $data['contact_line_id'] ?? null,
        ];
        if ($request->input('status', 'draft') !== 'draft' && ! ($mapped['phone'] || $mapped['email'] || $mapped['line_id'])) {
            throw ValidationException::withMessages(['contact_phone' => 'กรุณาระบุช่องทางติดต่อกลับอย่างน้อย 1 รายการ']);
        }

        return $mapped;
    }

    private function formLookups(): array
    {
        $businessId = $this->businessId();

        return [
            'channels' => LeadChannel::query()->where('business_id', $businessId)->where('is_active', true)->orderBy('sort')->get(),
            'branches' => BusinessBranch::query()->where('business_id', $businessId)->orderBy('business_branch_name_th')->get(),
            'employees' => EmployeeBranch::query()->whereHas('branch', fn ($q) => $q->where('business_id', $businessId))->with('employee.user')->get(),
            'projectTypes' => LeadProjectType::query()->where('business_id', $businessId)->where('is_active', true)->orderBy('sort')->get(),
            'categories' => ProductServiceCategory::query()->where('business_id', $businessId)->where('is_active', true)->orderBy('name')->get(),
            'items' => ProductServiceItem::query()->where('business_id', $businessId)->orderBy('name')->get(),
            'ads' => MarketingAd::query()->where('business_id', $businessId)->orderBy('name')->get(),
            'provinces' => Provinces::query()->orderBy('name_th')->get(),
            'closedReasons' => LeadClosedReason::query()->where('business_id', $businessId)->where('is_active', true)->orderBy('sort')->get(),
            'questions' => ProductServiceQuestion::query()->where('business_id', $businessId)->where('purpose', 'intake')->where('is_active', true)->orderBy('sort_order')->get(),
            'partners' => Partner::query()->where('business_id', $businessId)->orderBy('partner_name_th')->limit(50)->get(),
        ];
    }

    private function locationCascadeLookups(?Lead $lead = null): array
    {
        $provinceId = old('province_id', $lead?->province_id);
        $amphureId = old('amphure_id', $lead?->amphure_id);
        $tambonId = old('tambon_id', $lead?->tambon_id);

        $amphures = $provinceId
            ? Amphures::query()->where('province_id', $provinceId)->orderBy('name_th')->get()
            : collect();

        $tambons = $amphureId
            ? Tambons::query()->where('amphure_id', $amphureId)->orderBy('name_th')->get()
            : collect();

        $selectedZipCode = '';
        if ($tambonId) {
            $selectedZipCode = Tambons::query()->find($tambonId)?->zip_code ?? '';
        }

        return [
            'amphures' => $amphures,
            'tambons' => $tambons,
            'selectedZipCode' => $selectedZipCode,
        ];
    }

    private function assertOwned(Lead $lead): void
    {
        if ((string) $lead->business_id !== $this->businessId()) {
            abort(403);
        }
    }
}
