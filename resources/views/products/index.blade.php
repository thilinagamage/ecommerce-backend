@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Products</h4>


    {{-- Success Message --}}
    @if(session('success'))
         <div class="alert border-0 bg-light-success alert-dismissible fade show py-2">
              <div class="d-flex align-items-center">
                      <div class="fs-3 text-success"><i class="bi bi-check-circle-fill"></i>
              </div>
                <div class="ms-3">
                     <div class="text-success"> {{ session('success') }}</div>
                 </div>
            </div>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

            <div class="mb-3">
                <a href="{{ route('products.create') }}" class="btn btn-primary">Add New Product</a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Visibility</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $product)
                            @php
                                // Ensure variations is always a collection
                                $variations = $product->variations ?? collect();
                            @endphp
                            <tr>
                                <td>{{ $products->firstItem() + $index }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ ucfirst($product->type) }}</td>
                                <td>
                                    @if($product->type === 'simple')
                                        ${{ number_format($product->price, 2) }}
                                    @else
                                        Min: ${{ number_format($variations->min('price') ?? 0, 2) }}<br>
                                        Max: ${{ number_format($variations->max('price') ?? 0, 2) }}
                                    @endif
                                </td>
                                <td>
                                    @if($product->type === 'simple')
                                        {{ $variations->first()->stock ?? 0 }}
                                    @else
                                        {{ $variations->sum('stock') }}
                                    @endif
                                </td>
                                <td>{{ ucfirst($product->status) }}</td>
                                <td>{{ ucfirst($product->visibility) }}</td>
                                <td>
                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-warning">View</a>
                                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="d-flex justify-content-end">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
