<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $table ='invoice_items';
    protected $fillable=['invoice_id'];
    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public static function saveInvoiceItems($sell_id, $item)
    {
        $invoice_item = InvoiceItem::create([
            'invoice_id' => $sell_id,
//            'product_id' => $item['product_id'],
//            'variation_id' => $item['variation_id'],
//            'sale_quantity' => $item['sell_quantity'],
//            'sale_price' => $item['sell_price'],
            'total_price' => $item['subtotal']
        ]
        );

        if($invoice_item)
        {
            if($item['type'] == 'product'){
                InvoiceItem::create([
                    'product_id' => $item['id'],

                    'product_rate' => $item['rate'],
//                    'product_total_price' => $item['product_subtotal']

                ]);
            }
            else{
                InvoiceItem::create([
                    'service_id' => $item['id'],
//                    'service_quantity' => $item['service_quantity'],
////                    'sale_price' => $item['sell_price'],
                    'service_rate' => $item['rate'],
//                    'service_total_price' => $item['service_subtotal']

                ]);
            }

        }

        return $invoice_item;

    }


}
