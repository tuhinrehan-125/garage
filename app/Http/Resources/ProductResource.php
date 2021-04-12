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
            'type' => $this->type,
            'unit_id' => $this->unit_id,
            'unit_name' => $this->Unit ? $this->Unit->name : null,
            'brand' => $this->Brand?$this->Brand->name:null,
            'category' => $this->Category->name,
            'tax' => $this->tax,
            'tax_type' => $this->tax_type,
            'enable_stock' => $this->enable_stock,
            'alert_quantity' => $this->alert_quantity,
            'sku' => $this->sku,
            'product_description' => $this->product_description,
            'weight' => $this->weight,
            'barcode_type' => $this->barcode_type,
        ];
    }
}
