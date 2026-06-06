<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()->when($query, function ($q) use ($query) {
          $q->where('name', 'LIKE', "%{$query}%");
          })->get();

        return view('product.index', ['products' => $products]);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return view('product.show', ['product' => $product]);
    }

    public function create()
    {
        $categories = ProductCategory::all();

        return view('product.create', ['categories' => $categories]);
    }

    public function edit(string $id)
    {
        $product = Product::findOrFail($id);
        $categories = ProductCategory::all();

        return view('product.edit', ['product' => $product, 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->only(['name', 'stock', 'is_active', 'is_flood_tool', 'product_category_id']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('product.index');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $product = Product::findOrFail($id);
        $data = $request->only(['name', 'stock', 'is_active', 'is_flood_tool', 'product_category_id']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('product.index');
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('product.index');
    }
}
