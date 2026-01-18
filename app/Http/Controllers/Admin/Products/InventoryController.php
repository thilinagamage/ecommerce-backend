<?php

namespace App\Http\Controllers\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use App\Models\Product\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
  // List all inventory items
   public function index(Request $request)
    {
        $query = Product::with(['featuredImage', 'variations']);
            // Remove the manage_stock filter to show all products

        // Filter by stock status
        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'low_stock') {
                $query->whereRaw('stock_quantity <= low_stock_threshold')
                      ->where('stock_quantity', '>', 0);
            } elseif ($request->stock_status === 'out_of_stock') {
                $query->where('stock_quantity', '<=', 0);
            } elseif ($request->stock_status === 'in_stock') {
                $query->where('stock_quantity', '>', 0);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(20);

        // Get low stock count
        $lowStockCount = Product::whereRaw('stock_quantity <= low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->count();

        $outOfStockCount = Product::where('stock_quantity', '<=', 0)
            ->count();

        return view('products.inventory.index', compact('products', 'lowStockCount', 'outOfStockCount'));
    }

     public function adjustStock($id)
    {
        $product = Product::with('variations')->findOrFail($id);
        return view('products.inventory.adjust', compact('product'));
    }

    // Process stock adjustment
    public function updateStock(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:1',
            'variation_id' => 'nullable|exists:product_variations,id',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            StockMovement::createMovement(
                $id,
                $validated['variation_id'] ?? null,
                $validated['type'],
                $validated['quantity'],
                $validated['reference'] ?? null,
                $validated['notes'] ?? null
            );

            DB::commit();
            return redirect()->route('products.inventory.index')
                ->with('success', 'Stock updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update stock: ' . $e->getMessage());
        }
    }
       // View stock movements
    public function movements(Request $request)
    {
        $query = StockMovement::with(['product', 'variation', 'user']);

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $movements = $query->latest()->paginate(50);
        $products = Product::select('id', 'name')->get();

        return view('products.inventory.movements', compact('movements', 'products'));
    }

     // Low stock alerts
     public function lowStock()
    {
        $products = Product::where('manage_stock', true)
            ->whereRaw('stock_quantity <= low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->with('featuredImage')
            ->get();

        $variations = ProductVariation::where('manage_stock', true)
            ->whereRaw('stock_quantity <= low_stock_threshold')
            ->where('stock_quantity', '>', 0)
            ->with('product')
            ->get();

        return view('products.inventory.low-stock', compact('products', 'variations'));
    }


     // Out of stock
    public function outOfStock()
    {
        $products = Product::where('manage_stock', true)
            ->where('stock_quantity', '<=', 0)
            ->with('featuredImage')
            ->get();

        $variations = ProductVariation::where('manage_stock', true)
            ->where('stock_quantity', '<=', 0)
            ->with('product')
            ->get();

        return view('products.inventory.out-of-stock', compact('products', 'variations'));
    }

     public function viewVariations($id)
    {
        $product = Product::with(['variations.attributes', 'featuredImage'])->findOrFail($id);

        if ($product->product_type !== 'variable') {
            return redirect()->route('products.inventory.index')
                ->with('error', 'This is not a variable product');
        }

        return view('products.inventory.variations', compact('product'));
    }

}
