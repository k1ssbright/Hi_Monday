<?php

namespace App\Http\Controllers;

use App\Models\Business\BusinessBranch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Show the home page system selector.
     */
    public function index()
    {
        $contextRoute = $this->resolveContextRouteParameters();
        if ($contextRoute) {
            return redirect()->route('app.home', $contextRoute);
        }

        $user = Auth::user();

        return view('public.home.index', compact('user'));
    }

    /**
     * Enter app context from /app/{branch}.
     */
    public function openAppContext(Request $request)
    {
        return redirect()->route('office.index');
    }

    /**
     * Backward-compatible /office entry, redirect to context URL.
     */
    public function redirectToOfficeContext()
    {
        $contextRoute = $this->resolveContextRouteParameters();
        if (! $contextRoute) {
            return redirect()->route('home');
        }

        return redirect()->route('app.home', $contextRoute);
    }

    private function resolveContextRouteParameters(): ?array
    {
        $businessId = currentBusinessId();
        $branchId = currentBusinessBranchId();

        if (! $businessId || ! $branchId) {
            $user = Auth::user();
            if (! $user instanceof User) {
                return null;
            }

            $businessId = $user->businessUsers()->value('business_id');
            $branchId = $businessId
                ? BusinessBranch::query()->where('business_id', $businessId)->orderBy('business_status')->value('id')
                : null;
        }

        if (! $businessId || ! $branchId) {
            return null;
        }

        $branch = BusinessBranch::query()->find($branchId);
        if (! $branch || (string) $branch->business_id !== (string) $businessId) {
            return null;
        }

        return [
            'branch' => $branch->business_branch_code ?: $branch->id,
        ];
    }
}
