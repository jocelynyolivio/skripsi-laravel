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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date('expense_date'); // Tanggal pengeluaran
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // Admin yang mencatat pengeluaran
            $table->unsignedBigInteger('supplier_id')->nullable();

            $table->foreignId('coa_out')->constrained('chart_of_accounts')->onDelete('cascade'); // Akun kas/bank
            $table->foreignId('coa_in')->constrained('chart_of_accounts')->onDelete('cascade'); // Akun beban
            $table->text('payment_method');

            $table->decimal('amount', 15, 2); // Jumlah pengeluaran
            $table->text('description'); // Keterangan
            $table->string('reference_number', 50)->nullable(); // Nomor referensi (opsional)
            $table->timestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
