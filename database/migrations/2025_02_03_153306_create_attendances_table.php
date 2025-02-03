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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('no_id'); // Nomor ID pegawai/dokter
            $table->string('nama'); // Nama pegawai/dokter
            $table->date('tanggal'); // Tanggal presensi
            $table->time('jam_masuk')->nullable(); // Jam masuk
            $table->time('jam_pulang')->nullable(); // Jam pulang
            $table->timestamps(); // Untuk created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
