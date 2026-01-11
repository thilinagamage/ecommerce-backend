<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
      protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    /**
     * Auto-generate slug
     */
    protected static function booted()
    {
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });

        static::updating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Relationship: Tag ↔ Products
     */
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
