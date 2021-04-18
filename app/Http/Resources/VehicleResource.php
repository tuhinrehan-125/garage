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
            'owner' => $this->owner->first_name. ' '. $this->owner->last_name,
            'brand' => $this->brand_id? $this->brand->name : $this->brand_name,
            'reg_no' => $this->reg_no,
            'chassis_no' => $this->chassis_no,
            'model' => $this->model,
            'type' => $this->vehicleType? $this->vehicleType->name : ''
        ];
    }
}
