<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Data\Models\StationShift
 *
 * @property int $id
 * @property int $station_id
 * @property int $shift_id
 * @property string $schedule
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Data\Models\Shift $shift
 * @property-read \App\Data\Models\Station $station
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationShift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationShift newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\StationShift onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationShift query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationShift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationShift whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationShift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationShift whereSchedule($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationShift whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationShift whereStationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationShift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\StationShift withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\StationShift withoutTrashed()
 * @mixin \Eloquent
 */
class StationShift extends Model
{
    use SoftDeletes;


    public function station()
    {
        return $this->belongsTo(Station::class,'station_id');
    }
    public function shift(){
        return $this->belongsTo(Shift::class,'shift_id');
    }
}
