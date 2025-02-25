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
        Schema::create('journal_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->unsignedBigInteger('coa_id');
            $table->decimal('debit', 20, 2)->default(0);
            $table->decimal('credit', 20, 2)->default(0);
            $table->timestamps();

            $table->foreign('journal_entry_id')
                ->references('id')
                ->on('journal_entries')
                ->onDelete('cascade');

            $table->foreign('coa_id')
                ->references('id')
                ->on('chart_of_accounts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_details');
    }
};
