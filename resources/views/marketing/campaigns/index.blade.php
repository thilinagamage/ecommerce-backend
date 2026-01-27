@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="card-title mb-0">Marketing Campaigns</h4>
                    <a href="{{ route('marketing.campaigns.create') }}" class="btn btn-primary">
                        Create Campaign
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
                                <h6 class="card-title text-white">Total Campaigns</h6>
                                <h2 class="mb-0">{{ $stats['total'] ?? 0 }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white">Sent</h6>
                                <h2 class="mb-0">{{ $stats['sent'] ?? 0 }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white">Avg Open Rate</h6>
                                <h2 class="mb-0">{{ number_format($stats['avg_open_rate'] ?? 0, 1) }}%</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h6 class="card-title text-white">Total Revenue</h6>
                                <h2 class="mb-0">${{ number_format($stats['revenue'] ?? 0, 0) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filters --}}
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-5">
                        <input type="text"
                               name="search"
                               class="form-control"
                               placeholder="Search campaigns..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="email" {{ request('type') === 'email' ? 'selected' : '' }}>Email</option>
                            <option value="sms" {{ request('type') === 'sms' ? 'selected' : '' }}>SMS</option>
                            <option value="notification" {{ request('type') === 'notification' ? 'selected' : '' }}>Notification</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">Filter</button>
                    </div>
                </form>

                {{-- Campaigns Table --}}
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Campaign Name</th>
                                <th>Type</th>
                                <th>Recipients</th>
                                <th>Performance</th>
                                <th>Revenue</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="text-center">Coming soon - Campaigns feature</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </main>
@endsection
