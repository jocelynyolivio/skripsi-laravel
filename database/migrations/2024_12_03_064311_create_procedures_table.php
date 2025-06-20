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
        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->nullable();
            $table->string('name'); // Nama prosedur, misalnya Tambal Gigi
            $table->foreignId('procedure_type_id')
                ->nullable()
                ->constrained('procedure_types')
                ->nullOnDelete(); // <-- ini penting             // Deskripsi tentang prosedur
            $table->text('description')->nullable();
            $table->boolean('requires_tooth')->default(true); // Default: prosedur butuh nomor gigi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procedures');
    }
};
