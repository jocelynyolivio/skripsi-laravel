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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            // $table->unsignedBigInteger('doctor_id')->nullable();
            $table->unsignedBigInteger('procedure_id');
            $table->integer('quantity')->unsigned()->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2)->default(0);
            // $table->decimal('revenue_percentage', 5, 2)->nullable(); // Persentase bagi hasil dokter
            // $table->decimal('revenue_amount', 10, 2)->nullable(); // Jumlah bagi hasil dokter
            $table->timestamps();
        
            // Constraints
            $table->foreign('transaction_id')->references('id')->on('transactions')->cascadeOnDelete();
            // $table->foreign('doctor_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('procedure_id')->references('id')->on('procedures')->cascadeOnDelete();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
