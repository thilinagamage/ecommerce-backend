@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">Customer Loyalty Details</h4>
                        <p class="text-muted mb-0">{{ $customer->name }}</p>
                    </div>
                    <div>
                        <a href="{{ route('marketing.loyalty.customers') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Back to Customers
                        </a>
                    </div>
                </div>

                {{-- Customer Info Card --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="avatar-circle-xl mx-auto mb-3">
                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                </div>
                                <h5>{{ $customer->name }}</h5>
                                <p class="text-muted">{{ $customer->email }}</p>

                                @if($customer->loyaltyTier)
                                    <span class="badge bg-primary mb-3">
                                        <i class="bx bx-medal"></i> {{ $customer->loyaltyTier->name }}
                                    </span>
                                @endif

                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adjustPointsModal">
                                        <i class="bx bx-edit"></i> Adjust Points
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-muted">Current Points</h6>
                                        <h2 class="text-success mb-0">{{ number_format($customer->loyalty_points) }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-muted">Total Earned</h6>
                                        <h2 class="text-primary mb-0">{{ number_format($customer->total_points_earned) }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-muted">Total Redeemed</h6>
                                        <h2 class="text-warning mb-0">{{ number_format($totalRedeemed) }}</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="text-muted">Member Since</h6>
                                        <h2 class="text-info mb-0">{{ $customer->created_at->format('M Y') }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Transaction History --}}
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bx bx-history"></i> Transaction History</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Points</th>
                                        <th>Balance After</th>
                                        <th>Description</th>
                                        <th>Order</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $transaction)
                                        <tr>
                                            <td>
                                                <div>{{ $transaction->created_at->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $transaction->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td>
                                                @if($transaction->type === 'earned')
                                                    <span class="badge bg-success"><i class="bx bx-plus"></i> Earned</span>
                                                @elseif($transaction->type === 'redeemed')
                                                    <span class="badge bg-warning"><i class="bx bx-minus"></i> Redeemed</span>
                                                @elseif($transaction->type === 'expired')
                                                    <span class="badge bg-danger"><i class="bx bx-time"></i> Expired</span>
                                                @else
                                                    <span class="badge bg-info"><i class="bx bx-edit"></i> Adjusted</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong class="{{ $transaction->points > 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $transaction->points > 0 ? '+' : '' }}{{ number_format($transaction->points) }}
                                                </strong>
                                            </td>
                                            <td>{{ number_format($transaction->balance_after) }}</td>
                                            <td>{{ $transaction->description }}</td>
                                            <td>
                                                @if($transaction->order)
                                                    <a href="{{ route('orders.show', $transaction->order->id) }}" class="text-primary">
                                                        #{{ $transaction->order->order_number }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                No transactions yet
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $transactions->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    {{-- Adjust Points Modal --}}
    <div class="modal fade" id="adjustPointsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('marketing.loyalty.customers.adjust', $customer) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Adjust Points</h5>
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
@endsection

@push('styles')
<style>
    .avatar-circle-xl {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 40px;
    }
</style>
@endpush
