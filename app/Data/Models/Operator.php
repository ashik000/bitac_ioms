<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Data\Models\Operator
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $code
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Data\Models\Station[] $stations
 * @property-read int|null $stations_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Operator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Operator newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Operator onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Operator query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Operator whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Operator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Operator whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Operator whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Operator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Operator whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Operator whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Operator withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Operator withoutTrashed()
 * @mixin \Eloquent
 */

class Operator extends Model
{
    //
    use SoftDeletes;

    public function stations()
    {
        return $this->belongsToMany(Station::class, 'station_operators')
            ->as('meta')
            ->whereNull('station_operators.deleted_at')
            ->whereNull('station_operators.end_time')
            ->withPivot([
                'id',
                'start_time',
                'end_time'
            ]);
    }
}
