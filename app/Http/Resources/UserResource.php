<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);

        return [
            'id' => $this->id,
            'surname' => $this->surname,
            'first_name' => ucfirst($this->first_name),
            'last_name' => ucfirst($this->last_name),
            'username' => $this->username,
            'email' => $this->email,
//            'business_id' => $this->business_id,
            'language' => $this->language,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null,
//            'user_business_location' => auth()->user()->Business->location,
        ];
    }
}
