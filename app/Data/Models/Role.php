<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Data\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Role newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Role onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Role query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Role whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Role whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Role withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Role withoutTrashed()
 * @mixin \Eloquent
 */
class Role extends Model
{
    use SoftDeletes;
}
