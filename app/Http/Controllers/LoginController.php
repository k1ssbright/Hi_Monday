<?php

namespace App\Http\Controllers;

use App\Models\Address\Amphures;
use App\Models\Address\Provinces;
use App\Models\Address\Tambons;
use App\Models\Business\Business;
use App\Models\Business\BusinessBranch;
use App\Models\Business\BusinessUser;
use App\Models\Employee\Employee;
use App\Models\Employee\EmployeeBranch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('public.login.index');
    }

    /**
     * Show the register form.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $selectedProvinceName = '';
        $selectedAmphureName = '';
        $selectedTambonName = '';
        $selectedZipCode = '';

        if (old('tambon_id')) {
            $tambon = Tambons::query()
                ->with('amphure.province')
                ->find(old('tambon_id'));

            if ($tambon) {
                $selectedTambonName = $tambon->name_th;
                $selectedAmphureName = $tambon->amphure?->name_th ?? '';
                $selectedProvinceName = $tambon->amphure?->province?->name_th ?? '';
                $selectedZipCode = $tambon->zip_code ?? '';
            }
        }

        return view('public.register.index', compact(
            'selectedProvinceName',
            'selectedAmphureName',
            'selectedTambonName',
            'selectedZipCode',
        ));
    }

    /**
     * Handle authentication attempt.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'branch_code' => ['nullable', 'string', 'max:64'],
        ]);

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (! $user) {
                return redirect()->route('home');
            }

            try {
                $routeParams = $this->syncLoginContext($user, $request, $validated['branch_code'] ?? null);
            } catch (ValidationException $exception) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                throw $exception;
            }

            if ($routeParams) {
                return redirect()->route('app.home', $routeParams);
            }

            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
        ])->onlyInput('email');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->forget([
            'mainBusinessID',
            'mainBusinessBranchID',
            'mainEmployeeBranchID',
        ]);
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Process Step 1 of registration and redirect to Step 2.
     */
    public function processStep1(Request $request)
    {
        $validated = $request->validate([
            'prefix' => ['required', 'string', 'max:50'],
            'firstname' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'tax_no' => ['required', 'string', 'max:20'],
            'birthday' => ['required', 'date'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'province_id' => ['required', 'integer', 'exists:provinces,id'],
            'amphure_id' => ['required', 'integer', 'exists:amphures,id'],
            'tambon_id' => ['required', 'integer', 'exists:tambons,id'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'prefix.required' => 'กรุณากรอกคำนำหน้า',
            'firstname.required' => 'กรุณากรอกชื่อจริง',
            'surname.required' => 'กรุณากรอกนามสกุล',
            'tax_no.required' => 'กรุณากรอกเลขประจำตัวผู้เสียภาษี',
            'birthday.required' => 'กรุณากรอกวันเกิด',
            'birthday.date' => 'รูปแบบวันเกิดไม่ถูกต้อง',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานในระบบแล้ว',
            'phone.required' => 'กรุณากรอกเบอร์โทรศัพท์',
            'address.required' => 'กรุณากรอกที่อยู่',
            'province_id.required' => 'กรุณาเลือกจังหวัด',
            'amphure_id.required' => 'กรุณาเลือกอำเภอ/เขต',
            'tambon_id.required' => 'กรุณาเลือกตำบล/แขวง',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.confirmed' => 'รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน',
            'password' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร ประกอบด้วยตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก ตัวเลข และอักขระพิเศษ',
        ]);

        $validated['password_confirmation'] = $request->input('password_confirmation');

        $request->session()->put('register_step1', $validated);

        return redirect()->route('register.step2');
    }

    public function showCreateBusinessForm(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        $step1 = session('register_step1');
        if (! $step1 && ! old('prefix')) {
            return redirect()->route('register');
        }

        $provinces = Provinces::query()->orderBy('name_th')->get();
        $amphures = collect();
        $tambons = collect();

        $selectedProvinceId = old('business_province_id', session('register_step1.province_id'));
        $selectedAmphureId = old('business_amphure_id', session('register_step1.amphure_id'));
        $selectedTambonId = old('business_tambon_id', session('register_step1.tambon_id'));

        $selectedProvinceName = '';
        $selectedAmphureName = '';
        $selectedTambonName = '';
        $selectedZipCode = old('business_zip_code', '');

        if ($selectedTambonId) {
            $tambon = Tambons::query()
                ->with('amphure.province')
                ->find($selectedTambonId);

            if ($tambon) {
                $selectedTambonName = $tambon->name_th;
                $selectedAmphureName = $tambon->amphure?->name_th ?? '';
                $selectedProvinceName = $tambon->amphure?->province?->name_th ?? '';
                $selectedZipCode = $selectedZipCode ?: ($tambon->zip_code ?? '');
            }
        }

        if ($selectedProvinceId) {
            $amphures = Amphures::query()
                ->where('province_id', $selectedProvinceId)
                ->orderBy('name_th')
                ->get();
        }

        if ($selectedAmphureId) {
            $tambons = Tambons::query()
                ->where('amphure_id', $selectedAmphureId)
                ->orderBy('name_th')
                ->get();
        }

        $selectedZipCode = old('business_zip_code', $selectedZipCode);

        return view('public.register.create-business', compact(
            'provinces',
            'amphures',
            'tambons',
            'selectedProvinceId',
            'selectedAmphureId',
            'selectedTambonId',
            'selectedProvinceName',
            'selectedAmphureName',
            'selectedTambonName',
            'selectedZipCode'
        ));
    }

    /**
     * Handle registration, create user+business+employee, then login.
     */
    public function register(Request $request)
    {
        $step1 = session('register_step1', []);
        $mergedInput = array_merge($step1, $request->all());
        $request->merge($mergedInput);

        $data = $request->validate([
            'prefix' => ['required', 'string', 'max:50'],
            'firstname' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'tax_no' => ['required', 'string', 'max:20'],
            'birthday' => ['required', 'date'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'province_id' => ['required', 'integer', 'exists:provinces,id'],
            'amphure_id' => ['required', 'integer', 'exists:amphures,id'],
            'tambon_id' => ['required', 'integer', 'exists:tambons,id'],
            'business_name_th' => ['required', 'string', 'max:255'],
            'business_type' => ['required', 'in:1,2,3'],
            'business_vat_status' => ['required', 'in:0,1'],
            'business_address' => ['required', 'string', 'max:500'],
            'business_province_id' => ['required', 'integer', 'exists:provinces,id'],
            'business_amphure_id' => ['required', 'integer', 'exists:amphures,id'],
            'business_tambon_id' => ['required', 'integer', 'exists:tambons,id'],
            'business_account_finance_year' => ['required', 'integer', 'between:1,12'],
            'is_headquarter' => ['nullable', 'in:0,1'],
            'business_branch_no' => ['nullable', 'string', 'max:20'],
            'business_branch_name_th' => ['nullable', 'string', 'max:255'],
            'accept_terms_privacy' => ['accepted'],
            'accept_data_accuracy' => ['accepted'],
            'accept_uploaded_media_use' => ['accepted'],
            'accept_disclaimer' => ['accepted'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'prefix.required' => 'กรุณากรอกคำนำหน้า',
            'firstname.required' => 'กรุณากรอกชื่อจริง',
            'surname.required' => 'กรุณากรอกนามสกุล',
            'tax_no.required' => 'กรุณากรอกเลขประจำตัวผู้เสียภาษี',
            'birthday.required' => 'กรุณากรอกวันเกิด',
            'birthday.date' => 'รูปแบบวันเกิดไม่ถูกต้อง',
            'email.required' => 'กรุณากรอกอีเมล',
            'email.email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'email.unique' => 'อีเมลนี้ถูกใช้งานในระบบแล้ว',
            'phone.required' => 'กรุณากรอกเบอร์โทรศัพท์',
            'address.required' => 'กรุณากรอกที่อยู่',
            'province_id.required' => 'กรุณาเลือกจังหวัด',
            'amphure_id.required' => 'กรุณาเลือกอำเภอ/เขต',
            'tambon_id.required' => 'กรุณาเลือกตำบล/แขวง',
            'business_name_th.required' => 'กรุณากรอกชื่อธุรกิจ',
            'business_type.required' => 'กรุณาเลือกประเภทธุรกิจ',
            'business_vat_status.required' => 'กรุณาเลือกสถานะจดภาษีมูลค่าเพิ่ม',
            'business_address.required' => 'กรุณากรอกที่อยู่ธุรกิจ',
            'business_province_id.required' => 'กรุณาเลือกจังหวัดของธุรกิจ',
            'business_amphure_id.required' => 'กรุณาเลือกอำเภอ/เขตของธุรกิจ',
            'business_tambon_id.required' => 'กรุณาเลือกตำบล/แขวงของธุรกิจ',
            'business_account_finance_year.required' => 'กรุณาเลือกเดือนเริ่มต้นรอบบัญชี',
            'business_account_finance_year.between' => 'เดือนเริ่มต้นรอบบัญชีไม่ถูกต้อง',
            'accept_terms_privacy.accepted' => 'กรุณายอมรับข้อกำหนดการใช้บริการและนโยบายความเป็นส่วนตัว',
            'accept_data_accuracy.accepted' => 'กรุณายืนยันความถูกต้องของข้อมูลและอำนาจในการลงทะเบียน',
            'accept_uploaded_media_use.accepted' => 'กรุณายินยอมให้ระบบใช้ไฟล์ที่อัปโหลดบนเอกสารในระบบ',
            'accept_disclaimer.accepted' => 'กรุณารับทราบข้อจำกัดความรับผิดชอบของระบบ',
            'password.required' => 'กรุณากรอกรหัสผ่าน',
            'password.confirmed' => 'รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน',
            'password' => 'รหัสผ่านต้องมีอย่างน้อย 8 ตัวอักษร ประกอบด้วยตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก ตัวเลข และอักขระพิเศษ',
        ]);

        $amphure = Amphures::query()->find($data['amphure_id']);
        if (! $amphure || (int) $amphure->province_id !== (int) $data['province_id']) {
            throw ValidationException::withMessages([
                'amphure_id' => 'อำเภอ/เขตไม่ตรงกับจังหวัดที่เลือก',
            ]);
        }

        $tambon = Tambons::query()->find($data['tambon_id']);
        if (! $tambon || (int) $tambon->amphure_id !== (int) $data['amphure_id']) {
            throw ValidationException::withMessages([
                'tambon_id' => 'ตำบล/แขวงไม่ตรงกับอำเภอ/เขตที่เลือก',
            ]);
        }

        $businessAmphure = Amphures::query()->find($data['business_amphure_id']);
        if (! $businessAmphure || (int) $businessAmphure->province_id !== (int) $data['business_province_id']) {
            throw ValidationException::withMessages([
                'business_amphure_id' => 'อำเภอ/เขตของธุรกิจไม่ตรงกับจังหวัดที่เลือก',
            ]);
        }

        $businessTambon = Tambons::query()->find($data['business_tambon_id']);
        if (! $businessTambon || (int) $businessTambon->amphure_id !== (int) $data['business_amphure_id']) {
            throw ValidationException::withMessages([
                'business_tambon_id' => 'ตำบล/แขวงของธุรกิจไม่ตรงกับอำเภอ/เขตที่เลือก',
            ]);
        }

        $businessName = $data['business_name_th'];
        $businessType = (int) $data['business_type'];
        $businessVatStatus = (int) $data['business_vat_status'];
        $password = $data['password'];
        $financeYearMonth = (int) $data['business_account_finance_year'];
        $isHeadquarter = (int) ($data['is_headquarter'] ?? 0);
        $branchNo = $isHeadquarter === 1
            ? ($data['business_branch_no'] ?? '')
            : ($data['business_branch_no'] ?? '00000');
        $branchName = $isHeadquarter === 1
            ? ($data['business_branch_name_th'] ?? '')
            : ($data['business_branch_name_th'] ?? 'สำนักงานใหญ่');
        $branchPhone = $data['mobile_phone'] ?? ($data['office_phone'] ?? $data['phone']);

        $taxNoClean = substr(preg_replace('/[^0-9]/', '', (string) $data['tax_no']), 0, 13);
        if ($taxNoClean === '' && $data['tax_no'] !== '') {
            $taxNoClean = substr((string) $data['tax_no'], 0, 13);
        }

        $rawPrefix = $data['prefix'];
        $allowedPrefixes = ['นาย', 'นาง', 'นางสาว', 'Mr.', 'Mrs.', 'Ms.'];
        $dbPrefix = in_array($rawPrefix, $allowedPrefixes, true) ? $rawPrefix : 'นาย';
        $dbOtherPrefix = in_array($rawPrefix, $allowedPrefixes, true) ? null : $rawPrefix;

        [$user, $business, $branch] = DB::transaction(function () use ($data, $businessName, $businessType, $businessVatStatus, $password, $taxNoClean, $dbPrefix, $dbOtherPrefix, $financeYearMonth, $branchNo, $branchName, $branchPhone) {
            $user = User::create([
                'prefix' => $dbPrefix,
                'other_prefix' => $dbOtherPrefix,
                'firstname' => $data['firstname'],
                'surname' => $data['surname'],
                'tax_no' => $taxNoClean ?: null,
                'birthday' => $data['birthday'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'province_id' => $data['province_id'],
                'amphure_id' => $data['amphure_id'],
                'tambon_id' => $data['tambon_id'],
                'password' => $password,
                'active_status' => true,
            ]);

            $business = Business::create([
                'business_type' => $businessType,
                'business_tax_no' => $taxNoClean ?: null,
                'business_vat_status' => $businessVatStatus,
                'business_name_th' => $businessName,
                'create_by_user' => $user->id,
            ]);

            BusinessUser::create([
                'business_id' => $business->id,
                'user_id' => $user->id,
                'create_by_user' => $user->id,
            ]);

            $branchCode = $this->generateBusinessBranchCode($business);

            $branch = BusinessBranch::create([
                'business_id' => $business->id,
                'business_status' => 0,
                'business_branch_no' => $branchNo,
                'business_branch_code' => $branchCode,
                'business_branch_name_th' => $branchName,
                'business_address' => $data['business_address'],
                'province_id' => $data['business_province_id'],
                'amphure_id' => $data['business_amphure_id'],
                'tambon_id' => $data['business_tambon_id'],
                'business_phone' => $branchPhone,
                'business_account_finance_year' => $financeYearMonth,
                'business_business_finance_year' => $financeYearMonth,
            ]);

            $employee = Employee::create([
                'businesses_id' => $business->id,
                'user_id' => $user->id,
                'status' => true,
                'create_by_user' => $user->id,
            ]);

            $employeeBranch = EmployeeBranch::create([
                'employee_id' => $employee->id,
                'business_branch_id' => $branch->id,
                'employee_position' => 'ผู้ดูแลระบบ',
            ]);

            $employee->update([
                'create_by_employee_branch' => $employeeBranch->id,
            ]);

            return [$user, $business, $branch];
        });

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->forget('register_step1');
        $this->setBusinessSessionContext($request, $user, $business, $branch);

        return redirect()->route('app.home', [
            'branch' => $this->branchRouteSegment($branch),
        ]);
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('public.forgot_password.index');
    }

    /**
     * Handle forgot password request.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        return back()->with('status', 'ระบบได้ส่งคำแนะนำสำหรับการรีเซ็ตรหัสผ่านไปยังอีเมลของคุณเรียบร้อยแล้ว');
    }

    private function generateBusinessBranchCode(Business $business): string
    {
        $businessPart = strtoupper(substr(str_replace('-', '', (string) $business->id), 0, 6));
        $candidate = $businessPart.'-B001';
        $suffix = 2;

        while (BusinessBranch::query()->where('business_branch_code', $candidate)->exists()) {
            $candidate = $businessPart.'-B001-'.$suffix;
            $suffix++;
        }

        return $candidate;
    }

    private function syncLoginContext(User $user, Request $request, ?string $branchCode): ?array
    {
        $branch = null;
        if ($branchCode) {
            $branch = BusinessBranch::query()
                ->where('business_branch_code', $branchCode)
                ->first();

            if (! $branch) {
                throw ValidationException::withMessages([
                    'branch_code' => 'ไม่พบ Branch Code ที่ระบุ',
                ]);
            }

            if (! userMayAccessBusiness($user, (string) $branch->business_id)) {
                throw ValidationException::withMessages([
                    'branch_code' => 'คุณไม่มีสิทธิ์เข้าใช้งานสาขานี้',
                ]);
            }
        } else {
            $branch = $this->defaultBranchForUser($user);
        }

        if (! $branch) {
            $request->session()->forget([
                'mainBusinessID',
                'mainBusinessBranchID',
                'mainEmployeeBranchID',
            ]);

            return null;
        }

        $business = Business::query()->find($branch->business_id);
        if (! $business) {
            $request->session()->forget([
                'mainBusinessID',
                'mainBusinessBranchID',
                'mainEmployeeBranchID',
            ]);

            return null;
        }

        $this->setBusinessSessionContext($request, $user, $business, $branch);

        return [
            'branch' => $this->branchRouteSegment($branch),
        ];
    }

    private function defaultBranchForUser(User $user): ?BusinessBranch
    {
        $employeeBranch = EmployeeBranch::query()
            ->whereHas('employee', function ($query) use ($user): void {
                $query->where('user_id', $user->id);
            })
            ->first();

        if ($employeeBranch?->branch) {
            return $employeeBranch->branch;
        }

        $businessId = $user->businessUsers()->value('business_id');
        if (! $businessId) {
            return null;
        }

        return BusinessBranch::query()
            ->where('business_id', $businessId)
            ->orderBy('business_status')
            ->orderBy('business_branch_no')
            ->first();
    }

    private function setBusinessSessionContext(Request $request, User $user, Business $business, BusinessBranch $branch): void
    {
        $request->session()->put('mainBusinessID', (string) $business->id);
        $request->session()->put('mainBusinessBranchID', (string) $branch->id);

        $employeeBranchId = EmployeeBranch::query()
            ->where('business_branch_id', $branch->id)
            ->whereHas('employee', function ($query) use ($user, $business): void {
                $query->where('user_id', $user->id)
                    ->where('businesses_id', $business->id);
            })
            ->value('id');

        if ($employeeBranchId) {
            $request->session()->put('mainEmployeeBranchID', (string) $employeeBranchId);

            return;
        }

        $request->session()->forget('mainEmployeeBranchID');
    }

    private function branchRouteSegment(BusinessBranch $branch): string
    {
        return (string) ($branch->business_branch_code ?: $branch->id);
    }
}
