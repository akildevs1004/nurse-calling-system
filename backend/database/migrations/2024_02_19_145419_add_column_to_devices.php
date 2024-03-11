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
            $table->integer("smoke_alarm_status")->default(0);
            $table->dateTime("smoke_alarm_start_datetime")->nullable();
            $table->dateTime("smoke_alarm_end_datetime")->nullable();
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
            $table->dropColumn("smoke_alarm_status");
            $table->dropColumn("smoke_alarm_start_datetime");
            $table->dropColumn("smoke_alarm_end_datetime");
        });
    }
};
