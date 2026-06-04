<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    /** @use HasFactory<\Database\Factories\SuggestionFactory> */
    use HasFactory;


    protected $fillable = [
        'product_id',
        'reason',
        'is_active',
    ];
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
