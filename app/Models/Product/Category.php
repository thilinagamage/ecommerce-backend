<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'category_image',
        'status',
        'position',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'seo_index',
        'seo_follow',
    ];
        protected $casts = [
        'status' => 'boolean',
        'seo_index' => 'boolean',
        'seo_follow' => 'boolean',
        ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->where('status', true);
    }

    // public function products(){
    //     return $this->hasMany(Product::class);
    // }

    public function oldSlugs()
    {
        return $this->hasMany(CategorySlug::class);
    }

    //model events

    protected static function booted()
    {
        static::creating(function ($category) {
            if(empty($category->slug)){
                $category->slug = static::generateUniqueSlug($category->name);
            }
        });

        static::updating(function ($category) {
            if($category->isDirty('name') && empty($category->slug)){
                $category->slug = static::generateUniqueSlug($category->name, $category->id);
            }
        });

    }

     public static function generateUniqueSlug($name, $ignoreId = null)
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

    public function getUrlAttribute()
    {
        return url('/category/' . $this->slug);
    }

    public function getSeoTitleAttribute()
    {
        return $this->meta_title ?? $this->name;
    }

    public function getSeoDescriptionAttribute()
    {
        return $this->meta_description ?? Str::limit(strip_tags($this->description), 160);
    }
}
