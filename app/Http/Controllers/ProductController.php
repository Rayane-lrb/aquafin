<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index() {
        $products = Product::all();

        return view('product.index', ['products' => $products]);
    }

    public function show($id) {
        $product = Product::findOrFail($id);

        return view('product.show', ['product' => $product]);
    }

    public function create() {
        return view('product.create');
    }
    
    public function store(Request $request) {
    $request->validate([
        'name'                => ['required', 'string', 'max:255'],
        'stock'               => ['required', 'integer', 'min:0'],
        'product_category_id' => ['required', 'exists:product_categories,id'],
    ]);

    Product::create($request->only(['name', 'stock', 'is_active', 'is_flood_tool', 'product_category_id']));

    return redirect()->route('product.index');
}

public function edit(string $id) {
    $product = Product::findOrFail($id);

    return view('product.edit', ['product' => $product]);
}

public function update(Request $request, string $id) {
    $request->validate([
        'name'                => ['required', 'string', 'max:255'],
        'stock'               => ['required', 'integer', 'min:0'],
        'product_category_id' => ['required', 'exists:product_categories,id'],
    ]);

    $product = Product::findOrFail($id);
    $product->update($request->only(['name', 'stock', 'is_active', 'is_flood_tool', 'product_category_id']));

    return redirect()->route('product.index');
}

public function destroy(string $id) {
    $product = Product::findOrFail($id);
    $product->delete();

    return redirect()->route('product.index');
}
}
