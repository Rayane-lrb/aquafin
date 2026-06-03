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
}
