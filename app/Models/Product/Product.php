<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
        protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'type',
        'price',
        'sale_price',
        'status',
        'meta_title',
        'meta_description',
    ];

       protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name, $product->id);
            }
        });
    }

    public static function generateUniqueSlug($name, $ignoreId = null)
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

        // Global product images
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

        // Featured image
    public function featuredImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_featured', true);
    }

        // Product variations
    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

        public function isVariable()
    {
        return $this->type === 'variable';
    }

    public function isSimple()
    {
        return $this->type === 'simple';
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category', 'product_id', 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

}
