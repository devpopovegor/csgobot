<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Site
 *
 * @property int $id
 * @property string $url
 * @property string $get_data
 * @property int $active
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Site whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Site whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Site whereGetData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Site whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Site whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Site whereUrl($value)
 * @mixin \Eloquent
 */
class Site extends Model
{
    protected $fillable = ['url', 'get_data', 'active', 'id'];
}
