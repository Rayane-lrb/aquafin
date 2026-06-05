<?php

namespace App\Http\Controllers\Userzone;

use App\Http\Controllers\Controller;
use App\Models\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuggestionController extends Controller
{
    public function index()
    {
        $suggestions = Suggestion::with('user')->latest()->get();
        return view('suggestion.index', ['suggestions' => $suggestions]);
    }

    public function create()
    {
        return view('suggestion.create');
    }

    public function show(string $id)
    {
        $suggestion = Suggestion::with('user')->findOrFail($id);
        return view('suggestion.show', ['suggestion' => $suggestion]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
        ]);

        Suggestion::create([
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => 'in behandeling',
        ]);

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

    public function destroy(string $id)
    {
        Suggestion::findOrFail($id)->delete();
        return redirect()->route('suggestion.index');
    }
}
