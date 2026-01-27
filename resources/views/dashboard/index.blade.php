@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="mb-4">
        <h4 class="mb-1">Dashboard Overview</h4>
        <p class="text-muted mb-0">Welcome back! Here's what's happening with your store today.</p>
    </div>

    {{-- Quick Stats --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Today's Sales</p>
                            <h3 class="mb-0">${{ number_format($todaySales, 2) }}</h3>
                            <small class="text-{{ $salesGrowth >= 0 ? 'success' : 'danger' }}">
                                <i class="bx bx-{{ $salesGrowth >= 0 ? 'up' : 'down' }}-arrow-alt"></i>
                                {{ number_format(abs($salesGrowth), 1) }}% from yesterday
                            </small>
                        </div>
                        <div class="widget-icon bg-primary text-white">
                            <i class="bx bx-dollar-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Today's Orders</p>
                            <h3 class="mb-0">{{ number_format($todayOrders) }}</h3>
                            <small class="text-{{ $ordersGrowth >= 0 ? 'success' : 'danger' }}">
                                <i class="bx bx-{{ $ordersGrowth >= 0 ? 'up' : 'down' }}-arrow-alt"></i>
                                {{ number_format(abs($ordersGrowth), 1) }}% from yesterday
                            </small>
                        </div>
                        <div class="widget-icon bg-success text-white">
                            <i class="bx bx-cart fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Customers</p>
                            <h3 class="mb-0">{{ number_format($totalCustomers) }}</h3>
                            <small class="text-success">
                                <i class="bx bx-user-plus"></i>
                                {{ $newCustomersToday }} new today
                            </small>
                        </div>
                        <div class="widget-icon bg-info text-white">
                            <i class="bx bx-user fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Products</p>
                            <h3 class="mb-0">{{ number_format($totalProducts) }}</h3>
                            <small class="text-danger">
                                <i class="bx bx-error-circle"></i>
                                {{ $lowStockProducts }} low stock
                            </small>
                        </div>
                        <div class="widget-icon bg-warning text-white">
                            <i class="bx bx-package fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Revenue Chart and Recent Orders --}}
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bx bx-line-chart"></i> Revenue Overview</h5>
                    <select class="form-select form-select-sm" style="width: auto;" id="revenue-period">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 90 days</option>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-pie-chart-alt"></i> Sales by Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                    <div class="mt-3">
                        @foreach($ordersByStatus as $status)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ ucfirst($status->status) }}</span>
                                <strong>{{ $status->count }} orders</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Orders and Top Products --}}
    <div class="row mb-4">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bx bx-receipt"></i> Recent Orders</h5>
                    <a href="{{ route('orders.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('orders.show', $order->id) }}" class="text-primary">
                                                #{{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td>{{ $order->user?->name ?? $order->customer_name }}</td>
                                        <td>{{ $order->created_at->format('M d, H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{
                                                $order->status == 'completed' ? 'success' :
                                                ($order->status == 'processing' ? 'info' :
                                                ($order->status == 'pending' ? 'warning' : 'danger'))
                                            }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="text-end">${{ number_format($order->grand_total ?? $order->total, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No recent orders</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bx bx-trophy"></i> Top Selling Products</h5>
                    <a href="{{ route('analytics.products') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end">Sold</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $product)
                                    <tr>
                                        <td>
                                            <strong>{{ Str::limit($product->name, 30) }}</strong>
                                            <br><small class="text-muted">{{ $product->sku }}</small>
                                        </td>
                                        <td class="text-end">{{ number_format($product->total_quantity) }}</td>
                                        <td class="text-end"><strong>${{ number_format($product->total_revenue, 2) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">No sales data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts and Low Stock --}}
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-error-circle text-warning"></i> Low Stock Alert</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th class="text-end">Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lowStockItems as $item)
                                    <tr>
                                        <td><strong>{{ Str::limit($item->name, 25) }}</strong></td>
                                        <td><small class="text-muted">{{ $item->sku }}</small></td>
                                        <td class="text-end">
                                            <span class="badge bg-{{ $item->stock_quantity == 0 ? 'danger' : 'warning' }}">
                                                {{ $item->stock_quantity }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('products.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                                Restock
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">All products are in stock</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-star text-warning"></i> Recent Reviews</h5>
                </div>
                <div class="card-body">
                    @forelse($recentReviews as $review)
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $review->user?->name ?? 'Anonymous' }}</strong>
                                <div>
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bx bxs-star text-{{ $i <= $review->rating ? 'warning' : 'muted' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <small class="text-muted">{{ $review->product->name }}</small>
                            <p class="mb-1 mt-2">{{ Str::limit($review->comment, 100) }}</p>
                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                        </div>
                    @empty
                        <p class="text-center text-muted py-4">No recent reviews</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: @json($revenueData->pluck('date')),
            datasets: [{
                label: 'Revenue',
                data: @json($revenueData->pluck('revenue')),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($ordersByStatus->pluck('status')),
            datasets: [{
                data: @json($ordersByStatus->pluck('count')),
                backgroundColor: [
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Period selector
    document.getElementById('revenue-period').addEventListener('change', function() {
        window.location.href = '{{ route("admin.dashboard.index") }}?period=' + this.value;
    });
</script>
@endpush

@push('styles')
<style>
    .widget-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        opacity: 0.3;
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    }
</style>
@endpush
