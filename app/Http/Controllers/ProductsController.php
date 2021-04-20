<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\OpeningStockQty;
use App\Models\SubCategory;
use App\Models\Unit;
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

//    public function __construct()
//    {
//        $this->middleware('jwt', ['except' => ['index']]);
//    }

    public function index()
    {
        $product = Product::Active()->get();
        return response(ProductResource::collection($product), Response::HTTP_OK);
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
//                'image' => 'image|mimes:jpeg,jpg,png|max:300'
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
            $product->name = $request->name;
            $product->category_id = $request->category_id;
            $product->brand = $request->brand;
            $product->buying_price = $request->buying_price;
            $product->selling_price = $request->selling_price;
            $product->quantity = $request->quantity;
            $product->status = $request->status;
            $product->created_by = auth()->user()->id;
            $product->save();
            return response(new ProductResource($product), Response::HTTP_CREATED);
        }
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->brand = $request->brand;
        $product->buying_price = $request->buying_price;
        $product->selling_price = $request->selling_price;
        $product->quantity = $request->quantity;
        $product->status = $request->status;
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
//                'image' => 'image|mimes:jpeg,jpg,png|max:300'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }
        if ($request->hasFile('image')) {
            if ($product->image) {
                unlink($product->image);
            }
            $imageName = time() . '.' . $request->image->getClientOriginalExtension();

            $product->image = "images/products/{$imageName}";
            $product->name = $request->name;
            $product->category_id = $request->category_id;
            $product->brand = $request->brand;
            $product->buying_price = $request->buying_price;
            $product->selling_price = $request->selling_price;
            $product->quantity = $request->quantity;
            $product->status = $request->status;
            $product->updated_by = auth()->user()->id;
            $product->save();
            $request->image->move(public_path('images/products/'), $imageName);

            return response(new ProductResource($product), Response::HTTP_CREATED);
        }

        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->brand = $request->brand;
        $product->buying_price = $request->buying_price;
        $product->selling_price = $request->selling_price;
        $product->quantity = $request->quantity;
        $product->status = $request->status;
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
        $business_id = auth()->user()->business_id;

        $keyword = $request->name;

        $searhQ = DB::table('products')
            ->leftJoin('product_variations', 'product_variations.product_id', '=', 'products.id')
            ->where(function ($query) use ($keyword) {
                $query->where('products.name', 'like', '%' . $keyword . '%');
                $query->orWhere('sku', 'like', '%' . $keyword . '%');
                $query->orWhere('sub_sku', 'like', '%' . $keyword . '%');
            })
            ->where('business_id', $business_id)
            ->whereNull('products.deleted_at')
            ->whereNull('product_variations.deleted_at')
            ->select(
                'products.id as product_id',
                'products.name',
                'products.type',
                'products.sku as product_sku',
                'product_variations.id as variation_id',
                'product_variations.name as variation_name',
                'product_variations.name as variation',
                'product_variations.sub_sku as sub_sku',
                'product_variations.purchase_price as purchase_price',
                'product_variations.tax as tax'
            );
        $products = $searhQ->get();
        $result = [];
        if (!empty($products)) {
            foreach ($products as $key => $value) {
                if ($value->type == 'single') {
                    $result[] = [
                        'product_id' => $value->product_id,
                        'product_sku' => $value->product_sku,
                        'product' => $value->name . '-' . $value->product_sku,
                        'variation_id' => $value->variation_id,
                        'purchase_price' => $value->purchase_price,
                        'tax' => $value->tax
                    ];
                } else {
                    $result[] = [
                        'product_id' => $value->product_id,
                        'variation_id' => $value->variation_id,
                        'product' => $value->name . '(' . $value->variation_name . ')' . '-' . $value->sub_sku,
                        'purchase_price' => $value->purchase_price,
                        'tax' => $value->tax
                    ];
                }
            }
        }
        return json_encode($result);
    }

    public function getAllCategories()
    {
        $categories = Category::where('parent_id',null)->get();

        return response()->json($categories);
    }
}
