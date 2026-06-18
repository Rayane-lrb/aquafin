<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query            = $request->input('search');
        $selectedCategory = $request->input('category');
        $role             = auth()->user()?->role;
        $showCategories   = ! $query && ! $selectedCategory;

        // Catégories avec image aléatoire et compteur de produits
        $categories = ProductCategory::all()->map(function ($cat) use ($role) {
            $q = Product::where('product_category_id', $cat->id)
                ->when($role === 'technieker', fn ($q) => $q->where('is_active', true));

            $cat->product_count = $q->count();
            $cat->preview_image = (clone $q)->whereNotNull('image')->inRandomOrder()->value('image');
            return $cat;
        });

        $products = collect();
        if (! $showCategories) {
            $products = Product::query()
                ->when($role === 'technieker', fn ($q) => $q->where('is_active', true))
                ->when($query, fn ($q) => $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'LIKE', "%{$query}%")
                        ->orWhere('barcode', 'LIKE', "%{$query}%");
                }))
                ->when($selectedCategory, fn ($q) => $q->where('product_category_id', $selectedCategory))
                ->get();
        }

        $cartQty        = session('cart', []);
        $favoriteIds    = auth()->user()->favoriteProducts()->pluck('products.id')->flip();
        $favoriteProducts = auth()->user()->favoriteProducts()->where('is_active', true)->get();

        $pendingCount = 0;
        $urgentCount  = 0;
        if ($role === 'magazijnBeheerder') {
            $pendingCount = Order::where('status', OrderStatus::Pending->value)->count();
            $urgentCount  = Order::where('status', OrderStatus::Pending->value)->where('urgent', true)->count();
        }

        // Neerslag check via Open-Meteo
        $isRaining       = false;
        $currentPrecip   = 0;
        $neerslagProducts = collect();
        try {
            $weather = Http::timeout(3)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude'  => 51.2194,
                'longitude' => 4.4025,
                'current'   => 'precipitation,rain',
                'timezone'  => 'Europe/Brussels',
            ]);
            if ($weather->ok()) {
                $currentPrecip = $weather->json('current.precipitation') ?? 0;
                $isRaining     = $currentPrecip > 0;
            }
        } catch (\Exception) {}

        if ($isRaining) {
            $neerslagProducts = Product::where('needed_on_rain', true)
                ->where('is_active', true)
                ->get();
        }

        // Suggesties: neerslag-producten als het regent, anders meest besteld
        if ($isRaining) {
            $suggestedProducts = $neerslagProducts->take(6);
            $suggestLabel      = '🌧️ Aanbevolen bij neerslag';
            $suggestSub        = number_format($currentPrecip, 1) . ' mm/u gedetecteerd';
        } else {
            $suggestedProducts = Product::where('is_active', true)
                ->whereHas('orders')
                ->withCount('orders')
                ->orderByDesc('orders_count')
                ->whereNotIn('id', array_keys(session('cart', [])))
                ->limit(6)
                ->get();

            if ($suggestedProducts->isEmpty()) {
                $suggestedProducts = Product::where('is_active', true)->latest()->limit(6)->get();
            }
            $suggestLabel = '⭐ Aanbevolen producten';
            $suggestSub   = 'Meest besteld';
        }

        return view('product.index', [
            'products'         => $products,
            'query'            => $query,
            'categories'       => $categories,
            'selectedCategory' => $selectedCategory,
            'cartQty'          => $cartQty,
            'showCategories'   => $showCategories,
            'favoriteIds'      => $favoriteIds,
            'favoriteProducts' => $favoriteProducts,
            'suggestedProducts'  => $suggestedProducts,
            'suggestLabel'       => $suggestLabel,
            'suggestSub'         => $suggestSub,
            'pendingCount'      => $pendingCount,
            'urgentCount'       => $urgentCount,
            'isRaining'         => $isRaining,
            'currentPrecip'     => $currentPrecip,
            'neerslagProducts'  => $neerslagProducts,
        ]);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);

        return view('product.show', ['product' => $product]);
    }

    // ── Beveiligingscheck: alleen admin & magazijnBeheerder ──────────────────
    private function authorizeManage(): void
    {
        $role = auth()->user()?->role;
        if (! in_array($role, ['admin', 'magazijnBeheerder'])) {
            abort(403, 'Toegang geweigerd.');
        }
    }

    public function create()
    {
        $this->authorizeManage();
        $categories = ProductCategory::all();

        return view('product.create', ['categories' => $categories]);
    }

    public function edit(string $id)
    {
        $this->authorizeManage();
        $product = Product::findOrFail($id);
        $categories = ProductCategory::all();

        return view('product.edit', ['product' => $product, 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $this->authorizeManage();
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
        $this->authorizeManage();
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'barcode' => ['nullable', 'string', 'max:50', 'unique:products,barcode,'.$id],
            'stock' => ['required', 'integer', 'min:0'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->only(['name', 'barcode', 'stock', 'product_category_id']);
        $data['is_active']      = $request->boolean('is_active');
        $data['needed_on_rain'] = $request->boolean('needed_on_rain');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('product.index');
    }

    public function toggle(string $id)
    {
        $this->authorizeManage();
        $product = Product::findOrFail($id);
        $product->update(['is_active' => ! $product->is_active]);

        return redirect()->route('product.index');
    }

    public function destroy(string $id)
    {
        $this->authorizeManage();
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('product.index');
    }
}
