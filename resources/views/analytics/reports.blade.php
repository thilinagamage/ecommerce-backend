@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="mb-4">
        <h4 class="mb-1">Reports</h4>
        <p class="text-muted mb-0">Generate and export detailed reports</p>
    </div>

    {{-- Report Filters --}}
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bx bx-filter"></i> Report Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('analytics.reports') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Report Type</label>
                    <select name="type" class="form-select" required>
                        <option value="sales" {{ $type == 'sales' ? 'selected' : '' }}>Sales Report</option>
                        <option value="products" {{ $type == 'products' ? 'selected' : '' }}>Products Report</option>
                        <option value="customers" {{ $type == 'customers' ? 'selected' : '' }}>Customers Report</option>
                        <option value="inventory" {{ $type == 'inventory' ? 'selected' : '' }}>Inventory Report</option>
                        <option value="loyalty" {{ $type == 'loyalty' ? 'selected' : '' }}>Loyalty Report</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search"></i> Generate Report
                        </button>
                        <a href="{{ route('analytics.reports.export', ['type' => $type, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                           class="btn btn-success">
                            <i class="bx bx-download"></i> Export CSV
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Report Display --}}
    @if($type == 'sales')
        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Orders</h6>
                        <h3 class="mb-0">{{ number_format($data->count()) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Revenue</h6>
                        <h3 class="mb-0">${{ number_format($data->sum('grand_total') ?: $data->sum('total'), 2) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-start border-info border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Average Order Value</h6>
                        <h3 class="mb-0">${{ number_format($data->count() > 0 ? ($data->sum('grand_total') ?: $data->sum('total')) / $data->count() : 0, 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bx bx-receipt"></i> Sales Report</h5>
                <span class="badge bg-primary">{{ $data->count() }} orders</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order->id) }}" class="text-primary">
                                            #{{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ $order->user?->name ?? $order->customer_name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status == 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'danger' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="text-end"><strong>${{ number_format($order->grand_total ?? $order->total, 2) }}</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No orders found for this period</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($data->count() > 0)
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                    <td class="text-end"><strong>${{ number_format($data->sum('grand_total') ?: $data->sum('total'), 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($type == 'products')
        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Products Sold</h6>
                        <h3 class="mb-0">{{ number_format($data->count()) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Units</h6>
                        <h3 class="mb-0">{{ number_format($data->sum('total_sold')) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-start border-info border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Revenue</h6>
                        <h3 class="mb-0">${{ number_format($data->sum('total_revenue'), 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bx bx-package"></i> Products Report</h5>
                <span class="badge bg-primary">{{ $data->count() }} products</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th class="text-end">Units Sold</th>
                                <th class="text-end">Revenue</th>
                                <th class="text-end">Avg Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $product)
                                <tr>
                                    <td><strong>{{ $product->name }}</strong></td>
                                    <td><small class="text-muted">{{ $product->sku }}</small></td>
                                    <td class="text-end">{{ number_format($product->total_sold) }}</td>
                                    <td class="text-end"><strong>${{ number_format($product->total_revenue, 2) }}</strong></td>
                                    <td class="text-end">${{ number_format($product->total_revenue / max($product->total_sold, 1), 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No product sales for this period</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($data->count() > 0)
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($data->sum('total_sold')) }}</strong></td>
                                    <td class="text-end"><strong>${{ number_format($data->sum('total_revenue'), 2) }}</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($type == 'customers')
        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Customers</h6>
                        <h3 class="mb-0">{{ number_format($data->count()) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Orders</h6>
                        <h3 class="mb-0">{{ number_format($data->sum('orders_count')) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-start border-info border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Revenue</h6>
                        <h3 class="mb-0">${{ number_format($data->sum('total_spent'), 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bx bx-user"></i> Customers Report</h5>
                <span class="badge bg-primary">{{ $data->count() }} customers</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Email</th>
                                <th class="text-end">Orders</th>
                                <th class="text-end">Total Spent</th>
                                <th class="text-end">Avg Order Value</th>
                                <th>Last Order</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $customer)
                                <tr>
                                    <td><strong>{{ $customer->name }}</strong></td>
                                    <td>{{ $customer->email }}</td>
                                    <td class="text-end">{{ $customer->orders_count }}</td>
                                    <td class="text-end"><strong>${{ number_format($customer->total_spent, 2) }}</strong></td>
                                    <td class="text-end">${{ number_format($customer->total_spent / max($customer->orders_count, 1), 2) }}</td>
                                    <td>
                                        @if($customer->orders->first())
                                            {{ $customer->orders->first()->created_at->format('M d, Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No customer activity for this period</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($data->count() > 0)
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                    <td class="text-end"><strong>{{ number_format($data->sum('orders_count')) }}</strong></td>
                                    <td class="text-end"><strong>${{ number_format($data->sum('total_spent'), 2) }}</strong></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($type == 'inventory')
        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Products</h6>
                        <h3 class="mb-0">{{ number_format($data->count()) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Stock Units</h6>
                        <h3 class="mb-0">{{ number_format($data->sum('stock_quantity')) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-start border-info border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Inventory Value</h6>
                        <h3 class="mb-0">${{ number_format($data->sum(function($p) { return $p->stock_quantity * $p->regular_price; }), 2) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bx bx-box"></i> Inventory Report</h5>
                <span class="badge bg-primary">{{ $data->count() }} products</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th class="text-center">Stock</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Total Value</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $product)
                                <tr>
                                    <td><strong>{{ $product->name }}</strong></td>
                                    <td><small class="text-muted">{{ $product->sku }}</small></td>
                                    <td class="text-center">
                                        <span class="badge bg-{{ $product->stock_quantity > 10 ? 'success' : ($product->stock_quantity > 0 ? 'warning' : 'danger') }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                    <td class="text-end">${{ number_format($product->regular_price, 2) }}</td>
                                    <td class="text-end"><strong>${{ number_format($product->stock_quantity * $product->regular_price, 2) }}</strong></td>
                                    <td>
                                        @if($product->stock_quantity <= 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($product->stock_quantity < 10)
                                            <span class="badge bg-warning">Low Stock</span>
                                        @else
                                            <span class="badge bg-success">In Stock</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No products in inventory</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($data->count() > 0)
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="2" class="text-end"><strong>Totals:</strong></td>
                                    <td class="text-center"><strong>{{ number_format($data->sum('stock_quantity')) }}</strong></td>
                                    <td></td>
                                    <td class="text-end"><strong>${{ number_format($data->sum(function($p) { return $p->stock_quantity * $p->regular_price; }), 2) }}</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if($type == 'loyalty')
        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Transactions</h6>
                        <h3 class="mb-0">{{ number_format($data->count()) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Points Earned</h6>
                        <h3 class="mb-0 text-success">+{{ number_format($data->where('type', 'earned')->sum('points')) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-start border-warning border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Points Redeemed</h6>
                        <h3 class="mb-0 text-danger">{{ number_format($data->where('type', 'redeemed')->sum('points')) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bx bx-star"></i> Loyalty Program Report</h5>
                <span class="badge bg-primary">{{ $data->count() }} transactions</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th class="text-end">Points</th>
                                <th class="text-end">Balance After</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ $transaction->user->name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->type == 'earned' ? 'success' : 'warning' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="text-end {{ $transaction->points > 0 ? 'text-success' : 'text-danger' }}">
                                        <strong>{{ $transaction->points > 0 ? '+' : '' }}{{ number_format($transaction->points) }}</strong>
                                    </td>
                                    <td class="text-end">{{ number_format($transaction->balance_after) }}</td>
                                    <td><small>{{ $transaction->description }}</small></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No loyalty transactions for this period</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($data->count() > 0)
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="3" class="text-end"><strong>Net Points Change:</strong></td>
                                    <td class="text-end" colspan="3"><strong>{{ number_format($data->sum('points')) }}</strong></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    @endif
</main>
@endsection
