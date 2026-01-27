@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="card-title mb-1">Loyalty Tiers</h4>
                        <p class="text-muted mb-0">Manage your loyalty program tiers and rewards</p>
                    </div>
                    <div>
                        <a href="{{ route('marketing.loyalty.index') }}" class="btn btn-secondary me-2">
                            <i class="bx bx-arrow-back"></i> Back to Loyalty
                        </a>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTierModal">
                            <i class="bx bx-plus"></i> Create New Tier
                        </button>
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

                {{-- Tiers List --}}
                @if($tiers->count() > 0)
                    <div class="row">
                        @foreach($tiers as $tier)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 border {{ $tier->is_active ? 'border-primary' : 'border-secondary' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="card-title mb-1">
                                                    @if($tier->name == 'Bronze')
                                                        <i class="bx bx-medal text-warning"></i>
                                                    @elseif($tier->name == 'Silver')
                                                        <i class="bx bx-medal text-secondary"></i>
                                                    @elseif($tier->name == 'Gold')
                                                        <i class="bx bx-medal" style="color: #FFD700;"></i>
                                                    @elseif($tier->name == 'Platinum')
                                                        <i class="bx bx-crown text-info"></i>
                                                    @else
                                                        <i class="bx bx-star text-primary"></i>
                                                    @endif
                                                    {{ $tier->name }}
                                                </h5>
                                                <span class="badge bg-{{ $tier->is_active ? 'success' : 'secondary' }}">
                                                    {{ $tier->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                                    <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" href="#"
                                                           data-bs-toggle="modal"
                                                           data-bs-target="#editTierModal{{ $tier->id }}">
                                                            <i class="bx bx-edit"></i> Edit
                                                        </a>
                                                    </li>
                                                    @if($tier->users_count == 0)
                                                        <li>
                                                            <form action="{{ route('marketing.loyalty.tiers.destroy', $tier) }}"
                                                                  method="POST"
                                                                  onsubmit="return confirm('Are you sure you want to delete this tier?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="bx bx-trash"></i> Delete
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>

                                        <p class="text-muted small mb-3">{{ $tier->description }}</p>

                                        <div class="tier-details">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Points Required:</span>
                                                <strong>{{ number_format($tier->points_required) }} pts</strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Discount:</span>
                                                <strong class="text-success">{{ $tier->discount_percentage }}%</strong>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Members:</span>
                                                <strong class="text-primary">{{ number_format($tier->users_count) }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Edit Tier Modal --}}
                            <div class="modal fade" id="editTierModal{{ $tier->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('marketing.loyalty.tiers.update', $tier) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Tier: {{ $tier->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Tier Name</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $tier->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description" class="form-control" rows="2">{{ $tier->description }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Points Required</label>
                                                    <input type="number" name="points_required" class="form-control" value="{{ $tier->points_required }}" required min="0">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Discount Percentage</label>
                                                    <input type="number" name="discount_percentage" class="form-control" value="{{ $tier->discount_percentage }}" required min="0" max="100" step="0.01">
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $tier->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label">Active</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update Tier</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bx bx-trophy fs-1 text-muted"></i>
                        <p class="text-muted">No tiers created yet</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTierModal">
                            <i class="bx bx-plus"></i> Create Your First Tier
                        </button>
                    </div>
                @endif

            </div>
        </div>
    </main>

    {{-- Create Tier Modal --}}
    <div class="modal fade" id="createTierModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('marketing.loyalty.tiers.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Create New Tier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tier Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g., Bronze, Silver, Gold" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Brief description of this tier"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Points Required <span class="text-danger">*</span></label>
                            <input type="number" name="points_required" class="form-control" value="0" required min="0" placeholder="e.g., 500">
                            <small class="text-muted">Minimum points needed to reach this tier</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Discount Percentage <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="discount_percentage" class="form-control" value="5" required min="0" max="100" step="0.01">
                                <span class="input-group-text">%</span>
                            </div>
                            <small class="text-muted">Discount offered to members of this tier</small>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                                <label class="form-check-label">Active</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Tier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
