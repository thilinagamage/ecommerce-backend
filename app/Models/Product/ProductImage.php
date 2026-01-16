<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
   // Mass assignable fields
    protected $fillable = [
        'product_id',
        'path',
        'is_featured',
        'sort_order',
    ];

        public function product()
    {
        return $this->belongsTo(Product::class);
    }



 
}
