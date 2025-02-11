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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // Tanggal pengeluaran
            $table->decimal('amount', 10, 2); // Jumlah pengeluaran
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // Relasi ke kategori
            $table->text('description')->nullable(); // Deskripsi pengeluaran
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // Admin yang mencatat pengeluaran
            $table->date('expired_at')->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('dental_material_id')->nullable(); // Relasi ke bahan dental
            $table->integer('quantity')->nullable(); // Jumlah bahan dental yang dibeli
            
            $table->foreign('dental_material_id')->references('id')->on('dental_materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
