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
        Schema::create('salary_calculations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index(); // Relasi ke users
            $table->string('month'); // Format YYYY-MM

            // total hari kerja
            $table->integer('normal_shift')->default(0);
            $table->integer('holiday_shift')->default(0);

            // total gaji per shift
            $table->decimal('shift_pagi', 15, 2)->default(0);
            $table->decimal('shift_siang', 15, 2)->default(0);
            $table->decimal('lembur', 15, 2)->default(0);

            // Gaji Pokok dan Total
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->decimal('allowance', 15, 2)->default(0); // Tunjangan total
            $table->decimal('grand_total', 15, 2)->default(0); // Total keseluruhan

            $table->decimal('adjustment', 15, 2)->default(0);
            $table->text('adjustment_notes')->nullable();

            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_calculations');
    }
};
