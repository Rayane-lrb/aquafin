<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use App\Models\Product;
use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    // Alle suggesties ophalen
    public function index()
    {
        $suggestions = Suggestion::with('product')->get();
        return response()->json($suggestions);
    }

    // Formulier voor nieuwe suggestie (niet nodig voor API)
    public function create()
    {
        //
    }

    // Nieuwe suggestie opslaan
    public function store(Request $request)
    {
        // Validatie
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'reason'     => 'nullable|string|max:255',
            'is_active'  => 'boolean',
        ]);

        // Controle of product bestaat (extra zekerheid)
        $product = Product::find($validated['product_id']);
        if (!$product) {
            return response()->json([
                'message' => 'Product niet gevonden.'
            ], 404);
        }

        // Suggestie opslaan
        $suggestion = Suggestion::create($validated);

        return response()->json($suggestion, 201);
    }

    // Één suggestie tonen
    public function show(string $id)
    {
        $suggestion = Suggestion::with('product')->find($id);

        if (!$suggestion) {
            return response()->json([
                'message' => 'Suggestie niet gevonden.'
            ], 404);
        }

        return response()->json($suggestion);
    }

    // Formulier voor bewerken (niet nodig voor API)
    public function edit(string $id)
    {
        //
    }

    // Suggestie aanpassen
    public function update(Request $request, string $id)
    {
        $suggestion = Suggestion::find($id);

        if (!$suggestion) {
            return response()->json([
                'message' => 'Suggestie niet gevonden.'
            ], 404);
        }

        $validated = $request->validate([
            'product_id' => 'sometimes|exists:products,id',
            'reason'     => 'nullable|string|max:255',
            'is_active'  => 'boolean',
        ]);

        $suggestion->update($validated);

        return response()->json($suggestion);
    }

    // Suggestie verwijderen
    public function destroy(string $id)
    {
        $suggestion = Suggestion::find($id);

        if (!$suggestion) {
            return response()->json([
                'message' => 'Suggestie niet gevonden.'
            ], 404);
        }

        $suggestion->delete();

        return response()->json([
            'message' => 'Suggestie verwijderd.'
        ]);
    }
}