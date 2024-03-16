<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices_alarm_logs', function (Blueprint $table) {
            $table->id();
            $table->integer("company_id")->nullable();
            $table->string("serial_number")->nullable();
            $table->dateTime("alarm_start_datetime")->nullable();
            $table->dateTime("alarm_end_datetime")->nullable();
            $table->integer("response_minutes")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devices_alarm_logs');
    }
};
