<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'product_type' => $this->product_type,
            'status' => $this->status,
            'visibility' => $this->visibility,

            // Pricing
            'regular_price' => $this->regular_price,
            'sale_price' => $this->sale_price,
            'price' => $this->price, // Uses the accessor from your model
            'is_on_sale' => $this->sale_price && $this->price === $this->sale_price,

            // Stock
            'sku' => $this->sku,
            'stock_status' => $this->stock_status,
            'stock_quantity' => $this->manage_stock ? $this->stock_quantity : null,
            'in_stock' => $this->stock_status === 'in_stock',

            // Images
            'featured_image' => $this->featuredImage ? [
                'id' => $this->featuredImage->id,
                'url' => asset('storage/' . $this->featuredImage->path),
                'path' => $this->featuredImage->path,
            ] : null,

            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => asset('storage/' . $image->path),
                    'path' => $image->path,
                    'is_featured' => $image->is_featured,
                    'sort_order' => $image->sort_order,
                ];
            }),

            // Relationships
            'categories' => $this->whenLoaded('categories', function () {
                return $this->categories->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                    ];
                });
            }),

            'tags' => $this->whenLoaded('tags', function () {
                return $this->tags->map(function ($tag) {
                    return [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                    ];
                });
            }),

            'collection' => $this->whenLoaded('collection', function () {
                return $this->collection ? [
                    'id' => $this->collection->id,
                    'name' => $this->collection->name,
                    'slug' => $this->collection->slug,
                ] : null;
            }),

            // Variations
            'variations' => $this->whenLoaded('variations', function () {
                return $this->variations->map(function ($variation) {
                    return [
                        'id' => $variation->id,
                        'sku' => $variation->sku,
                        'price' => $variation->price,
                        'stock_quantity' => $variation->stock_quantity,
                        'attributes' => $variation->attributes ?? [],
                    ];
                });
            }),

            // Reviews
            'rating' => [
                'average' => round($this->averageRating(), 1),
                'count' => $this->reviewsCount(),
                'distribution' => $this->whenLoaded('approvedReviews', fn() => $this->ratingDistribution()),
            ],

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
