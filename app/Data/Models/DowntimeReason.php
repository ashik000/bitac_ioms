<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Data\Models\DowntimeReason
 *
 * @property int $id
 * @property int $reason_group_id
 * @property string $name
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Data\Models\DowntimeReasonGroup $downtimeReasonGroup
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReason newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReason newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\DowntimeReason onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReason query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReason whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReason whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReason whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReason whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReason whereReasonGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReason whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\DowntimeReason whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\DowntimeReason withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Data\Models\DowntimeReason withoutTrashed()
 * @mixin \Eloquent
 */
class DowntimeReason extends Model
{
    use SoftDeletes;
    protected $hidden = ['deleted_at','created_at','updated_at'];

    public function downtimeReasonGroup(){
        return $this->belongsTo(DowntimeReasonGroup::class, 'reason_group_id');
    }

}
