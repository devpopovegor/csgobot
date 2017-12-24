<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Steam
 *
 * @property int $id
 * @property string $steam_id
 * @property int $task_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Task $task
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Steam whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Steam whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Steam whereSteamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Steam whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Steam whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Steam extends Model
{
    protected $fillable = ['steam_id', 'task_id'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
