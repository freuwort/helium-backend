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
        Schema::create('two_factor_backup_codes', function (Blueprint $table) {
            $table->id();
            $table->morphs('authenticatable', 'tfa_backup_codes_authenticatable_index');
            $table->string('code')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('two_factor_backup_codes');
    }
};
