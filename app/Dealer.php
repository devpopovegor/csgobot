<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    protected $fillable = [
    	'first_name',
	    'last_name',
	    'username'
    ];
}
