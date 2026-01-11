@extends('layouts.admin.layout')

@section('content')
<div class="page-content">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Product Tags</h4>
        <a href="{{ route('products.tags.create') }}" class="btn btn-primary">
            + Add New Tag
        </a>
    </div>

    {{-- Success Message --}}
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

    {{-- Tags Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th width="10%">Products</th>
                            <th width="15%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tags as $index => $tag)
                            <tr>
                                <td>{{ $tags->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $tag->name }}</strong>
                                </td>
                                <td>
                                    <code>{{ $tag->slug }}</code>
                                </td>
                                <td>
                                    {{ Str::limit($tag->description, 60) }}
                                </td>
                                <td class="text-center">
                                    {{ $tag->products()->count() }}
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('products.tags.edit', $tag->id) }}"
                                           class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                        <form action="{{ route('products.tags.destroy', $tag->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this tag?')">
                                                Delete
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No tags found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $tags->links() }}
            </div>
        </div>
    </div>

</div>
@endsection
