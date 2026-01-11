@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="card">
        <div class="card-body">

            <!-- Header -->
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Category Details</h5>
                <div class="d-flex align-items-center gap-2">

                    <a href="{{ route('products.categories.index') }}" class="btn btn-outline-dark">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>

                    <a href="{{ route('products.categories.edit', $category) }}" class="btn btn-warning">
                        <i class="bi bi-pencil-fill"></i> Edit
                    </a>

                    <form action="{{ route('products.categories.destroy', $category) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to delete this category?')">
                            <i class="bi bi-trash-fill"></i> Delete
                        </button>
                    </form>

                </div>
            </div>

            <hr class="my-3">

            <!-- Category Details -->
            <div class="row g-4">
                <!-- Image -->
                <div class="col-md-4">
                    <div class="card">
                      <img src="{{ $category->category_image ? asset('storage/'.$category->category_image) : 'https://via.placeholder.com/150' }}"
                      alt="{{ $category->name }}">
                        <div class="card-body text-center">
                            <h6 class="card-title">{{ $category->name }}</h6>
                        </div>
                    </div>
                </div>

                <!-- Info -->
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th>Name:</th>
                                <td>{{ $category->name }}</td>
                            </tr>
                            <tr>
                                <th>Slug:</th>
                                <td>{{ $category->slug }}</td>
                            </tr>
                            <tr>
                                <th>Parent Category:</th>
                                <td>{{ $category->parent ? $category->parent->name : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($category->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Position:</th>
                                <td>{{ $category->position }}</td>
                            </tr>
                            <tr>
                                <th>Meta Title:</th>
                                <td>{{ $category->meta_title ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Meta Description:</th>
                                <td>{{ $category->meta_description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Meta Keywords:</th>
                                <td>{{ $category->meta_keywords ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Canonical URL:</th>
                                <td>{{ $category->canonical_url ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>SEO Index:</th>
                                <td>{{ $category->seo_index ? 'Index' : 'No Index' }}</td>
                            </tr>
                            <tr>
                                <th>SEO Follow:</th>
                                <td>{{ $category->seo_follow ? 'Follow' : 'No Follow' }}</td>
                            </tr>
                            <tr>
                                <th>Description:</th>
                                <td>{!! $category->description !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</main>
@endsection
