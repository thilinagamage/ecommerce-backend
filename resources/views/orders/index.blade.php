@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Orders</h4>
                    <a href="{{ route('orders.create') }}" class="btn btn-primary">
                        Create Order
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Statistics Cards --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white">Total Orders</h6>
                                <h2 class="mb-0">{{ number_format($stats['total']) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white">Pending</h6>
                                <h2 class="mb-0">{{ number_format($stats['pending']) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white">Processing</h6>
                                <h2 class="mb-0">{{ number_format($stats['processing']) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white">Total Revenue</h6>
                                <h2 class="mb-0">${{ number_format($stats['revenue'], 2) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Search orders..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="payment_status" class="form-select">
                            <option value="">Payment Status</option>
                            <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date"
                               name="date_from"
                               class="form-control"
                               value="{{ request('date_from') }}"
                               placeholder="From Date">
                    </div>
                    <div class="col-md-2">
                        <input type="date"
                               name="date_to"
                               class="form-control"
                               value="{{ request('date_to') }}"
                               placeholder="To Date">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                    </div>
                </form>

                {{-- Orders Table --}}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order->id) }}" class="text-primary fw-bold">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div>{{ $order->customer_name }}</div>
                                        <small class="text-muted">{{ $order->customer_email }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $order->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $order->items->count() }} items</span>
                                    </td>
                                    <td>
                                        <strong>${{ number_format($order->total, 2) }}</strong>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm status-select"
                                                data-order-id="{{ $order->id }}">
                                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->payment_status_badge }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('orders.show', $order->id) }}"
                                               class="btn btn-sm btn-info"
                                               title="View">
                                                View
                                            </a>
                                            <a href="{{ route('orders.invoice', $order->id) }}"
                                               class="btn btn-sm btn-secondary"
                                               title="Invoice"
                                               target="_blank">
                                                Invoice
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger delete-order"
                                                    data-order-id="{{ $order->id }}">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $orders->links() }}
                </div>

            </div>
        </div>
    </main>

    {{-- Delete Form (Hidden) --}}
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        // Quick status change
        document.querySelectorAll('.status-select').forEach(select => {
            // Store original value
            select.dataset.originalValue = select.value;

            select.addEventListener('change', function() {
                const orderId = this.dataset.orderId;
                const newStatus = this.value;
                const originalValue = this.dataset.originalValue;
                const selectElement = this;

                if (!confirm('Are you sure you want to change the order status to "' + newStatus + '"?')) {
                    // Revert to original value
                    this.value = originalValue;
                    return;
                }

                // Disable select while updating
                selectElement.disabled = true;

                fetch(`{{ url('orders') }}/${orderId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response:', response);

                    // Try to parse as JSON
                    return response.text().then(text => {
                        console.log('Response text:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            throw new Error('Invalid JSON response: ' + text.substring(0, 100));
                        }
                    });
                })
                .then(data => {
                    console.log('Data:', data);
                    if (data.success) {
                        // Update the original value
                        selectElement.dataset.originalValue = newStatus;
                        selectElement.disabled = false;

                        // Show success message
                        alert('Order status updated successfully!');

                        // Reload page to update UI
                        location.reload();
                    } else {
                        throw new Error(data.message || 'Update failed');
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    alert('Failed to update order status: ' + error.message);
                    // Revert to original value
                    selectElement.value = originalValue;
                    selectElement.disabled = false;
                });
            });
        });

        // Delete order
        document.querySelectorAll('.delete-order').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this order?')) {
                    const orderId = this.dataset.orderId;
                    const form = document.getElementById('deleteForm');
                    form.action = `/orders/${orderId}`;
                    form.submit();
                }
            });
        });
    </script>
@endsection
