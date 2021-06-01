<?php

namespace App\Http\Resources;

use App\Models\Invoice;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceItemResource extends JsonResource
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

            'id' => $this->product_id ? $this->product_id : $this->service_id,
            'vehicle_id' => $this->vehicle_id ? $this->vehicle_id : 'N\A',
            'vehicle_name' => $this->vehicle ? $this->vehicle->model : 'N/A',
            'reg_no' => $this->vehicle ? $this->vehicle->reg_no : 'N/A',
            "category_type" =>$this->product_id ? "Product":"Service",
            'chassis_no' => $this->vehicle ? $this->vehicle->chassis_no : 'N/A',
            'name' => $this->product ? $this->product->name : $this->service->name,
            'invoice_quantity' => $this->product_quantity ? $this->product_quantity : $this->service_quantity,
            'price' => $this->product_rate ? $this->product_rate : $this->service_rate,
            'subtotal' => $this->total_price ? $this->total_price : 'N/A',
        ];
    }
}
