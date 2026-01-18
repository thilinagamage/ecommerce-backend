<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Controllers\Controller;
use App\Models\Product\Order;
use App\Models\Product\OrderRefund;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
   public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'completed' => Order::where('status', 'delivered')->count(),
            'revenue' => Order::where('payment_status', 'paid')->sum('total'),
        ];

        return view('orders.index', compact('orders', 'stats'));
    }

    public function show($id)
    {
        $order = Order::with(['user', 'items.product', 'items.productVariation', 'statusHistory.user', 'notes.user', 'refunds'])
            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        $products = Product::with(['variations.attributes'])
            ->where('status', 'published')
            ->orderBy('name')
            ->get();

        // Prepare variations data for JavaScript
        $productVariationsData = $products->mapWithKeys(function($product) {
            return [$product->id => $product->variations->map(function($variation) {
                return [
                    'id' => $variation->id,
                    'label' => $variation->attributes->pluck('value')->implode(' / '),
                    'price' => (float) $variation->regular_price,
                    'sku' => $variation->sku,
                    'stock' => $variation->stock_quantity
                ];
            })];
        });

        return view('orders.create', compact('users', 'products', 'productVariationsData'));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'user_id' => 'nullable|exists:users,id',
        'customer_name' => 'required|string|max:255',
        'customer_email' => 'required|email|max:255',
        'customer_phone' => 'nullable|string|max:20',
        'billing_address_line1' => 'required|string|max:255',
        'billing_address_line2' => 'nullable|string|max:255',
        'billing_city' => 'required|string|max:100',
        'billing_state' => 'nullable|string|max:100',
        'billing_postal_code' => 'required|string|max:20',
        'billing_country' => 'required|string|max:100',
        // Remove 'same_as_billing' from validation - handle it manually
        'shipping_address_line1' => 'nullable|string|max:255',
        'shipping_address_line2' => 'nullable|string|max:255',
        'shipping_city' => 'nullable|string|max:100',
        'shipping_state' => 'nullable|string|max:100',
        'shipping_postal_code' => 'nullable|string|max:20',
        'shipping_country' => 'nullable|string|max:100',
        'payment_method' => 'nullable|string',
        'payment_status' => 'required|in:pending,paid,failed',
        'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.product_variation_id' => 'nullable|exists:product_variations,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric|min:0',
        'tax_amount' => 'nullable|numeric|min:0',
        'shipping_cost' => 'nullable|numeric|min:0',
        'discount_amount' => 'nullable|numeric|min:0',
        'shipping_method' => 'nullable|string|max:255',
    ]);

    // Handle same_as_billing checkbox manually
    $validated['same_as_billing'] = $request->has('same_as_billing') ? true : false;

    // If same as billing is false, validate shipping address is required
    if (!$validated['same_as_billing']) {
        $request->validate([
            'shipping_address_line1' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_postal_code' => 'required|string|max:20',
            'shipping_country' => 'required|string|max:100',
        ]);
    }

    $validated['order_number'] = Order::generateOrderNumber();

    // Calculate totals
    $subtotal = 0;
    foreach ($request->items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }

    $validated['subtotal'] = $subtotal;
    $validated['tax_amount'] = $request->input('tax_amount', 0);
    $validated['shipping_cost'] = $request->input('shipping_cost', 0);
    $validated['discount_amount'] = $request->input('discount_amount', 0);
    $validated['total'] = $subtotal + $validated['tax_amount'] + $validated['shipping_cost'] - $validated['discount_amount'];

    $order = Order::create($validated);

    // Create order items
    foreach ($request->items as $itemData) {
        $product = Product::find($itemData['product_id']);
        $variationId = $itemData['product_variation_id'] ?? null;
        $variationDetails = null;
        $sku = $product->sku;

        // If variation exists, get variation details
        if ($variationId) {
            $variation = ProductVariation::with('attributes')->find($variationId);
            if ($variation) {
                $variationDetails = $variation->attributes->pluck('value')->toArray();
                $sku = $variation->sku ?? $product->sku;
            }
        }

        $order->items()->create([
            'product_id' => $product->id,
            'product_variation_id' => $variationId,
            'product_name' => $product->name,
            'product_sku' => $sku,
            'variation_details' => $variationDetails,
            'quantity' => $itemData['quantity'],
            'price' => $itemData['price'],
            'subtotal' => $itemData['price'] * $itemData['quantity'],
            'total' => $itemData['price'] * $itemData['quantity'],
        ]);
    }

    // Log status history
    $order->statusHistory()->create([
        'new_status' => $validated['status'],
        'note' => 'Order created',
        'user_id' => auth()->id(),
    ]);

    return redirect()->route('orders.show', $order->id)
        ->with('success', 'Order created successfully');
}
    public function edit($id)
    {
        $order = Order::with(['items'])->findOrFail($id);
        $users = User::orderBy('name')->get();

        return view('orders.edit', compact('order', 'users'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'billing_address_line1' => 'required|string|max:255',
            'billing_address_line2' => 'nullable|string|max:255',
            'billing_city' => 'required|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_postal_code' => 'required|string|max:20',
            'billing_country' => 'required|string|max:100',
            'shipping_address_line1' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:20',
            'shipping_country' => 'nullable|string|max:100',
            'payment_method' => 'nullable|string',
            'tracking_number' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        $order->update($validated);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Order updated successfully');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully');
    }

    // Update order status
public function updateStatus(Request $request, $id)
{
    try {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,refunded,failed',
            'note' => 'nullable|string',
            'notify_customer' => 'boolean',
        ]);

        $order->updateStatus(
            $request->status,
            $request->note ?? null,
            $request->has('notify_customer')
        );

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    // Update payment status
    public function updatePaymentStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'payment_status' => 'required|in:pending,paid,failed,refunded,partially_refunded',
        ]);

        $order->update(['payment_status' => $request->payment_status]);

        return response()->json(['success' => true]);
    }

    // Add order note
    public function addNote(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'note' => 'required|string',
            'customer_visible' => 'boolean',
        ]);

        $order->addNote($request->note, $request->has('customer_visible'));

        return back()->with('success', 'Note added successfully');
    }

    // Process refund
    public function refund(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'amount' => 'required|numeric|min:0|max:' . $order->total,
            'reason' => 'required|string',
        ]);

        $refund = $order->refunds()->create([
            'refund_number' => OrderRefund::generateRefundNumber(),
            'amount' => $request->amount,
            'reason' => $request->reason,
            'status' => 'pending',
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Refund request created successfully');
    }

    // Print invoice
    public function invoice($id)
    {
        $order = Order::with(['items.product'])->findOrFail($id);
        return view('orders.invoice', compact('order'));
    }
}
