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
            //'business_location' => $this->Location->name,
            'owner' => $this->User->name,
            'supplier_id' => $this->Supplier->name,
            'purchase_status' => $this->purchase_status,
            'purchase_date' => $this->purchase_date,
            'total_purchase_quantity' => $this->total_purchase_quantity,
            'subtotal_cost' => $this->subtotal_cost,
            'purchase_discount' => $this->purchase_discount,
            'purchase_tax' => $this->purchase_tax,
            'total_cost' => round($this->total_cost,2),
            // 'shipping_charge' => $this->shipping_charge,
            // 'shipping_details' => $this->shipping_details,
            //'total_paid' =>round($this->payments->sum('payment_amount'),2),
            //'total_due' =>round($this->total_cost-$this->payments->sum('payment_amount'),2)
        ];

        if ($request->route()->parameters()) {
            
            $data['items'] = PurchaseItemsResource::collection($this->purchaseItems);
        }
        return $data;
    }
}

