<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Data\Models\StationProduct
 *
 * @property int $id
 * @property int $station_id
 * @property int $product_id
 * @property int $cycle_time
 * @property string $cycle_unit
 * @property int $cycle_timeout
 * @property int $units_per_signal
 * @property int $performance_threshold
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Data\Models\Product $product
 * @property-read \App\Data\Models\Station $station
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationProduct newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationProduct newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\StationProduct onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationProduct query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationProduct whereCycleTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationProduct whereCycleTimeout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationProduct whereCycleUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationProduct whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationProduct whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationProduct wherePerformanceThreshold($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationProduct whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationProduct whereStationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationProduct whereUnitsPerSignal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\StationProduct withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\StationProduct withoutTrashed()
 * @mixin \Eloquent
 */
class StationProduct extends Model
{
    use SoftDeletes;
    public function station()
    {
        return $this->belongsTo(Station::class,'station_id');
    }
    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }

}
