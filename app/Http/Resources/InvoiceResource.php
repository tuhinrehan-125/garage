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
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'contact_id' => $this->contact_id,
            'client_name' => $this->client? $this->client->name : null,
            'date' => $this->date,
            'total_amount' => $this->total_cost,
            'paid_amount' => $this->paid_price,
            'due_amount' => $this->due_price,
            'vat' => $this->vat,
            'status' => $this->status,

        ];
    }
}
