<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpeningStockQty extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "opening_stock_qties";
    protected $guarded = [];

    // public static function saveDefaultOpeningStock($business_id, $product_id, $variation_price, $variation_quantity)
    // {
    //     $business = Business::find($business_id);
    //     $business_locations = $business->Location;
    //     if (count($business_locations) > 0) {
    //         foreach ($business_locations as $bl) {
    //             OpeningStockQty::create([
    //                 'business_id' => $business_id,
    //                 'business_location_id' => $bl->id,
    //                 'product_id' => $product_id,
    //                 'price' => $variation_price,
    //                 'quantity' => $variation_quantity,
    //             ]);
    //         }
    //     }
    // }

    public static function saveOpeningStock($product, $opening_stock)
    {
        //store opening stock qty 
        OpeningStockQty::create([
            'business_id' => $product->business_id,
            'business_location_id' => $opening_stock->location_id,
            'product_id' => $product->id,
            'quantity' => $opening_stock->quantity,
        ]);

        //update quantity to  each location 
        $locationprodstock = LocationProductStock::where('product_id', $product->id)->where('business_id', $product->business_id)->where('location_id', $opening_stock->location_id)->first();
        if ($locationprodstock) {
            $locationprodstock->qty_available += $opening_stock->quantity;
            $locationprodstock->save();
        } else {
            LocationProductStock::create([
                'business_id' => $product->business_id,
                'location_id' => $opening_stock->location_id,
                'product_id' => $product->id,
                'qty_available' => $opening_stock->quantity,
            ]);
        }
    }
}
