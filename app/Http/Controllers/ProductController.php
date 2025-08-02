<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProductController extends Controller
{
    // product list
    public function index()
    {
        $products = Product::query()->select(['product_id', 'product_name', 'product_weight_g', 'product_qty_per_pack'])->get();
        return view('products.index', compact('products'));
    }

    // show create form
    public function create()
    {
        return view('products.create');
    }

    // store product
    public function store(ProductRequest $request)
    {
        try {
            Product::create($request->validated());
            return redirect()
                ->route('products.index')
                ->with('success', 'New product successfully added!');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()
                ->route('products.create')
                ->withErrors(['error' => $e->getMessage()])
                ->setStatusCode(500);
        }
    }

    // show product detail
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    // show edit form
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    // update product info
    public function update(ProductRequest $request, Product $product)
    {
        try {
            $product->update($request->validated());
            return redirect()->route('products.show', compact('product'))
                ->with('success', 'Product successfully updated!');
        } catch (Throwable $e) {
            Log::error($e->getMessage());;
            return redirect()->route('products.edit', status: 500)->withErrors(['error' => $e->getMessage()]);
        }
    }
}
