<?php

use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('userzone.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/technieker', function () {
    return view('userzone.technieker');
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
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/product/create', [ProductController::class, 'create'])->name('product.create');
require __DIR__.'/auth.php';
