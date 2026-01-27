@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">Loyalty Customers</h4>
                        <p class="text-muted mb-0">View and manage customer loyalty points</p>
                    </div>
                    <div>
                        <a href="{{ route('marketing.loyalty.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Back to Loyalty
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

                {{-- Search and Filter --}}
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <form action="{{ route('marketing.loyalty.customers') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="tier" class="form-select">
                                        <option value="">All Tiers</option>
                                        @foreach(\App\Models\Marketing\LoyaltyTier::orderBy('points_required')->get() as $tier)
                                            <option value="{{ $tier->id }}" {{ request('tier') == $tier->id ? 'selected' : '' }}>
                                                {{ $tier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="sort" class="form-select">
                                        <option value="points_desc" {{ request('sort') == 'points_desc' ? 'selected' : '' }}>Highest Points</option>
                                        <option value="points_asc" {{ request('sort') == 'points_asc' ? 'selected' : '' }}>Lowest Points</option>
                                        <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Recently Active</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bx bx-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Customers Table --}}
                <div class="table-responsive">
                    <table class="table table-hover" id="loyaltyCustomersTable">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Current Tier</th>
                                <th>Current Points</th>
                                <th>Total Earned</th>
                                <th>Total Redeemed</th>
                                <th>Member Since</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr class="customer-row" data-customer-id="{{ $customer->id }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2">
                                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong>{{ $customer->name }}</strong>
                                                <br><small class="text-muted">{{ $customer->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($customer->loyaltyTier)
                                            <span class="badge bg-primary">
                                                @if($customer->loyaltyTier->name == 'Bronze')
                                                    <i class="bx bx-medal"></i>
                                                @elseif($customer->loyaltyTier->name == 'Silver')
                                                    <i class="bx bx-medal"></i>
                                                @elseif($customer->loyaltyTier->name == 'Gold')
                                                    <i class="bx bx-medal"></i>
                                                @elseif($customer->loyaltyTier->name == 'Platinum')
                                                    <i class="bx bx-crown"></i>
                                                @else
                                                    <i class="bx bx-star"></i>
                                                @endif
                                                {{ $customer->loyaltyTier->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">No Tier</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-success">{{ number_format($customer->loyalty_points) }}</strong>
                                        <small class="text-muted">pts</small>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ number_format($customer->total_points_earned) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ number_format(abs($customer->loyaltyTransactions()->where('type', 'redeemed')->sum('points'))) }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $customer->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#viewCustomerModal{{ $customer->id }}">
                                                        <i class="bx bx-show"></i> View Details
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#adjustPointsModal{{ $customer->id }}">
                                                        <i class="bx bx-edit"></i> Adjust Points
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="bx bx-user-x fs-1 text-muted"></i>
                                        <p class="text-muted mb-0">No loyalty customers found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $customers->links() }}
                </div>

            </div>
        </div>
    </main>

    {{-- Modals - Moved outside main table --}}
    @foreach($customers as $customer)
        {{-- View Customer Modal --}}
        <div class="modal fade" id="viewCustomerModal{{ $customer->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $customer->name }} - Loyalty Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <div class="avatar-circle-lg mx-auto mb-2">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </div>
                                    <h6>{{ $customer->name }}</h6>
                                    <small class="text-muted">{{ $customer->email }}</small>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-6 mb-3">
                                        <label class="text-muted small">Current Tier</label>
                                        <div>
                                            @if($customer->loyaltyTier)
                                                <span class="badge bg-primary">{{ $customer->loyaltyTier->name }}</span>
                                            @else
                                                <span class="badge bg-secondary">No Tier</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="text-muted small">Current Points</label>
                                        <div><strong class="text-success">{{ number_format($customer->loyalty_points) }}</strong> pts</div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="text-muted small">Total Earned</label>
                                        <div><strong>{{ number_format($customer->total_points_earned) }}</strong> pts</div>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label class="text-muted small">Total Redeemed</label>
                                        <div><strong>{{ number_format(abs($customer->loyaltyTransactions()->where('type', 'redeemed')->sum('points'))) }}</strong> pts</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="mb-3">Recent Transactions</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Points</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer->loyaltyTransactions()->latest()->take(10)->get() as $transaction)
                                        <tr>
                                            <td>
                                                <span class="badge bg-{{ $transaction->type === 'earned' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                            <td class="{{ $transaction->points > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->points > 0 ? '+' : '' }}{{ number_format($transaction->points) }}
                                            </td>
                                            <td><small>{{ $transaction->description }}</small></td>
                                            <td><small>{{ $transaction->created_at->format('M d, Y') }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Adjust Points Modal --}}
        <div class="modal fade" id="adjustPointsModal{{ $customer->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('marketing.loyalty.customers.adjust', $customer) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Adjust Points: {{ $customer->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <strong>Current Balance:</strong> {{ number_format($customer->loyalty_points) }} points
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Adjustment Type</label>
                                <select name="adjustment_type" class="form-select" required>
                                    <option value="add">Add Points</option>
                                    <option value="subtract">Subtract Points</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Points</label>
                                <input type="number" name="points" class="form-control" required min="1">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Reason</label>
                                <textarea name="reason" class="form-control" rows="2" required placeholder="Explain why you're adjusting points..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Adjust Points</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('styles')
<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
    }
    .avatar-circle-lg {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 32px;
    }

    /* Force show all customer rows */
    #loyaltyCustomersTable tbody tr.customer-row {
        display: table-row !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    console.log('=== LOYALTY CUSTOMERS PAGE LOADED ===');

    // Prevent DataTables auto-initialization
    $.fn.dataTable.ext.errMode = 'none';

    // Check if DataTable was initialized
    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#loyaltyCustomersTable')) {
        console.log('DataTable detected - destroying it');
        $('#loyaltyCustomersTable').DataTable().destroy();
    }

    // Force show all customer rows
    const $customerRows = $('#loyaltyCustomersTable tbody tr.customer-row');
    console.log('Total customer rows:', $customerRows.length);

    $customerRows.each(function(index) {
        const $row = $(this);
        const name = $row.find('strong').first().text().trim();
        const email = $row.find('.text-muted').first().text().trim();
        const isVisible = $row.is(':visible');

        console.log(`Row ${index + 1}:`, {
            name: name,
            email: email,
            visible: isVisible,
            display: $row.css('display'),
            height: $row.height()
        });

        // Force show
        $row.show().css({
            'display': 'table-row',
            'visibility': 'visible',
            'opacity': '1'
        });
    });

    console.log('Visible rows after fix:', $('#loyaltyCustomersTable tbody tr.customer-row:visible').length);
});
</script>
@endpush
