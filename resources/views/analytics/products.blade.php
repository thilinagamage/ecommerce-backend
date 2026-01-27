@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Product Analytics</h4>
            <p class="text-muted mb-0">Track product performance and inventory insights</p>
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
                            <p class="text-muted mb-1">Total Products</p>
                            <h3 class="mb-0">{{ number_format($totalProducts) }}</h3>
                            <small class="text-muted">In catalog</small>
                        </div>
                        <div class="widget-icon bg-primary text-white">
                            <i class="bx bx-package fs-1"></i>
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
                            <p class="text-muted mb-1">Active Products</p>
                            <h3 class="mb-0">{{ number_format($activeProducts) }}</h3>
                            <small class="text-success">Published & Available</small>
                        </div>
                        <div class="widget-icon bg-success text-white">
                            <i class="bx bx-check-circle fs-1"></i>
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
                            <p class="text-muted mb-1">Low Stock</p>
                            <h3 class="mb-0">{{ number_format($lowStockProducts) }}</h3>
                            <small class="text-warning">Needs attention</small>
                        </div>
                        <div class="widget-icon bg-warning text-white">
                            <i class="bx bx-error fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Out of Stock</p>
                            <h3 class="mb-0">{{ number_format($outOfStockProducts) }}</h3>
                            <small class="text-danger">Immediate action required</small>
                        </div>
                        <div class="widget-icon bg-danger text-white">
                            <i class="bx bx-x-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Best Sellers & Category Performance --}}
    <div class="row mb-4">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bx bx-trophy"></i> Best Sellers</h5>
                    <small class="text-muted">Top 10 by revenue</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Units Sold</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bestSellers as $index => $product)
                                    <tr>
                                        <td>
                                            @if($index == 0)
                                                <i class="bx bxs-medal text-warning fs-4"></i>
                                            @elseif($index == 1)
                                                <i class="bx bxs-medal text-secondary fs-4"></i>
                                            @elseif($index == 2)
                                                <i class="bx bxs-medal text-warning fs-4" style="opacity: 0.6;"></i>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $product->name }}</strong>
                                        </td>
                                        <td><small class="text-muted">{{ $product->sku }}</small></td>
                                        <td>
                                            <span class="badge bg-info">{{ number_format($product->units_sold) }}</span>
                                        </td>
                                        <td><strong class="text-success">${{ number_format($product->revenue, 2) }}</strong></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No sales data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-category"></i> Category Performance</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="280"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Worst Performers --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bx bx-trending-down"></i> Slow Moving Products</h5>
                    <span class="badge bg-warning">Needs attention</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Stock</th>
                                    <th>Units Sold</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($worstPerformers as $product)
                                    <tr>
                                        <td><strong>{{ $product->name }}</strong></td>
                                        <td><small class="text-muted">{{ $product->sku }}</small></td>
                                        <td>
                                            <span class="badge bg-{{ $product->stock_quantity > 10 ? 'success' : ($product->stock_quantity > 0 ? 'warning' : 'danger') }}">
                                                {{ $product->stock_quantity }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($product->units_sold) }}</td>
                                        <td>
                                            @if($product->units_sold == 0 && $product->stock_quantity > 0)
                                                <span class="badge bg-danger">No Sales</span>
                                            @else
                                                <span class="badge bg-warning">Low Sales</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('products.edit', $product->id) }}">
                                                        <i class="bx bx-edit"></i> Edit Product
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="#">
                                                        <i class="bx bx-discount"></i> Create Discount
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="#">
                                                        <i class="bx bx-trash"></i> Mark for Review
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">All products are performing well!</td>
                                    </tr>
                                @endforelse
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
    document.getElementById('period-select').addEventListener('change', function() {
        window.location.href = '{{ route("analytics.products") }}?period=' + this.value;
    });

    // Category Performance Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: @json($categoryPerformance->pluck('name')),
            datasets: [{
                label: 'Revenue',
                data: @json($categoryPerformance->pluck('revenue')),
                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Units Sold',
                data: @json($categoryPerformance->pluck('units_sold')),
                backgroundColor: 'rgba(255, 99, 132, 0.8)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true
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
