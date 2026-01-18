@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Inventory Management</h4>
                    <a href="{{ route('products.inventory.movements') }}" class="btn btn-secondary">
                        View Stock Movements
                    </a>
                </div>

                {{-- Alerts --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Stock Status Cards --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Low Stock Items</h5>
                                <h2>{{ $lowStockCount }}</h2>
                                <a href="{{ route('products.inventory.low-stock') }}" class="btn btn-sm btn-light">View All</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h5 class="card-title">Out of Stock</h5>
                                <h2>{{ $outOfStockCount }}</h2>
                                <a href="{{ route('products.inventory.out-of-stock') }}" class="btn btn-sm btn-light">View All</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Products</h5>
                                <h2>{{ $products->total() }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <form method="GET" action="{{ route('products.inventory.index') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Search by name or SKU..."
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="stock_status" class="form-select">
                                <option value="">All Status</option>
                                <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('products.inventory.index') }}" class="btn btn-secondary w-100">Clear</a>
                        </div>
                    </div>
                </form>

                {{-- Inventory Table --}}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th>SKU</th>
                                <th>Type</th>
                                <th>Stock</th>
                                <th>Low Stock Alert</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->featuredImage)
                                                <img src="{{ asset('storage/' . $product->featuredImage->path) }}"
                                                     class="img-thumbnail me-2"
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <strong>{{ $product->name }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $product->sku ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $product->product_type === 'simple' ? 'info' : 'warning' }}">
                                            {{ ucfirst($product->product_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($product->product_type === 'simple')
                                            @if($product->manage_stock)
                                                <strong>{{ $product->stock_quantity ?? 0 }}</strong>
                                            @else
                                                <span class="badge bg-secondary">Not tracked</span>
                                            @endif
                                        @else
                                            @php
                                                $totalStock = $product->variations->sum('stock_quantity');
                                                $variationCount = $product->variations->count();
                                            @endphp
                                            <div>
                                                <strong>{{ $totalStock }}</strong> units
                                                <br>
                                                <small class="text-muted">({{ $variationCount }} variations)</small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->product_type === 'simple')
                                            {{ $product->low_stock_threshold ?? 'Not set' }}
                                        @else
                                            <span class="text-muted">Per variation</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->product_type === 'simple')
                                            @if(!$product->manage_stock)
                                                <span class="badge bg-secondary">Not Managed</span>
                                            @else
                                                @php
                                                    $stockQty = $product->stock_quantity ?? 0;
                                                    $threshold = $product->low_stock_threshold ?? 0;
                                                @endphp
                                                @if($stockQty <= 0)
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                @elseif($stockQty <= $threshold)
                                                    <span class="badge bg-warning">Low Stock</span>
                                                @else
                                                    <span class="badge bg-success">In Stock</span>
                                                @endif
                                            @endif
                                        @else
                                            @php
                                                $totalStock = $product->variations->sum('stock_quantity');
                                                $lowStockVariations = $product->variations->filter(function($v) {
                                                    return $v->stock_quantity <= ($v->low_stock_threshold ?? 0) && $v->stock_quantity > 0;
                                                })->count();
                                                $outOfStockVariations = $product->variations->filter(function($v) {
                                                    return $v->stock_quantity <= 0;
                                                })->count();
                                            @endphp
                                            @if($outOfStockVariations > 0)
                                                <span class="badge bg-danger">{{ $outOfStockVariations }} Out of Stock</span>
                                            @endif
                                            @if($lowStockVariations > 0)
                                                <span class="badge bg-warning">{{ $lowStockVariations }} Low Stock</span>
                                            @endif
                                            @if($outOfStockVariations == 0 && $lowStockVariations == 0)
                                                <span class="badge bg-success">All In Stock</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('products.inventory.adjust', $product->id) }}"
                                           class="btn btn-sm btn-primary">
                                            Adjust Stock
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No products found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $products->links() }}
                </div>

            </div>
        </div>
    </main>
@endsection
