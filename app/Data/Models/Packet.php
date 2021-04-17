<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Data\Models\Packet
 *
 * @property int $id
 * @property int $device_id
 * @property string $request
 * @property string $response
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $processing_start
 * @property \Illuminate\Support\Carbon|null $processing_end
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Device newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Device newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Device onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Device query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Device whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Device whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Device whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Device whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Device whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Device withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Device withoutTrashed()
 * @mixin \Eloquent
 */
class Packet extends Model
{
    use SoftDeletes;
}
