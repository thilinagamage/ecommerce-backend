<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductVariationAttribute extends Model
{
     protected $fillable = [
        'product_variation_id',
        'attribute_id',
        'attribute_value_id',
    ];



    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'attribute_id');
    }

    public function value()
    {
        return $this->belongsTo(ProductAttributeValue::class, 'attribute_value_id');
    }
}
