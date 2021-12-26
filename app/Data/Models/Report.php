<?php

namespace App\Data\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Data\Models\Report
 *
 * @property int $id
 * @property string $tag
 * @property int $station_id
 * @property int $team_id
 * @property int $product_id
 * @property int|null $operator_id
 * @property int $shift_id
 * @property string $generated_at
 * @property int $expected
 * @property int $produced
 * @property int $scraped
 * @property int $available
 * @property int $unplanned_downtime
 * @property int $planned_downtime
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Data\Models\Operator|null $operator
 * @property-read \App\Data\Models\Product $product
 * @property-read \App\Data\Models\Shift $shift
 * @property-read \App\Data\Models\Station $station
 * @property-read \App\Data\Models\Team $team
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report whereExpected($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report whereGeneratedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report whereOperatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report wherePlannedDowntime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report whereProduced($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report whereScraped($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report whereStationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report whereUnplannedDowntime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Data\Models\Report whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Report extends Model
{
    //
    public function station() {
        return $this->belongsTo(Station::Class);
    }

    public function team() {
        return $this->belongsTo(Team::Class);
    }


    public function product() {
        return $this->belongsTo(Product::Class);
    }

    public function shift() {
        return $this->belongsTo(Shift::Class);
    }

    public function operator() {
        return $this->belongsTo(Operator::Class);
    }
}
