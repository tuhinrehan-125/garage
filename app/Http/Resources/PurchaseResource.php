<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
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
            'purchase_status' => $this->purchase_status,
            'purchase_date' => $this->purchase_date,
            'total_purchase_quantity' => $this->total_purchase_quantity,
            'subtotal_cost' => $this->subtotal_cost,
            'purchase_discount' => $this->purchase_discount,
            'purchase_tax' => $this->purchase_tax,
            'total_cost' => $this->total_cost,
            'shipping_charge' => $this->shipping_charge,
            'shipping_details' => $this->shipping_details,
        ];
    }
}
