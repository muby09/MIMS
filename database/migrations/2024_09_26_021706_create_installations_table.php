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
        Schema::create('installations', function (Blueprint $table) {
            $table->id();
            $table->string('region_pid');
            $table->foreign('region_pid')->references('pid')->on('regions')->onDelete('cascade');
            $table->string('meter_number');
            $table->string('pid')->unique();
            $table->string('preload');
            $table->string('state');
            $table->string('doi',20)->nullable();
            $table->string('dt_name')->nullable();
            $table->string('dt_type')->nullable();
            $table->string('upriser')->nullable();
            $table->string('pole')->nullable();
            $table->string('tariff')->nullable();
            $table->string('advtariff')->nullable();
            $table->string('title')->nullable();
            $table->string('fullname')->nullable();
            $table->string('gsm')->nullable();
            $table->string('email')->nullable();
            $table->string('premises')->nullable();
            $table->string('phase')->nullable();
            $table->longText('address')->nullable();
            $table->longText('remark')->nullable();
            $table->string('feeder_33kv')->nullable();
            $table->string('feeder_11kv')->nullable();
            $table->string('meter_type')->nullable();
            $table->string('meter_brand')->nullable();
            $table->string('meter_tech')->nullable();
            $table->string('estimated')->nullable();
            $table->string('account_no')->nullable();
            $table->string('business_unit')->nullable();
            $table->string('x_cordinate')->nullable();
            $table->string('y_cordinate')->nullable();
            $table->string('installer')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('rf_channel')->nullable();
            $table->string('din')->nullable();
            $table->string('seal')->nullable();
            $table->string('dt_code')->nullable();
            $table->string('trading_zone')->nullable();
            $table->string('team_pid')->nullable();
            $table->string('creator')->nullable();
            $table->foreign('creator')->references('pid')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meters');
    }
};
