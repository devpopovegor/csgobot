<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Dealer
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property int $subscription
 * @property string $start_subscription
 * @property string $end_subscription
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dealer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dealer whereEndSubscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dealer whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dealer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dealer whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dealer whereStartSubscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dealer whereSubscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dealer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Dealer whereUsername($value)
 * @mixin \Eloquent
 */
class Dealer extends Model
{
    protected $fillable = [
    	'first_name',
	    'last_name',
	    'username'
    ];
}
