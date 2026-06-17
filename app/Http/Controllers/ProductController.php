<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

    $suggestedProducts = collect();
    $weatherAlert = 'none';

    $response = Http::timeout(5)->get('https://api.open-meteo.com/v1/forecast', [
        'latitude'        => 51.2194,
        'longitude'       => 4.4025,
        'daily'           => 'precipitation_sum,precipitation_hours,precipitation_probability_max',
        'timezone'        => 'Europe/Brussels',
        'forecast_days'   => 1,
    ]);

    if ($response->successful()) {
        $daily      = $response->json()['daily'] ?? [];
        $precip     = $daily['precipitation_sum'][0] ?? 0;
        $hours      = $daily['precipitation_hours'][0] ?? 0;
        $probability = $daily['precipitation_probability_max'][0] ?? 0;

        $isRainy    = $precip > 2 || $probability > 50;
        $isFlooding = $precip > 20 || $hours > 6;

        if ($isFlooding) {
            $weatherAlert = 'flood';
            $suggestedProducts = Product::where('is_active', true)
                ->where(function ($q) {
                    $q->where('is_flood_tool', true)
                      ->orWhere('needed_on_rain', true);
                })->get();
        } elseif ($isRainy) {
            $weatherAlert = 'rain';
            $suggestedProducts = Product::where('is_active', true)
                ->where('needed_on_rain', true)
                ->get();
        }
    }

    return view('product.index', [
        'products'          => $products,
        'query'             => $query,
        'categories'        => $categories,
        'selectedCategory'  => $selectedCategory,
        'cartQty'           => $cartQty,
        'suggestedProducts' => $suggestedProducts,
        'weatherAlert'      => $weatherAlert,
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
            'name'                => ['required', 'string', 'max:255'],
            'barcode'             => ['nullable', 'string', 'max:50', 'unique:products,barcode'],
            'stock'               => ['required', 'integer', 'min:0'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'image'               => ['nullable', 'image', 'max:2048'],
        ]);

        $data             = $request->only(['name', 'barcode', 'stock', 'product_category_id']);
        $data['is_active'] = true;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }


        if (empty($data['barcode'])) {
            $data['barcode'] = 'AQF-' . strtoupper(substr(uniqid(), -6)) . '-' . rand(100, 999);
        }

        $product = Product::create($data);


        if (!$request->filled('barcode')) {
            $product->update(['barcode' => 'AQF-' . str_pad($product->id, 6, '0', STR_PAD_LEFT)]);
        }

        return redirect()->route('product.index');
    }

        public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'                => ['required', 'string', 'max:255'],
            'barcode'             => ['nullable', 'string', 'max:50', 'unique:products,barcode,' . $id],
            'stock'               => ['required', 'integer', 'min:0'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'image'               => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->only(['name', 'barcode', 'stock', 'product_category_id']);
        $data['is_flood_tool']  = $request->boolean('is_flood_tool');
        $data['needed_on_rain'] = $request->boolean('needed_on_rain');  // ← cette ligne manque probablement

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('product.index');
    }

    public function toggle(string $id)
    {
        $product = Product::findOrFail($id);
        $product->update(['is_active' => !$product->is_active]);

        return redirect()->route('product.index');
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('product.index');
    }
}
