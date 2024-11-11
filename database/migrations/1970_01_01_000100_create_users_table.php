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
            // Core
            $table->id();
            $table->string('username')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->boolean('requires_password_change')->default(false);
            $table->boolean('requires_two_factor')->default(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamp('enabled_at')->nullable();
            $table->timestamp('blocked_at')->nullable();
            $table->string('block_reason')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            // Visuals
            $table->string('name')->nullable();
            $table->string('salutation')->nullable();
            $table->string('prefix')->nullable();
            $table->string('firstname')->nullable();
            $table->string('middlename')->nullable();
            $table->string('lastname')->nullable();
            $table->string('suffix')->nullable();
            $table->string('nickname')->nullable();
            $table->string('legalname')->nullable();
            $table->string('organisation')->nullable();
            $table->string('department')->nullable();
            $table->string('job_title')->nullable();
            $table->foreignId('main_address_id')->nullable()->constrained('addresses')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('billing_address_id')->nullable()->constrained('addresses')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->onDelete('set null')->onUpdate('cascade');
            $table->string('customer_id')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('member_id')->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
