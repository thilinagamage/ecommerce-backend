<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    protected $fillable = [
        'product_attribute_id',
        'value',
        'slug',
    ];

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, 'product_attribute_id');
    }
    public function variations()
{
    return $this->belongsToMany(
        ProductVariation::class,
        'product_variation_attributes',
        'product_attribute_value_id',
        'product_variation_id'
    )->withPivot('attribute_id')->withTimestamps();
}

}
