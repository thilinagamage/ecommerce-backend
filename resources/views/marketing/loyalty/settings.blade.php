@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">Loyalty Program Settings</h4>
                        <p class="text-muted mb-0">Configure your loyalty program rules and settings</p>
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

                <form action="{{ route('marketing.loyalty.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Points Earning Rules --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bx bx-star"></i> Points Earning Rules</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Points per Dollar Spent</label>
                                    <input type="number" name="points_per_dollar" class="form-control" value="{{ old('points_per_dollar', $settings['points_per_dollar'] ?? 1) }}" min="0" step="0.01">
                                    <small class="text-muted">How many points customers earn per $1 spent</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Points Calculation Method</label>
                                    <select name="calculation_method" class="form-select">
                                        <option value="per_dollar" {{ ($settings['calculation_method'] ?? 'per_dollar') == 'per_dollar' ? 'selected' : '' }}>Per Dollar</option>
                                        <option value="percentage" {{ ($settings['calculation_method'] ?? 'per_dollar') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                        <option value="fixed" {{ ($settings['calculation_method'] ?? 'per_dollar') == 'fixed' ? 'selected' : '' }}>Fixed Per Order</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Points Redemption --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bx bx-gift"></i> Points Redemption</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Points Required for $1 Off</label>
                                    <input type="number" name="points_per_dollar_off" class="form-control" value="{{ old('points_per_dollar_off', $settings['points_per_dollar_off'] ?? 100) }}" min="1">
                                    <small class="text-muted">How many points equal $1 discount</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Minimum Points to Redeem</label>
                                    <input type="number" name="minimum_points_redemption" class="form-control" value="{{ old('minimum_points_redemption', $settings['minimum_points_redemption'] ?? 100) }}" min="0">
                                    <small class="text-muted">Minimum points balance required to redeem</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Points Expiration --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bx bx-time"></i> Points Expiration</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="enable_expiration" id="enableExpiration" value="1" {{ ($settings['enable_expiration'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="enableExpiration">
                                        Enable Points Expiration
                                    </label>
                                </div>
                            </div>
                            <div class="row" id="expirationSettings">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Expiration Period</label>
                                    <div class="input-group">
                                        <input type="number" name="expiration_months" class="form-control" value="{{ old('expiration_months', $settings['expiration_months'] ?? 12) }}" min="1">
                                        <span class="input-group-text">months</span>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Expiration Warning (days before)</label>
                                    <input type="number" name="expiration_warning_days" class="form-control" value="{{ old('expiration_warning_days', $settings['expiration_warning_days'] ?? 30) }}" min="1">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Program Status --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bx bx-cog"></i> Program Status</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="program_active" id="programActive" value="1" {{ ($settings['program_active'] ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="programActive">
                                        <strong>Loyalty Program Active</strong>
                                    </label>
                                </div>
                                <small class="text-muted">Disable to pause point earning and redemption</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('marketing.loyalty.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Save Settings
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
    document.getElementById('enableExpiration').addEventListener('change', function() {
        const expirationSettings = document.getElementById('expirationSettings');
        if (this.checked) {
            expirationSettings.style.display = 'flex';
        } else {
            expirationSettings.style.display = 'none';
        }
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const enableExpiration = document.getElementById('enableExpiration');
        const expirationSettings = document.getElementById('expirationSettings');
        if (!enableExpiration.checked) {
            expirationSettings.style.display = 'none';
        }
    });
</script>
@endpush
