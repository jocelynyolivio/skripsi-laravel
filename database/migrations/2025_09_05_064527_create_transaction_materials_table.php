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
        Schema::create('transaction_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id'); // Foreign key ke transactions
            $table->unsignedBigInteger('dental_material_id'); // Foreign key ke dental_materials
            $table->integer('quantity'); // Jumlah bahan yang digunakan
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->onDelete('cascade');

            $table->foreign('dental_material_id')
                ->references('id')
                ->on('dental_materials')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_materials');
    }
};
