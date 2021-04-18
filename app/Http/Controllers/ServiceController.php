<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
class ServiceController extends Controller
{
    //    public function __construct()
//    {
//        $this->middleware('jwt', ['except' => ['index']]);
//    }

    public function index()
    {
        $services = Service::Active()->get();
        return response(ServiceResource::collection($services), Response::HTTP_OK);
    }


    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'owner_id' => 'required',
                'name' => 'required',
                'category_id' => 'required',
                'selling_price' => 'numeric',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }

        $service = new Service();
        $service->name  =$request->name;
        $service->owner_id =$request->owner_id;
        $service->category_id=$request->category_id;
        $service->selling_price=$request->selling_price;
        $service->status=$request->status;
        $service->description=$request->description;
        $service->save();

        return response(new ServiceResource($service), Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $validator = Validator::make(
            $request->all(),
            [
                'name' => "unique:services,name,$service->id,id",
                'selling_price' => 'numeric',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }
        if ($request->has('owner_id')) {
            $service->owner_id = $request->owner_id;
        }
        if ($request->has('name')) {
            $service->name = $request->name;
        }
        if ($request->has('category_id')) {
            $service->category_id  = $request->category_id;
        }
        if ($request->has('selling_price')) {
            $service->selling_price = $request->selling_price;
        }
        if ($request->has('status')) {
            $service->status = $request->status;
        }
        if ($request->has('description')) {
            $service->description = $request->description;
        }

        $service->save();

        return response(new ServiceResource($service), Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $service = Service::find($id);
        $service->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully'], 204);
    }
}
