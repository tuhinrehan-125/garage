<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
    public function brand(){
        return $this->belongsTo(Brand::class,'brand_id','id');

    }
    public function color(){
        return $this->belongsTo(Color::class,'color_id','id');
    }
    public function vehicleType(){
        return $this->belongsTo(VehicleType::class,'type_id','id');
    }

    public function scopeActive($query)
    {
        return $query->latest();
    }

}
