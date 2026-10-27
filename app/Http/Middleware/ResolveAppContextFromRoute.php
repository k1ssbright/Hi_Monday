<?php

namespace App\Http\Middleware;

use App\Models\Business\BusinessBranch;
use App\Models\Employee\EmployeeBranch;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ResolveAppContextFromRoute
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $branchParam = (string) $request->route('branch');

        $branch = BusinessBranch::query()
            ->where('business_branch_code', $branchParam)
            ->orWhere('id', $branchParam)
            ->first();

        if (! $branch) {
            abort(404, 'ไม่พบสาขา');
        }

        $business = $branch->business;
        if (! $business) {
            abort(404, 'ไม่พบบริษัท');
        }

        if (! userMayAccessBusiness($user, (string) $business->id)) {
            abort(403, 'ไม่มีสิทธิ์เข้าถึงบริษัทนี้');
        }

        $request->attributes->set('business_id', (string) $business->id);
        $request->attributes->set('business_branch_id', (string) $branch->id);

        $request->session()->put('mainBusinessID', (string) $business->id);
        $request->session()->put('mainBusinessBranchID', (string) $branch->id);
        URL::defaults([
            'branch' => (string) ($branch->business_branch_code ?: $branch->id),
        ]);

        $employeeBranchId = EmployeeBranch::query()
            ->where('business_branch_id', $branch->id)
            ->whereHas('employee', function ($query) use ($user, $business): void {
                $query->where('user_id', $user->id)
                    ->where('businesses_id', $business->id);
            })
            ->value('id');

        if ($employeeBranchId) {
            $request->session()->put('mainEmployeeBranchID', (string) $employeeBranchId);
        } else {
            $request->session()->forget('mainEmployeeBranchID');
        }

        // {branch} is only for context resolution — do not inject into controller actions.
        $request->route()?->forgetParameter('branch');

        return $next($request);
    }
}
