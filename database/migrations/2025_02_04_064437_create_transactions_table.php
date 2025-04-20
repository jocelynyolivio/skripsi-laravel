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
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('patient_id')->nullable();
                $table->unsignedBigInteger('doctor_id')->nullable();

                $table->unsignedBigInteger('medical_record_id')->nullable();
                $table->unsignedBigInteger('admin_id');
                $table->decimal('total_amount', 10, 2)->notNull();
                $table->enum('status', ['belum lunas', 'lunas'])->default('belum lunas');

                $table->decimal('revenue_percentage', 5, 2)->nullable(); // Persentase bagi hasil dokter
                $table->decimal('revenue_amount', 10, 2)->nullable(); // Jumlah bagi hasil dokter

                $table->string('birthday_voucher')->nullable();

                $table->timestamps();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

                // Foreign keys
                $table->foreign('patient_id')->references('id')->on('patients')->nullOnDelete();
                $table->foreign('doctor_id')->references('id')->on('users')->nullOnDelete();

                $table->foreign('medical_record_id')->references('id')->on('medical_records')->cascadeOnDelete();
                $table->foreign('admin_id')->references('id')->on('users')->cascadeOnDelete();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('transactions');
        }
    };
