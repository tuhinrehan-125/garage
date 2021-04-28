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

    public static function saveSellItems($sell_id, $item, $categoryId)
    {
        $sale_item = SaleItem::create([
            'sell_id' => $sell_id,
//            'product_id' => $item['product_id'],
//            'variation_id' => $item['variation_id'],
//            'sale_quantity' => $item['sell_quantity'],
//            'sale_price' => $item['sell_price'],
            'total_price' => $item['subtotal']
        ]);

        if($sale_item)
        {
            if($categoryId=='1'){
                SaleItem::create([
                    'product_id' => $item['product_id'],

                    'product_rate' => $item['product_rate'],
//                    'product_total_price' => $item['product_subtotal']

                ]);
            }
            else{
                SaleItem::create([
                    'service_id' => $item['service_id'],
//                    'service_quantity' => $item['service_quantity'],
////                    'sale_price' => $item['sell_price'],
                    'service_rate' => $item['service_rate'],
//                    'service_total_price' => $item['service_subtotal']

                ]);
            }

        }

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
