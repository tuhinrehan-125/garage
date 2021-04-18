<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseResource;
use App\Models\BusinessLocation;
use App\Models\Contact;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PurchaseController extends Controller
{
//     public function __construct()
//     {
//         $this->middleware('jwt', ['except' => ['index']]);
//     }

    public function index()
    {
        $purchase = Purchase::where('deleted_at', null)->get();
        return response(PurchaseResource::collection($purchase), Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'business_location_id' => 'required',
                'supplier_id' => 'required',
                'purchase_date' => 'required',
                'purchase_status' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }
        DB::beginTransaction();
        try {

            $purchase = new Purchase();

            $business_location = BusinessLocation::findOrFail($request->business_location_id);
            $purchase->business_location_id = $business_location->id;
            $purchase->contact_id = $request->supplier_id;
            $purchase->purchase_status = $request->purchase_status;
            $purchase->purchase_date = date("Y-m-d", strtotime($request->purchase_date));
            $purchase->purchase_discount = $request->purchase_discount;
            $purchase->purchase_tax = $request->purchase_tax;
            $purchase->shipping_charge = $request->shipping_cost;
            $purchase->shipping_details = $request->shipping_details;
            $purchase->created_by = auth()->user()->id;
            $purchase->updated_by = auth()->user()->id;
            $purchase->save();

            $item_purchase_quantity = 0;
            $item_subtotal_price = 0;
            $afterDiscount=0;
            $afterTax=0;

            foreach ($request->purchase_items as $store_items) {
                $purchase_item = PurchaseItem::savePurchaseItem($purchase->id, $store_items);
                $item_purchase_quantity += $purchase_item->purchase_quantity;
                $item_subtotal_price += $purchase_item->total_price;
            }

            $purchase->total_purchase_quantity = $item_purchase_quantity;
            $purchase->subtotal_cost = $item_subtotal_price;

            if($purchase->purchase_discount>0){
                $discountInPercentage = ($purchase->purchase_discount / 100);
                $afterDiscount = $item_subtotal_price - ($item_subtotal_price * $discountInPercentage);
            }
            if($purchase->purchase_tax>0){
                $taxInPercentage = ($purchase->purchase_tax / 100);
                $afterTax = $item_subtotal_price + ($item_subtotal_price * $taxInPercentage);
            }

            $purchase->total_cost = $item_subtotal_price - ($afterDiscount + $afterTax);

            $purchase->save();

            if ($request->payment_amount != null) {
                PurchasePayment::savePurchasePayment($purchase->id, $request->payment_amount, $request->payment_type, $request->payment_date);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'errmsg' => $e->getMessage()], 500);
        }
        DB::commit();

        return response(new PurchaseResource($purchase), Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {

        DB::beginTransaction();
        try {

            $purchase = Purchase::findOrFail($id);

            // $business_location = BusinessLocation::findOrFail($request->business_location_id);
            // $purchase->business_location_id = $business_location->id;

            if ($request->has('supplier_id')) {
                $purchase->contact_id = $request->supplier_id;
            }
            if ($request->has('purchase_status')) {
                $purchase->purchase_status = $request->purchase_status;
            }
            if ($request->has('purchase_date')) {
                $purchase->purchase_date = date("Y-m-d", strtotime($request->purchase_date));
            }
            if ($request->has('purchase_discount')) {
                $purchase->purchase_discount = $request->purchase_discount;
            }
            if ($request->has('purchase_tax')) {
                $purchase->purchase_tax = $request->purchase_tax;
            }
            if ($request->has('shipping_charge')) {
                $purchase->shipping_charge = $request->shipping_charge;
            }
            if ($request->has('shipping_details')) {
                $purchase->shipping_details = $request->shipping_details;
            }

            $purchase->created_by = auth()->user()->id;

            $purchase->updated_by = auth()->user()->id;

            $purchase->save();

            $item_purchase_quantity = 0;
            $item_subtotal_price = 0;
            if ($request->has('store_items')) {
                foreach ($request->store_items as $store_items) {
                    $existing_purchased_items = PurchaseItem::where('purchase_id', $purchase->id)->where('product_id', $store_items['product_id'])->where('variation_id', $store_items['variation_id'])->first();
                    if ($existing_purchased_items) {
                        $purchase_item_update = PurchaseItem::find($existing_purchased_items->id);
                        $purchase_item_update->purchase_id = $purchase->id;
                        $purchase_item_update->product_id = $store_items['product_id'];
                        $purchase_item_update->variation_id = $store_items['variation_id'];
                        $purchase_item_update->purchase_quantity = $store_items['purchase_quantity'];
                        $purchase_item_update->purchase_price = $store_items['purchase_price'];
                        $purchase_item_update->total_price = $store_items['purchase_quantity'] * $store_items['purchase_price'];

                        $purchase_item_update->save();
                    } else {

                        $purchase_item = PurchaseItem::savePurchaseItem($purchase->id, $store_items);
                        $item_purchase_quantity += $purchase_item->purchase_quantity;
                        $item_subtotal_price += $purchase_item->total_price;
                    }
                }
                $purchase->total_purchase_quantity = $item_purchase_quantity;
                $purchase->subtotal_cost = $item_subtotal_price;

                $discountInPercentage = ($purchase->purchase_discount / 100);
                $afterDiscount = $purchase->subtotal_cost - ($purchase->subtotal_cost * $discountInPercentage);

                $taxInPercentage = ($purchase->purchase_tax / 100);
                $afterTax = $purchase->subtotal_cost + ($purchase->subtotal_cost * $taxInPercentage);

                $purchase->total_cost = $purchase->subtotal_cost - $afterDiscount + $afterTax;
            }

            $purchase->save();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'errmsg' => $e->getMessage()], 500);
        }


        return response(new PurchaseResource($purchase), Response::HTTP_CREATED);
    }

    public function destroy($id)
    {
        $purchase = Purchase::where('id', $id)->first();

        $purchase->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully'], 204);
    }

    //For payment
    public function addPayment(Request $request, $id)
    {

        $purchase = Purchase::find($id);

        // return $purchase;
        $sum = PurchasePayment::where('purchase_id', $id);
        $previousPayment = $sum->sum("payment_amount");
        // $previousPayment = $purchase->PurchasePayment->sum("payment_amount");

        if ($purchase->total_cost >= ($previousPayment + $request->payment_amount)) {
            PurchasePayment::savePurchasePayment($purchase->id, $request->payment_amount, $request->payment_type, $request->payment_date);

            $purchase->payment_status = "partial";
            if ($purchase->total_cost == ($previousPayment + $request->payment_amount)) {
                $purchase->payment_status = "paid";
            }
            $purchase->save();

            return response(new PurchaseResource($purchase), Response::HTTP_CREATED);
        } elseif ($purchase->total_cost < ($previousPayment + $request->payment_amount)) {
            return response()->json(['unsuccessful' => true, 'message' => 'You can not pay more than the original amount!'], 400);
        }
    }

    public function returnPurchase(Request $request, $id)
    {
        $purchase = Purchase::find($id);

        // $sum = PurchasePayment::where('purchase_id', $id);
        // $previousPayment = $sum->sum("payment_amount");

        $business_location = BusinessLocation::findOrFail($request->business_location_id);
        $purchase->business_location_id = $business_location->id;
    }

    public function getContacts()
    {
        $contacts = Contact::get();
        return \response()->json($contacts);
    }

    public function getBusinessLocations()
    {
        $locations = BusinessLocation::get();
        return \response()->json($locations);
    }

    public function getProducts(Request $request)
    {
        $name = $request->name;
        $products = Product::where('name', 'LIKE', "%$name%")->get();
        return \response()->json($products);
    }


}
