<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetupItemsSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::create('items', function (Blueprint $table) {
		    $table->increments('id');
		    $table->string('name');
			$table->string('phase')->nullable();
		    $table->timestamps();
	    });

	    Schema::create('sites', function (Blueprint $table) {
		    $table->increments('id');
		    $table->string('url');
		    $table->string('get_data');
			$table->boolean('active');
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
	    Schema::drop('items');
	    Schema::drop('sites');
    }
}
