@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Stock Movement History</h4>
                    <a href="{{ route('products.inventory.index') }}" class="btn btn-secondary">
                        Back to Inventory
                    </a>
                </div>

                {{-- Filters --}}
                <form method="GET" action="{{ route('products.inventory.movements') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select name="product_id" class="form-select">
                                <option value="">All Products</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                            {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Stock In</option>
                                <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Stock Out</option>
                                <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                                <option value="sold" {{ request('type') === 'sold' ? 'selected' : '' }}>Sold</option>
                                <option value="return" {{ request('type') === 'return' ? 'selected' : '' }}>Return</option>
                                <option value="damaged" {{ request('type') === 'damaged' ? 'selected' : '' }}>Damaged</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('products.inventory.movements') }}" class="btn btn-secondary w-100">Clear</a>
                        </div>
                    </div>
                </form>

                {{-- Movements Table --}}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Variation</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Before</th>
                                <th>After</th>
                                <th>Reference</th>
                                <th>User</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $movement)
                                <tr>
                                    <td>{{ $movement->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('products.show', $movement->product_id) }}">
                                            {{ $movement->product->name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($movement->variation)
                                            @php
                                                $varLabel = $movement->variation->attributes
                                                    ->map(fn($a) => $a->value)
                                                    ->implode(' / ');
                                            @endphp
                                            {{ $varLabel }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badges = [
                                                'in' => 'success',
                                                'out' => 'danger',
                                                'adjustment' => 'warning',
                                                'sold' => 'info',
                                                'return' => 'primary',
                                                'damaged' => 'dark'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $badges[$movement->type] ?? 'secondary' }}">
                                            {{ ucfirst($movement->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(in_array($movement->type, ['in', 'return', 'adjustment']))
                                            <span class="text-success">+{{ $movement->quantity }}</span>
                                        @else
                                            <span class="text-danger">-{{ $movement->quantity }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $movement->quantity_before }}</td>
                                    <td><strong>{{ $movement->quantity_after }}</strong></td>
                                    <td>{{ $movement->reference ?? '-' }}</td>
                                    <td>{{ $movement->user->name ?? 'System' }}</td>
                                    <td>{{ Str::limit($movement->notes ?? '-', 30) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No stock movements found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $movements->appends(request()->query())->links() }}
                </div>

            </div>
        </div>
    </main>
@endsection
