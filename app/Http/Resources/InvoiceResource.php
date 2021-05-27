<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data= [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'contact_id' => $this->contact_id,
            'client_name' => $this->client? $this->client->name : null,
            'client_phone' => $this->client? $this->client->mobile : null,
            'date' => $this->date,
            'total_amount' => $this->total_cost,
            'paid_price' => $this->paid_price,
//            'due_amount' => $this->due_price,
            'due_price' => $this->due_price,
            'discount' => $this->discount,
            'vat' => $this->vat,
            'status' => $this->status,

        ];
        if ($request->route()->parameters()) {

            $data['items'] = InvoiceItemResource::collection($this->invoiceItems);

        }

        return $data;
    }
}
