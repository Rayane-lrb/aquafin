<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PrecipitationController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SuggestionController;
use App\Http\Controllers\Userzone\ProfileController;
use App\Http\Controllers\WarehouseController;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Aquafin
|--------------------------------------------------------------------------
|
| Volgorde: routes → controller → view
|
| Rollen:
|   - admin            → volledige toegang
|   - magazijnBeheerder → bestellingen + producten beheren
|   - technieker       → catalogus raadplegen + bestellen
|
*/

// ── Startpagina ────────────────────────────────────────────────────────────
// Iedereen wordt doorgestuurd naar de loginpagina
Route::get('/', fn () => redirect()->route('login'));

// Na inloggen → catalogus
Route::get('/dashboard', fn () => redirect()->route('product.index'))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// ── Profiel (alle ingelogde gebruikers) ────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// ── Hoofdgroep: ingelogd + geverifieerd ────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // ── Catalogus / Producten ─────────────────────────────────────────────
    Route::get('/product',              [ProductController::class, 'index'])->name('product.index');   // overzicht + zoeken
    Route::get('/product/create',       [ProductController::class, 'create'])->name('product.create'); // formulier nieuw product
    Route::post('/product',             [ProductController::class, 'store'])->name('product.store');   // opslaan nieuw product
    Route::get('/product/{id}',         [ProductController::class, 'show'])->name('product.show');     // detailpagina
    Route::get('/product/{id}/edit',    [ProductController::class, 'edit'])->name('product.edit');     // bewerkformulier
    Route::patch('/product/{id}',       [ProductController::class, 'update'])->name('product.update'); // opslaan wijziging
    Route::patch('/product/{id}/toggle',[ProductController::class, 'toggle'])->name('product.toggle'); // actief/inactief
    Route::delete('/product/{id}',      [ProductController::class, 'destroy'])->name('product.destroy'); // verwijderen

    // ── Productcategorieën ────────────────────────────────────────────────
    Route::get('/productcategory',            [ProductCategoryController::class, 'index'])->name('productcategory.index');
    Route::get('/productcategory/create',     [ProductCategoryController::class, 'create'])->name('productcategory.create');
    Route::post('/productcategory',           [ProductCategoryController::class, 'store'])->name('productcategory.store');
    Route::get('/productcategory/{id}/edit',  [ProductCategoryController::class, 'edit'])->name('productcategory.edit');
    Route::patch('/productcategory/{id}',     [ProductCategoryController::class, 'update'])->name('productcategory.update');
    Route::delete('/productcategory/{id}',    [ProductCategoryController::class, 'destroy'])->name('productcategory.destroy');

    // ── Mandje (cart) ─────────────────────────────────────────────────────
    Route::get('/cart',                   [CartController::class, 'index'])->name('cart.index');        // inhoud mandje
    Route::post('/cart/add/{product}',    [CartController::class, 'add'])->name('cart.add');            // product toevoegen
    Route::patch('/cart/update/{product}',[CartController::class, 'update'])->name('cart.update');      // hoeveelheid aanpassen
    Route::delete('/cart/remove/{product}',[CartController::class, 'remove'])->name('cart.remove');     // product verwijderen
    Route::post('/cart/checkout',         [CartController::class, 'checkout'])->name('cart.checkout');  // bestelling plaatsen
    Route::post('/cart/ajax/{product}',   [CartController::class, 'ajaxUpdate'])->name('cart.ajax');    // AJAX update (geen herlaad)

    // ── Bestellingen ──────────────────────────────────────────────────────
    Route::get('/order',                  [OrderController::class, 'index'])->name('order.index');      // mijn bestellingen (technieker)
    Route::get('/order/magazijn',         [OrderController::class, 'magazijn'])->name('order.magazijn'); // beheer (magazijnBeheerder)
    Route::get('/order/create',           [OrderController::class, 'create'])->name('order.create');
    Route::post('/order',                 [OrderController::class, 'store'])->name('order.store');
    Route::get('/order/{id}/edit',        [OrderController::class, 'edit'])->name('order.edit');
    Route::patch('/order/{id}',           [OrderController::class, 'update'])->name('order.update');

    // Individuele acties
    Route::patch('/order/{id}/approve',   [OrderController::class, 'approve'])->name('order.approve');  // goedkeuren
    Route::patch('/order/{id}/reject',    [OrderController::class, 'reject'])->name('order.reject');    // afkeuren
    Route::patch('/order/{id}/deliver',   [OrderController::class, 'deliver'])->name('order.deliver');  // geleverd markeren
    Route::patch('/order/{id}/urgent',    [OrderController::class, 'toggleUrgent'])->name('order.urgent'); // dringend toggle

    // Groepsacties (meerdere bestellingen tegelijk beheren)
    Route::patch('/order/group/{groupId}/approve',       [OrderController::class, 'groupApprove'])->name('order.group.approve');
    Route::patch('/order/group/{groupId}/reject',        [OrderController::class, 'groupReject'])->name('order.group.reject');
    Route::patch('/order/group/{groupId}/delivery-date', [OrderController::class, 'groupDeliveryDate'])->name('order.group.deliveryDate');
    Route::patch('/order/group/{groupId}/deliver',       [OrderController::class, 'groupDeliver'])->name('order.group.deliver');

    // ── Werfplaatsen (magazijnen) ─────────────────────────────────────────
    Route::get('/warehouse',            [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::get('/warehouse/create',     [WarehouseController::class, 'create'])->name('warehouse.create');
    Route::post('/warehouse',           [WarehouseController::class, 'store'])->name('warehouse.store');
    Route::get('/warehouse/{id}/edit',  [WarehouseController::class, 'edit'])->name('warehouse.edit');
    Route::patch('/warehouse/{id}',     [WarehouseController::class, 'update'])->name('warehouse.update');
    Route::delete('/warehouse/{id}',    [WarehouseController::class, 'destroy'])->name('warehouse.destroy');

    // ── Neerslag (weersdata via Open-Meteo API) ───────────────────────────
    Route::get('/neerslag', [PrecipitationController::class, 'index'])->name('neerslag.index');

    // ── Suggesties ────────────────────────────────────────────────────────
    Route::get('/suggestion', [SuggestionController::class, 'index'])->name('suggestion.index');

    // ── Favorieten (AJAX toggle) ──────────────────────────────────────────
    Route::post('/favorites/{product}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // ── Notificaties ──────────────────────────────────────────────────────
    Route::post('/notifications/read-all', fn () => auth()->user()->unreadNotifications->markAsRead())
        ->name('notifications.readAll'); // alle markeren als gelezen
    Route::post('/notifications/{id}/read', function ($id) {
        $notif = auth()->user()->unreadNotifications->where('id', $id)->first();
        if ($notif) $notif->markAsRead();
        return response()->noContent();
    })->name('notifications.read'); // één markeren als gelezen

});


// ── Admin: gebruikersbeheer ────────────────────────────────────────────────
// Alleen toegankelijk voor de 'admin' rol (middleware: admin)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users',            [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create',     [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users',           [AdminUserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit',  [AdminUserController::class, 'edit'])->name('users.edit');
    Route::patch('/users/{id}',     [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}',    [AdminUserController::class, 'destroy'])->name('users.destroy');
});


// ── Authenticatie (login, register, wachtwoord reset…) ────────────────────
require __DIR__.'/auth.php';
