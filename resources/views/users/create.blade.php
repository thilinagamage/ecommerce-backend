@extends('layouts.admin.layout')

@section('content')

<main class="page-content">
    <div class="card">
        <div class="card-body">

            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0">Add User</h5>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    Back
                </a>
            </div>

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="row g-3">

                    {{-- Name --}}
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input
                            type="text"
                            name="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            placeholder="Enter name"
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="Enter email"
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="col-md-6">
                        <label class="form-label">Password</label>
                        <input
                            type="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Enter password"
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="col-md-6">
                        <label class="form-label">Confirm Password</label>
                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Confirm password"
                        >
                    </div>

                    {{-- Role --}}
                    <div class="col-md-6">
                        <label class="form-label">Role</label>
                        <select
                            name="role"
                            class="form-select @error('role') is-invalid @enderror"
                        >
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}"
                                    {{ old('role') == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        Create User
                    </button>
                </div>

            </form>

        </div>
    </div>
</main>

@endsection
