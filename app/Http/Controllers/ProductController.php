<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    // product list
    public function index()
    {
        $products = Product::all(['product_id', 'product_name', 'product_unit_price', 'product_pack_price', 'product_weight_g', 'product_qty_per_pack']);
        return view('products.index', compact('products'));
    }

    // show create form
    public function create()
    {
        return view('products.create');
    }

    // store product
    public function store(StoreProductRequest $request)
    {
        try {
            $product = Product::create($request->validated());
            return redirect()->route('products.index');
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return redirect()->route('products.create', status: 500)->withErrors(['error' => $e->getMessage()]);
        }
    }
}
