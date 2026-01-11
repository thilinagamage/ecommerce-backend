@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0">Edit Category</h5>
                <a href="{{ route('products.categories.index') }}" class="btn btn-outline-dark">
                    Back to Categories
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

            <form action="{{ route('products.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Basic Info --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" name="name" id="name" class="form-control"
                           value="{{ old('name', $category->name) }}" required>
                </div>

                <div class="mb-3">
                    <label for="parent_id" class="form-label">Parent Category</label>
                    <select name="parent_id" id="parent_id" class="form-select">
                        <option value="">-- None --</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}"
                                {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Category Image</label>
                    <input type="file" name="image" id="image" class="form-control">
                    @if($category->image)
                        <img src="{{ asset('storage/'.$category->image) }}" alt="Category Image" class="mt-2" width="150">
                    @endif
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="status" id="status" class="form-check-input"
                           value="1" {{ old('status', $category->status) ? 'checked' : '' }}>
                    <label class="form-check-label" for="status">Active</label>
                </div>

                {{-- SEO Section --}}
                <div class="mb-3">
                    <h6>SEO Settings</h6>
                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control"
                               value="{{ old('slug', $category->slug) }}">
                    </div>
                    <div class="mb-3">
                        <label for="meta_title" class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" id="meta_title" class="form-control"
                               value="{{ old('meta_title', $category->meta_title) }}">
                    </div>
                    <div class="mb-3">
                        <label for="meta_description" class="form-label">Meta Description</label>
                        <textarea name="meta_description" id="meta_description" class="form-control" rows="3">{{ old('meta_description', $category->meta_description) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="meta_keywords" class="form-label">Meta Keywords</label>
                        <input type="text" name="meta_keywords" id="meta_keywords" class="form-control"
                               value="{{ old('meta_keywords', $category->meta_keywords) }}">
                    </div>
                    <div class="mb-3">
                        <label for="canonical_url" class="form-label">Canonical URL</label>
                        <input type="text" name="canonical_url" id="canonical_url" class="form-control"
                               value="{{ old('canonical_url', $category->canonical_url) }}">
                    </div>
                    <div class="mb-3 d-flex gap-3">
                        <div class="form-check">
                            <input type="checkbox" name="seo_index" id="seo_index" class="form-check-input" value="1"
                                   {{ old('seo_index', $category->seo_index) ? 'checked' : '' }}>
                            <label class="form-check-label" for="seo_index">Index</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="seo_follow" id="seo_follow" class="form-check-input" value="1"
                                   {{ old('seo_follow', $category->seo_follow) ? 'checked' : '' }}>
                            <label class="form-check-label" for="seo_follow">Follow</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update Category</button>
            </form>
        </div>
    </div>
</main>
@endsection
