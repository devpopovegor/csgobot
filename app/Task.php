<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['item_id', 'site_id', 'float', 'id', 'chat_id', 'pattern', 'client'];

    public function item(){
        return $this->belongsTo(Item::class);
    }

    public function steams()
    {
        return $this->hasMany(Steam::class);
    }
}
