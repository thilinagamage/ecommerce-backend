@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Coupons</h4>
                    <a href="{{ route('marketing.coupons.create') }}" class="btn btn-primary">
                        Create Coupon
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
                                <h6 class="card-title text-white">Total Coupons</h6>
                                <h2 class="mb-0">{{ number_format($stats['total']) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white">Active</h6>
                                <h2 class="mb-0">{{ number_format($stats['active']) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white">Expired</h6>
                                <h2 class="mb-0">{{ number_format($stats['expired']) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white">Total Usage</h6>
                                <h2 class="mb-0">{{ number_format($stats['total_usage']) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Search coupons..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="discount_type" class="form-select">
                            <option value="">All Types</option>
                            <option value="percentage" {{ request('discount_type') === 'percentage' ? 'selected' : '' }}>Percentage</option>
                            <option value="fixed" {{ request('discount_type') === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            <option value="free_shipping" {{ request('discount_type') === 'free_shipping' ? 'selected' : '' }}>Free Shipping</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                    </div>
                </form>

                {{-- Coupons Table --}}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Discount</th>
                                <th>Usage</th>
                                <th>Valid Period</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $coupon)
                                <tr>
                                    <td>
                                        <span class="badge bg-dark">{{ $coupon->code }}</span>
                                    </td>
                                    <td>
                                        <div>{{ $coupon->name }}</div>
                                        @if($coupon->description)
                                            <small class="text-muted">{{ Str::limit($coupon->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($coupon->discount_type === 'percentage')
                                            {{ $coupon->discount_value }}% OFF
                                        @elseif($coupon->discount_type === 'fixed')
                                            ${{ number_format($coupon->discount_value, 2) }} OFF
                                        @else
                                            FREE SHIPPING
                                        @endif
                                        @if($coupon->minimum_spend)
                                            <br><small class="text-muted">Min: ${{ number_format($coupon->minimum_spend, 2) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $coupon->usage_count }}
                                            @if($coupon->usage_limit)
                                                / {{ $coupon->usage_limit }}
                                            @endif
                                        </div>
                                        @if($coupon->usage_limit_per_user)
                                            <small class="text-muted">{{ $coupon->usage_limit_per_user }}/user</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($coupon->starts_at)
                                            <div><small>From: {{ $coupon->starts_at->format('M d, Y') }}</small></div>
                                        @endif
                                        @if($coupon->expires_at)
                                            <div><small>To: {{ $coupon->expires_at->format('M d, Y') }}</small></div>
                                        @else
                                            <small class="text-muted">No expiry</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $coupon->status_badge }}">
                                            {{ ucfirst($coupon->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('marketing.coupons.edit', $coupon->id) }}"
                                               class="btn btn-sm btn-primary">
                                                Edit
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger delete-coupon"
                                                    data-coupon-id="{{ $coupon->id }}">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No coupons found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $coupons->links() }}
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
        // Delete coupon
        document.querySelectorAll('.delete-coupon').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('Are you sure you want to delete this coupon?')) {
                    const couponId = this.dataset.couponId;
                    const form = document.getElementById('deleteForm');
                    form.action = "{{ route('marketing.coupons.index') }}/" + couponId;
                    form.submit();
                }
            });
        });
    </script>
@endsection
