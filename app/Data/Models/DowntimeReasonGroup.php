<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Data\Models\DowntimeReasonGroup
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReasonGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReasonGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\DowntimeReasonGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReasonGroup query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReasonGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReasonGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReasonGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReasonGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReasonGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\DowntimeReasonGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\DowntimeReasonGroup withoutTrashed()
 * @mixin \Eloquent
 */
class DowntimeReasonGroup extends Model
{
    use SoftDeletes;
    protected $hidden = ['deleted_at','created_at','updated_at'];
}
