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

            $table->string('transaction_code')->unique();
            $table->string('customer_name');

            $table->text('pickup_location');
            $table->text('dropoff_location');

            $table->text('cargo_details')->nullable();
            $table->date('scheduled_date');

            $table->foreignId('driver_id')
                  ->nullable()
                  ->constrained('driver')
                  ->nullOnDelete();

            $table->foreignId('vehicle_id')
                  ->nullable()
                  ->constrained('vehicle')
                  ->nullOnDelete();

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->enum('status', [
                'pending',
                'scheduled',
                'in_transit',
                'delivered',
                'cancelled'
            ])->default('pending');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
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
