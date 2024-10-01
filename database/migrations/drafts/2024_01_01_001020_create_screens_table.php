<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('screens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null')->onUpdate('cascade');
            $table->string('name')->nullable();
            $table->json('content')->nullable();
            $table->string('background')->nullable();
            $table->integer('width')->default(0);
            $table->integer('height')->default(0);
            $table->integer('duration')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('screens');
    }
};
