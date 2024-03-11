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
            $table->integer("smoke_enabled")->default(0);
            $table->integer("water_enabled")->default(0);

            $table->integer("acpower_enabled")->default(0);

            $table->integer("door_enabled")->default(0);
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
            $table->dropColumn("smoke_enabled");
            $table->dropColumn("water_enabled");

            $table->dropColumn("acpower_enabled");

            $table->dropColumn("door_enabled");
        });
    }
};
