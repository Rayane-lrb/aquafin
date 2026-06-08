<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        $orders = Order::query()
            ->with(['user', 'product'])
            ->when(auth()->user()->role === 'technieker', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->when($query, function ($q) use ($query) {
                $q->where('order_id', 'LIKE', "%{$query}%")
                ->orWhereHas('user', function ($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%");
                });
            })
            ->latest()
            ->get();

        return view('order.index', ['orders' => $orders, 'query' => $query]);
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();

        return view('order.create', ['products' => $products]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        Order::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'status' => 'in behandeling',
        ]);

        return redirect()->route('order.index');
    }

    public function edit(string $id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== Auth::id() && Auth::user()?->role !== 'admin') {
            abort(403);
        }

        $products = Product::where('is_active', true)->get();

        return view('order.edit', ['order' => $order, 'products' => $products]);
    }

    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== Auth::id() && Auth::user()?->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $order->update([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        return redirect()->route('order.index');
    }

    public function approve(string $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'goedgekeurd']);

        $product = $order->product;

        if ($product->stock < $order->quantity) {
            return redirect()->route('order.index')
                ->with('error', 'Niet genoeg stock om deze bestelling goed te keuren!');
        }

        $product->decrement('stock', $order->quantity);
    
        $order->update(['status' => 'goedgekeurd']);


        return redirect()->route('order.index');
    }

    public function reject(string $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'afgekeurd']);

        return redirect()->route('order.index');
    }
}
