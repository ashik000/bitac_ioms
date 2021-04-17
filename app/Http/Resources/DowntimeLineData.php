<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin  \App\Data\Models\Downtime
 */
class DowntimeLineData extends JsonResource
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
            'type'           => 'downtime',
            'id'             => $this->id,
            'start_time'     => $date->toDateTimeString(),
            'second_of_hour' => $date->minute * 60 + $date->second,
            'duration'       => $this->duration,
            'reason'         => $this->when($this->reason, function () {
                return [
                    'id'   => $this->reason->id,
                    'name' => $this->reason->name,
                    'type' => $this->reason->type,
                    'reason_group' => [
                        'id' => $this->reason->downtimeReasonGroup->id,
                        'name' => $this->reason->downtimeReasonGroup->name,
                        ],
                ];
            }),
        ];
    }
}
