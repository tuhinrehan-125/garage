<?php

namespace App\Http\Controllers;

use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['index']]);
    }

    public function index()
    {
        $vehicles = Vehicle::Active()->get();
        return response(VehicleResource::collection($vehicles), Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'owner_id' => 'required',
                'contact_id' => 'required',
                'model' => 'required',
                'reg_no' => 'required|unique:vehicles',
                'chassis_no' => 'required|unique:vehicles',
                'mileage' => 'required'
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }

        $vehicle = new Vehicle();
        $vehicle->owner_id  = $request->owner_id;
        $vehicle->contact_id = $request->contact_id;
        $vehicle->brand_id = $request->brand_id;
        //$vehicle->brand_name = $request->brand_name;
        $vehicle->model = $request->model;
        $vehicle->reg_no = $request->reg_no;
        $vehicle->chassis_no = $request->chassis_no;
        $vehicle->mileage = $request->mileage;
        $vehicle->color = $request->color;
        //$vehicle->type = $request->type_id;
        $vehicle->description = $request->description;
        $vehicle->save();

        return response(new VehicleResource($vehicle), Response::HTTP_CREATED);
    }


    public function update(Request $request, $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        $validator = Validator::make(
            $request->all(),
            [
                'reg_no' => "unique:vehicles,reg_no,$vehicle->id,id",
                'chassis_no' => "unique:vehicles,chassis_no,$vehicle->id,id",
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }
        if ($request->has('owner_id')) {
            $vehicle->owner_id = $request->owner_id;
        }
        if ($request->has('contact_id')) {
            $vehicle->contact_id = $request->contact_id;
        }
        if ($request->has('brand_id')) {
            $vehicle->brand_id = $request->brand_id;
        }
        if ($request->has('brand_name')) {
            $vehicle->brand_name = $request->brand_name;
        }
        if ($request->has('model')) {
            $vehicle->model = $request->model;
        }
        if ($request->has('reg_no')) {
            $vehicle->reg_no = $request->reg_no;
        }
        if ($request->has('chassis_no')) {
            $vehicle->chassis_no = $request->chassis_no;
        }
        if ($request->has('mileage')) {
            $vehicle->mileage = $request->mileage;
        }
        if ($request->has('color')) {
            $vehicle->color = $request->color;
        }
        if ($request->has('type_id')) {
            $vehicle->type_id = $request->type_id;
        }
        if ($request->has('description')) {
            $vehicle->description = $request->description;
        }

        $vehicle->save();

        return response(new VehicleResource($vehicle), Response::HTTP_OK);
    }


    public function destroy($id)
    {
        $vehicle = Vehicle::find($id);
        $vehicle->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully'], 204);
    }
}
