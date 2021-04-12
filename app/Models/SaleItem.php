<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    use HasFactory;
    protected $table = "sale_items";

    protected $fillable = [
        'sale_id',  'product_id', 'variation_id', 'sale_quantity', 'sale_price', 'total_price'
    ];

    public static function saveSaleItems($sale_id, $store_items)
    {
        $sale_item = SaleItem::create([
            'sale_id' => $sale_id,
            'product_id' => $store_items['product_id'],
            'variation_id' => $store_items['variation_id'],
            'sale_quantity' => $store_items['sale_quantity'],
            'sale_price' => $store_items['sale_price'],
            'total_price' => $store_items['sale_quantity'] * $store_items['sale_price'],
        ]);

        return $sale_item;
    }
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function SalePurchaseReturn()
    {
        return $this->belongsTo(SalePurchaseReturn::class);
    }
}
