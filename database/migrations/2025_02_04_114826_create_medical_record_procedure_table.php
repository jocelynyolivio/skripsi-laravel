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
        Schema::create('medical_record_procedure', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_record_id')->nullable(); // Foreign key ke medical_records

            $table->timestamps();

            $table->string('surface')->nullable();
            // Menambahkan foreign key constraints
            $table->foreign('medical_record_id')
                ->references('id')
                ->on('medical_records')
                ->onDelete('cascade');

            $table->unsignedBigInteger('procedure_id'); // Foreign key ke procedures
            $table->foreign('procedure_id')
                ->references('id')
                ->on('procedures')
                ->onDelete('cascade');

            $table->integer('tooth_number')->nullable();
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_record_procedure');
    }
};
