<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Data\Models\Scrap
 *
 * @property int $id
 * @property int $value
 * @property string $date
 * @property int $hour
 * @property int $product_id
 * @property int $station_id
 * @property int|null $shift_id
 * @property int|null $operator_id
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Scrap newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Scrap newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Scrap onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Scrap query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Scrap whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Scrap whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Scrap whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Scrap whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Scrap whereHour($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Scrap whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Scrap whereOperatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Scrap whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Scrap whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Scrap whereStationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Scrap whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Scrap whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Scrap withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Scrap withoutTrashed()
 * @mixin \Eloquent
 */
class Scrap extends Model
{
    use SoftDeletes;
    protected $hidden = ['deleted_at','created_at','updated_at'];
}
