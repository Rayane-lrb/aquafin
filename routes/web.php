<?php

use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Userzone\SuggestionController;
use App\Http\Controllers\OrderController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Redirect naar login
Route::get('/', function () {
    return redirect()->route('login');
});

// Groep met auth en verified middleware (voor alle routes die in de navigatie worden gebruikt)
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('userzone.dashboard');
    })->name('dashboard');

    // Technieker
    Route::get('/technieker', function (Request $request) {
        $products = Product::query();
        if ($request->filled('search')) {
            $products->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $products->where('product_category_id', $request->category);
        }
        return view('userzone.technieker', ['products' => $products->get()]);
    })->name('technieker');

    // Product routes
    Route::get('/product', [ProductController::class, 'index'])->name('product.index');
    Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
    Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('/product', [ProductController::class, 'store'])->name('product.store');
    Route::get('/product/{id}/edit', [ProductController::class, 'edit'])->name('product.edit');
    Route::patch('/product/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');

    // Order routes
    Route::get('/orders', [OrderController::class, 'index'])->name('order.index');
    Route::get('/orders/create', [OrderController::class, 'create'])->name('order.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('order.store');
    Route::put('/orders/{id}/approve', [OrderController::class, 'approve'])->name('order.approve');
    Route::put('/orders/{id}/reject', [OrderController::class, 'reject'])->name('order.reject');

    // Product Category routes
    Route::get('/productcategory', [ProductCategoryController::class, 'index'])->name('productcategory.index');
    Route::get('/productcategory/create', [ProductCategoryController::class, 'create'])->name('productcategory.create');
    Route::post('/productcategory', [ProductCategoryController::class, 'store'])->name('productcategory.store');
    Route::get('/productcategory/{id}/edit', [ProductCategoryController::class, 'edit'])->name('productcategory.edit');
    Route::patch('/productcategory/{id}', [ProductCategoryController::class, 'update'])->name('productcategory.update');
    Route::delete('/productcategory/{id}', [ProductCategoryController::class, 'destroy'])->name('productcategory.destroy');

    // Profile routes (bestaand)
    Route::get('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\Userzone\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth routes (blijft buiten de auth middleware)
require __DIR__.'/auth.php';
