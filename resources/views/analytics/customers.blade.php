@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">Customer Analytics</h4>
            <p class="text-muted mb-0">Understand your customer behavior and trends</p>
        </div>
        <div>
            <select class="form-select" id="period-select">
                <option value="7" {{ $period == 7 ? 'selected' : '' }}>Last 7 days</option>
                <option value="30" {{ $period == 30 ? 'selected' : '' }}>Last 30 days</option>
                <option value="90" {{ $period == 90 ? 'selected' : '' }}>Last 90 days</option>
                <option value="365" {{ $period == 365 ? 'selected' : '' }}>Last Year</option>
            </select>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Customers</p>
                            <h3 class="mb-0">{{ number_format($totalCustomers) }}</h3>
                            <small class="text-muted">All time</small>
                        </div>
                        <div class="widget-icon bg-primary text-white">
                            <i class="bx bx-user fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">New Customers</p>
                            <h3 class="mb-0">{{ number_format($newCustomers) }}</h3>
                            <small class="text-{{ $customerGrowth >= 0 ? 'success' : 'danger' }}">
                                <i class="bx bx-{{ $customerGrowth >= 0 ? 'up' : 'down' }}-arrow-alt"></i>
                                {{ number_format(abs($customerGrowth), 1) }}%
                            </small>
                        </div>
                        <div class="widget-icon bg-success text-white">
                            <i class="bx bx-user-plus fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-start border-info border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Avg Lifetime Value</p>
                            <h3 class="mb-0">${{ number_format($avgLifetimeValue, 2) }}</h3>
                            <small class="text-muted">Per customer</small>
                        </div>
                        <div class="widget-icon bg-info text-white">
                            <i class="bx bx-wallet fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Repeat Rate</p>
                            <h3 class="mb-0">{{ number_format($repeatRate, 1) }}%</h3>
                            <small class="text-muted">Returning customers</small>
                        </div>
                        <div class="widget-icon bg-warning text-white">
                            <i class="bx bx-refresh fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Customer Acquisition Chart --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-line-chart"></i> Customer Acquisition</h5>
                </div>
                <div class="card-body">
                    <canvas id="acquisitionChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-pie-chart-alt"></i> Customer Segments</h5>
                </div>
                <div class="card-body">
                    <canvas id="segmentationChart" height="200"></canvas>
                    <div class="mt-3">
                        <small class="d-block mb-1"><i class="bx bx-circle text-danger"></i> New: {{ $customerSegmentation['new'] }}</small>
                        <small class="d-block mb-1"><i class="bx bx-circle text-warning"></i> One-time: {{ $customerSegmentation['one_time'] }}</small>
                        <small class="d-block mb-1"><i class="bx bx-circle text-info"></i> Repeat: {{ $customerSegmentation['repeat'] }}</small>
                        <small class="d-block"><i class="bx bx-circle text-success"></i> Loyal: {{ $customerSegmentation['loyal'] }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Customers --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-crown"></i> Top Customers by Revenue</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Total Orders</th>
                                    <th>Total Spent</th>
                                    <th>Avg Order Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topCustomers as $index => $customer)
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
                                        <td><strong>{{ $customer->name }}</strong></td>
                                        <td>{{ $customer->email }}</td>
                                        <td>{{ $customer->total_orders }}</td>
                                        <td><strong>${{ number_format($customer->total_spent, 2) }}</strong></td>
                                        <td>${{ number_format($customer->total_spent / $customer->total_orders, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Geographic Distribution --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bx bx-world"></i> Geographic Distribution</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Country</th>
                                    <th>Customers</th>
                                    <th>Revenue</th>
                                    <th>Distribution</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalGeoRevenue = $geographicData->sum('revenue'); @endphp
                                @foreach($geographicData as $geo)
                                    <tr>
                                        <td><strong>{{ $geo->country }}</strong></td>
                                        <td>{{ number_format($geo->customer_count) }}</td>
                                        <td>${{ number_format($geo->revenue, 2) }}</td>
                                        <td>
                                            <div class="progress" style="height: 25px;">
                                                <div class="progress-bar bg-primary" role="progressbar"
                                                     style="width: {{ ($geo->revenue / max($totalGeoRevenue, 1)) * 100 }}%">
                                                    {{ number_format(($geo->revenue / max($totalGeoRevenue, 1)) * 100, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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
    document.getElementById('period-select').addEventListener('change', function() {
        window.location.href = '{{ route("analytics.customers") }}?period=' + this.value;
    });

    // Acquisition Chart
    const acquisitionCtx = document.getElementById('acquisitionChart').getContext('2d');
    new Chart(acquisitionCtx, {
        type: 'line',
        data: {
            labels: @json($customerAcquisition->pluck('date')),
            datasets: [{
                label: 'New Customers',
                data: @json($customerAcquisition->pluck('count')),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });

    // Segmentation Chart
    const segmentationCtx = document.getElementById('segmentationChart').getContext('2d');
    new Chart(segmentationCtx, {
        type: 'doughnut',
        data: {
            labels: ['New', 'One-time', 'Repeat', 'Loyal'],
            datasets: [{
                data: [
                    {{ $customerSegmentation['new'] }},
                    {{ $customerSegmentation['one_time'] }},
                    {{ $customerSegmentation['repeat'] }},
                    {{ $customerSegmentation['loyal'] }}
                ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush

@push('styles')
<style>
    .widget-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        opacity: 0.3;
    }
</style>
@endpush
