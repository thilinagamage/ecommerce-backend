<?php

namespace App\Services;

use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use App\Models\Product\ProductVariationAttribute;
use Illuminate\Support\Facades\DB;

class ProductVariationGenerator
{
    /**
     * Generate variations for a variable product
     *
     * @param Product $product
     * @param array $attributes
     *
     * Expected $attributes format:
     * [
     *   attribute_id => [value_id, value_id]
     * ]
     */
    public function generate(Product $product, array $attributes): void
    {
        if ($product->type !== 'variable') {
            return;
        }

        DB::transaction(function () use ($product, $attributes) {

            $combinations = $this->cartesianProduct($attributes);

            foreach ($combinations as $combination) {

                if ($this->variationExists($product, $combination)) {
                    continue;
                }

                $variation = ProductVariation::create([
                    'product_id' => $product->id,
                    'stock'      => 0,
                    'is_active'  => true,
                ]);

                foreach ($combination as $attributeId => $valueId) {
                    ProductVariationAttribute::create([
                        'product_variation_id'       => $variation->id,
                        'product_attribute_id'       => $attributeId,
                        'product_attribute_value_id' => $valueId,
                    ]);
                }
            }
        });
    }

    /**
     * Generate cartesian product of attributes
     */
    private function cartesianProduct(array $attributes): array
    {
        $result = [[]];

        foreach ($attributes as $attributeId => $values) {
            $append = [];

            foreach ($result as $product) {
                foreach ($values as $valueId) {
                    $append[] = $product + [$attributeId => $valueId];
                }
            }

            $result = $append;
        }

        return $result;
    }

    /**
     * Prevent duplicate variations
     */
    private function variationExists(Product $product, array $combination): bool
    {
        foreach ($product->variations as $variation) {

            $existing = $variation->attributes
                ->pluck('product_attribute_value_id', 'product_attribute_id')
                ->toArray();

            if ($existing == $combination) {
                return true;
            }
        }

        return false;
    }
}
