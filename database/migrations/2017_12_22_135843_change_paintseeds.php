<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangePaintseeds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('paintseeds', function (Blueprint $table) {
		    $table->dropForeign(['steam_id']);
		    $table->renameColumn('item_id', 'steam');
		    $table->renameColumn('steam_id', 'item_id');
		    $table->foreign('item_id')->references('id')->on('items');
		    $table->dropColumn(['name']);
		    $table->string('pattern_name')->nullable();
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('paintseeds', function (Blueprint $table) {
		    $table->dropForeign(['item_id']);
		    $table->renameColumn('item_id', 'steam_id');
		    $table->renameColumn('steam', 'item_id');
		    $table->foreign('steam_id')->references('id')->on('items');
//		    $table->dropColumn(['name']);
	    });
    }
}
