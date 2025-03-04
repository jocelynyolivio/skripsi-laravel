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
            Schema::create('medical_record_dental_material', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('medical_record_id');
                $table->unsignedBigInteger('dental_material_id');
                $table->integer('quantity'); // Kuantitas bahan dental yang digunakan
                $table->timestamps();
    
                // Foreign keys
                $table->foreign('medical_record_id')->references('id')->on('medical_records')->onDelete('cascade');
                $table->foreign('dental_material_id')->references('id')->on('dental_materials')->onDelete('cascade');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_record_dental_material');
    }
};
