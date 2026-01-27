@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Loyalty Program</h4>
                    <div>
                        <a href="{{ route('marketing.loyalty.tiers') }}" class="btn btn-secondary me-2">
                            <i class="bx bx-trophy"></i> Manage Tiers
                        </a>
                        <a href="{{ route('marketing.loyalty.customers') }}" class="btn btn-primary">
                            <i class="bx bx-user"></i> View Customers
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Statistics Cards --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white mb-1">Total Members</h6>
                                        <h2 class="mb-0">{{ number_format($stats['total_members']) }}</h2>
                                    </div>
                                    <div class="widget-icon">
                                        <i class="bx bx-group fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white mb-1">Active Points</h6>
                                        <h2 class="mb-0">{{ number_format($stats['active_points']) }}</h2>
                                    </div>
                                    <div class="widget-icon">
                                        <i class="bx bx-star fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white mb-1">Points Redeemed</h6>
                                        <h2 class="mb-0">{{ number_format($stats['redeemed_points']) }}</h2>
                                    </div>
                                    <div class="widget-icon">
                                        <i class="bx bx-gift fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title text-white mb-1">Loyalty Tiers</h6>
                                        <h2 class="mb-0">{{ $stats['tier_count'] }}</h2>
                                    </div>
                                    <div class="widget-icon">
                                        <i class="bx bx-trophy fs-1"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Loyalty Tiers Overview --}}
                <div class="card mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bx bx-trophy"></i> Loyalty Tiers Overview
                        </h5>
                        <a href="{{ route('marketing.loyalty.tiers') }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-plus"></i> Manage Tiers
                        </a>
                    </div>
                    <div class="card-body">
                        @if($tiers->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Tier Name</th>
                                            <th>Points Required</th>
                                            <th>Discount</th>
                                            <th>Members</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tiers as $tier)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="tier-icon me-2">
                                                            @if($tier->name == 'Bronze')
                                                                <i class="bx bx-medal text-warning fs-4"></i>
                                                            @elseif($tier->name == 'Silver')
                                                                <i class="bx bx-medal text-secondary fs-4"></i>
                                                            @elseif($tier->name == 'Gold')
                                                                <i class="bx bx-medal text-warning fs-4" style="color: #FFD700 !important;"></i>
                                                            @elseif($tier->name == 'Platinum')
                                                                <i class="bx bx-crown text-info fs-4"></i>
                                                            @else
                                                                <i class="bx bx-star text-primary fs-4"></i>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <strong>{{ $tier->name }}</strong>
                                                            @if($tier->description)
                                                                <br><small class="text-muted">{{ $tier->description }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark">
                                                        {{ number_format($tier->points_required) }} pts
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">
                                                        {{ $tier->discount_percentage }}% OFF
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $tier->users_count }} {{ Str::plural('member', $tier->users_count) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $tier->is_active ? 'success' : 'secondary' }}">
                                                        {{ $tier->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="bx bx-info-circle fs-3 me-3"></i>
                                    <div>
                                        <strong>📊 Get Started with Loyalty Tiers</strong><br>
                                        Create your first loyalty tier to start rewarding your customers!
                                    </div>
                                </div>
                                <a href="{{ route('marketing.loyalty.tiers') }}" class="btn btn-sm btn-primary mt-3">
                                    <i class="bx bx-plus"></i> Create Tier
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Recent Activity --}}
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bx bx-history"></i> Recent Points Activity
                        </h5>
                        <a href="{{ route('marketing.loyalty.customers') }}" class="btn btn-sm btn-outline-primary">
                            View All Customers
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Customer</th>
                                        <th>Activity</th>
                                        <th>Points</th>
                                        <th>Balance After</th>
                                        <th>Order</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentActivity as $activity)
                                        <tr>
                                            <td>
                                                <div>
                                                    <strong>{{ $activity->user->name }}</strong>
                                                    <br><small class="text-muted">{{ $activity->user->email }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($activity->type === 'earned')
                                                    <span class="badge bg-success">
                                                        <i class="bx bx-plus"></i> Earned
                                                    </span>
                                                @elseif($activity->type === 'redeemed')
                                                    <span class="badge bg-warning">
                                                        <i class="bx bx-minus"></i> Redeemed
                                                    </span>
                                                @elseif($activity->type === 'expired')
                                                    <span class="badge bg-danger">
                                                        <i class="bx bx-time"></i> Expired
                                                    </span>
                                                @else
                                                    <span class="badge bg-info">
                                                        <i class="bx bx-edit"></i> Adjusted
                                                    </span>
                                                @endif
                                                @if($activity->description)
                                                    <br><small class="text-muted">{{ $activity->description }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="{{ $activity->points > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $activity->points > 0 ? '+' : '' }}{{ number_format($activity->points) }}
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">
                                                    {{ number_format($activity->balance_after) }} pts
                                                </span>
                                            </td>
                                            <td>
                                                @if($activity->order)
                                                    <a href="{{ route('orders.show', $activity->order->id) }}" class="text-primary">
                                                        #{{ $activity->order->order_number }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $activity->created_at->format('M d, Y') }}</small>
                                                <br><small class="text-muted">{{ $activity->created_at->format('h:i A') }}</small>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <i class="bx bx-inbox fs-1 text-muted"></i>
                                                <p class="text-muted mb-0">No recent activity</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection

@push('styles')
<style>
    .widget-icon {
        opacity: 0.3;
    }
</style>
@endpush
