@extends('layouts.admin.layout')

@section('content')
<main class="page-content">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0">Edit Attribute: {{ $attribute->name }}</h5>
                <a href="{{ route('products.attributes.create') }}" class="btn btn-outline-dark">
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

            <form action="{{ route('products.attributes.update', $attribute->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Attribute Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $attribute->name) }}" class="form-control" id="name" required>
                </div>

                <div class="mb-3">
                    <label for="values" class="form-label">Values <span class="text-danger">*</span></label>
                    <input type="text" name="values"
                           value="{{ old('values', $attribute->values->pluck('value')->implode(',')) }}"
                           class="form-control" id="values" placeholder="Comma separated values e.g. Red,Blue,Green" required>
                    <small class="text-muted">Separate values with commas.</small>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-pencil-square"></i> Update Attribute
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection
