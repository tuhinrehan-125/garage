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
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['index']]);
    }

    public function index()
    {

        $invoices = Invoice::where('owner_id', auth()->user()->id)->paginate(5);
        return  InvoiceResource::collection($invoices);

//        return response(InvoiceResource::collection($invoices), Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'contact_id' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }
        DB::beginTransaction();
        try {
            $invoice = new Invoice();
            $invoice->invoice_number = (time() + rand(10, 1000));;
            $type = $request->type;
            $invoice->contact_id = $request->contact_id;
            $invoice->owner_id = auth()->user()->id;
//            $sale->sale_status = $request->sale_status;
            $invoice->paid_price = $request->paid_amount;
            $invoice->date = date("Y-m-d", strtotime($request->invoice_date));
            $invoice->discount = $request->invoice_discount;
            $invoice->vat = $request->invoice_tax;
            $invoice->created_by = auth()->user()->id;

            $invoice->save();
            $item_subtotal_price = 0;
            $afterTax = 0;
            foreach ($request->invoice_items as $item) {

                $invoiceItem = new InvoiceItem();
                $invoiceItem->invoice_id = $invoice->id;
                $invoiceItem->vehicle_id = $request->vehicle_id;
                if ( $item['category_type'] == "Product") {
//                if ( $request->category_type == "Product") {
                    $invoiceItem->product_id = $item['id'];
                    $invoiceItem->product_rate = $item['price'];
                    $invoiceItem->product_quantity = $item['invoice_quantity'];
                    $invoiceItem->total_price = $item['subtotal'];

                    // Subtracting quantity from products
                    $productID = $item['id'];
                    $invoice_quantity = $item['invoice_quantity'];
                    $product = Product::find($productID);
                    $old_quantity = $product->quantity;
                    if ($old_quantity >= $invoice_quantity) {
                        $product->quantity = $old_quantity - $invoice_quantity;
                        $product->save();
                    }
                    $invoiceItem->save();
                }
                else if ($item['category_type'] == "Service") {

                    $invoiceItem->service_id = $item['id'];
                    $invoiceItem->service_rate = $item['price'];
                    $invoiceItem->service_quantity = $item['invoice_quantity'];
                    $invoiceItem->total_price = $item['subtotal'];
                    $invoiceItem->save();
                }
                $item_subtotal_price += $invoiceItem->total_price;
            }
            if ($invoice->vat > 0) {
                $taxInPercentage = ($invoice->vat / 100);
                $afterTax = round($item_subtotal_price * $taxInPercentage);
            }

            $invoice->total_cost = ($item_subtotal_price + $afterTax) - $invoice->discount;
            $invoice->due_price = $invoice->total_cost - $invoice->paid_price;
            $invoice->status = $invoice->due_price ==0 ?"Paid":"Due";
            $invoice->save();
        }
        catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false,    'errmsg' => $e->getMessage()], 500);
        }
        DB::commit();
        return response(new InvoiceResource($invoice), Response::HTTP_CREATED);

//        $test = $request->invoice_items;
//
//        return response()->json($test);

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


    public function destroy($id)
    {
        $invoice = Invoice::where('id', $id)->first();

        $invoice->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully'], 204);
    }

    public function show(Invoice $invoice)
    {
        return new InvoiceResource($invoice);

    }

    public function getVehicles(Request $request)
    {
        $vehicles = Vehicle::where('owner_id', 1)->where('contact_id', $request->contact_id)->get();

        return response()->json($vehicles);
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

    public function getInvoiceDetails(Request $request)
    {
        $items = InvoiceItem::where('invoice_id', $request->id)->get();
        $lists = '';

        if (!empty($items)) {
            $lists = $items->map(function ($value) {
                return [
                    'id' => $value->id,
                    'vehicle_name' => $value->vehicle? $value->vehicle->model:'N/A',
                    'reg_no' => $value->vehicle? $value->vehicle->reg_no:'N/A',
                    'chassis_no' => $value->vehicle? $value->vehicle->chassis_no:'N/A',
                    'item_name' =>$value->product? $value->product->name: $value->service->name,
                    'item_quantity' => $value->product_quantity ? $value->product_quantity : $value->service_quantity,
                    'item_price' => $value->product_rate ? $value->product_rate : $value->service_rate,
                    'total_price' => $value->total_price ? $value->total_price : 'N/A',
                ];
            });
        }

        return response()->json( $lists);

    }


}
