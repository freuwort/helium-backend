<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('screen_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null')->onUpdate('cascade');
            $table->string('name')->nullable();
            $table->string('group')->nullable();
            $table->string('secret')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('screen_devices');
    }
};
