<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Contact extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "contacts";
    // public $timestamps = true;

    protected $filable = [
        'name',
        'type',
        "owner_id",
        'email',
        'address',
        'mobile',
        'deleted_at',
    ];

    public function scopeActive($query, $type)
    {
        return $query->where('type', $type)->where('owner_id',Auth::user()->id);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

}
