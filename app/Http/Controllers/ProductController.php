<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');
        $selectedCategory = $request->input('category');
        $role = auth()->user()?->role;

        $products = Product::query()
            ->when($role === 'technieker', fn ($q) => $q->where('is_active', true))
            ->when($query, fn ($q) => $q->where(function ($sub) use ($query) {
                $sub->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('barcode', 'LIKE', "%{$query}%");
            }))
            ->when($selectedCategory, fn ($q) => $q->where('product_category_id', $selectedCategory))
            ->get();

        $categories = ProductCategory::all();

        $cartQty = session('cart', []);

        return view('product.index', [
            'products' => $products,
            'query' => $query,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'cartQty' => $cartQty,
        ]);
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
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode'],
            'stock' => ['required', 'integer', 'min:0'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->only(['name', 'barcode', 'stock', 'product_category_id']);
        $data['is_active'] = true;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        // Auto-genereer barcode als leeg gelaten
        if (empty($data['barcode'])) {
            $data['barcode'] = 'AQF-'.strtoupper(substr(uniqid(), -6)).'-'.rand(100, 999);
        }

        $product = Product::create($data);

        // Overschrijf met barcode op basis van ID (stabiel en uniek)
        if (! $request->filled('barcode')) {
            $product->update(['barcode' => 'AQF-'.str_pad($product->id, 6, '0', STR_PAD_LEFT)]);
        }

        return redirect()->route('product.index');
    }

    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode,'.$id],
            'stock' => ['required', 'integer', 'min:0'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->only(['name', 'barcode', 'stock', 'product_category_id']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('product.index');
    }

    public function toggle(string $id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => ! $product->is_active]);

        return redirect()->route('product.index');
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('product.index');
    }
}
