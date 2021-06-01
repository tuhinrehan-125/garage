<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Resources\PurchaseItemsResource;
use App\Http\Resources\PurchaseResource;
use App\Models\BusinessLocation;
use App\Models\LocationProductStock;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\User;
use App\Models\Contact;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['index']]);
    }

    public function index()
    {
        $purchase = Purchase::get();
        return response(PurchaseResource::collection($purchase), Response::HTTP_OK);
    }

    public function getSuppliers()
    {
        $suppliers = Contact::where('owner_id',1)->where('type','supplier')->get();

        return response()->json($suppliers);

    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                // 'business_location_id' => 'required',
                // 'owner_id' => 'required',
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

            //$business_location = BusinessLocation::findOrFail($request->business_location_id);
            //$owner = User::findOrFail($request->owner_id);
            //$purchase->business_location_id = $business_location->id;
            //$purchase->owner_id = $owner->id;

            $purchase->owner_id = 1;
            $purchase->contact_id = $request->supplier_id;
            $purchase->purchase_status = $request->purchase_status;
            $purchase->purchase_date = date("Y-m-d", strtotime($request->purchase_date));
            // $purchase->purchase_discount = $request->purchase_discount;
            // $purchase->purchase_tax = $request->purchase_tax;
            //$purchase->shipping_charge = $request->shipping_cost;
            //$purchase->shipping_details = $request->shipping_details;
            $purchase->created_by = auth()->user()->id;
            $purchase->updated_by = auth()->user()->id;
            $purchase->save();

            // if (!empty($request->purchase_doc)) {
            //     $purchase_doc = $request->purchase_doc;
            //     Helper::uploadFile($purchase_doc, $purchase, $business_location->business_id);
            // }

            $item_purchase_quantity = 0;
            $item_subtotal_price = 0;
            $afterTax = 0;


            foreach ($request->purchase_items as $item) {
                $purchase_item = PurchaseItem::savePurchaseItem($purchase->id, $item);
                //$stockProduct = LocationProductStock::saveProductInStock($item, $business_location->id, $business_location->business_id);
                $item_purchase_quantity += $purchase_item->purchase_quantity;
                $item_subtotal_price += $purchase_item->total_price;
            }

            $purchase->total_purchase_quantity = $item_purchase_quantity;
            //$purchase->subtotal_cost = $item_subtotal_price;

            // if ($purchase->purchase_tax > 0) {
            //     $taxInPercentage = ($purchase->purchase_tax / 100);
            //     $afterTax = $item_subtotal_price * $taxInPercentage;
            // }

            //$purchase->total_cost = ($item_subtotal_price + $afterTax + $purchase->shipping_charge) - $purchase->purchase_discount;
            $purchase->total_cost = $item_subtotal_price;
            $purchase->save();

            // if ($request->payment_amount != null) {
            //     $purchase->payments()->create([
            //         'payment_amount' => $request->payment_amount,
            //         'payment_method' => $request->payment_method,
            //         'payment_date' =>  date("Y-m-d", strtotime($request->payment_date ? $request->payment_date : now()))
            //     ]);
            //     //PurchasePayment::savePurchasePayment($purchase->id, $request->payment_amount, $request->payment_type, $request->payment_date);
            // }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'errmsg' => $e->getMessage()], 500);
        }
        DB::commit();

        return response(new PurchaseResource($purchase), Response::HTTP_CREATED);
    }

    public function show(Purchase $purchase)
    {
        return new PurchaseResource($purchase);
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

    public function purchaseItemsList(Request $request)
    {
        $purchaseid = $request->purchase_id;
        $purchase = Purchase::findOrFail($purchaseid);
        $all_items = $purchase->purchaseItems;
        return response(PurchaseItemsResource::collection($all_items), Response::HTTP_OK);
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
        $products = Products::where('name', 'LIKE', "%$name%")->get();
        return \response()->json($products);
    }


}
