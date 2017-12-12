<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Steam extends Model
{
    protected $fillable = ['steam_id', 'task_id'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
