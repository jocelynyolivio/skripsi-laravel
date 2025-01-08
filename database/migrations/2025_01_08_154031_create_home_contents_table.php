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
        Schema::create('home_contents', function (Blueprint $table) {
            $table->id();
            $table->string('carousel_image')->nullable();
            $table->string('carousel_text')->nullable();
            $table->string('welcome_title')->nullable();
            $table->text('welcome_message')->nullable();
            $table->text('about_text')->nullable();
            $table->string('about_image')->nullable();
            $table->text('services_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_contents');
    }
};
