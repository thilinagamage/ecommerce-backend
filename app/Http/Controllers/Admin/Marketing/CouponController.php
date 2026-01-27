<?php

namespace App\Http\Controllers\Admin\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Marketing\Coupon;
use App\Models\Product\Category;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('discount_type')) {
            $query->where('discount_type', $request->discount_type);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $coupons = $query->latest()->paginate(20);

        // Statistics
        $stats = [
            'total' => Coupon::count(),
            'active' => Coupon::where('status', 'active')->count(),
            'expired' => Coupon::where('status', 'expired')->count(),
            'total_usage' => Coupon::sum('usage_count'),
        ];

        return view('marketing.coupons.index', compact('coupons', 'stats'));
    }

    public function create()
    {
        $products = Product::where('status', 'published')->orderBy('name')->get();
        $categories = Category::where('status', true)->orderBy('name')->get();

        return view('marketing.coupons.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed,free_shipping',
            'discount_value' => 'required|numeric|min:0',
            'minimum_spend' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|in:active,inactive,scheduled',
            'first_order_only' => 'boolean',
            'product_ids' => 'nullable|array',
            'category_ids' => 'nullable|array',
            'excluded_product_ids' => 'nullable|array',
            'excluded_category_ids' => 'nullable|array',
        ]);

        // Convert code to uppercase
        $validated['code'] = strtoupper($validated['code']);
        $validated['first_order_only'] = $request->has('first_order_only');

        Coupon::create($validated);

        return redirect()->route('marketing.coupons.index')
            ->with('success', 'Coupon created successfully');
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        $products = Product::where('status', 'published')->orderBy('name')->get();
        $categories = Category::where('status', true)->orderBy('name')->get();

        return view('marketing.coupons.edit', compact('coupon', 'products', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed,free_shipping',
            'discount_value' => 'required|numeric|min:0',
            'minimum_spend' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'status' => 'required|in:active,inactive,scheduled,expired',
            'first_order_only' => 'boolean',
            'product_ids' => 'nullable|array',
            'category_ids' => 'nullable|array',
            'excluded_product_ids' => 'nullable|array',
            'excluded_category_ids' => 'nullable|array',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['first_order_only'] = $request->has('first_order_only');

        $coupon->update($validated);

        return redirect()->route('marketing.coupons.index')
            ->with('success', 'Coupon updated successfully');
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return redirect()->route('marketing.coupons.index')
            ->with('success', 'Coupon deleted successfully');
    }

    // Generate random coupon code
    public function generateCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Coupon::where('code', $code)->exists());

        return response()->json(['code' => $code]);
    }

    // Validate coupon
    public function validate(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'cart_total' => 'nullable|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid coupon code'
            ]);
        }

        $validation = $coupon->isValid($request->user_id, $request->cart_total ?? 0);

        if ($validation['valid']) {
            $discount = $coupon->calculateDiscount($request->cart_total ?? 0);

            return response()->json([
                'valid' => true,
                'message' => 'Coupon applied successfully',
                'discount' => $discount,
                'discount_type' => $coupon->discount_type,
                'coupon' => $coupon
            ]);
        }

        return response()->json($validation);
    }
}
