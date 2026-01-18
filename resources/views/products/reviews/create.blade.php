@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title mb-4">Add New Review</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('products.reviews.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Product Selection --}}
                    <div class="mb-3">
                        <label class="form-label">Product <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-select" required>
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        {{-- Reviewer Name --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reviewer Name <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="reviewer_name"
                                   class="form-control"
                                   value="{{ old('reviewer_name') }}"
                                   required>
                        </div>

                        {{-- Reviewer Email --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Reviewer Email <span class="text-danger">*</span></label>
                            <input type="email"
                                   name="reviewer_email"
                                   class="form-control"
                                   value="{{ old('reviewer_email') }}"
                                   required>
                        </div>
                    </div>

                    {{-- Rating --}}
                    <div class="mb-3">
                        <label class="form-label">Rating <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rating-input">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio"
                                           name="rating"
                                           id="star{{ $i }}"
                                           value="{{ $i }}"
                                           {{ old('rating', 5) == $i ? 'checked' : '' }}>
                                    <label for="star{{ $i }}" class="star">★</label>
                                @endfor
                            </div>
                            <span id="rating-text" class="text-muted">5 stars</span>
                        </div>
                    </div>

                    {{-- Review Title --}}
                    <div class="mb-3">
                        <label class="form-label">Review Title (Optional)</label>
                        <input type="text"
                               name="title"
                               class="form-control"
                               value="{{ old('title') }}"
                               placeholder="Summarize your review">
                    </div>

                    {{-- Review Comment --}}
                    <div class="mb-3">
                        <label class="form-label">Review Comment <span class="text-danger">*</span></label>
                        <textarea name="comment"
                                  class="form-control"
                                  rows="5"
                                  required>{{ old('comment') }}</textarea>
                    </div>

                    {{-- Review Images --}}
                    <div class="mb-3">
                        <label class="form-label">Review Images (Optional)</label>
                        <input type="file"
                               name="images[]"
                               class="form-control"
                               multiple
                               accept="image/*"
                               id="reviewImages">
                        <div id="imagePreview" class="d-flex flex-wrap gap-2 mt-2"></div>
                        <small class="text-muted">You can upload multiple images (max 2MB each)</small>
                    </div>

                    <div class="row">
                        {{-- Status --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>
                                    Pending
                                </option>
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>
                                    Approved
                                </option>
                                <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>
                                    Rejected
                                </option>
                                <option value="spam" {{ old('status') == 'spam' ? 'selected' : '' }}>
                                    Spam
                                </option>
                            </select>
                        </div>

                        {{-- Verified Purchase --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label d-block">Options</label>
                            <div class="form-check">
                                <input type="checkbox"
                                       name="verified_purchase"
                                       value="1"
                                       class="form-check-input"
                                       {{ old('verified_purchase') ? 'checked' : '' }}
                                       id="verified_purchase">
                                <label class="form-check-label" for="verified_purchase">
                                    Verified Purchase
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Create Review</button>
                        <a href="{{ route('products.reviews.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </main>

    <style>
        .rating-input {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 5px;
        }

        .rating-input input[type="radio"] {
            display: none;
        }

        .rating-input .star {
            font-size: 2rem;
            color: #ddd;
            cursor: pointer;
            transition: color 0.2s;
        }

        .rating-input input[type="radio"]:checked ~ .star,
        .rating-input .star:hover,
        .rating-input .star:hover ~ .star {
            color: #ffc107;
        }
    </style>

    <script>
        // Update rating text
        document.querySelectorAll('input[name="rating"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const ratingText = document.getElementById('rating-text');
                const stars = ['1 star', '2 stars', '3 stars', '4 stars', '5 stars'];
                ratingText.textContent = stars[this.value - 1];
            });
        });

        // Image preview
        document.getElementById('reviewImages').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';

            Array.from(e.target.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const div = document.createElement('div');
                    div.className = 'position-relative';
                    div.innerHTML = `
                        <img src="${event.target.result}"
                             class="img-thumbnail"
                             style="width: 100px; height: 100px; object-fit: cover;">
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
@endsection
