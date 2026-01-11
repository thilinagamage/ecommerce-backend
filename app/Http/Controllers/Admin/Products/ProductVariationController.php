<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use App\Services\ProductVariationGenerator;
use Illuminate\Http\Request;

class ProductVariationController extends Controller
{
        public function generate(Request $request, Product $product)
    {
        $request->validate([
            'attributes' => 'required|array|min:1'
        ]);

        app(ProductVariationGenerator::class)
            ->generate($product, $request->attributes);

        return back()->with('success', 'Variations generated successfully');
    }

        public function update(Request $request, $variationId)
    {
        $request->validate([
            'price' => 'nullable|numeric',
            'stock' => 'required|integer|min:0',
            'sku'   => 'nullable|string|unique:product_variations,sku,' . $variationId,
        ]);

        $variation = ProductVariation::findOrFail($variationId);

        $variation->update($request->only('price', 'stock', 'sku'));

        return back()->with('success', 'Variation updated');
    }

        public function toggle($variationId)
    {
        $variation = ProductVariation::findOrFail($variationId);
        $variation->update(['is_active' => !$variation->is_active]);

        return back();
    }
}
