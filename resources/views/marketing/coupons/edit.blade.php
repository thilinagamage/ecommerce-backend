@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title mb-4">Edit Coupon - {{ $coupon->code }}</h4>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('marketing.coupons.update', $coupon->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Basic Information --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Basic Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="code"
                                           id="coupon_code"
                                           class="form-control text-uppercase"
                                           value="{{ old('code', $coupon->code) }}"
                                           required>
                                    <small class="text-muted">Code will be converted to uppercase</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Coupon Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           name="name"
                                           class="form-control"
                                           value="{{ old('name', $coupon->name) }}"
                                           required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description"
                                          class="form-control"
                                          rows="3">{{ old('description', $coupon->description) }}</textarea>
                            </div>

                            <div class="alert alert-info">
                                <strong>Usage Statistics:</strong>
                                This coupon has been used {{ $coupon->usage_count }} times
                                @if($coupon->usage_limit)
                                    out of {{ $coupon->usage_limit }} total uses allowed
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Discount Settings --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Discount Settings</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Discount Type <span class="text-danger">*</span></label>
                                    <select name="discount_type" id="discount_type" class="form-select" required>
                                        <option value="percentage" {{ old('discount_type', $coupon->discount_type) == 'percentage' ? 'selected' : '' }}>
                                            Percentage Discount (%)
                                        </option>
                                        <option value="fixed" {{ old('discount_type', $coupon->discount_type) == 'fixed' ? 'selected' : '' }}>
                                            Fixed Amount ($)
                                        </option>
                                        <option value="free_shipping" {{ old('discount_type', $coupon->discount_type) == 'free_shipping' ? 'selected' : '' }}>
                                            Free Shipping
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3" id="discount_value_field">
                                    <label class="form-label">
                                        <span id="discount_value_label">Discount Value</span>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           name="discount_value"
                                           class="form-control"
                                           value="{{ old('discount_value', $coupon->discount_value) }}"
                                           step="0.01"
                                           min="0"
                                           required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Minimum Spend ($)</label>
                                    <input type="number"
                                           name="minimum_spend"
                                           class="form-control"
                                           value="{{ old('minimum_spend', $coupon->minimum_spend) }}"
                                           step="0.01"
                                           min="0"
                                           placeholder="No minimum">
                                </div>

                                <div class="col-md-6 mb-3" id="maximum_discount_field">
                                    <label class="form-label">Maximum Discount ($)</label>
                                    <input type="number"
                                           name="maximum_discount"
                                           class="form-control"
                                           value="{{ old('maximum_discount', $coupon->maximum_discount) }}"
                                           step="0.01"
                                           min="0"
                                           placeholder="No maximum">
                                    <small class="text-muted">Only for percentage discounts</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Usage Limits --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Usage Limits</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Total Usage Limit</label>
                                    <input type="number"
                                           name="usage_limit"
                                           class="form-control"
                                           value="{{ old('usage_limit', $coupon->usage_limit) }}"
                                           min="1"
                                           placeholder="Unlimited">
                                    <small class="text-muted">Current usage: {{ $coupon->usage_count }}</small>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Usage Limit Per User</label>
                                    <input type="number"
                                           name="usage_limit_per_user"
                                           class="form-control"
                                           value="{{ old('usage_limit_per_user', $coupon->usage_limit_per_user) }}"
                                           min="1"
                                           placeholder="Unlimited">
                                </div>
                            </div>

                            <div class="form-check">
                                <input type="checkbox"
                                       name="first_order_only"
                                       class="form-check-input"
                                       id="first_order_only"
                                       {{ old('first_order_only', $coupon->first_order_only) ? 'checked' : '' }}>
                                <label class="form-check-label" for="first_order_only">
                                    First Order Only (for new customers)
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Validity Period --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Validity Period</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Start Date & Time</label>
                                    <input type="datetime-local"
                                           name="starts_at"
                                           class="form-control"
                                           value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Expiry Date & Time</label>
                                    <input type="datetime-local"
                                           name="expires_at"
                                           class="form-control"
                                           value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Product/Category Restrictions --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Restrictions (Optional)</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Specific Products</label>
                                    <select name="product_ids[]" class="form-select" multiple size="5">
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ in_array($product->id, old('product_ids', $coupon->product_ids ?? [])) ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Specific Categories</label>
                                    <select name="category_ids[]" class="form-select" multiple size="5">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ in_array($category->id, old('category_ids', $coupon->category_ids ?? [])) ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Excluded Products</label>
                                    <select name="excluded_product_ids[]" class="form-select" multiple size="5">
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                {{ in_array($product->id, old('excluded_product_ids', $coupon->excluded_product_ids ?? [])) ? 'selected' : '' }}>
                                                {{ $product->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Excluded Categories</label>
                                    <select name="excluded_category_ids[]" class="form-select" multiple size="5">
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ in_array($category->id, old('excluded_category_ids', $coupon->excluded_category_ids ?? [])) ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Status</h5>
                        </div>
                        <div class="card-body">
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status', $coupon->status) == 'active' ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="inactive" {{ old('status', $coupon->status) == 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>
                                <option value="scheduled" {{ old('status', $coupon->status) == 'scheduled' ? 'selected' : '' }}>
                                    Scheduled
                                </option>
                                <option value="expired" {{ old('status', $coupon->status) == 'expired' ? 'selected' : '' }}>
                                    Expired
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Update Coupon</button>
                        <a href="{{ route('marketing.coupons.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </main>

    <script>
        // Discount type change handler
        document.getElementById('discount_type').addEventListener('change', function() {
            const type = this.value;
            const valueField = document.getElementById('discount_value_field');
            const valueLabel = document.getElementById('discount_value_label');
            const maxField = document.getElementById('maximum_discount_field');

            if (type === 'percentage') {
                valueLabel.textContent = 'Discount Value (%)';
                valueField.style.display = 'block';
                maxField.style.display = 'block';
            } else if (type === 'fixed') {
                valueLabel.textContent = 'Discount Amount ($)';
                valueField.style.display = 'block';
                maxField.style.display = 'none';
            } else {
                valueField.style.display = 'none';
                maxField.style.display = 'none';
            }
        });

        // Trigger on page load
        document.getElementById('discount_type').dispatchEvent(new Event('change'));

        // Auto-uppercase code
        document.getElementById('coupon_code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
@endsection
