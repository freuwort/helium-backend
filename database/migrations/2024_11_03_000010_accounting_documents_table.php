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
        Schema::create('accounting_documents', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('status');
            $table->foreignId('sender_id')->constrained('accounting_contacts')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('recipient_id')->constrained('accounting_contacts')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->cascadeOnUpdate()->setNullOnDelete();
            $table->string('quote_id')->nullable();
            $table->string('order_id')->nullable();
            $table->string('invoice_id')->nullable();
            $table->string('refund_id')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('subject')->nullable();
            $table->text('decription')->nullable();
            $table->text('footer')->nullable();
            $table->string('currency', 4);
            $table->foreign('currency')->references('code')->on('currencies')->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('total_net', 10, 2);
            $table->decimal('total_gross', 10, 2);
            $table->decimal('total_tax', 10, 2);
            $table->timestamp('issue_date')->nullable();
            $table->timestamp('valid_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_date')->nullable();
            $table->timestamp('delivery_date')->nullable();
            $table->timestamp('shipping_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounting_documents');
    }
};
