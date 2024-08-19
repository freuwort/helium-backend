<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('screen_device_screen_playlist', function (Blueprint $table) {
            $table->foreignId('screen_device_id')->constrained('screen_devices')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('screen_playlist_id')->constrained('screen_playlists')->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('from_date')->nullable();
            $table->time('from_time')->nullable();
            $table->date('to_date')->nullable();
            $table->time('to_time')->nullable();
            $table->json('on_days')->nullable();
            $table->string('on_screen')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('screen_device_screen_playlist');
    }
};
