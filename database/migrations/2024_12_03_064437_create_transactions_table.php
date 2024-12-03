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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id'); // Foreign key ke tabel patients
            $table->unsignedBigInteger('procedure_id'); // Foreign key ke tabel procedures
            $table->unsignedBigInteger('doctor_id'); // Foreign key ke tabel users (dokter)
            $table->date('date'); // Tanggal prosedur dilakukan
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('cascade');

            $table->foreign('procedure_id')
                ->references('id')
                ->on('procedures')
                ->onDelete('cascade');

            $table->foreign('doctor_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
