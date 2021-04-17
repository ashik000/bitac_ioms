<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Data\Models\Shift
 *
 */
class ShiftResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'shift' => [
                'id' => $this->id,
                'name' => $this->name,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
            ]
        ];
    }
}
