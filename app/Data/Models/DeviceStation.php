<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Data\Models\DeviceStation
 *
 * @property int $id
 * @property int $device_id
 * @property int $station_id
 * @property int $port
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DeviceStation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DeviceStation newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\DeviceStation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DeviceStation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DeviceStation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DeviceStation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DeviceStation whereDeviceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DeviceStation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DeviceStation wherePort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DeviceStation whereStationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DeviceStation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\DeviceStation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\DeviceStation withoutTrashed()
 * @mixin \Eloquent
 */
class DeviceStation extends Model
{
    use SoftDeletes;


}
