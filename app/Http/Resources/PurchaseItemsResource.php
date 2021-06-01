<?php

namespace App\Http\Resources;

use App\Models\ProductVariation;
use App\Models\Purchase;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $purchase = Purchase::find($this->purchase_id);
        
        return [
            'id' => $this->id,
            'purchase_id' => $this->purchase_id ? $this->purchase_id : 'N\A',
            'purchase' => $this->purchase ? $this->purchase->name  : 'N/A',
            'product_id' => $this->product_id,
            'product' => $this->product ? $this->product->name : '',
            'quantity' => $this->purchase_quantity->$purchase_returned,
            'purchase_quantity' => $this->purchase_quantity ? $this->purchase_quantity : 'N/A',
            'purchase_price' => $this->purchase_price ? $this->purchase_price : 'N/A',
            'total_price' => $this->total_price ? $this->total_price : 'N/A',
        ];
    }
}
