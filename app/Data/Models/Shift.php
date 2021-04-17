<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Data\Models\Shift
 *
 * @property int $id
 * @property string $name
 * @property string $start_time
 * @property string $end_time
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Shift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Shift newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Shift onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Shift query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Shift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Shift whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Shift whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Shift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Shift whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Shift whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Shift whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Shift withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Shift withoutTrashed()
 * @mixin \Eloquent
 */
class Shift extends Model
{
    use SoftDeletes;
}
