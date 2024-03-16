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
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('short_name');
            $table->dropColumn('device_id');
            $table->dropColumn('ip');
            $table->dropColumn('port');
            $table->dropColumn('camera_sdk_url');
            $table->dropColumn('camera_save_images');
            $table->dropColumn('temparature_alarm_status');
            $table->dropColumn('temparature_alarm_start_datetime');
            $table->dropColumn('temparature_alarm_end_datetime');
            $table->dropColumn('humidity_alarm_status');
            $table->dropColumn('humidity_alarm_start_datetime');
            $table->dropColumn('humidity_alarm_end_datetime');
            $table->dropColumn('fire_alarm_status');
            $table->dropColumn('fire_alarm_start_datetime');
            $table->dropColumn('fire_alarm_end_datetime');
            $table->dropColumn('water_alarm_status');
            $table->dropColumn('water_alarm_start_datetime');
            $table->dropColumn('water_alarm_end_datetime');
            $table->dropColumn('power_alarm_status');
            $table->dropColumn('power_alarm_start_datetime');
            $table->dropColumn('power_alarm_end_datetime');
            $table->dropColumn('door_open_status');
            $table->dropColumn('door_open_start_datetime');
            $table->dropColumn('door_open_end_datetime');
            $table->dropColumn('smoke_alarm_status');
            $table->dropColumn('smoke_alarm_start_datetime');
            $table->dropColumn('smoke_alarm_end_datetime');
            $table->dropColumn('smoke_enabled');
            $table->dropColumn('water_enabled');
            $table->dropColumn('acpower_enabled');
            $table->dropColumn('door_enabled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('devices', function (Blueprint $table) {
            //
        });
    }
};
