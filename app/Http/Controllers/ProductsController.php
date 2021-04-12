<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Models\Products;
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

    public function __construct()
    {
        $this->middleware('jwt', ['except' => ['index']]);
    }

    public function index()
    {
        $product = Products::Active()->get();
        return response(ProductResource::collection($product), Response::HTTP_OK);
    }

    public function store(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'name'    => 'required',
                'category_id' => 'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->errors()], 422);
        }
        DB::beginTransaction();
        try {
            $product = new Products();
            $product->business_id = auth()->user()->business_id;
            $product->name = $request->name;
            $product->type = $request->type;
            $product->unit_id = $request->unit_id;
            $product->brand_id = $request->brand_id;
            $product->category_id = $request->category_id;
            $product->enable_stock = $request->enable_stock;
            $product->alert_quantity = $request->alert_quantity;
            $product->sku = Products::generateSku($request->sku);
            $product->product_description = $request->product_description;
            $product->weight = $request->weight;
            $product->barcode_type = $request->barcode_type;
            $product->created_by = auth()->user()->id;
            $product->save();
            if (!empty($request->image)) {
                $pro_image = $request->image;
                Helper::uploadImage($pro_image, $product, $product->business_id);
            }
            if ($product->type == 'single') {
                Products::createSingleProductVariation($product, $request->purchase_price, $request->sell_price, $request->tax);
            } elseif ($product->type == 'variable') {
                if (!empty($request->product_variation)) {
                    $product_variations = $request->product_variation;
                    foreach ($product_variations as $product_variation) {
                        Products::createVariableProductVariations($product, $product_variation);
                    }
                }
            }

            if ($request->add_opening_stock == 1) {
                foreach (json_decode($request->opening_stocks) as $opening_stock) {
                    OpeningStockQty::saveOpeningStock($product, $opening_stock);
                }
            }
            DB::commit();
            return response(new ProductResource($product), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'errmsg' => $e->getMessage(), 'line' => $e->getLine()], 500);
        }
    }

    public function show(Products $product)
    {
        return new ProductResource($product);
    }

    public function update(Request $request, $id)
    {
        $product = Products::findOrFail($id);

        if ($request->has('name')) {
            $product->name = $request->name;
        }
        if ($request->has('type')) {
            $product->type = $request->type;
        }
        if ($request->has('unit_id')) {
            $product->unit_id = $request->unit_id;
        }
        if ($request->has('brand_id')) {
            $product->brand_id = $request->brand_id;
        }
        if ($request->has('enable_stock')) {
            $product->enable_stock = $request->enable_stock;
        }
        if ($request->has('alert_quantity')) {
            $product->alert_quantity = $request->alert_quantity;
        }
        if ($request->has('sku')) {
            $product->sku = $request->sku;
        }
        if ($request->has('product_description')) {
            $product->product_description = $request->product_description;
        }
        if ($request->has('weight')) {
            $product->weight = $request->weight;
        }
        if ($request->has('price')) {
            $product->price = $request->price;
        }
        if ($request->has('category_id')) {
            $product->category_id = $request->category_id;
        }
        // if ($request->has('subcategory_id') && $request->subcategory_id !== 'null') {
        //     $product->subcategory_id = $request->subcategory_id;
        // }
        if ($request->has('barcode_type')) {
            $product->barcode_type = $request->barcode_type;
        }

        // if ($request->file('image')) {
        //     if ($product->image) {
        //         $path = public_path() . "/images/" . basename($product->image);
        //         unlink($path);
        //         $this->storeImg($request->image, $product);
        //     } else {
        //         $this->storeImg($request->image, $product);
        //     }
        // }

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
        $product = Products::where('id', $id)->first();

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
                        'product_id' =>  $value->product_id,
                        'product_sku' => $value->product_sku,
                        'product' => $value->name . '-' . $value->product_sku,
                        'variation_id' => $value->variation_id,
                        'purchase_price' => $value->purchase_price,
                        'tax' => $value->tax
                    ];
                } else {
                    $result[] = [
                        'product_id' =>  $value->product_id,
                        'variation_id' => $value->variation_id,
                        'product' => $value->name.'('.$value->variation_name.')' . '-' . $value->sub_sku,
                        'purchase_price' => $value->purchase_price,
                        'tax' => $value->tax
                    ];
                }
            }
        }
        return json_encode($result);
    }
}
