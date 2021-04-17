<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Data\Models\StationOperator
 *
 * @property int $id
 * @property int $station_id
 * @property int $operator_id
 * @property string $start_time
 * @property string|null $end_time
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Data\Models\Operator $operator
 * @property-read \App\Data\Models\Station $station
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationOperator active()
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationOperator newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationOperator newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\StationOperator onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationOperator query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationOperator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationOperator whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationOperator whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationOperator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationOperator whereOperatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationOperator whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationOperator whereStationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationOperator whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\StationOperator withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\StationOperator withoutTrashed()
 * @mixin \Eloquent
 */

class StationOperator extends Model
{
    use SoftDeletes;

    public function operator() {
        return $this->belongsTo(Operator::Class);
    }

    public function station() {
        return $this->belongsTo(Station::Class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('end_time');
    }
}
