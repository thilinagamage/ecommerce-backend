@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    @if(session('success'))
         <div class="alert border-0 bg-light-success alert-dismissible fade show py-2">
              <div class="d-flex align-items-center">
                      <div class="fs-3 text-success"><i class="bi bi-check-circle-fill"></i>
              </div>
                <div class="ms-3">
                     <div class="text-success"> {{ session('success') }}</div>
                 </div>
            </div>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0">Categories</h5>
                </div>
                <div class="d-flex align-items-center justify-content-between gap-3">

                    {{-- Search Form --}}
                    <form action="{{ route('products.categories.index') }}"  id="categorySearch" method="GET" class="d-flex">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search categories...">
                        <button type="submit" class="btn btn-primary ms-2">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>

                    {{-- Parent Filter --}}
                    <form action="{{ route('products.categories.index') }}" method="GET" class="d-flex">
                        <select name="parent_id" class="form-select">
                            <option value="">All Parents</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-primary ms-2">Filter</button>
                        <a href="{{ route('products.categories.index') }}">
                            <button type="button" class="btn btn-outline-dark ms-2">Reset</button>
                        </a>
                    </form>

                    {{-- Add Category --}}
                    <a href="{{ route('products.categories.create') }}" class="btn btn-primary align-items-center ms-1">
                        Add Category
                    </a>

                </div>
            </div>

            {{-- Categories Table --}}
            <div class="table-responsive mt-3">
                <table id="categories-table" class="table align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Parent</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                        <tr>
                            <td>
                                @if($category->category_image)
                                    <img src="{{ $category->category_image ? asset('storage/'.$category->category_image) : 'https://via.placeholder.com/150' }}" alt="{{ $category->name }}" width="100">

                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->parent?->name ?? '-' }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>
                                @if($category->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions d-flex align-items-center gap-2 fs-6">
                                    {{-- View --}}
                                    <a href="{{ route('products.categories.show', $category) }}" class="text-primary" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="View">
                                        <button type="submit" class="btn btn-outline-info"><i class="bi bi-eye-fill"></i></button>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('products.categories.edit', $category->id) }}" class="text-warning" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit">
                                        <button type="submit" class="btn btn-outline-warning"><i class="bi bi-pencil-fill"></i></button>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('products.categories.destroy', $category) }}" method="POST"
                                        style="display:inline;" class="text-danger" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Delete">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="mt-3">
                    {{ $categories->withQueryString()->links() }}
                </div>
            </div>

        </div>
    </div>
</main>
@endsection
@push('datatable-scripts')
    <script src="{{ asset('assets/js/datatables/categories.index.js') }}"></script>
@endpush
