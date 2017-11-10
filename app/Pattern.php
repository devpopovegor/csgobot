<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pattern extends Model
{
    protected $fillable = ['name', 'item_id', 'value'];

    public function item(){
        return $this->belongsTo(Item::class);
    }

}
