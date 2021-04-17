<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin  \App\Data\Models\ProductionLog
 */
class ProductionLogLineData extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $date = Carbon::parse($this->produced_at);

        $secondOfHour = $date->minute * 60 + $date->second - $this->cycle_time;

        if ($secondOfHour < 0) {
            $secondOfHour = 0;
        }

        return [
            'type'           => 'log',
            'id'             => $this->id,
            'start_time'     => $date->toDateTimeString(),
            'second_of_hour' => $secondOfHour,
            'duration'       => $this->cycle_time,
        ];
    }
}
