<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Service;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['index']]);
    }

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
            $invoice->invoice_number = (time() + rand(10, 1000));;
            $invoice->contact_id = $request->contact_id;
            $invoice->owner_id = auth()->user()->id;
//            $sale->sale_status = $request->sale_status;
            $invoice->date = date("Y-m-d", strtotime($request->date));
            $invoice->discount = $request->discount;
            $invoice->vat = $request->vat;


            $invoice->save();
            $item_sale_quantity = 0;
            $item_subtotal_price = 0;
            $afterTax = 0;

            foreach ($request->invoice_items as $item) {

                $invoiceItem = new InvoiceItem();
                $invoiceItem->invoice_id = $invoice->id;
                $invoice->vehicle_id = $request->vehicle_id;
                if ($item['category_id'] == 1) {
                    $invoiceItem->product_id = $item['product_id'];
                    $invoiceItem->product_rate = $item['product_rate'];
                    $invoiceItem->total_price = $item['subtotal'];
                    $invoiceItem->save();
                } else if ($item['category_id'] == 2) {

                    $invoiceItem->service_id = $item['service_id'];
                    $invoiceItem->service_rate = $item['service_rate'];
                    $invoiceItem->total_price = $item['subtotal'];
                    $invoiceItem->save();
                }
//                $invoiceItem->total_price = $item['subtotal'];
                $item_subtotal_price += $invoiceItem->total_price;
            }
            if ($invoice->vat > 0) {
                $taxInPercentage = ($invoice->vat / 100);
                $afterTax = $item_subtotal_price * $taxInPercentage;
            }

            $invoice->total_cost = ($item_subtotal_price + $afterTax) - $invoice->discount;

            $invoice->save();


        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'errmsg' => $e->getMessage()], 500);
        }
        DB::commit();

        return response(new InvoiceResource($invoice), Response::HTTP_CREATED);
    }


    public function getVehicles(Request $request)
    {
//         $vehicles = Vehicle::where('owner_id',1)->where('contact_id',$request->contact_id)->get();
        $vehicles = Vehicle::where('contact_id', $request->contact_id)->get();

        return response()->json($vehicles);
    }

    public function invoiceProductSearch(Request $request)
    {
        $type = $request->type;

//        dd($type);

//        return response()->json($type);
        $keyword = $request->name;

        if ($type == 1) {
//            $searchQ = Product::where('name', 'like', '%' . $keyword . '%')->where('owner_id', auth()->user()->id)->get();
            $searchQ = Product::where('name', 'like', '%' . $keyword . '%')->where('owner_id', auth()->user()->id);

        }
        else {
//            $searchQ = Service::where('name', 'like', '%' . $keyword . '%')->where('owner_id', auth()->user()->id)->get();
            $searchQ = Service::where('name', 'like', '%' . $keyword . '%')->where('owner_id', auth()->user()->id);
        }

        $products = $searchQ->get();
        $result = [];
        if (!empty($products)) {
            foreach ($products as $key => $value) {
                if ($type == 1) {
                    $result[] = [
                        'id' => $value->id,
                        'name' => $value->name,
//                        'buying_price' => $value->buying_price,
                        'price' => $value->selling_price,
                    ];
                } else {
                    $result[] = [
                        'id' => $value->id,
                        'name' => $value->name,
                        'price' => $value->selling_price,
                    ];
                }
            }
        }
//        return json_encode($searchQ);
        return json_encode($result);
//        return response()->json($result);
    }

    public function getInvoiceItems(Request $request)
    {
        if ($request->category_id === 1) {
            $items = Product::where('category_id', 1)->where('owner_id', auth()->user()->id)->get();

        } else {
            $items = Service::where('category_id', 2)->where('owner_id', auth()->user()->id)->get();
        }
        return response()->json($items);
    }


}
