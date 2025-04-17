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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id')->unique();

            // informasi pribadi
            $table->string('name')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('nik')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('religion')->nullable();
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed'])->nullable();
            $table->string('family_status')->nullable();
            $table->string('occupation')->nullable();
            $table->string('nationality')->nullable();

            // Alamat Rumah
            $table->text('home_address')->nullable();
            $table->string('home_city')->nullable();
            $table->string('home_zip_code')->nullable();
            $table->string('home_country')->nullable();
            $table->string('home_phone')->nullable();
            $table->string('home_mobile')->nullable();
            $table->string('home_email')->nullable();

            // Alamat Kantor
            $table->text('office_address')->nullable();
            $table->string('office_city')->nullable();
            $table->string('office_zip_code')->nullable();
            $table->string('office_country')->nullable();
            $table->string('office_phone')->nullable();
            $table->string('office_mobile')->nullable();
            $table->string('office_email')->nullable();

            // Kontak Darurat
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            // Upload Dokumen
            $table->string('form_data_awal')->nullable(); // Upload form awal
            $table->string('informed_consent')->nullable(); // Upload persetujuan pasien

            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();

            $table->rememberToken();
            $table->timestamps();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
