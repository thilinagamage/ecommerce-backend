@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0">Add New Category</h5>
                <a href="{{ route('products.categories.index') }}" class="btn btn-outline-dark">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            {{-- Display Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('products.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                {{-- Basic Info --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" id="name" required>
                </div>

                <div class="mb-3">
                    <label for="parent_id" class="form-label">Parent Category</label>
                    <select name="parent_id" id="parent_id" class="form-select">
                        <option value="">-- None --</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" rows="3" class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Category Image</label>
                    <input type="file" name="image" id="image" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="position" class="form-label">Position</label>
                    <input type="number" name="position" value="{{ old('position', 0) }}" class="form-control" id="position">
                </div>

                {{-- SEO Section --}}
                <div class="mb-3 border p-3 rounded bg-light">
                    <h6 class="mb-3">SEO Settings</h6>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug (URL)</label>
                        <input type="text" name="slug" value="{{ old('slug') }}" class="form-control" id="slug">
                        <small class="text-muted">Optional. Auto-generated from name if empty.</small>
                    </div>

                    <div class="mb-3">
                        <label for="meta_title" class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title') }}" class="form-control" id="meta_title">
                    </div>

                    <div class="mb-3">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea name="meta_description" rows="2" class="form-control" id="meta_description">{{ old('meta_description') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="meta_keywords" class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" class="form-control" id="meta_keywords">
                        <small class="text-muted">Comma-separated keywords</small>
                    </div>

                    <div class="mb-3">
                        <label for="canonical_url" class="form-label">Canonical URL</label>
                        <input type="url" name="canonical_url" value="{{ old('canonical_url') }}" class="form-control" id="canonical_url">
                    </div>

                    <div class="mb-3 d-flex gap-3">
                        <div>
                            <label for="seo_index" class="form-label">Index</label>
                            <select name="seo_index" id="seo_index" class="form-select">
                                <option value="1" {{ old('seo_index', 1) == 1 ? 'selected' : '' }}>Index</option>
                                <option value="0" {{ old('seo_index') == 0 ? 'selected' : '' }}>Noindex</option>
                            </select>
                        </div>
                        <div>
                            <label for="seo_follow" class="form-label">Follow</label>
                            <select name="seo_follow" id="seo_follow" class="form-select">
                                <option value="1" {{ old('seo_follow', 1) == 1 ? 'selected' : '' }}>Follow</option>
                                <option value="0" {{ old('seo_follow') == 0 ? 'selected' : '' }}>Nofollow</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection
