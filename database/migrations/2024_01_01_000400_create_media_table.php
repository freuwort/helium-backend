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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('media')->onDelete('cascade')->onUpdate('cascade');
            $table->string('drive')->nullable();
            $table->string('src_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('name')->nullable();
            $table->string('access')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media');
    }
};
