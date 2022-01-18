<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Data\Models\Station
 *
 */
class StationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'description'   => $this->description,
            'station_type'  => $this->station_type,
            'oee_threshold' => $this->oee_threshold,
            'station_group' => new StationGroupResource($this->stationGroup),
        ];
    }
}
