<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductStoreRequest;
use App\Http\Requests\Product\ProductUpdateRequest;
use App\Models\Product\Product;
use App\Http\Resources\Product as ProductResources;
use App\Http\Resources\ProductCollection;
use App\Utopia\Repositories\Interfaces\Product\ProductRepositoryInterface;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository){
        $this->productRepository = $productRepository;
    }

    public function index(){
        return new ProductCollection($this->productRepository->paginate());
    }

    public function store(ProductStoreRequest $request){
        $product = $this->productRepository->create($request);
        return response()->json( new ProductResources($product),201);
    }

    public function show(int $id){
        $product = $this->productRepository->findOrFail($id);
        return response()->json( new ProductResources($product),200);
    }

    public function update(ProductUpdateRequest $request, int $id){
        $product = $this->productRepository->findOrFail($id);
        $product = $this->productRepository->update($request, $product);
        return response()->json( new ProductResources($product));
    }

    public function destroy(int $id){
        $product = $this->productRepository->findOrFail($id);
        $this->productRepository->destroy($product);
        return response()->json(null,204);
    }
}
