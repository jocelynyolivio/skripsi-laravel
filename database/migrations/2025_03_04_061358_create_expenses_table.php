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
            $table->string('expense_id', 50)->nullable();
            $table->date('expense_date');

            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');

            $table->unsignedBigInteger('supplier_id')->nullable();

            $table->foreignId('coa_out')->constrained('chart_of_accounts')->onDelete('cascade');
            $table->foreignId('coa_in')->constrained('chart_of_accounts')->onDelete('cascade');

            $table->text('payment_method');

            $table->decimal('amount', 15, 2);

            $table->string('status')->default('active');
            $table->string('attachment_path')->nullable();

            $table->text('description')->nullable();
            $table->string('reference_number', 50)->nullable(); // Nomor referensi (nyatet lek tokped dll)
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
