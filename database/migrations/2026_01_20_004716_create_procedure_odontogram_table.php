<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('procedure_odontogram', function (Blueprint $table) {
            $table->id();
            $table->integer('tooth_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        
            // Foreign keys
            $table->foreignId('medical_record_id')->constrained()->onDelete('cascade');
            $table->foreignId('procedure_id')->constrained()->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('procedure_odontogram');
    }
};