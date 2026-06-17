<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuggestionController extends Controller
{
    public function index()
    {
        $suggestions = Suggestion::with('user')
            ->when(auth()->user()->role === 'technieker', function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->latest()
            ->get();

        return view('suggestion.index', ['suggestions' => $suggestions]);
    }

    public function create()
    {
        return view('suggestion.create');
    }

    public function show(string $id)
    {
        $suggestion = Suggestion::with('user')->findOrFail($id);
        $categories = ProductCategory::all();

        return view('suggestion.show', ['suggestion' => $suggestion, 'categories' => $categories]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:5120'],
        ]);

        $data = [
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'in behandeling',
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('suggestions', 'public');
        }

        Suggestion::create($data);

        return redirect()->route('suggestion.index');
    }

    public function approve(string $id)
    {
        Suggestion::findOrFail($id)->update(['status' => 'goedgekeurd']);

        return redirect()->route('suggestion.index');
    }

    public function reject(string $id)
    {
        Suggestion::findOrFail($id)->update(['status' => 'afgekeurd']);

        return redirect()->route('suggestion.index');
    }

    public function addToCatalog(Request $request, string $id)
    {
        $suggestion = Suggestion::findOrFail($id);

        // Vérifications
        if ($suggestion->status !== 'goedgekeurd' || ! $suggestion->image) {
            abort(403);
        }

        $request->validate([
            'product_category_id' => ['required', 'exists:product_categories,id'],
        ]);

        Product::create([
            'name' => $suggestion->title,
            'image' => $suggestion->image,
            'stock' => 0,
            'is_active' => true,
            'is_flood_tool' => false,
            'product_category_id' => $request->product_category_id,
        ]);

        return redirect()->route('product.index')->with('success', "'{$suggestion->title}' toegevoegd aan de catalogus.");
    }

    public function destroy(string $id)
    {
        Suggestion::findOrFail($id)->delete();

        return redirect()->route('suggestion.index');
    }
}
