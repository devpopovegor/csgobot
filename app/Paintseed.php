<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Paintseed
 *
 * @property int $id
 * @property int $steam_id
 * @property string $value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string $name
 * @property int|null $item_id
 * @property-read \App\Item|null $item
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Paintseed whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Paintseed whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Paintseed whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Paintseed whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Paintseed whereSteamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Paintseed whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Paintseed whereValue($value)
 * @mixin \Eloquent
 */
class Paintseed extends Model
{
    protected $fillable = ['item_id', 'value', 'name', 'steam_id', 'float'];

    public function item(){
        return $this->belongsTo(Item::class);
    }
}
