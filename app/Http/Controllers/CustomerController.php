<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['index','customerSearch']]);
    }

    public function index()
    {
        $customer = Customer::where('delete_status', 1)->get();

        return response(CustomerResource::collection($customer), Response::HTTP_OK);
    }

    public function store(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'name'     => 'required',
                'address'     => 'required',
                'mobile_no'    => 'required',
                'nid_no'    => 'required',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success'=>false,'error' => $validator->errors()],422);
        }

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->address = $request->address;
        $customer->mobile_no = $request->mobile_no;
        $customer->alt_mobile_no = $request->alt_mobile_no;
        $customer->shop_name = $request->shop_name;
        $customer->shop_address = $request->shop_address;
        $customer->nid_no = $request->nid_no;


        $customer->introducer_name = $request->introducer_name;
        $customer->intro_mobile_no = $request->intro_mobile_no;

         if ($request->nid_image!=='null') {
            $nidimg=$request->nid_image;
            $nidextname=$nidimg->getClientOriginalExtension();
            $nid_img_name = substr(md5(uniqid(rand(1,6))).microtime(true), 0, 15).'.'.$nidextname;
            $nidimg->move(public_path('images'), $nid_img_name);
            $customer->nid_image = $nid_img_name;
        }

        if ($request->customer_img!=='null') {
            $customer_img=$request->customer_img;
            $c_extname=$customer_img->getClientOriginalExtension();
            $c_img_name = substr(md5(uniqid(rand(1,6))).microtime(true), 0, 15).'.'.$c_extname;
            $customer_img->move(public_path('images'), $c_img_name);
            $customer->customer_img = $c_img_name;
        }

        $customer->save();
        return response(new CustomerResource($customer), Response::HTTP_CREATED);
    }



    public function update(Request $request, $id)
    {

        $customer = Customer::findOrFail($id);

        if ($request->has('name')) {
            $customer->name = $request->name;
        }
        if ($request->has('address')) {
            $customer->address = $request->address;
        }
        if ($request->has('mobile_no')) {
            $customer->mobile_no = $request->mobile_no;
        }
        if ($request->has('alt_mobile_no')) {
            $customer->alt_mobile_no = $request->alt_mobile_no;
        }
        if ($request->has('shop_name')) {
            $customer->shop_name = $request->shop_name;
        }
        if ($request->has('shop_address')) {
            $customer->shop_address = $request->shop_address;
        }
        if ($request->has('commission')) {
            $customer->commission = $request->commission;
        }
        if ($request->has('nid_no')) {
            $customer->nid_no = $request->nid_no;
        }
        if ($request->has('introducer_name')) {
            $customer->introducer_name = $request->introducer_name;
        }
        if ($request->has('intro_mobile_no')) {
            $customer->intro_mobile_no = $request->intro_mobile_no;
        }

        $customer->save();
        return response(new CustomerResource($customer), Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        if ($customer->delete_status == 1) {
            $customer->delete_status = 0;
        } else {
            $customer->delete_status = 1;
        }

        $customer->save();
        return response()->json(['success'=>true,'message'=>'Deleted successfully'],204);
    }

    public function customerSearch(Request $request)
    {
        $name=$request->name;
        if($name){
        $customers = Customer::where('name', 'LIKE', "%$name%")->get();
        return response(CustomerResource::collection($customers), Response::HTTP_OK);
        }

    }
}
