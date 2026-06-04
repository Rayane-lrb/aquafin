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
}
