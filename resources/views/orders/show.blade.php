@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        {{-- Order Header --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Order {{ $order->order_number }}</h4>
                        <p class="text-muted mb-0">
                            Placed on {{ $order->created_at->format('M d, Y h:i A') }}
                        </p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('orders.invoice', $order->id) }}"
                           class="btn btn-secondary"
                           target="_blank">
                            Print Invoice
                        </a>
                        <a href="{{ route('orders.edit', $order->id) }}"
                           class="btn btn-primary">
                            Edit Order
                        </a>
                        <a href="{{ route('orders.index') }}"
                           class="btn btn-outline-secondary">
                            Back to Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            {{-- Main Content --}}
            <div class="col-md-8">
                {{-- Order Items --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($item->product && $item->product->featuredImage)
                                                        <img src="{{ asset('storage/' . $item->product->featuredImage->path) }}"
                                                             class="img-thumbnail"
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <div>{{ $item->product_name }}</div>
                                                        @if($item->variation_details)
                                                            <small class="text-muted">
                                                                {{ collect($item->variation_details)->implode(' / ') }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $item->product_sku }}</td>
                                            <td>${{ number_format($item->price, 2) }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td><strong>${{ number_format($item->total, 2) }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end">Subtotal:</td>
                                        <td><strong>${{ number_format($order->subtotal, 2) }}</strong></td>
                                    </tr>
                                    @if($order->tax_amount > 0)
                                        <tr>
                                            <td colspan="4" class="text-end">Tax:</td>
                                            <td><strong>${{ number_format($order->tax_amount, 2) }}</strong></td>
                                        </tr>
                                    @endif
                                    @if($order->shipping_cost > 0)
                                        <tr>
                                            <td colspan="4" class="text-end">Shipping:</td>
                                            <td><strong>${{ number_format($order->shipping_cost, 2) }}</strong></td>
                                        </tr>
                                    @endif
                                    @if($order->discount_amount > 0)
                                        <tr>
                                            <td colspan="4" class="text-end">Discount:</td>
                                            <td><strong class="text-danger">-${{ number_format($order->discount_amount, 2) }}</strong></td>
                                        </tr>
                                    @endif>
                                    <tr class="table-active">
                                        <td colspan="4" class="text-end"><h5 class="mb-0">Total:</h5></td>
                                        <td><h5 class="mb-0">${{ number_format($order->total, 2) }}</h5></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Customer Information --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Billing Address</h6>
                                <p class="mb-0">
                                    {{ $order->customer_name }}<br>
                                    {{ $order->billing_address_line1 }}<br>
                                    @if($order->billing_address_line2)
                                        {{ $order->billing_address_line2 }}<br>
                                    @endif
                                    {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_postal_code }}<br>
                                    {{ $order->billing_country }}
                                </p>
                                <p class="mt-2 mb-0">
                                    <strong>Email:</strong> {{ $order->customer_email }}<br>
                                    @if($order->customer_phone)
                                        <strong>Phone:</strong> {{ $order->customer_phone }}
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>Shipping Address</h6>
                                @if($order->same_as_billing)
                                    <p class="text-muted">Same as billing address</p>
                                @else
                                    <p class="mb-0">
                                        {{ $order->customer_name }}<br>
                                        {{ $order->shipping_address_line1 }}<br>
                                        @if($order->shipping_address_line2)
                                            {{ $order->shipping_address_line2 }}<br>
                                        @endif
                                        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                                        {{ $order->shipping_country }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Order Notes --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Order Notes</h5>
                    </div>
                    <div class="card-body">
                        @forelse($order->notes as $note)
                            <div class="mb-3 p-3 {{ $note->customer_visible ? 'bg-light' : 'bg-white' }} border rounded">
                                <div class="d-flex justify-content-between">
                                    <small class="text-muted">
                                        {{ $note->user->name ?? 'System' }} - {{ $note->created_at->format('M d, Y h:i A') }}
                                    </small>
                                    @if($note->customer_visible)
                                        <span class="badge bg-info">Customer Visible</span>
                                    @endif
                                </div>
                                <p class="mb-0 mt-2">{{ $note->note }}</p>
                            </div>
                        @empty
                            <p class="text-muted">No notes yet.</p>
                        @endforelse

                        {{-- Add Note Form --}}
                        <form method="POST" action="{{ route('orders.add-note', $order->id) }}" class="mt-3">
                            @csrf
                            <div class="mb-2">
                                <textarea name="note"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Add a note..."
                                          required></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input type="checkbox"
                                           name="customer_visible"
                                           class="form-check-input"
                                           id="customer_visible">
                                    <label class="form-check-label" for="customer_visible">
                                        Visible to customer
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">Add Note</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Status History --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Status History</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @forelse($order->statusHistory as $history)
                                <div class="mb-3">
                                    <div class="d-flex align-items-start">
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-{{ $order->status_badge }}">
                                                {{ ucfirst($history->new_status) }}
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <p class="mb-1">
                                                <strong>{{ ucfirst($history->new_status) }}</strong>
                                                @if($history->old_status)
                                                    (from {{ ucfirst($history->old_status) }})
                                                @endif
                                            </p>
                                            @if($history->note)
                                                <p class="mb-1 text-muted">{{ $history->note }}</p>
                                            @endif
                                            <small class="text-muted">
                                                {{ $history->created_at->format('M d, Y h:i A') }}
                                                by {{ $history->user->name ?? 'System' }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-muted">No history available.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-md-4">
                {{-- Order Status --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Order Status</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('orders.update-status', $order->id) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Note (Optional)</label>
                                <textarea name="note" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox"
                                       name="notify_customer"
                                       class="form-check-input"
                                       id="notify_customer">
                                <label class="form-check-label" for="notify_customer">
                                    Notify customer
                                </label>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Status</button>
                        </form>
                    </div>
                </div>

                {{-- Payment Information --}}
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Payment Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Payment Status:</strong><br>
                            <span class="badge bg-{{ $order->payment_status_badge }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                        @if($order->payment_method)
                            <div class="mb-2">
                                <strong>Payment Method:</strong><br>
                                {{ ucfirst($order->payment_method) }}
                            </div>
                        @endif
                        @if($order->transaction_id)
                            <div class="mb-2">
                                <strong>Transaction ID:</strong><br>
                                {{ $order->transaction_id }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Shipping Information --}}
                @if($order->shipping_method || $order->tracking_number)
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Shipping Information</h5>
                        </div>
                        <div class="card-body">
                            @if($order->shipping_method)
                                <div class="mb-2">
                                    <strong>Shipping Method:</strong><br>
                                    {{ $order->shipping_method }}
                                </div>
                            @endif
                            @if($order->tracking_number)
                                <div class="mb-2">
                                    <strong>Tracking Number:</strong><br>
                                    {{ $order->tracking_number }}
                                </div>
                            @endif
                            @if($order->shipped_at)
                                <div class="mb-2">
                                    <strong>Shipped At:</strong><br>
                                    {{ $order->shipped_at->format('M d, Y h:i A') }}
                                </div>
                            @endif
                            @if($order->delivered_at)
                                <div class="mb-2">
                                    <strong>Delivered At:</strong><br>
                                    {{ $order->delivered_at->format('M d, Y h:i A') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Refunds --}}
                @if($order->refunds->count() > 0 || $order->canBeRefunded())
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Refunds</h5>
                        </div>
                        <div class="card-body">
                            @forelse($order->refunds as $refund)
                                <div class="mb-2 p-2 bg-light rounded">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $refund->refund_number }}</strong>
                                        <span class="badge bg-{{ $refund->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($refund->status) }}
                                        </span>
                                    </div>
                                    <div>Amount: ${{ number_format($refund->amount, 2) }}</div>
                                    @if($refund->reason)
                                        <small class="text-muted">{{ $refund->reason }}</small>
                                    @endif
                                </div>
                            @empty
                                <p class="text-muted mb-2">No refunds yet.</p>
                            @endforelse

                            @if($order->canBeRefunded())
                                <button type="button" class="btn btn-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#refundModal">
                                    Process Refund
                                </button>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-primary btn-sm">
                                Edit Order
                            </a>
                            <a href="{{ route('orders.invoice', $order->id) }}" class="btn btn-secondary btn-sm" target="_blank">
                                Print Invoice
                            </a>
                            @if($order->canBeCancelled())
                                <form method="POST" action="{{ route('orders.update-status', $order->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Cancel this order?')">
                                        Cancel Order
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Refund Modal --}}
    <div class="modal fade" id="refundModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('orders.refund', $order->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Process Refund</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Refund Amount</label>
                            <input type="number"
                                   name="amount"
                                   class="form-control"
                                   step="0.01"
                                   max="{{ $order->total }}"
                                   value="{{ $order->total }}"
                                   required>
                            <small class="text-muted">Order total: ${{ number_format($order->total, 2) }}</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason</label>
                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Process Refund</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
