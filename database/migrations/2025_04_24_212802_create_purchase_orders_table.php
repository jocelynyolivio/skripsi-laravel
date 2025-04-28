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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->date('order_date');
            $table->unsignedBigInteger('purchase_request_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->date('due_date')->nullable();
            $table->date('ship_date')->nullable();
            // $table->enum('status', ['draft', 'sent', 'approved', 'completed', 'cancelled'])->default('draft');
            $table->text('shipping_address')->nullable();
            $table->string('payment_requirement')->nullable(); //kyk jatuh tempo ato cod dll
            $table->decimal('discount', 15, 2)->nullable();
            $table->decimal('ongkos_kirim', 15, 2)->nullable();
            $table->decimal('harga_total', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
