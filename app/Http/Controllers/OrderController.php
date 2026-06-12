<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        $orders = Order::query()
            ->with(['user', 'product', 'warehouse'])
            ->when(auth()->user()->role === 'technieker', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('order_id', 'LIKE', "%{$query}%")
                        ->orWhereHas('user', function ($s) use ($query) {
                            $s->where('name', 'LIKE', "%{$query}%");
                        });
                });
            })
            ->orderByDesc('urgent')
            ->orderBy('user_id')
            ->orderByDesc('created_at')
            ->get();

        // Group by user; groups with urgent orders come first
        $grouped = $orders
            ->groupBy('user_id')
            ->sortByDesc(fn ($g) => $g->max('urgent') ? 1 : 0);

        return view('order.index', [
            'grouped' => $grouped,
            'orders'  => $orders,   // kept for tab counts
            'query'   => $query,
        ]);
    }

    public function create(Request $request)
    {
        $products   = Product::where('is_active', true)->get();
        $warehouses = Warehouse::all();
        $productId  = $request->input('product_id');

        return view('order.create', compact('products', 'warehouses', 'productId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id'   => ['required', 'exists:products,id'],
            'quantity'     => ['required', 'integer', 'min:1'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
        ]);

        Order::create([
            'user_id'      => Auth::id(),
            'product_id'   => $request->product_id,
            'quantity'     => $request->quantity,
            'status'       => 'in behandeling',
            'warehouse_id' => $request->warehouse_id,
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

    public function approve(Request $request, string $id)
    {
        $order   = Order::findOrFail($id);
        $product = $order->product;

        if ($product->stock < $order->quantity) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Niet genoeg stock om deze bestelling goed te keuren!'], 422);
            }
            return redirect()->route('order.index')->with('error', 'Niet genoeg stock om deze bestelling goed te keuren!');
        }

        $product->decrement('stock', $order->quantity);
        $order->update(['status' => 'goedgekeurd']);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'status' => 'goedgekeurd', 'order_id' => $order->id]);
        }
        return redirect()->route('order.index');
    }

    public function reject(Request $request, string $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'afgekeurd']);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'status' => 'afgekeurd', 'order_id' => $order->id]);
        }
        return redirect()->route('order.index');
    }

    public function deliver(Request $request, string $id)
    {
        $role = Auth::user()?->role;

        if (!in_array($role, ['magazijnBeheerder', 'admin'])) {
            abort(403);
        }

        $order = Order::findOrFail($id);

        if ($order->status !== 'goedgekeurd') {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Enkel goedgekeurde bestellingen kunnen afgeleverd worden.'], 422);
            }
            return redirect()->route('order.index')->with('error', 'Enkel goedgekeurde bestellingen kunnen afgeleverd worden.');
        }

        $order->update(['status' => 'geleverd']);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'status' => 'geleverd', 'order_id' => $order->id]);
        }
        return redirect()->route('order.index')->with('success', 'Bestelling gemarkeerd als geleverd.');
    }

    public function toggleUrgent(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== Auth::id() && Auth::user()?->role !== 'admin') {
            abort(403);
        }

        if (in_array($order->status, ['geleverd', 'afgekeurd'])) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Urgentie kan niet meer gewijzigd worden.'], 422);
            }
            return redirect()->route('order.index')->with('error', 'Urgentie kan niet meer gewijzigd worden.');
        }

        $order->update(['urgent' => !$order->urgent]);
        $order->refresh();

        if ($request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'urgent'   => (bool) $order->urgent,
                'order_id' => $order->id,
                'message'  => $order->urgent ? '🚨 Bestelling gemarkeerd als URGENT.' : 'Urgentie opgeheven.',
            ]);
        }
        return redirect()->route('order.index')
            ->with('success', $order->urgent ? '🚨 Bestelling gemarkeerd als URGENT.' : 'Urgentie opgeheven.');
    }
}