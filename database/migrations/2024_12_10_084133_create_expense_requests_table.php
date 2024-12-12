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
        Schema::create('expense_requests', function (Blueprint $table) {
            $table->id();
            $table->string('item_name'); // Nama barang
            $table->text('description')->nullable(); // Deskripsi permintaan
            $table->integer('quantity'); // Jumlah barang
            $table->decimal('estimated_cost', 10, 2); // Perkiraan biaya
            $table->enum('status', ['Requested', 'Approved', 'Rejected', 'Done'])->default('Requested'); // Status permintaan
            $table->unsignedBigInteger('requested_by'); // ID admin yang membuat permintaan
            $table->unsignedBigInteger('approved_by')->nullable(); // ID atasan yang menyetujui
            $table->timestamps();

            // Relasi
            $table->foreign('requested_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_requests');
    }
};
