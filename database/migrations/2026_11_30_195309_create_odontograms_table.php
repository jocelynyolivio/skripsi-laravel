<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odontograms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_record_id'); // FK ke medical_records
            $table->string('tooth_number'); // Nomor gigi (misal: 11, 21, dll.)
            $table->string('status'); // Status dari gigi (misal: sehat, berlubang, tambalan, dll.)
            $table->text('notes')->nullable(); // Catatan terkait kondisi gigi
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('medical_record_id')->references('id')->on('medical_records')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odontogram');
    }
};
