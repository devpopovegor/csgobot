<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSteamTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('steams', function (Blueprint $table) {
		    $table->increments('id');
		    $table->string('steam_id');
		    $table->integer('site_id')->unsigned();
		    $table->foreign('site_id')->references('id')->on('sites');
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
	    Schema::dropIfExists('steams');
    }
}
