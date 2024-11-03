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
        Schema::create('accounting_document_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accounting_document_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('type');
            $table->string('description');
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 4);
            $table->foreign('unit')->references('code')->on('units')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('tax_rate', 6, 2);
            $table->decimal('price_net', 10, 2);
            $table->decimal('price_gross', 10, 2);
            $table->decimal('price_tax', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_document_items');
    }
};
