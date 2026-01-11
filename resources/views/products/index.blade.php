@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0">Products List</h5>
                <a href="{{ route('products.create') }}" class="btn btn-outline-primary">
                    <i class="bi bi-plus-circle"></i> Add New Product
                </a>
            </div>

            {{-- Search / Filters --}}
            <div class="mb-3">
                <form method="GET" action="{{ route('products.index') }}" class="row g-2">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or SKU">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="simple" {{ request('type') == 'simple' ? 'selected' : '' }}>Simple</option>
                            <option value="variable" {{ request('type') == 'variable' ? 'selected' : '' }}>Variable</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-dark w-100"><i class="bi bi-search"></i> Filter</button>
                    </div>
                </form>
            </div>

            {{-- Products Table --}}
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>SEO</th>
                            <th>Created At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>{{ $loop->iteration + ($products->currentPage()-1) * $products->perPage() }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ ucfirst($product->type) }}</td>
                                <td>
                                    @if($product->type === 'simple')
                                        ${{ number_format($product->price, 2) }}
                                    @else
                                        Min: ${{ number_format($product->variations->min('price') ?? 0, 2) }}<br>
                                        Max: ${{ number_format($product->variations->max('price') ?? 0, 2) }}
                                    @endif
                                </td>
                                <td>
                                    @if($product->type === 'simple')
                                        {{ $product->variations->first()->stock ?? 0 }}
                                    @else
                                        {{ $product->variations->sum('stock') }}
                                    @endif
                                </td>
                                <td>
                                    @if($product->status === 'published')
                                        <span class="badge bg-success">Published</span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </td>
                                <td>
                                    <small>Meta: {{ Str::limit($product->meta_title, 30) }}</small><br>
                                    <small>Slug: {{ $product->slug }}</small>
                                </td>
                                <td>{{ $product->created_at->format('d M, Y') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="d-flex justify-content-end mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</main>
@endsection
