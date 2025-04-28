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
        Schema::create('purchase_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            // $table->date('purchase_date')->nullable();
            $table->string('payment_requirement')->nullable();
            $table->date('received_date')->nullable();
            $table->date('due_date')->nullable();
            // $table->decimal('total_amount', 15, 2);
            $table->decimal('discount', 15, 2)->nullable();
            $table->decimal('ongkos_kirim', 15, 2)->nullable();
            $table->decimal('grand_total', 15, 2);
            $table->enum('status', ['pending', 'received'])->default('pending');
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
