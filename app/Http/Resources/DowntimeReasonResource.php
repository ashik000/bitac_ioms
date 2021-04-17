<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DowntimeReasonResource extends JsonResource
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
            'id'    =>  $this->id,
            'name'  =>  $this->name,
            'type'  =>  $this->type,
            'reason_group' => [
                'id' => $this->downtimeReasonGroup->id,
                'name' => $this->downtimeReasonGroup->name,
            ]
        ];
    }
}
