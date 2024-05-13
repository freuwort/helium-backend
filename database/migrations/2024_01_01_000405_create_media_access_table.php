<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media_accesses', function (Blueprint $table) {
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete()->cascadeOnUpdate();
            $table->nullableMorphs('model');
            $table->string('type')->nullable();
            $table->string('permission')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media_accesses');
    }
};
