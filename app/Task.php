<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Task
 *
 * @property int $id
 * @property int $item_id
 * @property int $site_id
 * @property string|null $float
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $chat_id
 * @property string|null $pattern
 * @property string|null $client
 * @property-read \App\Item $item
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Steam[] $steams
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Task whereChatId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Task whereClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Task whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Task whereFloat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Task whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Task whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Task wherePattern($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Task whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Task whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
