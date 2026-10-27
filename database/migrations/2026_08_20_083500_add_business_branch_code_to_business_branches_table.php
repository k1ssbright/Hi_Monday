<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('business_branches', function (Blueprint $table) {
            $table->string('business_branch_code', 64)
                ->nullable()
                ->after('business_branch_no')
                ->comment('Public branch code for URL/login context');
        });

        $branches = DB::table('business_branches')
            ->select('id', 'business_id', 'business_branch_no')
            ->orderBy('business_id')
            ->orderBy('id')
            ->get();

        $runningByBusiness = [];
        $usedCodes = [];

        foreach ($branches as $branch) {
            $businessId = (string) $branch->business_id;
            $runningByBusiness[$businessId] = ($runningByBusiness[$businessId] ?? 0) + 1;
            $sequence = $runningByBusiness[$businessId];

            $branchNoDigits = preg_replace('/\D+/', '', (string) ($branch->business_branch_no ?? ''));
            $branchPart = $branchNoDigits !== ''
                ? str_pad(substr($branchNoDigits, -3), 3, '0', STR_PAD_LEFT)
                : str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);

            $businessPart = strtoupper(substr(str_replace('-', '', $businessId), 0, 6));
            $baseCode = $businessPart.'-B'.$branchPart;
            $candidate = $baseCode;
            $suffix = 2;

            while (isset($usedCodes[$candidate])) {
                $candidate = $baseCode.'-'.$suffix;
                $suffix++;
            }

            $usedCodes[$candidate] = true;

            DB::table('business_branches')
                ->where('id', $branch->id)
                ->update(['business_branch_code' => $candidate]);
        }

        Schema::table('business_branches', function (Blueprint $table) {
            $table->unique('business_branch_code', 'business_branches_branch_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_branches', function (Blueprint $table) {
            $table->dropUnique('business_branches_branch_code_unique');
            $table->dropColumn('business_branch_code');
        });
    }
};
