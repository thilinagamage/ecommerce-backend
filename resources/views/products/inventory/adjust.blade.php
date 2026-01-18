@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title mb-4">Adjust Stock - {{ $product->name }}</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('products.inventory.update', $product->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Product Info --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Product Name</label>
                                <p class="form-control-plaintext">{{ $product->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">SKU</label>
                                <p class="form-control-plaintext">{{ $product->sku ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            @if($product->featuredImage)
                                <img src="{{ asset('storage/' . $product->featuredImage->path) }}"
                                     class="img-thumbnail"
                                     style="max-width: 200px;">
                            @endif
                        </div>
                    </div>

                    {{-- Current Stock --}}
                    @if($product->product_type === 'simple')
                        <div class="alert alert-info">
                            <strong>Current Stock:</strong> {{ $product->stock_quantity ?? 0 }} units
                        </div>
                    @else
                        <div class="alert alert-info">
                            <strong>Variable Product</strong> - Select a variation below to adjust its stock
                        </div>

                        {{-- Show all variations with current stock --}}
                        <div class="card mb-4">
                            <div class="card-header">
                                <strong>Current Variation Stock Levels</strong>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Variation</th>
                                                <th>SKU</th>
                                                <th>Current Stock</th>
                                                <th>Status</th>
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
                                                    <td>{{ $comboLabel }}</td>
                                                    <td>{{ $variation->sku ?? 'N/A' }}</td>
                                                    <td><strong>{{ $stock }}</strong></td>
                                                    <td>
                                                        @if($stock <= 0)
                                                            <span class="badge bg-danger">Out of Stock</span>
                                                        @elseif($stock <= 10)
                                                            <span class="badge bg-warning">Low Stock</span>
                                                        @else
                                                            <span class="badge bg-success">In Stock</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- For Variable Products --}}
                    @if($product->product_type === 'variable')
                        <div class="mb-3">
                            <label class="form-label">Select Variation <span class="text-danger">*</span></label>
                            <select name="variation_id" class="form-select" required>
                                <option value="">-- Select Variation --</option>
                                @foreach($product->variations as $variation)
                                    @php
                                        $comboLabel = $variation->attributes
                                            ->sortBy('product_attribute_id')
                                            ->map(fn($attr) => $attr->value)
                                            ->implode(' / ');
                                    @endphp
                                    <option value="{{ $variation->id }}">
                                        {{ $comboLabel }} - Current Stock: {{ $variation->stock_quantity ?? 0 }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Adjustment Type --}}
                    <div class="mb-3">
                        <label class="form-label">Adjustment Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required>
                            <option value="">-- Select Type --</option>
                            <option value="in">Stock In (Add)</option>
                            <option value="out">Stock Out (Remove)</option>
                            <option value="adjustment">Adjustment (Correction)</option>
                        </select>
                        <small class="text-muted">
                            <strong>Stock In:</strong> Receiving new inventory<br>
                            <strong>Stock Out:</strong> Removing damaged/lost items<br>
                            <strong>Adjustment:</strong> Manual correction
                        </small>
                    </div>

                    {{-- Quantity --}}
                    <div class="mb-3">
                        <label class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number"
                               name="quantity"
                               class="form-control"
                               min="1"
                               required>
                    </div>

                    {{-- Reference --}}
                    <div class="mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text"
                               name="reference"
                               class="form-control"
                               placeholder="e.g., PO-12345, INV-789">
                        <small class="text-muted">Purchase order, invoice, or batch number</small>
                    </div>

                    {{-- Notes --}}
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Additional notes about this stock movement..."></textarea>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Stock</button>
                        <a href="{{ route('products.inventory.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>

                </form>

            </div>
        </div>
    </main>
@endsection
