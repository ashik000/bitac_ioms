<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Data\Models\Downtime
 *
 * @property int $id
 * @property int $production_log_id
 * @property int|null $reason_id
 * @property string $start_time
 * @property int $duration
 * @property int|null $shift_id
 * @property int|null $operator_id
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Data\Models\DowntimeReason|null $reason
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Downtime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Downtime newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Downtime query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Downtime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Downtime whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Downtime whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Downtime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Downtime whereOperatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Downtime whereProductionLogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Downtime whereReasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Downtime whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Downtime whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Downtime whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Downtime extends Model
{
    public function reason()
    {
        return $this->belongsTo(DowntimeReason::class, 'reason_id');
    }

    protected $fillable = [
        'id',
        'start_time',
        'duration',
        'production_log_id',
        'shift_id',
        'operator_id',
        'team_id',
        'created_at',
        'updated_at'
    ];
}
