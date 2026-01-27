<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['featuredImage', 'images', 'categories', 'tags', 'collection'])
            ->where('status', 'published')
            ->whereIn('visibility', ['shop','both']);  // ← CHANGED: where to whereIn

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        // Filter by collection
        if ($request->has('collection')) {
            $query->whereHas('collection', function ($q) use ($request) {
                $q->where('slug', $request->input('collection'));
            });
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->input('tag'));
            });
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('regular_price', '>=', $request->input('min_price'));
        }
        if ($request->has('max_price')) {
            $query->where('regular_price', '<=', $request->input('max_price'));
        }

        // Filter by stock status
        if ($request->has('in_stock') && $request->boolean('in_stock')) {
            $query->where('stock_status', 'in_stock');
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $allowedSorts = ['created_at', 'name', 'regular_price', 'sale_price'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = min($request->input('per_page', 12), 100);
        $products = $query->paginate($perPage);

        return ProductResource::collection($products);
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'published')
            ->whereIn('visibility', ['shop','both'])  // ← CHANGED: where to whereIn
            ->with([
                'featuredImage',
                'images',
                'categories',
                'tags',
                'collection',
                'variations.attributes',
                'approvedReviews' => function ($query) {
                    $query->latest()->limit(10);
                }
            ])
            ->firstOrFail();

        return new ProductResource($product);
    }

    public function featured(Request $request)
    {
        $limit = min($request->input('limit', 8), 50);

        $products = Product::query()
            ->with(['featuredImage', 'images', 'categories'])
            ->where('status', 'published')
            ->whereIn('visibility', ['shop','both'])  // ← CHANGED: where to whereIn
            ->latest()
            ->limit($limit)
            ->get();

        return ProductResource::collection($products);
    }

    public function related(string $slug, Request $request)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $limit = min($request->input('limit', 4), 20);

        $relatedProducts = Product::query()
            ->with(['featuredImage', 'images', 'categories'])
            ->where('status', 'published')
            ->whereIn('visibility', ['shop','both'])  // ← CHANGED: where to whereIn
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function ($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        return ProductResource::collection($relatedProducts);
    }
}
