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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('pid')->unique();
            $table->string('creator')->nullable();
            $table->string('allocated_meter_number')->nullable();
            $table->string('account_number')->nullable();
            $table->string('meter_type')->nullable();
            $table->string('map')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('feeder_name')->nullable();
            $table->string('business_unit')->nullable();
            $table->string('state')->nullable();
            $table->string('total_billings')->nullable();
            $table->string('total_settlement')->nullable();
            $table->string('region_pid')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
