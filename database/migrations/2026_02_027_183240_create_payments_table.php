<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id');
            $table->date('payment_date')->default(DB::raw('CURRENT_DATE'));
            $table->decimal('amount', 10, 2);
            $table->text('payment_method');
            $table->unsignedBigInteger('coa_id'); // Akun yang digunakan untuk membayar
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign Key Constraint
            $table->foreign('transaction_id')
                ->references('id')->on('transactions')
                ->onDelete('cascade');

            $table->foreign('coa_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
