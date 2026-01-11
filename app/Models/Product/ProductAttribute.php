<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function values()
    {
        return $this->hasMany(ProductAttributeValue::class);
    }
}
