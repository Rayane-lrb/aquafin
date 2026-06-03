<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index() {
        $productCategories = ProductCategory::all();

        return view('productcategory.index', ['productcategories' => $productCategories]);
    }

    public function create() {
        return view('productcategory.create');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255']
        ]);

        ProductCategory::create(
            ['name' => $request->name]
        );

        return redirect()->route('productcategory.index');

    }

    public function edit(string $id) {
        $productCategory = ProductCategory::findOrFail($id);

        return view('productcategory.edit', ['productcategory' => $productCategory]);
    }

}
