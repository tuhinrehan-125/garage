<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "sales";

    protected $fillable = [
        'ref_no',  'contact_id', 'business_location_id', 'sale_tax', 'sale_discount', 'shipping_cost', 'sale_status', 'total_sale_quantity', 'total_cost', 'subtotal_cost', 'payment_status', 'sale_date', 'sale_note', 'staff_note', 'created_by'
    ];

    public function sale_items()
    {
        return $this->hasMany(SaleItem::class);
    }
    public function SalePurchaseReturn()
    {
        return $this->belongsTo(SalePurchaseReturn::class);
    }
}
