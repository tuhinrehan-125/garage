<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Http\Resources\ColorResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ColorController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['index']]);
    }

    public function index()
    {
        $color = Color::Active()->get();
        return response(ColorResource::collection($color), Response::HTTP_OK);
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

        $color = new Color();
        $color->owner_id = auth()->user()->id;
        $color->name = $request->name;
        $color->description = $request->description;

        $color->save();

        return response(new ColorResource($color), Response::HTTP_CREATED);
    }

    public function update(Request $request, $id)
    {
        $color = Color::findOrFail($id);

        if ($request->has('name')) {
            $color->name = $request->name;
        }
        if ($request->has('description')) {
            $color->description = $request->description;
        }

        $color->save();

        return response(new ColorResource($color), Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $color = Color::where('id', $id)->first();

        $color->delete();

        return response()->json(['success' => true, 'message' => 'Deleted successfully'], 204);
    }
}
