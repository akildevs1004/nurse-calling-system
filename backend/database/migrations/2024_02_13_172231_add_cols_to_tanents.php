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
        Schema::table('tanents', function (Blueprint $table) {
            $table->integer("room_category_id")->default(0);
            $table->integer("room_sub_category_id")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tanents', function (Blueprint $table) {
            $table->dropColumn("room_category_id");
            $table->dropColumn("room_sub_category_id");
        });
    }
};
