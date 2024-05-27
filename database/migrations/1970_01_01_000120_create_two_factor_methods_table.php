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
        Schema::create('two_factor_methods', function (Blueprint $table) {
            $table->id();
            $table->morphs('authenticatable');
            $table->string('type'); // totp, sms, email
            $table->string('recipient')->nullable();
            $table->string('secret')->nullable();
            $table->string('code')->nullable();
            $table->json('backup_codes')->nullable();
            $table->boolean('default')->default(false);
            $table->boolean('enabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('two_factor_methods');
    }
};
