<?php

namespace App\Http\Controllers;

use App\Http\Resources\VehicleResource;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class VehicleController extends Controller
{
//    public function __construct()
//    {
//        $this->middleware('jwt', ['except' => ['index']]);
//    }

    public function index()
    {
        $vehicles = Vehicle::Active()->get();
        return response(VehicleResource::collection($vehicles), Response::HTTP_OK);
    }
}
