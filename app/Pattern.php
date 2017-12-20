<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Pattern
 *
 * @property int $id
 * @property int $item_id
 * @property string $name
 * @property int $value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Item $item
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pattern whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pattern whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pattern whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pattern whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pattern whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Pattern whereValue($value)
 * @mixin \Eloquent
 */
class Pattern extends Model
{
    protected $fillable = ['name', 'item_id', 'value'];

    public function item(){
        return $this->belongsTo(Item::class);
    }

}
