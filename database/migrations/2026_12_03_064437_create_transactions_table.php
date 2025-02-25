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
                $table->unsignedBigInteger('medical_record_id')->nullable();
                $table->unsignedBigInteger('admin_id');
                $table->decimal('total_amount', 10, 2)->notNull();
                $table->enum('status', ['belum lunas', 'lunas'])->default('belum lunas');
                $table->timestamps();
            
                // Foreign keys
                $table->foreign('patient_id')->references('id')->on('patients')->nullOnDelete();
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
