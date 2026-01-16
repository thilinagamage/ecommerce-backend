<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Collection extends Model
{
        protected $fillable = [
        'name',
        'slug',
        'description',
        'banner_image',
        'status',
        'start_date',
        'end_date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($collection) {
            if (empty($collection->slug)) {
                $collection->slug = Str::slug($collection->name);
            }
        });
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function isActive()
    {
        $now = now();

        return $this->status &&
            (!$this->start_date || $this->start_date <= $now) &&
            (!$this->end_date || $this->end_date >= $now);
    }
}
