<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Data\Models\Product
 *
 */
class ProductResource extends JsonResource
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
            "product"=>[
                "id"    =>$this->id,
                "name"  =>$this->name,
                "code"  =>$this->code,
                "unit"  =>$this->unit,
                "product_group"=>[
                    "id"     => $this->productGroup->id,
                    "name"   => $this->productGroup->name
                ]

            ]
        ];
    }
}
