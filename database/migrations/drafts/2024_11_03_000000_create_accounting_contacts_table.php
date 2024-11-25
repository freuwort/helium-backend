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
        Schema::create('accounting_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name');
            $table->foreignId('sync_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('owner_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('main_address_id')->nullable()->constrained('addresses')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('billing_address_id')->nullable()->constrained('addresses')->onDelete('set null')->onUpdate('cascade');
            $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->onDelete('set null')->onUpdate('cascade');
            $table->string('vat_id')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('supplier_id')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('contact_person')->nullable();
            $table->integer('version')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_contacts');
    }
};
