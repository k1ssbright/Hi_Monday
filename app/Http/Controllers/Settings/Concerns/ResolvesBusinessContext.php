<?php

namespace App\Http\Controllers\Settings\Concerns;

use Illuminate\Support\Facades\Auth;

trait ResolvesBusinessContext
{
    protected function businessId(): string
    {
        $id = currentBusinessId();
        if ($id === null || $id === '') {
            abort(422, 'ไม่พบ business_id กรุณาเลือกบริษัทก่อนดำเนินการ');
        }

        if (Auth::check()) {
            assertBusinessAccess($id);
        }

        return $id;
    }

    protected function createBy(): ?string
    {
        return currentEmployeeBranchId();
    }
}
