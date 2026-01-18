@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Product Reviews</h4>
                    <a href="{{ route('products.reviews.create') }}" class="btn btn-primary">
                        Add Review
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Statistics Cards --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Reviews</h5>
                                <h2>{{ $stats['total'] }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Pending</h5>
                                <h2>{{ $stats['pending'] }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Approved</h5>
                                <h2>{{ $stats['approved'] }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Avg Rating</h5>
                                <h2>{{ $stats['average_rating'] }} <small>★</small></h2>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Search reviews..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="spam" {{ request('status') === 'spam' ? 'selected' : '' }}>Spam</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="rating" class="form-select">
                            <option value="">All Ratings</option>
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                    {{ $i }} ★
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="product_id" class="form-select">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                    </div>
                </form>

                {{-- Bulk Actions --}}
                <form method="POST" action="{{ route('products.reviews.bulk-action') }}" id="bulkActionForm">
                    @csrf
                    <div class="d-flex gap-2 mb-3">
                        <select name="action" class="form-select" style="width: auto;">
                            <option value="">Bulk Actions</option>
                            <option value="approve">Approve</option>
                            <option value="reject">Reject</option>
                            <option value="spam">Mark as Spam</option>
                            <option value="delete">Delete</option>
                        </select>
                        <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure?')">Apply</button>
                    </div>

                    {{-- Reviews Table --}}
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>Product</th>
                                    <th>Reviewer</th>
                                    <th>Rating</th>
                                    <th>Review</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $review)
                                    <tr>
                                        <td>
                                            <input type="checkbox"
                                                   name="review_ids[]"
                                                   value="{{ $review->id }}"
                                                   class="review-checkbox">
                                        </td>
                                        <td>
                                            <a href="{{ route('products.show', $review->product_id) }}" target="_blank">
                                                {{ $review->product->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <div>{{ $review->reviewer_name }}</div>
                                            <small class="text-muted">{{ $review->reviewer_email }}</small>
                                            @if($review->verified_purchase)
                                                <br><span class="badge bg-success">Verified Purchase</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="text-warning">
                                                {{ $review->rating_stars }}
                                            </div>
                                            <small>{{ $review->rating }}/5</small>
                                        </td>
                                        <td>
                                            @if($review->title)
                                                <strong>{{ Str::limit($review->title, 50) }}</strong><br>
                                            @endif
                                            <small>{{ Str::limit($review->comment, 100) }}</small>
                                            @if($review->images)
                                                <br><span class="badge bg-info">{{ count($review->images) }} images</span>
                                            @endif
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm status-select"
                                                    data-review-id="{{ $review->id }}">
                                                <option value="pending" {{ $review->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ $review->status === 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="rejected" {{ $review->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                <option value="spam" {{ $review->status === 'spam' ? 'selected' : '' }}>Spam</option>
                                            </select>
                                        </td>
                                        <td>
                                            <small>{{ $review->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('products.reviews.show', $review->id) }}"
                                                   class="btn btn-sm btn-info"
                                                   title="View">
                                                    View
                                                </a>
                                                <a href="{{ route('products.reviews.edit', $review->id) }}"
                                                   class="btn btn-sm btn-primary"
                                                   title="Edit">
                                                    Edit
                                                </a>
                                                <button type="button"
                                                        class="btn btn-sm btn-danger delete-review"
                                                        data-review-id="{{ $review->id }}">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No reviews found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $reviews->links() }}
                </div>

            </div>
        </div>
    </main>

    {{-- Delete Form (Hidden) --}}
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        // Select all checkboxes
        document.getElementById('selectAll').addEventListener('change', function() {
            document.querySelectorAll('.review-checkbox').forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Quick status change
        document.querySelectorAll('.status-select').forEach(select => {
            select.addEventListener('change', function() {
                const reviewId = this.dataset.reviewId;
                const status = this.value;

                fetch(`/reviews/${reviewId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                });
            });
        });

        // Delete review
        document.querySelectorAll('.delete-review').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this review?')) {
                    const reviewId = this.dataset.reviewId;
                    const form = document.getElementById('deleteForm');
                    form.action = `/reviews/${reviewId}`;
                    form.submit();
                }
            });
        });
    </script>
@endsection
