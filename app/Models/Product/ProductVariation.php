<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'stock',
        'is_active',
    ];

    /* -----------------
     | Relationships
     |-----------------*/

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Attribute values (Color: Red, Size: M)
    public function attributes()
    {
        return $this->hasMany(ProductVariationAttribute::class);
    }

    // Variation-specific images
    public function images()
    {
        return $this->hasMany(ProductVariationImage::class);
    }

    public function defaultImage()
    {
        return $this->hasOne(ProductVariationImage::class)
                    ->where('is_default', true);
    }
}
