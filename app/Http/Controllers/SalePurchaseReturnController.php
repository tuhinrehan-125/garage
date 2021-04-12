<?php

namespace App\Http\Controllers;

use App\Models\SalePurchaseReturn;
use App\Http\Resources\SalePurchaseReturnResource;
use App\Models\BusinessLocation;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\PurchasePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class SalePurchaseReturnController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('jwt', ['except' => ['index']]);
    // }

    public function index()
    {
        $spr = SalePurchaseReturn::whereNotNull('purchase_id')->get();
        return response(SalePurchaseReturnResource::collection($spr), Response::HTTP_OK);
    }
    public function index2()
    {
        $spr = SalePurchaseReturn::whereNotNull('sale_id')->get();
        return response(SalePurchaseReturnResource::collection($spr), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function returnPurchase(Request $request, $id)
    {

        DB::beginTransaction();
        try {

            $purchase = Purchase::findOrFail($id);


            // if ($request->has('purchase_status')) {
            //     $purchase->purchase_status = $request->purchase_status;
            // }


            $purchase->updated_by = auth()->user()->id;

            $item_purchase_quantity = 0;
            $item_subtotal_price = 0;

            $totalQty = 0;
            $totalPrice = 0;

            $existing_purchased_items = PurchaseItem::where('purchase_id', $purchase->id)->where('product_id', $request->product_id)->where('variation_id', $request->variation_id)->first();
            if ($existing_purchased_items) {
                $purchase_item_update = PurchaseItem::find($existing_purchased_items->id);
                $purchase_item_update->purchase_id = $purchase->id;
                $purchase_item_update->product_id = $request->product_id;
                $purchase_item_update->variation_id = $request->variation_id;

                $purchase_item_update->purchase_quantity = $purchase_item_update->purchase_quantity - $request->quantity;

                $item_purchase_quantity = $purchase_item_update->purchase_quantity;
                $purchase_item_update->total_price = $purchase_item_update->purchase_price * $item_purchase_quantity;
                $item_subtotal_price = $purchase_item_update->total_price;

                //for save to the sale-purchase-return table
                $totalQty = $request->quantity;
                $totalPrice = $request->quantity * $purchase_item_update->purchase_price;

                $purchase_item_update->save();
            }

            $purchase->total_purchase_quantity = $item_purchase_quantity;
            $purchase->subtotal_cost = $item_subtotal_price;

            $discountInPercentage = ($purchase->purchase_discount / 100);
            $afterDiscount = $purchase->subtotal_cost - ($purchase->subtotal_cost * $discountInPercentage);

            $taxInPercentage = ($purchase->purchase_tax / 100);
            $afterTax = $purchase->subtotal_cost + ($purchase->subtotal_cost * $taxInPercentage);

            $purchase->total_cost = $purchase->subtotal_cost - $afterDiscount + $afterTax;

            $purchase->save();

            // Adding return items to the sale_purchase_return table.
            $business_id = auth()->user()->business_id;
            $spr = new SalePurchaseReturn();
            SalePurchaseReturn::savePurchaseItemReturn($purchase->id, $business_id, $request->business_location_id, $request->product_id,  $totalQty, $totalPrice);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'errmsg' => $e->getMessage()], 500);
        }
        DB::commit();

        return response(new SalePurchaseReturnResource($purchase), Response::HTTP_CREATED);
    }

    public function returnSale(Request $request, $id)
    {

        DB::beginTransaction();
        try {

            $sale = Sale::findOrFail($id);

            $sale->updated_by = auth()->user()->id;

            $item_sale_quantity = 0;
            $item_subtotal_price = 0;

            $totalQty = 0;
            $totalPrice = 0;

            $existing_sold_items = SaleItem::where('sale_id', $sale->id)->where('product_id', $request->product_id)->where('variation_id', $request->variation_id)->first();
            if ($existing_sold_items) {
                $sale_item_update = SaleItem::find($existing_sold_items->id);
                $sale_item_update->sale_id = $sale->id;
                $sale_item_update->product_id = $request->product_id;
                $sale_item_update->variation_id = $request->variation_id;

                $sale_item_update->sale_quantity = $sale_item_update->sale_quantity - $request->quantity;

                $item_sale_quantity = $sale_item_update->sale_quantity;
                $sale_item_update->total_price = $sale_item_update->sale_price * $item_sale_quantity;
                $item_subtotal_price = $sale_item_update->total_price;

                //for save to the sale-purchase-return table
                $totalQty = $request->quantity;
                $totalPrice = $request->quantity * $sale_item_update->sale_price;

                $sale_item_update->save();
            }

            $sale->total_sale_quantity = $item_sale_quantity;
            $sale->subtotal_cost = $item_subtotal_price;

            $discountInPercentage = ($sale->sale_discount / 100);
            $afterDiscount = $sale->subtotal_cost - ($sale->subtotal_cost * $discountInPercentage);

            $taxInPercentage = ($sale->sale_tax / 100);
            $afterTax = $sale->subtotal_cost + ($sale->subtotal_cost * $taxInPercentage);

            $sale->total_cost = $sale->subtotal_cost - $afterDiscount + $afterTax;

            $sale->save();

            // Adding return items to the sale_purchase_return table.
            $business_id = auth()->user()->business_id;
            $spr = new SalePurchaseReturn();
            SalePurchaseReturn::saveSaleItemReturn($sale->id, $business_id, $request->business_location_id, $request->product_id,  $totalQty, $totalPrice);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'errmsg' => $e->getMessage()], 500);
        }
        DB::commit();

        return response(new SalePurchaseReturnResource($sale), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SalePurchaseReturn  $salePurchaseReturn
     * @return \Illuminate\Http\Response
     */
    public function show(SalePurchaseReturn $salePurchaseReturn)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SalePurchaseReturn  $salePurchaseReturn
     * @return \Illuminate\Http\Response
     */
    public function edit(SalePurchaseReturn $salePurchaseReturn)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SalePurchaseReturn  $salePurchaseReturn
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SalePurchaseReturn $salePurchaseReturn)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SalePurchaseReturn  $salePurchaseReturn
     * @return \Illuminate\Http\Response
     */
    public function destroy(SalePurchaseReturn $salePurchaseReturn)
    {
        //
    }
}
