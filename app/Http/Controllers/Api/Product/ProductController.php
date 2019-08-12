<?php

namespace App\Http\Controllers\Api\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Http\Resources\Product as ProductResources;
use App\Http\Resources\ProductCollection;

class ProductController extends Controller
{
    public function index(){
        return new ProductCollection(Product::paginate(25));
    }

    public function store(Request $request){
        $product = Product::create($request->all());
        return response()->json( new ProductResources($product),201);
    }

    public function show(int $id){
        $product = Product::findOrFail($id);
        return response()->json( new ProductResources($product),200);
    }

    public function update(Request $request, int $id){
        $product = Product::findOrFail($id);
        $product->update($request->all());
        return response()->json( new ProductResources($product));
    }

    public function destroy(int $id){
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(null,204);
    }
}
