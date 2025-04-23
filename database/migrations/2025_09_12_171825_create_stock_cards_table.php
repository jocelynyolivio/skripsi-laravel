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
        Schema::create('stock_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dental_material_id');
            $table->date('date');
            $table->string('reference_number'); // ID transaksi/pembelian
            $table->decimal('price_in', 10, 2)->nullable();
            $table->decimal('price_out', 10, 2)->nullable();
            $table->integer('quantity_in')->default(0);
            $table->integer('quantity_out')->default(0);
            $table->integer('remaining_stock');
            $table->decimal('average_price', 10, 2);

            $table->enum('type', ['purchase', 'usage', 'adjustment'])->default('purchase');
            $table->string('note')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();

            $table->foreign('dental_material_id')->references('id')->on('dental_materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_cards');
    }
};
