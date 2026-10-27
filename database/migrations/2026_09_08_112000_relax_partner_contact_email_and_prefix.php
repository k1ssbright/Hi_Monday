<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partner_contacts', function (Blueprint $table) {
            $table->string('email')->nullable()->comment('อีเมลผู้ติดต่อ')->change();
            $table->string('prefix', 32)->comment('คำนำหน้าชื่อ')->change();
        });
    }

    public function down(): void
    {
        Schema::table('partner_contacts', function (Blueprint $table) {
            $table->string('email')->nullable(false)->comment('อีเมลผู้ติดต่อ')->change();
        });
    }
};
