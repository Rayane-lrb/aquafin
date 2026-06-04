<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuggestionController extends Controller
{
    public function index() {
        $suggestions = Suggestion::all();

        return view('suggestion.index', ['suggestions' => $suggestions]);
    }

    public function show(string $id) {
        $suggestion = Suggestion::findOrFail($id);

        return view('suggestion.show', ['suggestion' => $suggestion]);
    }

    public function create() {
        return view('suggestion.create');
    }

    public function store(request $request) {
        $request->validate([
            'title' => 'required|string|min:5|max:255',
            'description' => 'required|string|min:10|max:2000',
        ]);

        Suggestion::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id(),
            'status'  => 'pending'
        ]);

        return redirect()->route('suggestion.create');
    }

    public function approve(string $id) {
        $suggestion = Suggestion::findOrFail($id);

        $suggestion::update([
            'status' => 'approved'
        ]);

        return redirect()->route('suggestion.index');
    }

    public function reject(string $id) {
                $suggestion = Suggestion::findOrFail($id);

        $suggestion::update([
            'status' => 'rejected'
        ]);

        return redirect()->route('suggestion.index');
    }
}
