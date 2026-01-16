<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Product\Category;
use App\Models\Product\Collection;
use App\Models\Product\Product;
use App\Models\Product\ProductAttribute;
use App\Models\Product\ProductAttributeValue;
use App\Models\Product\ProductImage;
use App\Models\Product\ProductVariation;
use App\Models\Product\ProductVariationAttribute;
use App\Models\Product\ProductVariationImage;
use App\Models\Product\Tag;
use App\Services\ProductVariationGenerator;
use Illuminate\Container\Attributes\Storage as AttributesStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller

{
    public function index()
    {
        $products = Product::latest()->paginate(20);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $collections = Collection::all();
        $categories = Category::where('status', '1')->get();
        //$categories = Category::all();
        $tags = Tag::all();
        $attributes = ProductAttribute::with('values')->get();
        return view('products.create', compact('collections', 'tags', 'categories', 'attributes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'product_type' => 'required|in:simple,variable',
            'status' => 'required|in:draft,published',
            'visibility' => 'required|in:shop,search,both,hidden',
            'collection_id' => 'nullable|exists:collections,id',

            'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',

            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',

            // SIMPLE PRODUCT FIELDS
            'sku' => 'nullable|unique:products,sku',
            'manage_stock' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'backorders' => 'boolean',

            'regular_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:regular_price',
            'sale_price_from' => 'nullable|date',
            'sale_price_to' => 'nullable|date|after_or_equal:sale_price_from',

            // VARIABLE PRODUCT
            'variations' => 'nullable|array',
            'variations.*.sku' => 'nullable|string',
            'variations.*.regular_price' => 'required_if:product_type,variable|numeric|min:0',
            'variations.*.stock_quantity' => 'nullable|integer|min:0',
            'variations.*.attributes' => 'required_if:product_type,variable|array',
        ]);

        // SIMPLE PRODUCT
        if ($validated['product_type'] === 'simple') {
            $validated['manage_stock'] = $request->has('manage_stock');
            $validated['backorders'] = $request->has('backorders');

            if ($validated['manage_stock']) {
                $validated['stock_status'] =
                    ($validated['stock_quantity'] > 0) ? 'in_stock' : 'out_of_stock';
            } else {
                $validated['stock_quantity'] = null;
                $validated['low_stock_threshold'] = null;
                $validated['stock_status'] = 'in_stock';
            }

            if (empty($validated['sale_price'])) {
                $validated['sale_price'] = null;
                $validated['sale_price_from'] = null;
                $validated['sale_price_to'] = null;
            }
        } else {
            // VARIABLE PRODUCT → parent has NO price/stock
            $validated['sku'] = null;
            $validated['regular_price'] = null;
            $validated['sale_price'] = null;
            $validated['stock_quantity'] = null;
            $validated['manage_stock'] = false;
            $validated['stock_status'] = 'in_stock';
        }

        $product = Product::create($validated);

        // Attach categories and tags
        if ($request->filled('categories')) {
            $product->categories()->attach($request->categories);
        }
        if ($request->filled('tags')) {
            $product->tags()->attach($request->tags);
        }

        // VARIABLE PRODUCT → Handle variations
if ($validated['product_type'] === 'variable' && $request->filled('variations')) {
    foreach ($request->variations as $variationData) {

        $variation = $product->variations()->create([
            'sku' => $variationData['sku'] ?? null,
            'regular_price' => $variationData['regular_price'],
            'sale_price' => $variationData['sale_price'] ?? null,
            'manage_stock' => true,
            'stock_quantity' => $variationData['stock_quantity'] ?? 0,
            'stock_status' => ($variationData['stock_quantity'] ?? 0) > 0
                ? 'in_stock'
                : 'out_of_stock',
        ]);

        // FIXED: Properly attach attributes with pivot data
        if (!empty($variationData['attributes'])) {
            $pivotData = [];
            foreach ($variationData['attributes'] as $valueId) {
                $value = ProductAttributeValue::find($valueId);
                if ($value) {
                    $pivotData[$value->id] = ['attribute_id' => $value->product_attribute_id];
                }
            }
            $variation->attributes()->sync($pivotData);
        }
    }
}


        // Handle product images
        $this->handleProductImages($request, $product);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
    }

public function edit($id)
{
    $product = Product::with([
        'variations.attributes', // This loads ProductAttributeValue models
        'categories',
        'tags',
        'images',
        'featuredImage',
        'galleryImages'
    ])->findOrFail($id);

    $collections = Collection::all();
    $categories  = Category::where('status', '1')->get();
    $tags        = Tag::all();
    $attributes  = ProductAttribute::with('values')->get();

    // Get all unique attribute value IDs used across all variations
    $selectedValueIds = $product->variations
        ->flatMap(function ($variation) {
            return $variation->attributes->pluck('id');
        })
        ->unique()
        ->toArray();

    return view('products.edit', compact(
        'product',
        'collections',
        'tags',
        'categories',
        'attributes',
        'selectedValueIds'
    ));
}


public function update(Request $request, $id)
{
    $product = Product::with('variations.attributes')->findOrFail($id);

    // ---------------------------
    // VALIDATION
    // ---------------------------
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
        'short_description' => 'nullable|string',
        'description' => 'nullable|string',
        'product_type' => 'required|in:simple,variable',
        'status' => 'required|in:draft,published',
        'visibility' => 'required|in:shop,search,both,hidden',
        'collection_id' => 'nullable|exists:collections,id',

        'featured_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'gallery_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        'categories' => 'nullable|array',
        'categories.*' => 'exists:categories,id',

        'tags' => 'nullable|array',
        'tags.*' => 'exists:tags,id',

        'sku' => 'nullable|unique:products,sku,' . $product->id,
        'manage_stock' => 'boolean',
        'stock_quantity' => 'nullable|integer|min:0',
        'low_stock_threshold' => 'nullable|integer|min:0',
        'backorders' => 'boolean',

        'regular_price' => 'nullable|numeric|min:0',
        'sale_price' => 'nullable|numeric|min:0|lt:regular_price',
        'sale_price_from' => 'nullable|date',
        'sale_price_to' => 'nullable|date|after_or_equal:sale_price_from',
    ]);

    $validated['manage_stock'] = $request->has('manage_stock');
    $validated['backorders'] = $request->has('backorders');

    if ($validated['manage_stock']) {
        $validated['stock_status'] =
            ($validated['stock_quantity'] > 0) ? 'in_stock' : 'out_of_stock';
    } else {
        $validated['stock_quantity'] = null;
        $validated['low_stock_threshold'] = null;
        $validated['stock_status'] = 'in_stock';
    }

    if (empty($validated['sale_price'])) {
        $validated['sale_price'] = null;
        $validated['sale_price_from'] = null;
        $validated['sale_price_to'] = null;
    }

    // ---------------------------
    // HANDLE PRODUCT TYPE SWITCH
    // ---------------------------
    if ($validated['product_type'] === 'simple') {
        $product->variations()->delete();
    }

    if ($validated['product_type'] === 'variable') {
        // Clear simple product fields
        $validated['regular_price'] = null;
        $validated['sale_price'] = null;
        $validated['sale_price_from'] = null;
        $validated['sale_price_to'] = null;
        $validated['stock_quantity'] = null;
        $validated['low_stock_threshold'] = null;
        $validated['manage_stock'] = false;
        $validated['backorders'] = false;
    }

    // ---------------------------
    // REMOVE DELETED VARIATIONS
    // ---------------------------
    if ($request->filled('removed_variations')) {
        $ids = explode(',', $request->removed_variations);
        foreach ($product->variations()->whereIn('id', $ids)->get() as $variation) {
            if ($variation->image) {
                Storage::disk('public')->delete($variation->image);
            }
            $variation->delete();
        }
    }

    // ---------------------------
    // UPDATE PRODUCT
    // ---------------------------
    $product->update($validated);

    // ---------------------------
    // HANDLE VARIATIONS
    // ---------------------------
