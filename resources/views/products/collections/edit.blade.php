@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4>Edit Collection</h4>
            <a href="{{ route('products.collections.index') }}" class="btn btn-secondary">
                Back
            </a>
        </div>

        {{-- Edit Form --}}
        <form action="{{ route('products.collections.update', $collection) }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">

                {{-- LEFT COLUMN --}}
                <div class="col-lg-8">

                    {{-- Basic Info --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Basic Information</strong>
                        </div>
                        <div class="card-body">

                            {{-- Name --}}
                            <div class="mb-3">
                                <label class="form-label">Collection Name *</label>
                                <input type="text"
                                       name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $collection->name) }}"
                                       required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Slug --}}
                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text"
                                       name="slug"
                                       class="form-control @error('slug') is-invalid @enderror"
                                       value="{{ old('slug', $collection->slug) }}">
                                <small class="text-muted">
                                    Leave empty to auto-generate
                                </small>
                                @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Description --}}
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description"
                                          rows="4"
                                          class="form-control">{{ old('description', $collection->description) }}</textarea>
                            </div>

                        </div>
                    </div>

                    {{-- Schedule --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Schedule</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Start Date</label>
                                    <input type="date"
                                           name="start_date"
                                           class="form-control"
                                           value="{{ old('start_date', $collection->start_date) }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">End Date</label>
                                    <input type="date"
                                           name="end_date"
                                           class="form-control"
                                           value="{{ old('end_date', $collection->end_date) }}">
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN --}}
                <div class="col-lg-4">

                    {{-- Status --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Status</strong>
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="status"
                                       value="1"
                                       {{ old('status', $collection->status) ? 'checked' : '' }}>
                                <label class="form-check-label">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Banner Image --}}
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>Banner Image</strong>
                        </div>
                        <div class="card-body">

                            @if($collection->banner_image)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $collection->banner_image) }}"
                                         class="img-fluid rounded">
                                </div>
                            @endif

                            <input type="file"
                                   name="banner_image"
                                   class="form-control">
                            <small class="text-muted">
                                Recommended size: 1200×400
                            </small>

                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="card">
                        <div class="card-body d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                Update Collection
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </form>

    </div>
</main>
@endsection
