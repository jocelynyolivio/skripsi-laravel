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
        Schema::create('odontograms', function (Blueprint $table) {
            $table->bigIncrements('id'); // ID Primary Key
            $table->unsignedBigInteger('patient_id'); // Foreign Key ke tabel pasien
            $table->unsignedBigInteger('medical_record_id')->nullable(); // Foreign Key ke tabel rekam medis (opsional)
            $table->integer('tooth_number'); // Nomor gigi (1-32)
            $table->string('condition', 255)->default('Healthy'); // Kondisi gigi (default: Healthy)
            $table->text('notes')->nullable(); // Catatan tambahan (opsional)
            $table->timestamps(); // Timestamps untuk created_at dan updated_at

            // Foreign key constraints
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade'); // Cascade jika pasien dihapus
            $table->foreign('medical_record_id')->references('id')->on('medical_records')->onDelete('cascade'); // Cascade jika rekam medis dihapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odontograms');
    }
};

