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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null')->onUpdate('cascade');
            $table->string('type')->nullable();
            $table->string('name')->nullable();
            $table->string('group')->nullable();
            $table->string('pin')->nullable();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->onDelete('set null')->onUpdate('cascade');
            $table->string('os_platform')->nullable();
            $table->string('os_arch')->nullable();
            $table->string('os_release')->nullable();
            $table->string('app_version')->nullable();
            $table->timestamp('pin_generated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
