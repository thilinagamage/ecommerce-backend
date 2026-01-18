@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-0">{{ $product->name }} - Variations</h4>
                        <small class="text-muted">SKU: {{ $product->sku ?? 'N/A' }}</small>
                    </div>
                    <div>
                        <a href="{{ route('products.inventory.adjust', $product->id) }}" class="btn btn-primary btn-sm">
                            Adjust Stock
                        </a>
                        <a href="{{ route('products.inventory.index') }}" class="btn btn-secondary btn-sm">
                            Back to Inventory
                        </a>
                    </div>
                </div>

                {{-- Product Info --}}
                <div class="row mb-4">
                    <div class="col-md-8">
                        @php
                            $totalStock = $product->variations->sum('stock_quantity');
                            $inStockCount = $product->variations->filter(fn($v) => $v->stock_quantity > 0)->count();
                            $lowStockCount = $product->variations->filter(fn($v) => $v->stock_quantity <= 10 && $v->stock_quantity > 0)->count();
                            $outOfStockCount = $product->variations->filter(fn($v) => $v->stock_quantity <= 0)->count();
                        @endphp

                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $totalStock }}</h3>
                                        <small>Total Stock</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $inStockCount }}</h3>
                                        <small>In Stock</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $lowStockCount }}</h3>
                                        <small>Low Stock</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h3>{{ $outOfStockCount }}</h3>
                                        <small>Out of Stock</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        @if($product->featuredImage)
                            <img src="{{ asset('storage/' . $product->featuredImage->path) }}"
                                 class="img-thumbnail w-100"
                                 style="max-height: 200px; object-fit: cover;">
                        @endif
                    </div>
                </div>

                {{-- Variations Table --}}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Image</th>
                                <th>Variation</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock Quantity</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->variations as $variation)
                                @php
                                    $comboLabel = $variation->attributes
                                        ->sortBy('product_attribute_id')
                                        ->map(fn($attr) => $attr->value)
                                        ->implode(' / ');
                                    $stock = $variation->stock_quantity ?? 0;
                                @endphp
                                <tr>
                                    <td>
                                        @if($variation->image)
                                            <img src="{{ asset('storage/' . $variation->image) }}"
                                                 class="img-thumbnail"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ $comboLabel }}</strong></td>
                                    <td>{{ $variation->sku ?? 'N/A' }}</td>
                                    <td>
                                        ${{ number_format($variation->regular_price, 2) }}
                                        @if($variation->sale_price)
                                            <br>
                                            <small class="text-danger">
                                                Sale: ${{ number_format($variation->sale_price, 2) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <h5 class="mb-0">{{ $stock }}</h5>
                                    </td>
                                    <td>
                                        @if($stock <= 0)
                                            <span class="badge bg-danger">Out of Stock</span>
                                        @elseif($stock <= 10)
                                            <span class="badge bg-warning">Low Stock</span>
                                        @else
                                            <span class="badge bg-success">In Stock</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('products.inventory.adjust', $product->id) }}?variation={{ $variation->id }}"
                                           class="btn btn-sm btn-primary">
                                            Adjust
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">Total Stock:</th>
                                <th colspan="3"><h5 class="mb-0">{{ $totalStock }} units</h5></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </main>
@endsection
