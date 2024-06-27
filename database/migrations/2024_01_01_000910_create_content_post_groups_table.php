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
        Schema::create('content_post_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained('content_spaces')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('post_id')->nullable();
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null')->onUpdate('cascade');
            $table->boolean('hidden')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content_post_groups');
    }
};
