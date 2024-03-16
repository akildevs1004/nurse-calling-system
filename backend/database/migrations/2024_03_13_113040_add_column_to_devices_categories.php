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
        Schema::table('devices_categories', function (Blueprint $table) {
            $table->string("normal_icon")->nullable();
            $table->string("warning_icon")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('devices_categories', function (Blueprint $table) {
            $table->dropColumn("normal_icon");
            $table->dropColumn("warning_icon");
        });
    }
};
