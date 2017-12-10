<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Paintseed extends Model
{
    protected $fillable = ['item_id', 'value', 'name', 'steam_id'];

    public function item(){
        return $this->belongsTo(Item::class);
    }
}
