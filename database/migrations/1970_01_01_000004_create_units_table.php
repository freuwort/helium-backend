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
        Schema::create('units', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('name');
            $table->string('symbol');
            $table->enum('type', ['numeric', 'length', 'area', 'volume', 'mass', 'time', 'temperature', 'electric_current', 'luminous_intensity', 'amount_of_substance', 'angle', 'digital', 'currency', 'other']);

            $table->index('code');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('units');
    }
};
