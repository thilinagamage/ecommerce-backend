@extends('layouts.admin.layout')

@section('content')
<div class="page-content">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Edit Tag</h4>
        <a href="{{ route('products.tags.index') }}" class="btn btn-secondary">
            ← Back to Tags
        </a>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('products.tags.update', $tag->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">

                {{-- Tag Name --}}
                <div class="mb-3">
                    <label class="form-label">
                        Tag Name <span class="text-danger">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name', $tag->name) }}"
                           required>
                </div>

                {{-- Slug --}}
                <div class="mb-3">
                    <label class="form-label">Slug</label>
                    <input type="text"
                           name="slug"
                           class="form-control"
                           value="{{ old('slug', $tag->slug) }}">
                    <small class="text-muted">
                        Changing the slug will affect SEO URLs.
                    </small>
                </div>

                {{-- Description --}}
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description"
                              class="form-control"
                              rows="4">{{ old('description', $tag->description) }}</textarea>
                </div>

            </div>

            {{-- Form Actions --}}
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">
                    Update Tag
                </button>
                <a href="{{ route('products.tags.index') }}" class="btn btn-light">
                    Cancel
                </a>
            </div>
        </div>

    </form>

</div>
@endsection
