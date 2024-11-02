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
        Schema::create('user_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('salutation')->nullable();
            $table->string('prefix')->nullable();
            $table->string('firstname')->nullable();
            $table->string('middlename')->nullable();
            $table->string('lastname')->nullable();
            $table->string('suffix')->nullable();
            $table->string('nickname')->nullable();
            $table->string('legalname')->nullable();
            $table->string('organisation')->nullable();
            $table->string('department')->nullable();
            $table->string('job_title')->nullable();
            $table->foreignId('main_address_id')->nullable()->constrained('addresses')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('billing_address_id')->nullable()->constrained('addresses')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->onDelete('set null')->onUpdate('cascade');
            $table->string('customer_id')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('member_id')->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_info');
    }
};
