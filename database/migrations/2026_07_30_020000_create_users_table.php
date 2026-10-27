<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique()->comment('email for login');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('prefix', ['นาย', 'นาง', 'นางสาว', 'Mr.', 'Mrs.', 'Ms.'])->comment('คำนำหน้าชื่อ');
            $table->string('other_prefix')->nullable()->comment('คำนำหน้าชื่ออื่นๆ');
            $table->string('firstname');
            $table->string('surname');
            $table->string('phone')->nullable();
            $table->boolean('active_status')->default(true);
            $table->string('signature')->nullable();
            $table->date('birthday')->nullable();
            $table->string('address')->nullable();
            $table->foreignId('province_id')->nullable()->constrained('provinces');
            $table->foreignId('amphure_id')->nullable()->constrained('amphures');
            $table->foreignId('tambon_id')->nullable()->constrained('tambons');
            $table->string('tax_no')->nullable();
            $table->string('profile_image')->default('user.jpg');
            $table->rememberToken();
            $table->foreignUuid('create_by_user')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
