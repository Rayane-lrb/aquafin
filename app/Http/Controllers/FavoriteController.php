<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function toggle(Request $request, Product $product)
    {
        $user = auth()->user();

        $exists = $user->favoriteProducts()->where('product_id', $product->id)->exists();

        if ($exists) {
            $user->favoriteProducts()->detach($product->id);
            $isFavorite = false;
        } else {
            $user->favoriteProducts()->attach($product->id);
            $isFavorite = true;
        }

        return response()->json([
            'success'    => true,
            'favorite'   => $isFavorite,
            'product_id' => $product->id,
        ]);
    }
}
