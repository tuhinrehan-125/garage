<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category? $this->category->name:'',
            'category_id' => $this->category_id? $this->category_id:'',
            'buying_price' => $this->buying_price,
            'selling_price' => $this->selling_price,
            'quantity' => $this->quantity,
            'brand' => $this->brand,
            'status' => $this->status,

        ];
    }
}
