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
        Schema::create('dental_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama bahan dental
            $table->string('description')->nullable(); // Deskripsi bahan dental
            $table->integer('stock_quantity'); // Jumlah stok yang tersedia
            $table->decimal('unit_price', 8, 2)->nullable(); // Harga satuan bahan
            $table->timestamps();

            // $table->unsignedBigInteger('medical_record_id');
            // $table->unsignedBigInteger('dental_material_id');

                        // Foreign keys
                        // $table->foreign('medical_record_id')->references('id')->on('medical_records')->onDelete('cascade');
                        // $table->foreign('dental_material_id')->references('id')->on('dental_materials')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dental_materials');
    }
};
