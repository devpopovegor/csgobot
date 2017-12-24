<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Item
 *
 * @property int $id
 * @property string $name
 * @property string|null $phase
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $full_name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Paintseed[] $paintseeds
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Pattern[] $patterns
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Task[] $tasks
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereFullName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item wherePhase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Item whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Item extends Model
{
	protected $fillable = ['name', 'phase', 'id', 'full_name'];

	public function tasks(){
        return $this->hasMany(Task::class);
    }

    public function paintseeds(){
        return $this->hasMany(Paintseed::class, 'item_id');
    }

    public function patterns(){
        return $this->hasMany(Pattern::class, 'item_id');
    }
}
