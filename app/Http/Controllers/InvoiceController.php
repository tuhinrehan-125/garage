<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
class InvoiceController extends Controller
{
//     public function __construct()
//     {
//         $this->middleware('jwt', ['except' => ['index']]);
//     }
    public function index()
    {
        $invoices = Invoice::all();
        return response(InvoiceResource::collection($invoices), Response::HTTP_OK);
    }

    public function store(Request $request)
    {
//        $validator = Validator::make(
//            $request->all(),
//            [
////                'business_location_id' => 'required',
////                'supplier_id' => 'required',
//            ]
//        );
//
//        if ($validator->fails()) {
//            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
//        }
        DB::beginTransaction();
        try {
            $invoice = new Invoice();
            $invoice->invoice_number  = (time() + rand(10, 1000));;
            $invoice->contact_id = $request->contact_id;
//            $invoice->owner_id = auth()->user()->id;
            $invoice->owner_id = 1;
//            $sale->sale_status = $request->sale_status;
            $invoice->date = date("Y-m-d", strtotime($request->date));

            $invoice->discount = $request->discount;
            $invoice->vat = $request->vat;

//            $sale->shipping_cost = $request->shipping_cost;

//            $invoice->created_by = auth()->user()->id;
//            $invoice->updated_by = auth()->user()->id;

            $invoice->save();

//            dd($invoice);
            $item_sale_quantity = 0;
            $item_subtotal_price = 0;
            $afterTax=0;

//            foreach ($request->invoice_items as $item) {
//                $sell_item = InvoiceItem::saveInvoiceItems($invoice->id, $item);
//                $item_subtotal_price += $sell_item->total_price;
//            }

            foreach ($request->invoice_items as $item) {

                $invoiceItem = new InvoiceItem();
//                $categoryId = $request->category_id;
                $invoiceItem->invoice_id = $invoice->id;
                if($item['category_id'] == 1){
                    $invoiceItem->product_id = $item['product_id'];
                    $invoiceItem->product_rate = $item['product_rate'];
                    $invoiceItem->total_price = $item['subtotal'];
                    $invoiceItem->save();
                }
                else if ($item['category_id'] == 2){

                    $invoiceItem->service_id = $item['service_id'];
                    $invoiceItem->service_rate = $item['service_rate'];
                    $invoiceItem->total_price = $item['subtotal'];
                    $invoiceItem->save();
                }
//                $invoiceItem->total_price = $item['subtotal'];
                $item_subtotal_price += $invoiceItem->total_price;
            }
            if($invoice->vat>0){
                $taxInPercentage = ($invoice->vat / 100);
                $afterTax = $item_subtotal_price * $taxInPercentage;
            }

            $invoice->total_cost = ($item_subtotal_price + $afterTax) - $invoice->sell_discount;

            $invoice->save();


        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'errmsg' => $e->getMessage()], 500);
        }
        DB::commit();

        return response(new InvoiceResource($invoice), Response::HTTP_CREATED);
    }


}
