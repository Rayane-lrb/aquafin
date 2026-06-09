<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::withCount('orders')->get();

        return view('warehouse.index', compact('warehouses'));
    }

    public function create()
    {
        return view('warehouse.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        Warehouse::create($request->only('name', 'address'));

        return redirect()->route('warehouse.index');
    }

    public function edit(string $id)
    {
        $warehouse = Warehouse::findOrFail($id);

        return view('warehouse.edit', compact('warehouse'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        Warehouse::findOrFail($id)->update($request->only('name', 'address'));

        return redirect()->route('warehouse.index');
    }

    public function destroy(string $id)
    {
        Warehouse::findOrFail($id)->delete();

        return redirect()->route('warehouse.index');
    }
}
