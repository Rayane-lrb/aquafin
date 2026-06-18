<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::when($request->filled('search'), fn ($q) => $q
            ->where('name', 'like', '%'.$request->search.'%')
            ->orWhere('email', 'like', '%'.$request->search.'%'))
            ->orderByRaw("CASE role WHEN 'admin' THEN 0 WHEN 'magazijnBeheerder' THEN 1 WHEN 'technieker' THEN 2 ELSE 3 END")
            ->get();

        return view('admin.users.index', ['users' => $users]);
    }

    public function create()
    {
        $warehouses = Warehouse::orderBy('name')->get();

        return view('admin.users.create', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'email'                => ['required', 'email', 'unique:users,email'],
            'password'             => ['required', 'string', 'min:8'],
            'role'                 => ['required', 'in:admin,magazijnBeheerder,technieker'],
            'default_warehouse_id' => ['nullable', 'exists:warehouses,id'],
        ]);

        User::create([
            'name'                 => $request->name,
            'email'                => $request->email,
            'password'             => Hash::make($request->password),
            'role'                 => $request->role,
            'default_warehouse_id' => $request->role === 'technieker' ? $request->default_warehouse_id : null,
        ]);

        return redirect()->route('admin.users.index');
    }

    public function edit(string $id)
    {
        $user       = User::findOrFail($id);
        $warehouses = Warehouse::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'warehouses'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'email'                => ['required', 'email', 'unique:users,email,'.$user->id],
            'role'                 => ['required', 'in:admin,magazijnBeheerder,technieker'],
            'default_warehouse_id' => ['nullable', 'exists:warehouses,id'],
        ]);

        $user->update([
            'name'                 => $request->name,
            'email'                => $request->email,
            'role'                 => $request->role,
            'default_warehouse_id' => $request->role === 'technieker' ? $request->default_warehouse_id : null,
        ]);

        return redirect()->route('admin.users.index');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return redirect()->route('admin.users.index');
    }
}
