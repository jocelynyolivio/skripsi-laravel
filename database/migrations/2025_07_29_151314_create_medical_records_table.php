<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('medical_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
        $table->foreignId('reservation_id')->nullable()->constrained('reservations')->onDelete('cascade');
        $table->string('teeth_condition');
        $table->string('treatment');
        $table->json('odontogram')->nullable(); // Jika diperlukan untuk diagram gigi
        $table->text('notes')->nullable();
        $table->date('date');
        $table->unsignedBigInteger('doctor_id')->nullable();
        $table->unsignedBigInteger('procedure_id')->nullable();
        $table->foreign('doctor_id')->references('id')->on('users')->onDelete('set null');
        $table->foreign('procedure_id')->references('id')->on('procedures')->onDelete('set null');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
