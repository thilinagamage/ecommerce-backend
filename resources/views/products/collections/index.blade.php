@extends('layouts.admin.layout')

@section('content')
<div class="page-content">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Collections</h4>
        <a href="{{ route('products.collections.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Collection
        </a>
    </div>

    {{-- Flash Message --}}
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

    {{-- Table --}}
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="60">#</th>
                        <th>Banner</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Active Period</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($collections as $collection)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            {{-- Banner --}}
                            <td>
                                @if($collection->banner_image)
                                    <img src="{{ asset('storage/' . $collection->banner_image) }}"
                                         alt="{{ $collection->name }}"
                                         width="60"
                                         class="rounded">
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Name --}}
                            <td>
                                <strong>{{ $collection->name }}</strong>
                            </td>

                            {{-- Slug --}}
                            <td class="text-muted">
                                {{ $collection->slug }}
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($collection->isActive())
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>

                            {{-- Date Range --}}
                            <td>
                                @if($collection->start_date || $collection->end_date)
                                    <small>
                                        {{ $collection->start_date ?? '—' }}
                                        →
                                        {{ $collection->end_date ?? '—' }}
                                    </small>
                                @else
                                    <span class="text-muted">Always</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('products.collections.edit', $collection->id) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form action="{{ route('products.collections.destroy', $collection->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Delete this collection?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No collections found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $collections->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
