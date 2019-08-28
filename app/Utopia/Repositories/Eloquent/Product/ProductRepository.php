<?php

namespace App\Utopia\Repositories\Eloquent\Product;

use App\Models\Product\Image;
use App\Models\Product\Product;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Utopia\Repositories\Eloquent\AbstractRepository;
use App\Utopia\Repositories\Interfaces\Product\ProductRepositoryInterface;


class ProductRepository extends AbstractRepository implements ProductRepositoryInterface {

    public function __construct()
    {
        parent::__construct('Models\\Product\\Product');
    }

    public function create(ProductStoreRequest $request){

        if($request->hasFile('image')){
            $path = $request->file('image')->store('product_images','public');
            $image = Image::create([
                'path' => $path
            ])->id;
        }else{
            $image = null;
        }

        return Product::create([
            'image_id'=> $image,
            'name'=> $request->name,
            'slug'=> str_slug($request->name),
            'price' => $request->price
        ]);

    }

    public function update(ProductUpdateRequest $request, Product $product){

        if($request->hasFile('image')){
            $path = $request->file('image')->store('product_images','public');
            $image = Image::create([
                'path' => $path
            ])->id;
        }else{
            $image = $product->image_id;
        }

        $product->update([
            'image_id' => $image,
            'name'=> $request->name,
            'slug'=> str_slug($request->name),
            'price' => $request->price
        ]);

        return $product;
    }

    public function destroy(Product $product){
        $product->delete();
    }

}
