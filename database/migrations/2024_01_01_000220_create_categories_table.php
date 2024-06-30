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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('set null')->onUpdate('cascade');
            $table->boolean('inherit_access')->default(true);
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null')->onUpdate('cascade');
            $table->string('type');
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->text('content')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->boolean('hidden')->default(false);
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
        Schema::dropIfExists('categories');
    }
};
