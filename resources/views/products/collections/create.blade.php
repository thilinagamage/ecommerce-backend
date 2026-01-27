@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Create Collection</h4>
            <a href="{{ route('products.collections.index') }}" class="btn btn-secondary">
                Back
            </a>
        </div>

        {{-- Form --}}
        <form action="{{ route('products.collections.store') }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf

            <div class="row">
                {{-- LEFT SIDE --}}
                <div class="col-lg-8">

                    {{-- Basic Info --}}
                    <div class="card mb-4">
                        <div class="card-body">

                            {{-- Name --}}
                            <div class="mb-3">
                                <label class="form-label">
                                    Collection Name <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       name="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}"
                                       placeholder="Summer Collection">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Slug --}}
                            <div class="mb-3">
                                <label class="form-label">Slug</label>
                                <input type="text"
                                       name="slug"
                                       class="form-control @error('slug') is-invalid @enderror"
                                       value="{{ old('slug') }}"
                                       placeholder="summer-collection">
                                <small class="text-muted">
                                    Leave empty to auto-generate
                                </small>
                                @error('slug')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description"
                                          rows="4"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="Describe this collection...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>

                </div>

                {{-- RIGHT SIDEBAR --}}
                <div class="col-lg-4">

                    {{-- Status --}}
                    <div class="card mb-4">
                        <div class="card-body">

                            <h6 class="mb-3">Status</h6>

                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                       type="checkbox"
                                       name="status"
                                       value="1"
                                       id="status"
                                       {{ old('status', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="status">
                                    Active
                                </label>
                            </div>

                        </div>
                    </div>

                    {{-- Banner Image --}}
                    <div class="card mb-4">
                        <div class="card-body">

                            <h6 class="mb-3">Banner Image</h6>

                            <input type="file"
                                   name="banner_image"
                                   class="form-control @error('banner_image') is-invalid @enderror">

                            <small class="text-muted">
                                Used for collection landing page & sliders
                            </small>

                            @error('banner_image')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>
                    </div>

                    {{-- Schedule --}}
                    <div class="card mb-4">
                        <div class="card-body">

                            <h6 class="mb-3">Schedule</h6>

                            {{-- Start Date --}}
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="date"
                                       name="start_date"
                                       class="form-control @error('start_date') is-invalid @enderror"
                                       value="{{ old('start_date') }}">
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- End Date --}}
                            <div class="mb-3">
                                <label class="form-label">End Date</label>
                                <input type="date"
                                       name="end_date"
                                       class="form-control @error('end_date') is-invalid @enderror"
                                       value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <small class="text-muted">
                                Leave empty for permanent collections
                            </small>

                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="card">
                        <div class="card-body">
                            <button type="submit" class="btn btn-primary w-100">
                                Create Collection
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </div>
</main>
@endsection
