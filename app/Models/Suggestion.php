<?php

namespace App\Models;

use Database\Factories\SuggestionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suggestion extends Model
{
    /** @use HasFactory<SuggestionFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'title', 'description', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
