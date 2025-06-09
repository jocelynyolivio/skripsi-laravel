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
            $table->string('unit_type')->nullable();
            $table->string('description')->nullable(); // Deskripsi bahan dental
            $table->timestamps();
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
