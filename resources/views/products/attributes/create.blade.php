@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Add New Attribute</h5>
                <a href="{{ route('products.attributes.index') }}" class="btn btn-outline-dark">
                    <i class="bi bi-arrow-left"></i> Back
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

            <form action="{{ route('products.attributes.store') }}" method="POST">
                @csrf


                <div class="mb-3">
                    <label for="name" class="form-label">Attribute Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" id="name" required>
                </div>

                <div class="mb-3">
                    <label for="values" class="form-label">Values <span class="text-danger">*</span></label>
                    <input type="text" name="values" value="{{ old('values') }}" class="form-control" id="values" placeholder="Comma separated e.g. Red,Blue,Green" required>
                    <small class="text-muted">Enter all possible values separated by commas.</small>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Attribute
                    </button>
                </div>
            </form>

            {{-- Existing Attributes List --}}
            @if($attributes->count())
                <hr class="my-4">
                <h6>Existing Attributes</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Values</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attributes as $attribute)
                        <tr>
                            <td>{{ $attribute->name }}</td>
                            <td>{{ implode(', ', $attribute->values->pluck('value')->toArray()) }}</td>
                            <td>
                                <a href="{{ route('products.attributes.edit', $attribute->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('products.attributes.destroy', $attribute->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this attribute?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>
    </div>
</main>
@endsection
