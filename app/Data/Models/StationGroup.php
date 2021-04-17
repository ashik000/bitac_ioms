<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Data\Models\StationGroup
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\StationGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationGroup query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\StationGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\StationGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\StationGroup withoutTrashed()
 * @mixin \Eloquent
 */
class StationGroup extends Model
{
    use SoftDeletes;
}
