@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Sales Analytics</h4>
            <p class="text-muted mb-0">Monitor your sales performance and revenue</p>
        </div>
        <div>
            <select class="form-select" id="period-select">
                <option value="7" {{ $period == 7 ? 'selected' : '' }}>Last 7 days</option>
                <option value="30" {{ $period == 30 ? 'selected' : '' }}>Last 30 days</option>
                <option value="90" {{ $period == 90 ? 'selected' : '' }}>Last 90 days</option>
                <option value="365" {{ $period == 365 ? 'selected' : '' }}>Last Year</option>
            </select>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Revenue</p>
                            <h3 class="mb-0">${{ number_format($totalRevenue, 2) }}</h3>
                            <small class="text-{{ $revenueGrowth >= 0 ? 'success' : 'danger' }}">
                                <i class="bx bx-{{ $revenueGrowth >= 0 ? 'up' : 'down' }}-arrow-alt"></i>
                                {{ number_format(abs($revenueGrowth), 1) }}% vs previous period
                            </small>
                        </div>
                        <div class="widget-icon bg-primary text-white">
                            <i class="bx bx-dollar-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Orders</p>
                            <h3 class="mb-0">{{ number_format($totalOrders) }}</h3>
                            <small class="text-{{ $ordersGrowth >= 0 ? 'success' : 'danger' }}">
                                <i class="bx bx-{{ $ordersGrowth >= 0 ? 'up' : 'down' }}-arrow-alt"></i>
                                {{ number_format(abs($ordersGrowth), 1) }}% vs previous period
                            </small>
                        </div>
                        <div class="widget-icon bg-success text-white">
                            <i class="bx bx-cart fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Avg Order Value</p>
                            <h3 class="mb-0">${{ number_format($avgOrderValue, 2) }}</h3>
                            <small class="text-muted">Per transaction</small>
                        </div>
                        <div class="widget-icon bg-info text-white">
                            <i class="bx bx-line-chart fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Conversion Rate</p>
                            <h3 class="mb-0">{{ number_format(($totalOrders / max($totalOrders, 1)) * 100, 1) }}%</h3>
                            <small class="text-muted">Orders to visits</small>
                        </div>
                        <div class="widget-icon bg-warning text-white">
                            <i class="bx bx-trending-up fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales Chart --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-line-chart"></i> Sales Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        {{-- Top Products --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bx bx-trophy"></i> Top Selling Products</h5>
                    <small class="text-muted">{{ $period }} days</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>Units</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProducts as $index => $product)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $product->name }}</strong>
                                            <br><small class="text-muted">{{ $product->sku }}</small>
                                        </td>
                                        <td>{{ number_format($product->total_quantity) }}</td>
                                        <td><strong>${{ number_format($product->total_revenue, 2) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sales by Status --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-pie-chart-alt"></i> Orders by Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Methods --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-credit-card"></i> Payment Methods</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Payment Method</th>
                                    <th>Orders</th>
                                    <th>Total Amount</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($paymentMethods as $method)
                                    <tr>
                                        <td>
                                            <strong>{{ ucfirst(str_replace('_', ' ', $method->payment_method ?? 'Unknown')) }}</strong>
                                        </td>
                                        <td>{{ number_format($method->count) }}</td>
                                        <td>${{ number_format($method->total, 2) }}</td>
                                        <td>
                                            <div class="progress" style="height: 25px;">
                                                <div class="progress-bar" role="progressbar"
                                                     style="width: {{ ($method->total / max($totalRevenue, 1)) * 100 }}%">
                                                    {{ number_format(($method->total / max($totalRevenue, 1)) * 100, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Period selector
    document.getElementById('period-select').addEventListener('change', function() {
        window.location.href = '{{ route("analytics.sales") }}?period=' + this.value;
    });

    // Sales Trend Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($dailySales->pluck('date')),
            datasets: [{
                label: 'Revenue',
                data: @json($dailySales->pluck('revenue')),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                yAxisID: 'y',
                tension: 0.4
            }, {
                label: 'Orders',
                data: @json($dailySales->pluck('orders')),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                yAxisID: 'y1',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Revenue ($)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Orders'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                },
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json($salesByStatus->pluck('status')),
            datasets: [{
                data: @json($salesByStatus->pluck('count')),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
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
</style>
@endpush
