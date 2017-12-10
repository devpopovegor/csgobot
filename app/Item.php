<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
	protected $fillable = ['name', 'phase', 'id', 'full_name'];

	public function tasks(){
        return $this->hasMany(Task::class);
    }

    public function patterns(){
        return $this->hasMany(Pattern::class);
    }

    public function paintseeds(){
        return $this->hasMany(Paintseed::class);
    }
}
