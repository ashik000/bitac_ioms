<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin  \App\Data\Models\SlowProduction
 */
class SlowProductionLineData extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $date = Carbon::parse($this->start_time);

        return [
            'type'           => 'slow',
            'id'             => $this->id,
            'start_time'     => $date->toDateTimeString(),
            'second_of_hour' => $date->minute * 60 + $date->second,
            'duration'       => $this->duration,
        ];
    }
}
