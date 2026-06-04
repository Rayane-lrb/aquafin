<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'product'])->latest()->get();

        return view('order.index', ['orders' => $orders]);
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
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        Order::create([
            'user_id'    => Auth::id(),
            'product_id' => $request->product_id,
            'quantity'   => $request->quantity,
            'status'     => 'pending',
        ]);

        return redirect()->route('order.index');
    }

    public function approve(string $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'approved']);

        return redirect()->route('order.index');
    }

    public function reject(string $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'rejected']);

        return redirect()->route('order.index');
    }
}