<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return redirect()->route('product.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/technieker', function (Request $request) {
    $products = Product::query();

    if ($request->filled('search')) {
        $products->where('name', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('category')) {
        $products->where('product_category_id', $request->category);
    }

    return view('userzone.technieker', ['products' => $products->get()]);
})->middleware(['auth', 'verified'])->name('technieker');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/productcategory', [ProductCategoryController::class, 'index'])->name('productcategory.index');
Route::get('/productcategory/create', [ProductCategoryController::class, 'create'])->name('productcategory.create');
Route::post('/productcategory', [ProductCategoryController::class, 'store'])->name('productcategory.store');
Route::get('/productcategory/{id}/edit', [ProductCategoryController::class, 'edit'])->name('productcategory.edit');
Route::patch('/productcategory/{id}', [ProductCategoryController::class, 'update'])->name('productcategory.update');
Route::delete('/productcategory/{id}', [ProductCategoryController::class, 'destroy'])->name('productcategory.destroy');

Route::get('/product', [ProductController::class, 'index'])->name('product.index');
Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::post('/product', [ProductController::class, 'store'])->name('product.store');
Route::get('/product/{id}/edit', [ProductController::class, 'edit'])->name('product.edit');
Route::patch('/product/{id}', [ProductController::class, 'update'])->name('product.update');
Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');

Route::get('/order', [OrderController::class, 'index'])->name('order.index');
Route::get('/order/create', [OrderController::class, 'create'])->name('order.create');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');
Route::patch('/order/{id}/approve', [OrderController::class, 'approve'])->name('order.approve');
Route::patch('/order/{id}/reject', [OrderController::class, 'reject'])->name('order.reject');

Route::get('/suggestion', [SuggestionController::class, 'index'])->name('suggestion.index');
Route::get('/suggestion/{id}', [SuggestionController::class, 'show'])->name('suggestion.show');
Route::get('/suggestion/create', [SuggestionController::class, 'create'])->('suggestion.create');
Route::post('/suggestion', [SuggestionController::class, 'store'])->name('suggestion.store');
Route::get('/suggestion/{id}/edit', [SuggestionController::class, 'edit'])->name('suggestion.edit');

require __DIR__.'/auth.php';