if ($validated['product_type'] === 'variable' && $request->has('variations')) {
    foreach ($request->variations as $index => $data) {

        // Create or update variation
        $variation = $product->variations()->updateOrCreate(
            ['id' => $data['id'] ?? null],
            [
                'sku' => $data['sku'] ?? null,
                'regular_price' => $data['regular_price'] ?? null,
                'sale_price' => $data['sale_price'] ?? null,
                'stock_quantity' => $data['stock_quantity'] ?? 0,
                'manage_stock' => true,
                'stock_status' => ($data['stock_quantity'] > 0) ? 'in_stock' : 'out_of_stock',
            ]
        );

        // FIXED: Properly sync attributes
        if (!empty($data['attributes'])) {
            $pivotData = [];

            foreach ($data['attributes'] as $valueId) {
                $value = ProductAttributeValue::find($valueId);
                if ($value) {
                    // Key is the attribute_value_id, value is pivot columns
                    $pivotData[$value->id] = [
                        'attribute_id' => $value->product_attribute_id
                    ];
                }
            }

            // Sync will remove old and add new
            $variation->attributes()->sync($pivotData);
        } else {
            // If no attributes provided, detach all
            $variation->attributes()->sync([]);
        }

        // Handle variation image
        if ($request->hasFile("variations.$index.image")) {
            // Delete old image
            if ($variation->image) {
                Storage::disk('public')->delete($variation->image);
            }

            $path = $request->file("variations.$index.image")->store('variations', 'public');
            $variation->image = $path;
            $variation->save();
        }
    }
}

    // ---------------------------
    // SYNC RELATIONS
    // ---------------------------
    $product->categories()->sync($request->categories ?? []);
    $product->tags()->sync($request->tags ?? []);

    // ---------------------------
    // HANDLE PRODUCT IMAGES
    // ---------------------------
    $this->handleProductImages($request, $product);

    return redirect()
        ->route('products.index')
        ->with('success', 'Product updated successfully');
}


