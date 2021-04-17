<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Data\Models\Product
 *
 * @property int $id
 * @property string $name
 * @property int $product_group_id
 * @property string $code
 * @property string $unit
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Data\Models\ProductGroup $productGroup
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Product newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Product query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Product whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Product whereProductGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Product whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\Product withoutTrashed()
 * @mixin \Eloquent
 */
class Product extends Model
{
    use SoftDeletes;

    public function productGroup(){
        return $this->belongsTo(ProductGroup::class);
    }
}
