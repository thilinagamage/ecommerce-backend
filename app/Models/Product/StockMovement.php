<?php

namespace App\Models\Product;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
  protected $fillable = [
        'product_id',
        'product_variation_id',
        'type',
        'quantity',
        'quantity_before',
        'quantity_after',
        'reference',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Helper method to create stock movement
    public static function createMovement($productId, $variationId, $type, $quantity, $reference = null, $notes = null)
    {
        if ($variationId) {
            $variation = ProductVariation::find($variationId);
            $quantityBefore = $variation->stock_quantity ?? 0;

            // Calculate new quantity based on type
            $quantityAfter = match($type) {
                'in', 'return', 'adjustment' => $quantityBefore + abs($quantity),
                'out', 'sold', 'damaged' => $quantityBefore - abs($quantity),
                default => $quantityBefore
            };

            // Update variation stock
            $variation->update(['stock_quantity' => $quantityAfter]);

            return self::create([
                'product_id' => $productId,
                'product_variation_id' => $variationId,
                'type' => $type,
                'quantity' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'reference' => $reference,
                'notes' => $notes,
                'user_id' => auth()->id(),
            ]);
        } else {
            $product = Product::find($productId);
            $quantityBefore = $product->stock_quantity ?? 0;

            $quantityAfter = match($type) {
                'in', 'return', 'adjustment' => $quantityBefore + abs($quantity),
                'out', 'sold', 'damaged' => $quantityBefore - abs($quantity),
                default => $quantityBefore
            };

            // Update product stock
            $product->update(['stock_quantity' => $quantityAfter]);

            return self::create([
                'product_id' => $productId,
                'product_variation_id' => null,
                'type' => $type,
                'quantity' => $quantity,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'reference' => $reference,
                'notes' => $notes,
                'user_id' => auth()->id(),
            ]);
        }
    }
}
