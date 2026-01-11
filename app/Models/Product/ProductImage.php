<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
   // Mass assignable fields
    protected $fillable = [
        'product_id',
        'image_path',
        'is_featured',
        'sort_order',
    ];

      public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

       public function featuredImage()
    {
        return $this->hasOne(ProductImage::class)
                    ->where('is_featured', 1);
    }
      public function galleryImages()
    {
        return $this->hasMany(ProductImage::class)
                    ->whereNull('variation_id')
                    ->where('is_featured', 0)
                    ->orderBy('sort_order');
    }
    /**
     * The product that this image belongs to
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Helper: Check if this is the featured image
     */
    // public function isFeatured(): bool
    // {
    //     return $this->is_featured;
    // }

    /**
     * Accessor: Get full URL of image
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->image_path);
    }
}
