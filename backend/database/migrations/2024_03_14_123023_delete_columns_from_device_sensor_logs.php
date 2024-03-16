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
        Schema::table('device_sensor_logs', function (Blueprint $table) {
            $table->dropColumn('water_leakage');
            $table->dropColumn('power_failure');
            $table->dropColumn('door_status');
            $table->dropColumn('fire_alarm');
            $table->dropColumn('smoke_alarm');
            $table->integer('alarm_status')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('device_sensor_logs', function (Blueprint $table) {
            //
        });
    }
};
