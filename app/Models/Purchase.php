<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "purchases";

    protected $fillable = [
        'purchase_status',  'purchase_date', 'total_purchase_quantity', 'subtotal_cost', 'purchase_discount', 'purchase_tax', 'total_cost', 'shipping_charge', 'shipping_details', 'payment_status', 'note', 'purchase_return', 'deleted_at',
    ];

    public function PurchaseItem()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function Supplier()
    {
        return $this->hasOne(Contact::class, 'contact_id');
    }

    public function PurchasePayment()
    {
        return $this->hasMany(PurchasePayment::class, 'purchase_id');
    }

    public function SalePurchaseReturn()
    {
        return $this->hasMany(SalePurchaseReturn::class);
    }
}
