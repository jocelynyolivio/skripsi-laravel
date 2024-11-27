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
        $table->id();
        $table->string('nama');
        $table->string('nomor_telepon');
        $table->date('tanggal_reservasi');
        $table->time('jam_reservasi');
        $table->timestamps();
        $table->unsignedBigInteger('doctor_id');
        $table->foreign('doctor_id')->references('id')->on('users')->onDelete('cascade'); // Relasi ke tabel users
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
