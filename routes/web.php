<?php

use App\Http\Controllers\ProductCategoryController;
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

require __DIR__.'/auth.php';
