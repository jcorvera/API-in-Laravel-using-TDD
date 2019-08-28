<?php

namespace App\Utopia\Repositories\Interfaces\Product;

use App\Models\Product\Product;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;

interface ProductRepositoryInterface{

    public function create(ProductStoreRequest $request);

    public function update(ProductUpdateRequest $request, Product $product);

    public function destroy(Product $product);

}
