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
    Schema::create('reservations', function (Blueprint $table) {
        $table->id(); // Primary key
        $table->unsignedBigInteger('schedule_id'); // Foreign key ke tabel schedules
        $table->unsignedBigInteger('patient_id'); // Foreign key ke tabel patients
        $table->unsignedBigInteger('doctor_id'); // Foreign key ke tabel users
        $table->date('tanggal_reservasi'); // Tanggal reservasi
        $table->time('jam_reservasi'); // Jam reservasi
        $table->timestamps();
    
        // Foreign key constraints
        $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('cascade');
        $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
