<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Data\Models\Station
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $station_group_id
 * @property int $oee_threshold
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Data\Models\ProductionLog[] $productionLogs
 * @property-read int|null $production_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Data\Models\Product[] $products
 * @property-read int|null $products_count
 * @property-read \App\Data\Models\StationGroup $stationGroup
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Station newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Station newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Station onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Station query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Station whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Station whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Station whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Station whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Station whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Station whereOeeThreshold($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Station whereStationGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Station whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Station withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Station withoutTrashed()
 * @mixin \Eloquent
 */
class Station extends Model
{
    use SoftDeletes;

    public function products()
    {
        return $this->belongsToMany(Product::class, 'station_products')
                    ->as('meta')
                    ->withPivot([
                        'cycle_time',
                        'cycle_timeout',
                        'units_per_signal',
                        'performance_threshold',
                    ]);
    }

    public function productionLogs()
    {
        return $this->hasMany(ProductionLog::class);
    }

    public function stationGroup()
    {
        return $this->belongsTo(StationGroup::class);
    }
}
