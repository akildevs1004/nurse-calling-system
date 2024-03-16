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
        Schema::table('companies', function (Blueprint $table) {
            $table->string("device_normal_top_color")->nullable();
            $table->string("device_normal_body_color")->nullable();
            $table->string("device_alarm_top_color")->nullable();
            $table->string("device_alarm_body_color")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn("device_normal_top_color");
            $table->dropColumn("device_normal_body_color");
            $table->dropColumn("device_alarm_top_color");
            $table->dropColumn("device_alarm_body_color");
        });
    }
};
