<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    private function denyTechnieker(): void
    {
        if (auth()->user()?->role === 'technieker') {
            abort(403);
        }
    }

    public function index()
    {
        $productCategories = ProductCategory::all();

        return view('productcategory.index', ['productCategories' => $productCategories]);
    }

    public function create()
    {
        $this->denyTechnieker();

        return view('productcategory.create');
    }

    public function store(Request $request)
    {
        $this->denyTechnieker();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        ProductCategory::create(
            ['name' => $request->name]
        );

        return redirect()->route('productcategory.index');

    }

    public function edit(string $id)
    {
        $this->denyTechnieker();

        $productCategory = ProductCategory::findOrFail($id);

        return view('productcategory.edit', ['productcategory' => $productCategory]);
    }

    public function update(Request $request, string $id)
    {
        $this->denyTechnieker();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $productCategory = ProductCategory::findOrFail($id);
        $productCategory->update([
            'name' => $request->name,
        ]);

        return redirect()->route('productcategory.index');
    }

    public function destroy(string $id)
    {
        $this->denyTechnieker();

        $productCategory = ProductCategory::findOrFail($id);
        $productCategory->delete();

        return redirect()->route('productcategory.index');
    }
}
