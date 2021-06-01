<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\OpeningStockQty;
use App\Models\SubCategory;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['index']]);
    }

    public function index()
    {

        $product = Product::Active()->paginate(8);
        return  ProductResource::collection($product);
//        return response(ProductResource::collection($product), Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|unique:products',
                'category_id' => 'required',
                'buying_price' => 'required|numeric',
                'selling_price' => 'required|numeric',
                'quantity' => 'required|numeric',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }
        $product = new Product();
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $product->image = "images/products/{$imageName}";
            $request->image->move(public_path('images/products/'), $imageName);
        }
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->owner_id = auth()->user()->id;
        $product->brand_id = $request->brand_id;
        $product->buying_price = $request->buying_price;
        $product->selling_price = $request->selling_price;
        $product->quantity = $request->quantity;
        $product->status = $request->status == "Active"? 0 :1;
        $product->created_by = auth()->user()->id;

        $product->save();
        return response(new ProductResource($product), Response::HTTP_CREATED);

    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function update(Request $request, $id)
    {

        $product = Product::findOrFail($id);
        $validator = Validator::make(
            $request->all(),
            [
                'name' => "unique:products,name,$product->id,id",
                'buying_price' => 'numeric',
                'selling_price' => 'numeric',
                'quantity' => 'numeric',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();
            $product->image = "images/products/{$imageName}";
            $request->image->move(public_path('images/products/'), $imageName);
        }
        if ($request->has('name')) {
            $product->name = $request->name;
        }
        if ($request->has('category_id')) {
            $product->category_id = $request->category_id;
        }
        if ($request->has('brand_id')) {
            $product->brand_id = $request->brand_id;
        }
        if ($request->has('buying_price')) {
            $product->buying_price = $request->buying_price;
        }
        if ($request->has('selling_price')) {
            $product->selling_price = $request->selling_price;
        }
        if ($request->has('quantity')) {
            $product->quantity = $request->quantity;
        }

        if ($request->has('status')) {
            $product->status = $request->status == "Active"? 0 :1;
        }

        $product->updated_by = auth()->user()->id;
        $product->save();
        return response(new ProductResource($product), Response::HTTP_CREATED);
    }

    protected function storeImg($image, $product)
    {
        $extname = $image->getClientOriginalExtension();
        $img_name = substr(md5(uniqid(rand(1, 6))) . microtime(true), 0, 15) . '.' . $extname;
        $image->move(public_path('images'), $img_name);
        $product->image = $img_name;
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        $product->delete();
        return response()->json(['success' => true, 'message' => 'Deleted successfully'], 204);
    }



    public function productSearch(Request $request)
    {
        $type = $request->type;
        $keyword = $request->name;
        if ($type == "Product") {
            $searchQ = Product::where('name', 'like', '%' . $keyword . '%')->where('owner_id', auth()->user()->id)->where('status',0);

        }
        else {
            $searchQ = Service::where('name', 'like', '%' . $keyword . '%')->where('owner_id', auth()->user()->id);
        }
        $products = $searchQ->get();
        $result = [];
        if (!empty($products)) {
            foreach ($products as $key => $value) {
                if ($type == "Product") {
                    $result[] = [
                        'id' => $value->id,
                        'name' => $value->name,
                        'price' => $value->selling_price,
                        "category_type" => "Product"
                    ];
                } else {
                    $result[] = [
                        'id' => $value->id,
                        'name' => $value->name,
                        'price' => $value->selling_price,
                        "category_type" => "Service"
                    ];
                }
            }
        }

        return json_encode($result);
    }



    public function getAllCategories()
    {
        $categories = Category::where('parent_id',0)->where('owner_id',auth()->user()->id)->get();

        return response()->json($categories);
    }

    public function getAllBrands()
    {
        $brands = Brand::where('owner_id',auth()->user()->id)->get();

        return response()->json($brands);
    }

}
