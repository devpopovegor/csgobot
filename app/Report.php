<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['item_id', 'site_id', 'float', 'id', 'chat_id', 'pattern', 'client'];

    public function item(){
        return $this->belongsTo(Item::class);
    }
}
