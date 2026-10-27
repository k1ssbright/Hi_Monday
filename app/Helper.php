<?php

use App\Models\Address\Amphures;
use App\Models\Address\Provinces;
use App\Models\Address\Tambons;
use App\Models\Business\Business;
use App\Models\Business\BusinessUser;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

/*
|--------------------------------------------------------------------------
| Auth / Business access
|--------------------------------------------------------------------------
*/

if (! function_exists('userMayAccessBusiness')) {
    /**
     * Whether the user belongs to the given business (via business_users).
     */
    function userMayAccessBusiness(?User $user, string $businessId): bool
    {
        if (! $user || $businessId === '') {
            return false;
        }

        return BusinessUser::query()
            ->where('user_id', $user->id)
            ->where('business_id', $businessId)
            ->exists();
    }
}

if (! function_exists('currentBusinessId')) {
    /**
     * Resolve active business: request attribute → route → POST → session.
     */
    function currentBusinessId(): ?string
    {
        $request = request();

        if ($request && $request->attributes->has('business_id')) {
            return (string) $request->attributes->get('business_id');
        }

        if ($request) {
            $routeBusiness = $request->route('business');
            if ($routeBusiness !== null && $routeBusiness !== '') {
                return (string) $routeBusiness;
            }

            if ($request->filled('business_id') && Auth::check()) {
                $bid = (string) $request->input('business_id');
                if (userMayAccessBusiness(Auth::user(), $bid)) {
                    return $bid;
                }
            }
        }

        $sessionId = Session::get('mainBusinessID');

        return ($sessionId !== null && $sessionId !== '') ? (string) $sessionId : null;
    }
}

if (! function_exists('currentBusinessBranchId')) {
    /**
     * Resolve active branch: request attribute → route → session.
     */
    function currentBusinessBranchId(): ?string
    {
        $request = request();

        if ($request && $request->attributes->has('business_branch_id')) {
            return (string) $request->attributes->get('business_branch_id');
        }

        if ($request) {
            $routeBranch = $request->route('branch');
            if ($routeBranch !== null && $routeBranch !== '') {
                return (string) $routeBranch;
            }
        }

        $sessionId = Session::get('mainBusinessBranchID');

        return ($sessionId !== null && $sessionId !== '') ? (string) $sessionId : null;
    }
}

if (! function_exists('currentEmployeeBranchId')) {
    /**
     * Active employee_branch for create_by: session → first branch of the user in the current business.
     */
    function currentEmployeeBranchId(): ?string
    {
        $sessionId = Session::get('mainEmployeeBranchID');
        if ($sessionId !== null && $sessionId !== '') {
            return (string) $sessionId;
        }

        if (! Auth::check()) {
            return null;
        }

        $authUser = Auth::user();
        if (! $authUser instanceof User) {
            return null;
        }

        $businessId = currentBusinessId();
        $employeeQuery = $authUser->employees();
        if ($businessId) {
            $employeeQuery->where('businesses_id', $businessId);
        }

        $employee = $employeeQuery->first();

        return $employee?->employeeBranches()->value('id');
    }
}

if (! function_exists('assertBusinessAccess')) {
    /**
     * @throws HttpException
     */
    function assertBusinessAccess(?string $businessId): void
    {
        if ($businessId === null || $businessId === '') {
            abort(422, 'ไม่พบ business_id');
        }

        if (! Business::whereKey($businessId)->exists()) {
            abort(404, 'ไม่พบบริษัท');
        }

        if (! Auth::check() || ! userMayAccessBusiness(Auth::user(), (string) $businessId)) {
            abort(403, 'ไม่มีสิทธิ์เข้าถึงบริษัทนี้');
        }

        $context = currentBusinessId();
        if ($context !== null && $context !== '' && (string) $businessId !== (string) $context) {
            abort(403, 'บริษัทของเอกสารไม่ตรงกับบริษัทที่กำลังทำงาน');
        }
    }
}

/*
|--------------------------------------------------------------------------
| File uploads
|--------------------------------------------------------------------------
*/

if (! function_exists('uploadFileAddTime')) {
    /**
     * Sanitize filename, append timestamp, store on public_uploads disk.
     *
     * @throws RuntimeException
     */
    function uploadFileAddTime(?UploadedFile $file): ?string
    {
        if (! $file) {
            return null;
        }

        $fileOldName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileOldName = mb_convert_encoding($fileOldName, 'UTF-8', 'auto');

        $guessExtension = $file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'bin';

        $sanitizedFileName = preg_replace('/[^A-Za-z0-9ก-ฮ\-\_]/u', '-', $fileOldName);
        $sanitizedFileName = preg_replace('/-+/', '-', (string) $sanitizedFileName);
        $sanitizedFileName = trim((string) $sanitizedFileName, '-');
        if ($sanitizedFileName === '') {
            $sanitizedFileName = 'file';
        }

        $fileName = $sanitizedFileName.'-'.time().'.'.$guessExtension;

        if (! $file->storeAs('', $fileName, 'public_uploads')) {
            throw new RuntimeException('File upload failed.');
        }

        return $fileName;
    }
}

