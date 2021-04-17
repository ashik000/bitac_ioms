<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Data\Models\SlowProduction
 *
 * @property int $id
 * @property int $production_log_id
 * @property string $start_time
 * @property int $duration
 * @property string|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\SlowProduction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\SlowProduction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\SlowProduction query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\SlowProduction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\SlowProduction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\SlowProduction whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\SlowProduction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\SlowProduction whereProductionLogId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\SlowProduction whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\SlowProduction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SlowProduction extends Model
{
    //
}
