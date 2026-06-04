<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = ['name', 'stock', 'is_active', 'is_flood_tool', 'product_category_id'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }
