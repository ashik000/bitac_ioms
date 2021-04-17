<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Data\Models\ProductGroup
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductGroup newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\ProductGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductGroup query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\ProductGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\ProductGroup withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\ProductGroup withoutTrashed()
 * @mixin \Eloquent
 */
class ProductGroup extends Model{
    use SoftDeletes;
    protected $hidden = ['deleted_at','created_at','updated_at'];
}
