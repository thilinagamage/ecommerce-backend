@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Product Details</h4>
                    <div>
                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary btn-sm">
                            Edit Product
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-secondary btn-sm">
                            Back to List
                        </a>
                    </div>
                </div>

                {{-- BASIC INFO --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Product Name</label>
                            <p class="form-control-plaintext">{{ $product->name }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Slug</label>
                            <p class="form-control-plaintext">{{ $product->slug }}</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Product Type</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $product->product_type === 'simple' ? 'info' : 'warning' }}">
                                    {{ ucfirst($product->product_type) }}
                                </span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Status</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $product->status === 'published' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Visibility</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-primary">{{ ucfirst($product->visibility) }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        @if($product->featuredImage)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Featured Image</label>
                                <div>
                                    <img src="{{ asset('storage/' . $product->featuredImage->path) }}"
                                         class="img-thumbnail"
                                         style="max-width: 300px;">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- DESCRIPTION --}}
                @if($product->short_description)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Short Description</label>
                        <p class="form-control-plaintext">{{ $product->short_description }}</p>
                    </div>
                @endif

                @if($product->description)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <div class="form-control-plaintext">{!! nl2br(e($product->description)) !!}</div>
                    </div>
                @endif

                {{-- GALLERY IMAGES --}}
                @if($product->galleryImages->count() > 0)
                    <div class="mb-4">
                        <label class="form-label fw-bold">Gallery Images</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($product->galleryImages as $image)
                                <img src="{{ asset('storage/' . $image->path) }}"
                                     class="img-thumbnail"
                                     style="width: 120px; height: 120px; object-fit: cover;">
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- CATEGORIES --}}
                @if($product->categories->count() > 0)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Categories</label>
                        <p class="form-control-plaintext">
                            @foreach($product->categories as $category)
                                <span class="badge bg-secondary">{{ $category->name }}</span>
                            @endforeach
                        </p>
                    </div>
                @endif

                {{-- TAGS --}}
                @if($product->tags->count() > 0)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tags</label>
                        <p class="form-control-plaintext">
                            @foreach($product->tags as $tag)
                                <span class="badge bg-info">{{ $tag->name }}</span>
                            @endforeach
                        </p>
                    </div>
                @endif

                {{-- COLLECTION --}}
                @if($product->collection_id)
                    <div class="mb-3">
                        <label class="form-label fw-bold">Collection</label>
                        <p class="form-control-plaintext">
                            <span class="badge bg-primary">{{ $product->collection->name ?? 'N/A' }}</span>
                        </p>
                    </div>
                @endif

                <hr class="my-4">

                {{-- SIMPLE PRODUCT DETAILS --}}
                @if($product->product_type === 'simple')
                    <h5 class="mb-3">Product Details</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">SKU</label>
                                <p class="form-control-plaintext">{{ $product->sku ?? 'N/A' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Regular Price</label>
                                <p class="form-control-plaintext">
                                    ${{ number_format($product->regular_price, 2) }}
                                </p>
                            </div>

                            @if($product->sale_price)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Sale Price</label>
                                    <p class="form-control-plaintext text-danger">
                                        ${{ number_format($product->sale_price, 2) }}
                                    </p>
                                </div>
                            @endif

                            @if($product->sale_price_from || $product->sale_price_to)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Sale Period</label>
                                    <p class="form-control-plaintext">
                                        {{ $product->sale_price_from ? \Carbon\Carbon::parse($product->sale_price_from)->format('M d, Y') : 'N/A' }}
                                        -
                                        {{ $product->sale_price_to ? \Carbon\Carbon::parse($product->sale_price_to)->format('M d, Y') : 'N/A' }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Manage Stock</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $product->manage_stock ? 'success' : 'secondary' }}">
                                        {{ $product->manage_stock ? 'Yes' : 'No' }}
                                    </span>
                                </p>
                            </div>

                            @if($product->manage_stock)
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Stock Quantity</label>
                                    <p class="form-control-plaintext">{{ $product->stock_quantity }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Low Stock Threshold</label>
                                    <p class="form-control-plaintext">{{ $product->low_stock_threshold ?? 'N/A' }}</p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Allow Backorders</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $product->backorders ? 'success' : 'secondary' }}">
                                            {{ $product->backorders ? 'Yes' : 'No' }}
                                        </span>
                                    </p>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label fw-bold">Stock Status</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $product->stock_status === 'in_stock' ? 'success' : 'danger' }}">
                                        {{ ucfirst(str_replace('_', ' ', $product->stock_status)) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- VARIABLE PRODUCT VARIATIONS --}}
                @if($product->product_type === 'variable')
                    <h5 class="mb-3">Product Variations ({{ $product->variations->count() }})</h5>

                    @if($product->variations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Variation</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->variations as $variation)
                                        @php
                                            $comboLabel = $variation->attributes
                                                ->sortBy('product_attribute_id')
                                                ->map(fn($attr) => $attr->value)
                                                ->implode(' / ');
                                        @endphp
                                        <tr>
                                            <td>{{ $comboLabel ?: 'No attributes' }}</td>
                                            <td>{{ $variation->sku ?? 'N/A' }}</td>
                                            <td>
                                                <div>
                                                    <strong>${{ number_format($variation->regular_price, 2) }}</strong>
                                                </div>
                                                @if($variation->sale_price)
                                                    <div class="text-danger small">
                                                        Sale: ${{ number_format($variation->sale_price, 2) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                @if($variation->manage_stock)
                                                    <span class="badge bg-info">{{ $variation->stock_quantity }}</span>
                                                @else
                                                    <span class="badge bg-secondary">Not tracked</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $variation->stock_status === 'in_stock' ? 'success' : 'danger' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $variation->stock_status)) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($variation->image)
                                                    <img src="{{ asset('storage/' . $variation->image) }}"
                                                         class="img-thumbnail"
                                                         style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <span class="text-muted">No image</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No variations have been created for this product yet.
                        </div>
                    @endif
                @endif

                <hr class="my-4">

                {{-- METADATA --}}
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Created At</label>
                            <p class="form-control-plaintext">
                                {{ $product->created_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Last Updated</label>
                            <p class="form-control-plaintext">
                                {{ $product->updated_at->format('M d, Y h:i A') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="mt-4">
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary">
                        Edit Product
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">
                        Back to List
                    </a>
                    <form method="POST"
                          action="{{ route('products.destroy', $product->id) }}"
                          class="d-inline"
                          onsubmit="return confirm('Are you sure you want to delete this product?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Product</button>
                    </form>
                </div>

            </div>
        </div>
    </main>
@endsection
