<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleResource;
use App\Models\BusinessLocation;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class SaleController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('jwt', ['except' => ['index']]);
    // }

    public function index()
    {
        $sales = Sale::all();
        return response(SaleResource::collection($sales), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'business_location_id'    => 'required',
                'supplier_id' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }
        DB::beginTransaction();
        try {
            $sale = new Sale();

            $business_location = BusinessLocation::findOrFail($request->business_location_id);
            // dd($business_location);
            $sale->business_location_id = $business_location->id;

            $sale->ref_no = $request->ref_no;
            $sale->contact_id = $request->supplier_id;
            $sale->sale_status = $request->sale_status;
            $sale->sale_date = date("Y-m-d", strtotime($request->sale_date));

            $sale->sale_discount = $request->sale_discount;
            $sale->sale_tax = $request->sale_tax;

            $sale->shipping_cost = $request->shipping_cost;

            $sale->created_by = auth()->user()->id;
            $sale->updated_by = auth()->user()->id;

            $sale->save();

            $item_sale_quantity = 0;
            $item_subtotal_price = 0;
            foreach ($request->store_items as $store_items) {
                $sale_item = SaleItem::saveSaleItems($sale->id, $store_items);
                $item_sale_quantity += $sale_item->sale_quantity;
                $item_subtotal_price += $sale_item->total_price;
            }

            $sale->total_sale_quantity = $item_sale_quantity;
            $sale->subtotal_cost = $item_subtotal_price;

            $discountInPercentage = ($sale->sale_discount / 100);
            $afterDiscount = $sale->subtotal_cost - ($sale->subtotal_cost * $discountInPercentage);

            $taxInPercentage = ($sale->sale_tax / 100);
            $afterTax = $sale->subtotal_cost + ($sale->subtotal_cost * $taxInPercentage);

            $sale->total_cost = $sale->subtotal_cost - $afterDiscount + $afterTax;

            $sale->save();
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'errmsg' => $e->getMessage()], 500);
        }
        DB::commit();

        return response(new SaleResource($sale), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sale = Sale::find($id);

        $sale->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully'], 204);
    }
}
