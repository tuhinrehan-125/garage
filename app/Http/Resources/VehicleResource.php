<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'contact_id' => $this->contact? $this->contact->name : '',
            'brand_id' => $this->brand_id? $this->brand->name : $this->brand_name,
            'reg_no' => $this->reg_no,
            'chassis_no' => $this->chassis_no,
            'mileage' => $this->mileage,
            'model' => $this->model,
            'color_id' => $this->color? $this->color->name : '',
            'type_id' => $this->vehicleType? $this->vehicleType->name : '',
            'description' => $this->description,
        ];
    }
}
