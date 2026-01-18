@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Review Details</h4>
                    <div>
                        <a href="{{ route('products.reviews.edit', $review->id) }}" class="btn btn-primary btn-sm">
                            Edit Review
                        </a>
                        <a href="{{ route('products.reviews.index') }}" class="btn btn-secondary btn-sm">
                            Back to List
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="row">
                    {{-- Main Review Content --}}
                    <div class="col-md-8">
                        {{-- Product Info --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Product Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3">
                                    @if($review->product->featuredImage)
                                        <img src="{{ asset('storage/' . $review->product->featuredImage->path) }}"
                                             class="img-thumbnail"
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    @endif
                                    <div>
                                        <h5>{{ $review->product->name }}</h5>
                                        <a href="{{ route('products.show', $review->product_id) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           target="_blank">
                                            View Product
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Review Content --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Review Content</h5>
                            </div>
                            <div class="card-body">
                                {{-- Rating --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Rating</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="text-warning" style="font-size: 1.5rem;">
                                            {{ $review->rating_stars }}
                                        </div>
                                        <span class="badge bg-warning">{{ $review->rating }}/5</span>
                                    </div>
                                </div>

                                {{-- Title --}}
                                @if($review->title)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Title</label>
                                        <h5>{{ $review->title }}</h5>
                                    </div>
                                @endif

                                {{-- Comment --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Comment</label>
                                    <p class="mb-0">{{ $review->comment }}</p>
                                </div>

                                {{-- Review Images --}}
                                @if($review->images && count($review->images) > 0)
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Review Images</label>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($review->images as $imagePath)
                                                <a href="{{ asset('storage/' . $imagePath) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $imagePath) }}"
                                                         class="img-thumbnail"
                                                         style="width: 150px; height: 150px; object-fit: cover;">
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Helpful Votes --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Helpful Votes</label>
                                    <div class="d-flex gap-3">
                                        <span class="badge bg-success">
                                            👍 {{ $review->helpful_count }} Helpful
                                        </span>
                                        <span class="badge bg-danger">
                                            👎 {{ $review->not_helpful_count }} Not Helpful
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Admin Reply --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Admin Reply</h5>
                            </div>
                            <div class="card-body">
                                @if($review->admin_reply)
                                    <div class="alert alert-secondary">
                                        <p class="mb-1">{{ $review->admin_reply }}</p>
                                        <small class="text-muted">
                                            Replied on {{ $review->admin_reply_at->format('M d, Y h:i A') }}
                                        </small>
                                    </div>
                                @else
                                    <p class="text-muted">No admin reply yet.</p>
                                @endif

                                {{-- Add/Edit Reply Form --}}
                                <form method="POST" action="{{ route('products.reviews.add-reply', $review->id) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea name="admin_reply"
                                                  class="form-control"
                                                  rows="3"
                                                  placeholder="Write a reply to this review..."
                                                  required>{{ old('admin_reply', $review->admin_reply) }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        {{ $review->admin_reply ? 'Update Reply' : 'Add Reply' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Sidebar --}}
                    <div class="col-md-4">
                        {{-- Reviewer Info --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Reviewer Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>Name:</strong><br>
                                    {{ $review->reviewer_name }}
                                </div>
                                <div class="mb-2">
                                    <strong>Email:</strong><br>
                                    <a href="mailto:{{ $review->reviewer_email }}">{{ $review->reviewer_email }}</a>
                                </div>
                                @if($review->user_id)
                                    <div class="mb-2">
                                        <strong>User Account:</strong><br>
                                        <a href="#" class="text-primary">View Profile</a>
                                    </div>
                                @endif
                                @if($review->verified_purchase)
                                    <div class="mb-2">
                                        <span class="badge bg-success">✓ Verified Purchase</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Review Status --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Review Status</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Current Status:</strong><br>
                                    <span class="badge bg-{{ $review->status_badge }}">
                                        {{ ucfirst($review->status) }}
                                    </span>
                                </div>

                                {{-- Quick Status Actions --}}
                                <div class="d-grid gap-2">
                                    @if($review->status !== 'approved')
                                        <form method="POST" action="{{ route('products.reviews.update-status', $review->id) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="btn btn-success btn-sm w-100">
                                                Approve Review
                                            </button>
                                        </form>
                                    @endif

                                    @if($review->status !== 'rejected')
                                        <form method="POST" action="{{ route('products.reviews.update-status', $review->id) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="btn btn-warning btn-sm w-100">
                                                Reject Review
                                            </button>
                                        </form>
                                    @endif

                                    @if($review->status !== 'spam')
                                        <form method="POST" action="{{ route('products.reviews.update-status', $review->id) }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="spam">
                                            <button type="submit" class="btn btn-dark btn-sm w-100">
                                                Mark as Spam
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Review Metadata --}}
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Review Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong>Review ID:</strong><br>
                                    #{{ $review->id }}
                                </div>
                                <div class="mb-2">
                                    <strong>Created:</strong><br>
                                    {{ $review->created_at->format('M d, Y h:i A') }}
                                </div>
                                <div class="mb-2">
                                    <strong>Last Updated:</strong><br>
                                    {{ $review->updated_at->format('M d, Y h:i A') }}
                                </div>
                                @if($review->order_id)
                                    <div class="mb-2">
                                        <strong>Order:</strong><br>
                                        <a href="#" class="text-primary">#{{ $review->order_id }}</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('products.reviews.edit', $review->id) }}" class="btn btn-primary btn-sm">
                                        Edit Review
                                    </a>
                                    <form method="POST"
                                          action="{{ route('reviews.destroy', $review->id) }}"
                                          onsubmit="return confirm('Are you sure you want to delete this review?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm w-100">
                                            Delete Review
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection
