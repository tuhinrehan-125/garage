<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class VehicleType extends Model
{
    use HasFactory;
    protected $table = "vehicle_types";
    protected $fillable = [
        'name', 'description',
    ];

    public function vehicles(){

        return $this->hasMany(Vehicle::class,'id','type_id');
    }

    public function scopeActive($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
