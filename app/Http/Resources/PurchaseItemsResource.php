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
        //get previous purchase return quantity
        $purchase_returned=0;
        $purchase = Purchase::find($this->purchase_id);
        $purchase_returned=$purchase->return->where('variation_id', $this->product_variation_id)->sum('return_quantity');

        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_variation_id' => $this->product_variation_id,
            'product' => $this->product ? $this->product->name : '',
            'product_variation' => $this->productVariation ? $this->productVariation->name : '',
            'quantity' => $this->purchase_quantity-$purchase_returned,
        ];
    }
}
