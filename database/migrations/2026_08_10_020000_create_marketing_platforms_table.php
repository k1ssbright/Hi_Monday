<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** แพลตฟอร์มเริ่มต้น */
    private array $defaults = [
        'Facebook', 'Instagram', 'Google', 'TikTok', 'LINE', 'YouTube', 'Website', 'Referral', 'Offline/Event',
    ];

    public function up(): void
    {
        Schema::create('marketing_platforms', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('business_id')->constrained('businesses');
            $table->string('name');
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignUuid('create_by')->nullable()->constrained('employee_branches');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['business_id', 'is_active']);
        });

        $now = now();
        foreach (DB::table('businesses')->pluck('id') as $bizId) {
            $rows = [];
            foreach ($this->defaults as $i => $name) {
                $rows[] = [
                    'business_id' => $bizId,
                    'name'        => $name,
                    'sort'        => $i,
                    'is_active'   => true,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ];
            }
            DB::table('marketing_platforms')->insert($rows);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_platforms');
    }
};
