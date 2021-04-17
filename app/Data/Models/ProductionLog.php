<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Data\Models\ProductionLog
 *
 * @property int $id
 * @property int $station_id
 * @property int $product_id
 * @property int $cycle_interval
 * @property string $produced_at
 * @property string $synced_at
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Data\Models\Station $station
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductionLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductionLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductionLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductionLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductionLog whereCycleInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductionLog whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductionLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductionLog whereProducedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductionLog whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductionLog whereStationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductionLog whereSyncedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductionLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProductionLog extends Model
{
    public function station()
    {
        return $this->belongsTo(Station::class, 'station_id');
    }
}
