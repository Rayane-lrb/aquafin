<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    public function index() {
        $productcategories = ProductCategory::all();

        return view('productcategory.index', ['productcategories' => $productcategories]);
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
}
