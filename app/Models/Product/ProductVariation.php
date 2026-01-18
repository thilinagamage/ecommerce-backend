<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'image',
        'regular_price',
        'sale_price',
        'sale_price_from',
        'sale_price_to',
        'manage_stock',
        'stock_quantity',
        'backorders',
        'stock_status',
    ];

// In ProductVariation.php
public function attributes()
{
    return $this->belongsToMany(
        ProductAttributeValue::class,
        'product_variation_attributes',
        'product_variation_id',
        'attribute_value_id' // Make sure this matches your pivot table column
    )->withPivot('attribute_id')->withTimestamps();
}


    public function image()
{
    return $this->hasOne(ProductVariationImage::class, 'product_variation_id');
}



    // Woo-style active price
    public function getPriceAttribute()
    {
        $today = now();

        if (
            $this->sale_price &&
            (!$this->sale_price_from || $today->gte($this->sale_price_from)) &&
            (!$this->sale_price_to || $today->lte($this->sale_price_to))
        ) {
            return $this->sale_price;
        }

        return $this->regular_price;
    }

}
