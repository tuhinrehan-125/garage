<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SaleResource extends JsonResource
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
            'business_location_id' => $this->business_location_id,
            'supplier_id' => $this->contact_id,
            'sale_tax' => $this->sale_tax,
            'sale_date' => $this->sale_date,
            'subtotal_cost' => $this->subtotal_cost,
            'sale_discount' => $this->sale_discount,
            'total_cost' => $this->total_cost,
            'shipping_charge' => $this->shipping_cost,
        ];
    }
}
