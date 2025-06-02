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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Kode Akun (misal: 1100)
            $table->string('name');           // Nama Akun (misal: Kas)
            $table->enum('type', ['asset', 'liability', 'equity', 'revenue', 'expense','contra_expense','contra_revenue','contra_asset']);

            $table->boolean('is_cash_equivalent')->default(false); // Apakah akun ini Kas atau Setara Kas?
            $table->enum('cash_flow_activity', ['operating', 'investing', 'financing', 'none'])->nullable()->default('none'); // Aktivitas Arus Kas utama yang terkait DENGAN AKUN INI (jika bukan kas)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
