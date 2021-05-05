<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Category extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "categories";
    // public $timestamps = true;

    protected $filable = [

        'name',
        'description',
        'delete_status'
    ];

    public function SubCategories()
    {
        return $this->hasMany(SubCategory::class, 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function Parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('owner_id',Auth::user()->id)->orderBy('created_at', 'desc');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
