<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductVariationImage extends Model
{
    protected $fillable = [
        'product_variation_id',
        'image_path',
        'is_default',
    ];

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }
}

