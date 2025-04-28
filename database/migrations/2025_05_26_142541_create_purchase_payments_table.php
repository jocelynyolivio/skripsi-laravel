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
        Schema::create('purchase_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_invoice_id');
            $table->unsignedBigInteger('coa_id');
            $table->date('payment_date');
            $table->string('payment_method')->nullable();
            $table->decimal('purchase_amount', 15, 2);
            $table->decimal('total_debt', 15, 2);
            $table->enum('payment_status', ['paid', 'unpaid', 'partial']);
            $table->text('notes')->nullable();
            $table->timestamps();
        
            $table->foreign('purchase_invoice_id')->references('id')->on('purchase_invoices')->onDelete('cascade');
            $table->foreign('coa_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_payments');
    }
};
