<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFkPs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paintseeds', function (Blueprint $table) {
            $table->integer('steam_id')->unsigned();
            $table->foreign('steam_id')->references('id')->on('items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('paintseeds', function (Blueprint $table) {
            $table->dropForeign(['steam_id']);
        });
    }
}
