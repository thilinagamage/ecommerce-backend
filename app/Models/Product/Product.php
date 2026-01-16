<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Product extends Model
{
        protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'product_type',
        'status',
        'visibility',
        'collection_id',

        'sku',
        'manage_stock',
        'stock_quantity',
        'low_stock_threshold',
        'stock_status',
        'backorders',

        'regular_price',
        'sale_price',
        'sale_price_from',
        'sale_price_to',
    ];

        // Slug auto-generate
    protected static function booted()
    {
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });

    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function featuredImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_featured', true);
    }

    public function galleryImages()
    {
        return $this->hasMany(ProductImage::class)->where('is_featured', false)->orderBy('sort_order');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }

    public function getPriceAttribute()
    {
        $today = Carbon::today();

        if (
            $this->sale_price &&
            (
                (!$this->sale_price_from || $today->gte($this->sale_price_from)) &&
                (!$this->sale_price_to || $today->lte($this->sale_price_to))
            )
        ) {
            return $this->sale_price;
        }

        return $this->regular_price;
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }



}
