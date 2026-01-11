<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductVariationAttribute extends Model
{
     protected $fillable = [
        'product_variation_id',
        'product_attribute_id',
        'product_attribute_value_id',
    ];

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }

    public function value()
    {
        return $this->belongsTo(ProductAttributeValue::class, 'product_attribute_value_id');
    }
}
