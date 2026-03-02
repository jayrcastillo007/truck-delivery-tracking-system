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
        Schema::table('transactions', function (Blueprint $table) {
             // Pickup coordinates
            $table->decimal('pickup_lat', 10, 8)->nullable()->after('pickup_location');
            $table->decimal('pickup_long', 11, 8)->nullable()->after('pickup_lat');

            // Dropoff coordinates
            $table->decimal('dropoff_lat', 10, 8)->nullable()->after('dropoff_location');
            $table->decimal('dropoff_long', 11, 8)->nullable()->after('dropoff_lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
           $table->dropColumn([
                'pickup_lat',
                'pickup_long',
                'dropoff_lat',
                'dropoff_long'
            ]);
        });
    }
};