if (! function_exists('uploadBase64')) {
    /**
     * Decode a data-URI / base64 image and store on public_uploads disk.
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    function uploadBase64(string $textBase64): string
    {
        if (! str_contains($textBase64, ',')) {
            throw new InvalidArgumentException('Invalid base64 payload.');
        }

        [$meta, $data] = explode(',', $textBase64, 2);
        preg_match('/data:([^;]+);base64/i', $meta, $matches);
        $mime = $matches[1] ?? 'application/octet-stream';

        $extMap = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
        ];
        $ext = $extMap[strtolower($mime)] ?? (explode('/', $mime)[1] ?? 'bin');
        $ext = preg_replace('/[^a-z0-9]/i', '', (string) $ext) ?: 'bin';

        $binary = base64_decode($data, true);
        if ($binary === false) {
            throw new InvalidArgumentException('Unable to decode base64 data.');
        }

        $filename = sprintf('%s.%s', bin2hex(random_bytes(8)).date('YmdHis'), $ext);

        if (! Storage::disk('public_uploads')->put($filename, $binary)) {
            throw new RuntimeException('Base64 file upload failed.');
        }

        return $filename;
    }
}

if (! function_exists('imageToBase64')) {
    /**
     * Convert a local path or URL to a data-URI (empty string on failure).
     */
    function imageToBase64(?string $url): string
    {
        if ($url === null || $url === '') {
            return '';
        }

        $path = parse_url($url, PHP_URL_PATH) ?: $url;
        $type = pathinfo($path, PATHINFO_EXTENSION) ?: 'png';
        $data = false;

        $candidates = [
            base_path(ltrim((string) $path, '/')),
            public_path(ltrim((string) $path, '/')),
        ];

        // Files stored via public_uploads land under upload_file/
        $basename = basename((string) $path);
        if ($basename !== '') {
            $candidates[] = base_path('upload_file/'.$basename);
            $candidates[] = public_path('upload_file/'.$basename);
        }

        foreach ($candidates as $local) {
            if (is_file($local)) {
                $data = @file_get_contents($local);
                if ($data !== false) {
                    break;
                }
            }
        }

        if ($data === false && filter_var($url, FILTER_VALIDATE_URL)) {
            $context = stream_context_create([
                'http' => [
                    'header' => "User-Agent: OneClick/1.0\r\n",
                    'timeout' => 5,
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);
            $data = @file_get_contents($url, false, $context);
        }

        if ($data === false) {
            return '';
        }

        return 'data:image/'.$type.';base64,'.base64_encode($data);
    }
}

/*
|--------------------------------------------------------------------------
| Thai date / option lists
|--------------------------------------------------------------------------
*/

if (! function_exists('monthThai')) {
    function monthThai($strDate): string
    {
        if ($strDate === null || $strDate === '') {
            return '';
        }

        $months = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];

        if (is_numeric($strDate) && (int) $strDate >= 1 && (int) $strDate <= 12) {
            $month = (int) $strDate;
        } else {
            $timestamp = strtotime((string) $strDate);
            if ($timestamp === false) {
                return '';
            }
            $month = (int) date('n', $timestamp);
        }

        return $months[$month] ?? '';
    }
}

if (! function_exists('optionMonth')) {
    function optionMonth(): array
    {
        $months = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
        $list = [];

        for ($i = 1; $i <= 12; $i++) {
            $list[] = [
                'value' => str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'name' => $months[$i],
            ];
        }

        return $list;
    }
}

if (! function_exists('optionDate')) {
    function optionDate(): array
    {
        $list = [];
        for ($i = 1; $i <= 31; $i++) {
            $list[] = [
                'value' => str_pad((string) $i, 2, '0', STR_PAD_LEFT),
                'name' => $i,
            ];
        }

        return $list;
    }
}

if (! function_exists('optionDay')) {
    function optionDay(): array
    {
        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $daysOfWeekThai = ['วันจันทร์', 'วันอังคาร', 'วันพุธ', 'วันพฤหัสบดี', 'วันศุกร์', 'วันเสาร์', 'วันอาทิตย์'];
        $list = [];

        for ($i = 0; $i < 7; $i++) {
            $list[] = [
                'value' => $daysOfWeek[$i],
                'name' => $daysOfWeekThai[$i],
            ];
        }

        return $list;
    }
}

if (! function_exists('dayBusinessThai')) {
    function dayBusinessThai(?string $strDate): string
    {
        $daysOfWeek = [
            'Monday' => 'วันจันทร์',
            'Tuesday' => 'วันอังคาร',
            'Wednesday' => 'วันพุธ',
            'Thursday' => 'วันพฤหัสบดี',
            'Friday' => 'วันศุกร์',
            'Saturday' => 'วันเสาร์',
            'Sunday' => 'วันอาทิตย์',
        ];

        return $daysOfWeek[$strDate] ?? '';
    }
}

if (! function_exists('businessType')) {
    function businessType($typeNo): string
    {
        return match ((string) $typeNo) {
            '1' => 'บริษัท',
            '2' => 'ห้างหุ้นส่วน',
            '3' => 'บุคคลธรรมดา',
            default => '',
        };
    }
}

if (! function_exists('optionUserType')) {
    function optionUserType(): array
    {
        return [
            ['value' => 'Owner', 'name' => 'เจ้าของ'],
            ['value' => 'Approver', 'name' => 'ผู้อนุมัติ'],
            ['value' => 'Account', 'name' => 'บัญชี'],
            ['value' => 'Human Resources', 'name' => 'ฝ่ายบุคคล'],
            ['value' => 'PurchaseManager', 'name' => 'หัวหน้าฝ่ายซื้อ'],
            ['value' => 'Purchase', 'name' => 'ฝ่ายซื้อ'],
            ['value' => 'SalesManager', 'name' => 'หัวหน้าฝ่ายขาย'],
            ['value' => 'Sales', 'name' => 'ฝ่ายขาย'],
            ['value' => 'TechManager', 'name' => 'หัวหน้าเทคนิค'],
            ['value' => 'Technician', 'name' => 'ฝ่ายเทคนิค'],
            ['value' => 'MarketingManager', 'name' => 'หัวหน้าการตลาด'],
            ['value' => 'Marketing', 'name' => 'ฝ่ายการตลาด'],
            ['value' => 'General', 'name' => 'ทั่วไป เด็กฝึกงาน'],
            ['value' => 'Supplier', 'name' => 'คู่ค้า'],
        ];
    }
}

if (! function_exists('userTypeLevels')) {
    function userTypeLevels(): array
    {
        return [
            'Administrator' => 100,
            'Owner' => 100,
            'Approver' => 90,
            'Account' => 80,
            'Human Resources' => 70,
            'PurchaseManager' => 60,
            'Purchase' => 55,
            'SalesManager' => 50,
            'Sales' => 45,
            'TechManager' => 40,
            'Technician' => 35,
            'MarketingManager' => 30,
            'Marketing' => 25,
            'General' => 10,
        ];
    }
}

if (! function_exists('userType')) {
    function userType($value): string
    {
        foreach (optionUserType() as $item) {
            if ($item['value'] === $value) {
                return $item['name'];
            }
        }

        return '';
    }
}

/*
|--------------------------------------------------------------------------
| Money / document totals
|--------------------------------------------------------------------------
*/

if (! function_exists('InputNumber')) {
    /**
     * Strip commas and normalize to 4 decimal places (string), or 0 when empty.
     */
    function InputNumber($string): string|int
    {
        if ($string === null || $string === '') {
            return 0;
        }

        $value = str_replace(',', '', (string) $string);

        return number_format((float) $value, 4, '.', '');
    }
}

if (! function_exists('RoundMoney')) {
    /**
     * Round money to 2 decimals (Thai RD: 3rd decimal >= 5 rounds up).
     */
    function RoundMoney($n): float
    {
        return round((float) $n, 2);
    }
}

if (! function_exists('CalculateLineItemMoneyTotals')) {
    /**
     * Recalculate document money totals (round per line then sum).
     *
     * @param  array<int, float|string>  $productAmounts
     * @param  array<int, float|string>  $productPrices
     * @param  array<int, float|string>  $discountAmounts
     * @param  array<int, float|string>  $vatAmounts
     * @param  int|string  $vatType  0=none, 1=exclusive, 2+=inclusive
     * @param  int|string  $discountType  1=baht, else=%
     * @param  float|string|null  $whtPercent  empty/null = no WHT; "0" = use $whtPriceManual
     * @param  float|string|null  $whtPriceManual  used when wht percent is "0"
     * @param  float|string  $paymentRatioPercent  0–100
     * @param  float|string  $adjustment
     * @param  string  $whtBase  "before_vat" or "after_discount"
     * @return array<string, float>
     */
    function CalculateLineItemMoneyTotals(
        array $productAmounts,
        array $productPrices,
        array $discountAmounts,
        array $vatAmounts,
        $vatType,
        $discountType,
        $whtPercent = null,
        $whtPriceManual = null,
        $paymentRatioPercent = 100,
        $adjustment = 0,
        $whtBase = 'before_vat'
    ): array {
        $before_discount = 0.0;
        $discount = 0.0;
        $none_vat = 0.0;
        $is_vat = 0.0;
        $total_vat_price = 0.0;
        $sum_price = 0.0;
        $count = count($productAmounts);

        for ($index = 0; $index < $count; $index++) {
            $product_amount = (float) InputNumber($productAmounts[$index] ?? 0);
            $product_price = (float) InputNumber($productPrices[$index] ?? 0);
            $discount_amount = (float) InputNumber($discountAmounts[$index] ?? 0);
            $vat_amount = (float) InputNumber($vatAmounts[$index] ?? 0);

            if ((string) $discountType === '1') {
                $sum_discount = RoundMoney($discount_amount);
            } else {
                $sum_discount = RoundMoney(($product_price * $product_amount) * ($discount_amount / 100));
            }

            $line_gross = RoundMoney($product_price * $product_amount);
            $before_discount = RoundMoney($before_discount + $line_gross);
            $discount = RoundMoney($discount + $sum_discount);

            $sum_price = RoundMoney($line_gross - $sum_discount);

            if ((string) $vatType === '0' || $vat_amount == 0) {
                $none_vat = RoundMoney($none_vat + $sum_price);
            } elseif ((string) $vatType === '1') {
                $is_vat = RoundMoney($is_vat + $sum_price);
                $vat_price = RoundMoney($sum_price * ($vat_amount / 100));
                $total_vat_price = RoundMoney($total_vat_price + $vat_price);
            } else {
                $product_total_price = RoundMoney($sum_price * (100 / ($vat_amount + 100)));
                $vat_price = RoundMoney($sum_price - $product_total_price);
                $is_vat = RoundMoney($is_vat + $product_total_price);
                $total_vat_price = RoundMoney($total_vat_price + $vat_price);
            }
        }

        $after_discount = RoundMoney($before_discount - $discount);
        $base_before_vat = RoundMoney($none_vat + $is_vat);

        $wht_percent_raw = $whtPercent;
        $wht_is_manual = ($wht_percent_raw !== null && $wht_percent_raw !== '' && (string) $wht_percent_raw === '0');
        $wht_price = 0.0;
        if ($wht_percent_raw !== null && $wht_percent_raw !== '' && ! $wht_is_manual) {
            $wht_rate = (float) InputNumber($wht_percent_raw);
            if ($whtBase === 'after_discount') {
                $wht_price = RoundMoney($after_discount * ($wht_rate / 100));
            } else {
                $wht_price = RoundMoney($base_before_vat * ($wht_rate / 100));
            }
        }

        $payment_ratio = max(0, min(100, (float) InputNumber($paymentRatioPercent))) / 100;
        $adjustment = RoundMoney((float) InputNumber($adjustment));

        $none_r = RoundMoney($none_vat * $payment_ratio);
        $is_r = RoundMoney($is_vat * $payment_ratio);
        $vat_r = RoundMoney($total_vat_price * $payment_ratio);
        if ($wht_is_manual) {
            $wht_r = RoundMoney((float) InputNumber($whtPriceManual ?? 0));
        } else {
            $wht_r = RoundMoney($wht_price * $payment_ratio);
        }
        $total_r = RoundMoney(($none_r + $is_r + $vat_r) - $wht_r);

        return [
            'before_discount' => RoundMoney($before_discount * $payment_ratio),
            'discount' => RoundMoney($discount * $payment_ratio),
            'after_discount' => RoundMoney($after_discount * $payment_ratio),
            'none_vat' => $none_r,
            'is_vat' => $is_r,
            'total_vat_price' => $vat_r,
            'wht_price' => $wht_r,
            'total_price' => $total_r,
            'adjustment' => $adjustment,
            'net_payment' => RoundMoney($total_r + $adjustment),
            'sum_price' => RoundMoney($sum_price * $payment_ratio),
            'payment_ratio' => RoundMoney($payment_ratio * 100),
        ];
    }
}

if (! function_exists('CalculateLineProductTotal')) {
    /**
     * Line net after discount.
     */
    function CalculateLineProductTotal($productAmount, $productPrice, $discountAmount, $discountType): float
    {
        $product_amount = (float) InputNumber($productAmount);
        $product_price = (float) InputNumber($productPrice);
        $discount_amount = (float) InputNumber($discountAmount);

        if ((string) $discountType === '1') {
            $sum_discount = RoundMoney($discount_amount);
        } else {
            $sum_discount = RoundMoney(($product_price * $product_amount) * ($discount_amount / 100));
        }

        $line_gross = RoundMoney($product_price * $product_amount);

        return RoundMoney($line_gross - $sum_discount);
    }
}

/*
|--------------------------------------------------------------------------
| Text / phone / validation
|--------------------------------------------------------------------------
*/

if (! function_exists('textReplaceSpacial')) {
    function textReplaceSpacial(?string $string): string
    {
        return (string) preg_replace('/[^\p{L}\p{M}\p{Z}\p{N}\p{P}]/u', '', (string) $string);
    }
}

if (! function_exists('formatPhoneNumber')) {
    /** Digits only, max 10. */
    function formatPhoneNumber(?string $phoneNumber): string
    {
        $phoneNumber = preg_replace('/\D/', '', (string) $phoneNumber);

        return substr((string) $phoneNumber, 0, 10);
    }
}

if (! function_exists('extractFirstTenDigits')) {
    function extractFirstTenDigits(?string $string): string
    {
        $digits = preg_replace('/\D/', '', (string) $string);

        return substr((string) $digits, 0, 10);
    }
}

if (! function_exists('formatInternationalPhoneNumber')) {
    function formatInternationalPhoneNumber(?string $phoneNumber, string $defaultCountryCode = 'TH'): string
    {
        if ($phoneNumber === null || $phoneNumber === '') {
            return '+66-000000000';
        }

        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        $countryCodes = [
            'TH' => '+66',
            'US' => '+1',
            'UK' => '+44',
            'CN' => '+86',
            'JP' => '+81',
            'KR' => '+82',
            'DE' => '+49',
            'FR' => '+33',
            'IN' => '+91',
            'AU' => '+61',
        ];

        foreach ($countryCodes as $code) {
            $digits = ltrim($code, '+');
            if (str_starts_with((string) $phoneNumber, $digits)) {
                return $code.'-'.substr((string) $phoneNumber, strlen($digits));
            }
        }

        if (isset($countryCodes[$defaultCountryCode])) {
            return $countryCodes[$defaultCountryCode].'-'.ltrim((string) $phoneNumber, '0');
        }

        return (string) $phoneNumber;
    }
}

if (! function_exists('isValidDate')) {
    /** Matches d-m-yy or dd-mm-yyyy style. */
    function isValidDate($value): bool
    {
        return (bool) preg_match('/^\d{1,2}-\d{1,2}-\d{2,4}$/', (string) $value);
    }
}

if (! function_exists('isValidTime')) {
    function isValidTime($value): bool
    {
        return (bool) preg_match('/^\d{1,2}:\d{2}$/', (string) $value);
    }
}

if (! function_exists('countLinesInString')) {
    function countLinesInString(?string $string): int
    {
        $line_count = substr_count((string) $string, "\n") + 1;

        return max($line_count, 3);
    }
}

if (! function_exists('findFirstRowWithDateTime')) {
    function findFirstRowWithDateTime(array $data): ?array
    {
        $dateColumnIndex = 1;
        $timeColumnIndex = 2;

        foreach ($data as $row) {
            if (! empty($row[$dateColumnIndex]) && ! empty($row[$timeColumnIndex])) {
                return $row;
            }
        }

        return null;
    }
}

/*
|--------------------------------------------------------------------------
| Loan schedule
|--------------------------------------------------------------------------
*/

if (! function_exists('calculateDueDate')) {
    function calculateDueDate($date, $addTime, $installmentFrequency, $due_day, $customDay, $customMonth): string
    {
        $setDate = date('Y-m-d', strtotime((string) $date));

        if ((int) $addTime === 0) {
            return $setDate;
        }

        if ($installmentFrequency === 'monthly') {
            $days = ((int) $addTime) * 30;
            $setDate = date('Y-m-d', strtotime($setDate."+{$days} days"));
            if ($due_day === 'end_date') {
                $setDate = date('Y-m-t', strtotime($setDate));
            } else {
                $calDate = ((int) $customDay) - 1;
                $setDate = date('Y-m-d', strtotime('first day of this month', strtotime($setDate)));
                $setDate = date('Y-m-d', strtotime("+{$calDate} days", strtotime($setDate)));
            }
        } elseif ($installmentFrequency === 'yearly') {
            $customMonth = str_pad((string) $customMonth, 2, '0', STR_PAD_LEFT);
            $setDate = date("Y-{$customMonth}-d", strtotime($setDate."+{$addTime} years"));
            if ($due_day === 'end_date') {
                $setDate = date('Y-m-t', strtotime($setDate));
            } else {
                $customDay = str_pad((string) $customDay, 2, '0', STR_PAD_LEFT);
                $setDate = date("Y-m-{$customDay}", strtotime($setDate));
            }
        }

        return $setDate;
    }
}

if (! function_exists('calculateLoanRepayment')) {
    function calculateLoanRepayment(
        $principal,
        $numPeriods,
        $interestRate,
        $interestType,
        $firstInstallmentDate,
        $installmentFrequency,
        $customDay = null,
        $customMonth = null,
        $due_day = null
    ): array {
        $repaymentSchedule = [];
        $remainingPrincipal = (float) $principal;
        $totalPrincipalPayment = 0.0;
        $totalInterestPayment = 0.0;
        $totalPayment = 0.0;
        $numPeriods = (int) $numPeriods;

        for ($i = 0; $i < $numPeriods; $i++) {
            $principalPayment = 0.0;
            $interestPayment = 0.0;

            if ((float) $interestRate != 0) {
                if ($interestType == '1') {
                    $monthlyInterestRate = ((float) $interestRate / 100) / 12;
                    $fixedPayment = (float) $principal * ($monthlyInterestRate * pow(1 + $monthlyInterestRate, $numPeriods))
                        / (pow(1 + $monthlyInterestRate, $numPeriods) - 1);
                    $interestPayment = $remainingPrincipal * $monthlyInterestRate;
                    $principalPayment = $fixedPayment - $interestPayment;
                } elseif ($interestType == '2') {
                    $interestPayment = (float) $interestRate;
                    $principalPayment = ((float) $principal / $numPeriods);
                } elseif ($interestType == '3') {
                    $interestPayment = ((float) $interestRate / 100) * (float) $principal;
                    $principalPayment = ((float) $principal / $numPeriods);
                } elseif ($interestType == '4') {
                    $annualInterest = ((float) $interestRate / 100) * (float) $principal;
                    $interestPayment = $annualInterest / 12;
                    $principalPayment = ((float) $principal / $numPeriods);
                }
            } else {
                $principalPayment = (float) $principal / $numPeriods;
                $interestPayment = 0.0;
            }

            $remainingPrincipal -= $principalPayment;
            $totalPrincipalPayment += $principalPayment;
            $totalInterestPayment += $interestPayment;
            $totalPayment += $principalPayment + $interestPayment;

            $dueDate = calculateDueDate($firstInstallmentDate, $i, $installmentFrequency, $due_day, $customDay, $customMonth);
            $repaymentSchedule[] = [
                'period' => $i + 1,
                'due_date' => $dueDate,
                'principal_payment' => round($principalPayment, 2),
                'interest_payment' => round($interestPayment, 2),
                'total_payment' => round($principalPayment + $interestPayment, 2),
                'remaining_principal' => round($remainingPrincipal, 2),
            ];
        }

        $repaymentSchedule['totals'] = [
            'total_principal_payment' => round($totalPrincipalPayment, 2),
            'total_interest_payment' => round($totalInterestPayment, 2),
            'total_payment' => round($totalPayment, 2),
        ];

        return $repaymentSchedule;
    }
}

if (! function_exists('getNextDate')) {
    function getNextDate($specifiedDay, $specifiedDate, $specifiedMonth): string
    {
        $date = new DateTime;

        if (! empty($specifiedDay)) {
            $date->modify('next '.$specifiedDay);
        }

        if (! empty($specifiedDate)) {
            $date->setDate((int) $date->format('Y'), (int) $date->format('m'), (int) $specifiedDate);
        }

        if (! empty($specifiedMonth)) {
            $date->setDate((int) $date->format('Y'), (int) $specifiedMonth, (int) $date->format('d'));
            if (! empty($specifiedDate) && (int) $date->format('d') < (int) $specifiedDate) {
                $date->modify('first day of next month');
            }
        }

        if (! empty($specifiedDate) && ! empty($specifiedMonth)
            && ((int) $date->format('j') != (int) $specifiedDate || (int) $date->format('n') != (int) $specifiedMonth)) {
            $date->modify('first day of next month');
            $date->setDate((int) $date->format('Y'), (int) $date->format('m'), (int) $specifiedDate);
        }

        return $date->format('Y-m-d');
    }
}

/*
|--------------------------------------------------------------------------
| Withholding tax labels
|--------------------------------------------------------------------------
*/

if (! function_exists('whtFormType')) {
    function whtFormType($type): string
    {
        return match ((string) $type) {
            '1' => 'ภ.ง.ด.1ก',
            '2' => 'ภ.ง.ด.1ก พิเศษ',
            '3' => 'ภ.ง.ด.2',
            '4' => 'ภ.ง.ด.3',
            '5' => 'ภ.ง.ด.2ก',
            '6' => 'ภ.ง.ด.3ก',
            '7' => 'ภ.ง.ด.53',
            default => '',
        };
    }
}

if (! function_exists('whtFormList')) {
    function whtFormList(): array
    {
        $list = [];
        for ($i = 1; $i <= 7; $i++) {
            $list[] = [
                'id' => $i,
                'name' => whtFormType($i),
            ];
        }

        return $list;
    }
}

if (! function_exists('whtPaidType')) {
    function whtPaidType($type): string
    {
        return match ((string) $type) {
            '0' => '6. อื่นๆ (กรุณาระบุ)',
            '1' => '1. เงินเดือน ค่าจ้าง เบี้ยเลี้ยง โบนัส ฯลฯ ตามมาตรา 40 (1)',
            '2' => '2. ค่าธรรมเนียม ค่านายหน้า ฯลฯ ตามมาตรา 40 (2)',
            '3' => '3. ค่าแห่งลิขสิทธิ์ ฯลฯ ตามมาตรา 40(3)',
            '4' => '4. (ก) ค่าดอกเบี้ย ฯลฯ ตามมาตรา 40(4) (ก)',
            '5' => '4. (ข)(1.1) กิจการที่ต้องเสียภาษีเงินได้นิติบุคคลในอัตราร้อยละ 30 ของกำไรสุทธิ',
            '6' => '4. (ข)(1.2) กิจการที่ต้องเสียภาษีเงินได้นิติบุคคลในอัตราร้อยละ 25 ของกำไรสุทธิ',
            '7' => '4. (ข)(1.3) กิจการที่ต้องเสียภาษีเงินได้นิติบุคคลในอัตราร้อยละ 20 ของกำไรสุทธิ',
            '8' => '4. (ข)(1.4) กิจการที่ต้องเสียภาษีเงินได้นิติบุคคลในอัตราร้อยละ อื่นๆ (ระบุ)',
            '9' => '4. (ข)(2.1) กำไรสุทธิของกิจการที่ได้รับยกเว้นเงินได้นิติบุคคล',
            '10' => '4. (ข)(2.2) เงินปันผลหรือเงินส่วนแบ่งของกำไรที่ได้รับยกเว้น...',
            '11' => '4. (ข)(2.3) กำไรสุทธิส่วนที่ได้หักผลขาดทุนสิทธิยกมาไม่เกิน 5 ปี...',
            '12' => '4. (ข)(2.4) กำไรที่รับรู้ทางบัญชีโดยวิธีส่วนได้เสีย (equity method)',
            '13' => '4. (ข)(2.5) อื่นๆ (ระบุ)',
            '14' => '5. การจ่ายเงินได้ที่ต้องหักภาษี ณ ที่จ่ายตามคำสั่งกรมสรรพากร....',
            default => '',
        };
    }
}

if (! function_exists('whtPaidList')) {
    function whtPaidList(): array
    {
        $list = [];
        for ($i = 1; $i <= 14; $i++) {
            $list[] = [
                'id' => $i,
                'name' => whtPaidType($i),
            ];
        }
        $list[] = [
            'id' => 0,
            'name' => whtPaidType(0),
        ];

        return $list;
    }
}

/*
|--------------------------------------------------------------------------
| Thai baht text / tax number display
|--------------------------------------------------------------------------
*/

if (! function_exists('baht_text')) {
    function baht_text($number, bool $include_unit = true, bool $display_zero = true): ?string
    {
        $BAHT_TEXT_NUMBERS = ['ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า'];
        $BAHT_TEXT_UNITS = ['', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน'];
        $BAHT_TEXT_ONE_IN_TENTH = 'เอ็ด';
        $BAHT_TEXT_TWENTY = 'ยี่';
        $BAHT_TEXT_INTEGER = 'ถ้วน';
        $BAHT_TEXT_BAHT = 'บาท';
        $BAHT_TEXT_SATANG = 'สตางค์';
        $BAHT_TEXT_POINT = 'จุด';

        if (! is_numeric($number)) {
            return null;
        }

        $number = (float) $number;

        if ($number < 0) {
            return 'ลบ'.baht_text(abs($number), $include_unit, $display_zero);
        }

        if ($number == 0) {
            return $display_zero
                ? ($include_unit ? $BAHT_TEXT_NUMBERS[0].$BAHT_TEXT_BAHT.$BAHT_TEXT_INTEGER : $BAHT_TEXT_NUMBERS[0])
                : '';
        }

        $log = floor(log($number, 10));
        if ($log > 5) {
            $millions = (int) floor($log / 6);
            $million_value = pow(1000000, $millions);
            $normalised_million = floor($number / $million_value);
            $rest = $number - ($normalised_million * $million_value);
            $millions_text = str_repeat($BAHT_TEXT_UNITS[6], $millions);

            return baht_text($normalised_million, false).$millions_text.baht_text($rest, true, false);
        }

        $number_str = (string) floor($number);
        $text = '';
        $unit = 0;

        if ($display_zero && $number_str == '0') {
            $text = $BAHT_TEXT_NUMBERS[0];
        } else {
            for ($i = strlen($number_str) - 1; $i > -1; $i--) {
                $current_number = (int) $number_str[$i];
                $unit_text = '';

                if ($unit == 0 && $i > 0) {
                    $previous_number = isset($number_str[$i - 1]) ? (int) $number_str[$i - 1] : 0;
                    if ($current_number == 1 && $previous_number > 0) {
                        $unit_text .= $BAHT_TEXT_ONE_IN_TENTH;
                    } elseif ($current_number > 0) {
                        $unit_text .= $BAHT_TEXT_NUMBERS[$current_number];
                    }
                } elseif ($unit == 1 && $current_number == 2) {
                    $unit_text .= $BAHT_TEXT_TWENTY;
                } elseif ($current_number > 0 && ($unit != 1 || $current_number != 1)) {
                    $unit_text .= $BAHT_TEXT_NUMBERS[$current_number];
                }

                if ($current_number > 0) {
                    $unit_text .= $BAHT_TEXT_UNITS[$unit];
                }

                $text = $unit_text.$text;
                $unit++;
            }
        }

        if ($include_unit) {
            $text .= $BAHT_TEXT_BAHT;
            $satang = explode('.', number_format($number, 2, '.', ''))[1];
            $text .= $satang == 0
                ? $BAHT_TEXT_INTEGER
                : baht_text((int) $satang, false).$BAHT_TEXT_SATANG;
        } else {
            $exploded = explode('.', (string) $number);
            if (isset($exploded[1])) {
                $text .= $BAHT_TEXT_POINT;
                $decimal = (string) $exploded[1];
                for ($i = 0; $i < strlen($decimal); $i++) {
                    $text .= $BAHT_TEXT_NUMBERS[(int) $decimal[$i]];
                }
            }
        }

        return $text;
    }
}

if (! function_exists('taxNoTo50')) {
    function taxNoTo50($text): string
    {
        $content = '';
        $number_str = str_split(str_pad((string) $text, 13, '0', STR_PAD_LEFT));

        for ($i = 0; $i < 13; $i++) {
            $current_number = $number_str[$i];

            $content .= match ((string) $i) {
                '0' => "<span class='tax-no'>{$current_number}</span><span class='tax-line'></span>",
                '1' => "<span class='tax-no none-right'>{$current_number}</span>",
                '2', '3' => "<span class='tax-no dot-left none-right'>{$current_number}</span>",
                '4' => "<span class='tax-no dot-left'>{$current_number}</span><span class='tax-line'></span>",
                '5' => "<span class='tax-no none-right'>{$current_number}</span>",
                '6', '7', '8' => "<span class='tax-no dot-left none-right'>{$current_number}</span>",
                '9' => "<span class='tax-no dot-left'>{$current_number}</span><span class='tax-line'></span>",
                '10' => "<span class='tax-no none-right'>{$current_number}</span>",
                '11' => "<span class='tax-no dot-left'>{$current_number}</span><span class='tax-line'></span>",
                '12' => "<span class='tax-no'>{$current_number}</span>",
                default => '',
            };
        }

        return $content;
    }
}

/*
|--------------------------------------------------------------------------
| Address parsing (OneClick schema — province/amphure/tambon ids)
|--------------------------------------------------------------------------
*/

if (! function_exists('extractProvince')) {
    function extractProvince(?string $address): ?string
    {
        $pattern = '/(กรุงเทพมหานคร|สมุทรปราการ|นนทบุรี|ปทุมธานี|พระนครศรีอยุธยา|อ่างทอง|ลพบุรี|สิงห์บุรี|ชัยนาท|สระบุรี|ชลบุรี|ระยอง|จันทบุรี|ตราด|ฉะเชิงเทรา|ปราจีนบุรี|นครนายก|สระแก้ว|นครราชสีมา|บุรีรัมย์|สุรินทร์|ศรีสะเกษ|อุบลราชธานี|ยโสธร|ชัยภูมิ|อำนาจเจริญ|หนองบัวลำภู|ขอนแก่น|อุดรธานี|เลย|หนองคาย|มหาสารคาม|ร้อยเอ็ด|กาฬสินธุ์|สกลนคร|นครพนม|มุกดาหาร|เชียงใหม่|ลำพูน|ลำปาง|อุตรดิตถ์|แพร่|น่าน|พะเยา|เชียงราย|แม่ฮ่องสอน|นครสวรรค์|อุทัยธานี|กำแพงเพชร|ตาก|สุโขทัย|พิษณุโลก|พิจิตร|เพชรบูรณ์|ราชบุรี|กาญจนบุรี|สุพรรณบุรี|นครปฐม|สมุทรสาคร|สมุทรสงคราม|เพชรบุรี|ประจวบคีรีขันธ์|นครศรีธรรมราช|กระบี่|พังงา|ภูเก็ต|สุราษฎร์ธานี|ระนอง|ชุมพร|สงขลา|สตูล|ตรัง|พัทลุง|ปัตตานี|ยะลา|นราธิวาส)/u';
        preg_match($pattern, (string) $address, $matches);

        return $matches[0] ?? null;
    }
}

if (! function_exists('extractAmphure')) {
    function extractAmphure(?string $address): ?string
    {
        preg_match('/\s(เขต|อำเภอ|อ\.)\s?([^\d\s]+)/u', (string) $address, $matches);

        return $matches[2] ?? null;
    }
}

if (! function_exists('extractTambon')) {
    function extractTambon(?string $address): ?string
    {
        preg_match('/\s(ตำบล|ต\.|แขวง)\s?([^\d\s]+)/u', (string) $address, $matches);

        return $matches[2] ?? null;
    }
}

if (! function_exists('parseAddress')) {
    /**
     * Parse a Thai free-text address into structured fields for OneClick.
     *
     * @return array{
     *     postcode: ?string,
     *     province_id: int|null,
     *     amphure_id: int|null,
     *     tambon_id: int|null,
     *     province_name: ?string,
     *     amphure_name: ?string,
     *     tambon_name: ?string,
     *     street: ?string,
     *     building: ?string,
     *     country: string
     * }
     */
    function parseAddress(?string $fullAddress): array
    {
        $fullAddress = (string) $fullAddress;
        $province = null;
        $amphure = null;
        $tambon = null;
        $zipCode = null;

        preg_match('/\d{5}$/', $fullAddress, $matches);
        if (! empty($matches)) {
            $zipCode = $matches[0];
        }

        $provinceName = extractProvince($fullAddress);
        if ($provinceName) {
            $province = Provinces::query()
                ->where('name_th', 'LIKE', '%'.$provinceName.'%')
                ->first();
        }

        $amphureName = extractAmphure($fullAddress);
        if ($province && $amphureName) {
            $amphure = Amphures::query()
                ->where('province_id', $province->id)
                ->where('name_th', 'LIKE', '%'.$amphureName.'%')
                ->first();
        }

        $tambonName = extractTambon($fullAddress);
        if ($amphure && $tambonName) {
            $tambonQuery = Tambons::query()
                ->where('amphure_id', $amphure->id)
                ->where('name_th', 'LIKE', '%'.$tambonName.'%');

            if ($zipCode) {
                $tambonQuery->where('zip_code', $zipCode);
            }

            $tambon = $tambonQuery->first();
        }

        preg_match('/^(\d+\/?\d*)/', $fullAddress, $buildingMatch);
        $building = isset($buildingMatch[1]) ? trim($buildingMatch[1]) : null;

        preg_match('/(ถนน|ซอย|ม\.|หมู่บ้าน|หมู่ที่|ถ\.)\s?([\p{Thai}0-9\/]+)/u', $fullAddress, $streetMatch);
        $street = isset($streetMatch[2]) ? trim($streetMatch[2]) : null;

        return [
            'postcode' => $zipCode,
            'province_id' => $province?->id,
            'amphure_id' => $amphure?->id,
            'tambon_id' => $tambon?->id,
            'province_name' => $province?->name_th,
            'amphure_name' => $amphure?->name_th,
            'tambon_name' => $tambon?->name_th,
            'street' => $street,
            'building' => $building,
            'country' => 'TH',
        ];
    }
}

/*
|--------------------------------------------------------------------------
| SQL helpers
|--------------------------------------------------------------------------
*/

if (! function_exists('groupConcatSql')) {
    /**
     * GROUP_CONCAT syntax for MySQL vs SQLite.
     */
    function groupConcatSql(string $column, string $separator = ', '): string
    {
        $quoted = "'".str_replace("'", "''", $separator)."'";

        return DB::connection()->getDriverName() === 'sqlite'
            ? "GROUP_CONCAT({$column}, {$quoted})"
            : "GROUP_CONCAT({$column} SEPARATOR {$quoted})";
    }
}
