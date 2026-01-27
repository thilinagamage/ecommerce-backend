@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Gift Cards</h4>
                    <a href="{{ route('marketing.gift-cards.create') }}" class="btn btn-primary">
                        Create Gift Card
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
                                <h6 class="card-title text-white">Total Gift Cards</h6>
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
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white">Total Balance</h6>
                                <h2 class="mb-0">${{ number_format($stats['total_balance'], 2) }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white">Redeemed</h6>
                                <h2 class="mb-0">{{ number_format($stats['redeemed']) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Search by code or email..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="redeemed" {{ request('status') === 'redeemed' ? 'selected' : '' }}>Redeemed</option>
                            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                    </div>
                </form>

                {{-- Gift Cards Table --}}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Recipient</th>
                                <th>Initial Balance</th>
                                <th>Current Balance</th>
                                <th>Status</th>
                                <th>Expires</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($giftCards as $card)
                                <tr>
                                    <td>
                                        <span class="badge bg-dark">{{ $card->code }}</span>
                                    </td>
                                    <td>
                                        <div>{{ $card->recipient_email }}</div>
                                        @if($card->recipient)
                                            <small class="text-muted">{{ $card->recipient->name }}</small>
                                        @endif
                                    </td>
                                    <td>${{ number_format($card->initial_balance, 2) }}</td>
                                    <td>
                                        <strong class="{{ $card->balance > 0 ? 'text-success' : 'text-muted' }}">
                                            ${{ number_format($card->balance, 2) }}
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $card->status === 'active' ? 'success' : ($card->status === 'redeemed' ? 'info' : 'danger') }}">
                                            {{ ucfirst($card->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($card->expires_at)
                                            {{ $card->expires_at->format('M d, Y') }}
                                        @else
                                            <span class="text-muted">No expiry</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('marketing.gift-cards.show', $card->id) }}"
                                               class="btn btn-sm btn-info">
                                                View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No gift cards found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $giftCards->links() }}
                </div>

            </div>
        </div>
    </main>
@endsection
