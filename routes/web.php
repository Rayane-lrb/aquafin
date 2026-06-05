<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Userzone\SuggestionController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Redirect
Route::get('/', fn () => redirect()->route('login'));

Route::get('/dashboard', fn () => redirect()->route('product.index'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Technieker view
Route::get('/technieker', function (Request $request) {
    $products = Product::query();

    if ($request->filled('search')) {
        $products->where('name', 'like', '%'.$request->search.'%');
    }

    if ($request->filled('category')) {
        $products->where('product_category_id', $request->category);
    }

    return view('userzone.technieker', ['products' => $products->get()]);
})->middleware(['auth', 'verified'])->name('technieker');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Product categories
Route::get('/productcategory', [ProductCategoryController::class, 'index'])->name('productcategory.index');
Route::get('/productcategory/create', [ProductCategoryController::class, 'create'])->name('productcategory.create');
Route::post('/productcategory', [ProductCategoryController::class, 'store'])->name('productcategory.store');
Route::get('/productcategory/{id}/edit', [ProductCategoryController::class, 'edit'])->name('productcategory.edit');
Route::patch('/productcategory/{id}', [ProductCategoryController::class, 'update'])->name('productcategory.update');
Route::delete('/productcategory/{id}', [ProductCategoryController::class, 'destroy'])->name('productcategory.destroy');

// Products
Route::get('/product', [ProductController::class, 'index'])->name('product.index');
Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::post('/product', [ProductController::class, 'store'])->name('product.store');
Route::get('/product/{id}/edit', [ProductController::class, 'edit'])->name('product.edit');
Route::patch('/product/{id}', [ProductController::class, 'update'])->name('product.update');
Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');

// Orders
Route::get('/order', [OrderController::class, 'index'])->name('order.index');
Route::get('/order/create', [OrderController::class, 'create'])->name('order.create');
Route::post('/order', [OrderController::class, 'store'])->name('order.store');
Route::get('/order/{id}/edit', [OrderController::class, 'edit'])->name('order.edit');
Route::patch('/order/{id}', [OrderController::class, 'update'])->name('order.update');
Route::patch('/order/{id}/approve', [OrderController::class, 'approve'])->name('order.approve');
Route::patch('/order/{id}/reject', [OrderController::class, 'reject'])->name('order.reject');

// Suggestions
Route::get('/suggestion', [SuggestionController::class, 'index'])->name('suggestion.index');
Route::get('/suggestion/create', [SuggestionController::class, 'create'])->name('suggestion.create');
Route::get('/suggestion/{id}', [SuggestionController::class, 'show'])->name('suggestion.show');
Route::post('/suggestion', [SuggestionController::class, 'store'])->name('suggestion.store');
Route::patch('/suggestion/{id}/approve', [SuggestionController::class, 'approve'])->name('suggestion.approve');
Route::patch('/suggestion/{id}/reject', [SuggestionController::class, 'reject'])->name('suggestion.reject');
Route::delete('/suggestion/{id}', [SuggestionController::class, 'destroy'])->name('suggestion.destroy');

// Admin - User management
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
});

require __DIR__.'/auth.php';
