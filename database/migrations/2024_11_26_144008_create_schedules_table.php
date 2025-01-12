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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('doctor_id'); // Foreign key ke tabel users
            $table->date('date'); // Tanggal jadwal
            $table->time('time_start'); // Jam mulai
            $table->time('time_end'); // Jam selesai
            $table->boolean('is_available')->default(true); // Status ketersediaan
            $table->timestamps(); // Kolom created_at dan updated_at

            // Foreign key constraint
            $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};