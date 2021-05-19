<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $table = "services";
    protected $fillable = [
        'name', 'category_id', 'selling_price', 'status', 'description'
    ];


    public function invoiceItems(){
        return $this->hasMany(InvoiceItem::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function owner()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeActive($query)
    {
        return $query->where('status','active')->latest();
    }
    // public function scopeActive($query)
    // {
    //     return $query->orderBy('created_at', 'desc');
    // }
}
