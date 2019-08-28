<?php

namespace App\Utopia\Repositories\Interfaces\Product;

use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Models\Product\Product;

interface ProductRepositoryInterface{

    public function create(ProductStoreRequest $request);

    public function update(ProductUpdateRequest $request, Product $product);

    public function destroy(Product $product);

}
