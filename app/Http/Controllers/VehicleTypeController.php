<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleType;
use App\Http\Resources\VehicleTypeResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class VehicleTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['index']]);
    }

    public function index()
    {
        $vehicleType = VehicleType::Active()->get();
        return response(VehicleTypeResource::collection($vehicleType), Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name'     => 'required',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }

        $vehicleType = new VehicleType();
        $vehicleType->owner_id = auth()->user()->id;
        $vehicleType->name = $request->name;
        $vehicleType->description = $request->description;

        $vehicleType->save();

        return response(new VehicleTypeResource($vehicleType), Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $vehicleType = VehicleType::findOrFail($id);

        if ($request->has('name')) {
            $vehicleType->name = $request->name;
        }
        if ($request->has('description')) {
            $vehicleType->description = $request->description;
        }

        $vehicleType->save();

        return response(new VehicleTypeResource($vehicleType), Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $vehicleType = VehicleType::where('id', $id)->first();

        $vehicleType->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully'], 204);
    }
}
