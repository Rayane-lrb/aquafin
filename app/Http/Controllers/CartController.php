<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart       = session('cart', []);
        $products   = Product::findMany(array_keys($cart));
        $warehouses = Warehouse::all();

        return view('cart.index', compact('cart', 'products', 'warehouses'));
    }

    public function add(Request $request, Product $product)
    {
        $cart = session('cart', []);
        $qty  = max(1, (int) $request->input('quantity', 1));

        $cart[$product->id] = ($cart[$product->id] ?? 0) + $qty;
        session(['cart' => $cart]);

        return back()->with('success', "'{$product->name}' toegevoegd aan het mandje.");
    }

    public function update(Request $request, Product $product)
    {
        $cart = session('cart', []);
        $qty  = (int) $request->input('quantity', 1);

        if ($qty <= 0) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = $qty;
        }

        session(['cart' => $cart]);

        return back();
    }

    public function remove(Product $product)
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);

        return back();
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Je mandje is leeg.');
        }

        foreach ($cart as $productId => $quantity) {
            Order::create([
                'user_id'      => Auth::id(),
                'product_id'   => $productId,
                'quantity'     => $quantity,
                'status'       => 'in behandeling',
                'warehouse_id' => $request->warehouse_id,
            ]);
        }

        session()->forget('cart');

        return redirect()->route('order.index')->with('success', 'Bestelling geplaatst!');
    }
}
