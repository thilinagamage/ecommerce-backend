@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">Loyalty Reports</h4>
                        <p class="text-muted mb-0">Analytics and insights for your loyalty program</p>
                    </div>
                    <div>
                        <a href="{{ route('marketing.loyalty.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back"></i> Back to Loyalty
                        </a>
                    </div>
                </div>

                {{-- Date Range Filter --}}
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <form action="{{ route('marketing.loyalty.reports') }}" method="GET">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date', now()->subMonth()->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date', now()->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bx bx-filter"></i> Apply Filter
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-outline-secondary w-100" onclick="window.print()">
                                        <i class="bx bx-printer"></i> Print Report
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Summary Cards --}}
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-start border-primary border-4">
                            <div class="card-body">
                                <h6 class="text-muted">Points Earned</h6>
                                <h3 class="text-success">+{{ number_format($stats['points_earned']) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-warning border-4">
                            <div class="card-body">
                                <h6 class="text-muted">Points Redeemed</h6>
                                <h3 class="text-danger">-{{ number_format($stats['points_redeemed']) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-info border-4">
                            <div class="card-body">
                                <h6 class="text-muted">New Members</h6>
                                <h3 class="text-primary">{{ number_format($stats['new_members']) }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-start border-success border-4">
                            <div class="card-body">
                                <h6 class="text-muted">Active Members</h6>
                                <h3 class="text-success">{{ number_format($stats['active_members']) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Points Activity Chart --}}
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bx bx-line-chart"></i> Points Activity Over Time</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="pointsChart" height="80"></canvas>
                    </div>
                </div>

                {{-- Top Customers --}}
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="bx bx-trophy"></i> Top Point Earners</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Rank</th>
                                                <th>Customer</th>
                                                <th>Points</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($topEarners as $index => $earner)
                                                <tr>
                                                    <td>
                                                        @if($index == 0)
                                                            <i class="bx bxs-medal text-warning fs-5"></i>
                                                        @elseif($index == 1)
                                                            <i class="bx bxs-medal text-secondary fs-5"></i>
                                                        @elseif($index == 2)
                                                            <i class="bx bxs-medal text-warning fs-5" style="opacity: 0.6;"></i>
                                                        @else
                                                            {{ $index + 1 }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $earner->name }}</td>
                                                    <td><strong>{{ number_format($earner->total_points_earned) }}</strong></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="bx bx-pie-chart-alt"></i> Tier Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="tierChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Points Activity Chart
    const pointsCtx = document.getElementById('pointsChart').getContext('2d');
    new Chart(pointsCtx, {
        type: 'line',
        data: {
            labels: @json($chartData['labels']),
            datasets: [{
                label: 'Points Earned',
                data: @json($chartData['earned']),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4
            }, {
                label: 'Points Redeemed',
                data: @json($chartData['redeemed']),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    // Tier Distribution Chart
    const tierCtx = document.getElementById('tierChart').getContext('2d');
    new Chart(tierCtx, {
        type: 'doughnut',
        data: {
            labels: @json($tierDistribution['labels']),
            datasets: [{
                data: @json($tierDistribution['data']),
                backgroundColor: [
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(192, 192, 192, 0.8)',
                    'rgba(255, 215, 0, 0.8)',
                    'rgba(173, 216, 230, 0.8)',
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });
</script>
@endpush
