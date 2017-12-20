<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Report
 *
 * @property int $id
 * @property int $item_id
 * @property int $site_id
 * @property string|null $float
 * @property string|null $pattern
 * @property string|null $client
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Item $item
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereFloat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report wherePattern($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereSiteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Report whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Report extends Model
{
    protected $fillable = ['item_id', 'site_id', 'float', 'id', 'chat_id', 'pattern', 'client'];

    public function item(){
        return $this->belongsTo(Item::class);
    }
}