protected function handleProductImages(Request $request, Product $product)
{
    /**
     * ------------------------------------
     * REMOVE GALLERY IMAGES
     * ------------------------------------
     */
    if ($request->filled('removed_gallery_images')) {
        $ids = explode(',', $request->removed_gallery_images);

        $images = ProductImage::whereIn('id', $ids)->get();
        foreach ($images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
    }

    /**
     * ------------------------------------
     * FEATURED IMAGE
     * ------------------------------------
     */
    if ($request->hasFile('featured_image')) {

        // Remove old featured image
        $oldFeatured = $product->featuredImage;
        if ($oldFeatured) {
            Storage::disk('public')->delete($oldFeatured->path);
            $oldFeatured->delete();
        }

        $path = $request->file('featured_image')->store('products', 'public');

        $product->images()->create([
            'path' => $path,
            'is_featured' => true,
            'sort_order' => 0,
        ]);
    }

    /**
     * ------------------------------------
     * GALLERY IMAGES
     * ------------------------------------
     */
    if ($request->hasFile('gallery_images')) {
        $order = $product->galleryImages()->max('sort_order') ?? 0;

        foreach ($request->file('gallery_images') as $file) {
            $path = $file->store('products', 'public');

            $product->images()->create([
                'path' => $path,
                'is_featured' => false,
                'sort_order' => ++$order,
            ]);
        }
    }
}


    public function deleteGalleryImage(ProductImage $image)
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json(['success' => true]);
    }

    
    public function show($id)
    {
        $product = Product::with([
            'featuredImage',
            'galleryImages',
            'categories',
            'tags',
            'collection',
            'variations.attributes'
        ])->findOrFail($id);

        return view('products.show', compact('product'));
    }
    public function destroy($id)
    {
        $product = Product::findOrFail($id); // find the product or throw 404

        // Optional: delete related variations if you have them
        if ($product->type === 'variable') {
            $product->variations()->delete();
        }

        $product->delete(); // delete the product

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully');
    }




    public function removeImage(ProductImage $image) {}
}
