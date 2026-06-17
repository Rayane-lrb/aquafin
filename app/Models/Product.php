<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = ['name', 'barcode', 'image', 'stock', 'is_active', 'is_flood_tool', 'needed_on_rain', 'product_category_id'];

    protected $casts = [
    'is_active'      => 'boolean',
    'is_flood_tool'  => 'boolean',
    'needed_on_rain' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
