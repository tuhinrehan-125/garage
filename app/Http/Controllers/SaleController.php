<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleResource;
use App\Models\BusinessLocation;
use App\Models\Category;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Invoice;
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
//                'business_location_id' => 'required',
//                'supplier_id' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }
        DB::beginTransaction();
        try {
            $invoice = new Invoice();
            $invoice->invoice_number  = (time() + rand(10, 1000));;
            $invoice->contact_id = $request->supplier_id;
//            $sale->sale_status = $request->sale_status;
            $invoice->date = date("Y-m-d", strtotime($request->date));

            $invoice->discount = $request->discount;
            $invoice->vat = $request->vat;

//            $sale->shipping_cost = $request->shipping_cost;

            $invoice->created_by = auth()->user()->id;
            $invoice->updated_by = auth()->user()->id;

            $invoice->save();

            $item_sale_quantity = 0;
            $item_subtotal_price = 0;
            $afterTax=0;
            $categoryId = $request->category_id;
            foreach ($request->sell_items as $item) {
                $sell_item = SaleItem::saveSellItems($invoice->id, $item, $categoryId);

//                $item_sell_quantity += $sell_item->sell_quantity;
                $item_subtotal_price += $sell_item->total_price;
            }
//            $sale->total_sell_quantity = $item_sell_quantity;
//            $sale->subtotal_cost = $item_subtotal_price;

            if($invoice->vat>0){
                $taxInPercentage = ($invoice->vat / 100);
                $afterTax = $item_subtotal_price * $taxInPercentage;
            }

            $invoice->total_cost = ($item_subtotal_price + $afterTax) - $invoice->sell_discount;

            $invoice->save();

////            $sale->total_sale_quantity = $item_sale_quantity;
////            $sale->subtotal_cost = $item_subtotal_price;
//            $sale->total_price = $item_subtotal_price;
//
//            $discountInPercentage = ($sale->discount / 100);
//            $afterDiscount = $item_subtotal_price - ($item_subtotal_price * $discountInPercentage);
//
//            $taxInPercentage = ($sale->vat / 100);
//            $sale->vat_parcentage = $taxInPercentage;
//            $afterTax = $sale->total_price + ($sale->total_price * $taxInPercentage);
//
//            $sale->total_cost = $sale->subtotal_cost - $afterDiscount + $afterTax;


        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'errmsg' => $e->getMessage()], 500);
        }
        DB::commit();

        return response(new SaleResource($invoice), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sale = Sale::find($id);

        $sale->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully'], 204);
    }

    public function getClients()
    {
        $clients = Contact::where('type', 'customer')->get();
        return response()->json($clients);
    }
    public function getCategories()
    {
        $categories = Category::where('parent_id', 0)->get();
        return response()->json($categories);
    }
}
