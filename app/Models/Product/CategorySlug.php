<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategorySlug extends Model
{
    protected $fillable = [
        'category_id',
        'old_slug',
    ];

    public function category(): BelongsTo{ return $this->belongsTo(Category::class);
    }
}
