@extends('layouts.admin.layout')

@section('content')
    <main class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="mb-0">Users</h5>
                    </div>
                    <div class="d-flex align-items-center justify-content-between gap-3">

                    <form action="{{ route('users.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Search users...">
                        <button type="submit" class="btn btn-primary ms-2">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                    <form action="{{ route('users.index') }}" method="GET"  class="d-flex">
                        <select name="role" class="form-select">
                            <option value="">All Roles</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-primary  ms-2">Filter</button>
                        <a href="{{ route('users.index') }}"> <button type="submit" class="btn btn-outline-dark ms-2">Reset</button></a>
                    </form>
                    {{-- @can('create_users') --}}
                    <a href="{{ route('users.create') }}" class="btn btn-primary align-items-center ms-1">
                        Add User
                    </a>
                    {{-- @endcan --}}
                    </div>
                </div>
                <div class="table-responsive mt-3">
                    <table id="users-table" class="table align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th width="180">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        {{ $user->getRoleNames()->first() ?? 'N/A' }}
                                    </td>
                                    <td>
                                        <div class="table-actions d-flex align-items-center gap-2 fs-6">
                                            <a href="javascript:;" class="text-primary" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="Views">
                                                <button type="submit" class="btn btn-outline-info"><i
                                                        class="bi bi-eye-fill"></i></button>

                                            </a>
                                            {{-- @can('edit_users') --}}
                                            <a href="{{ route('users.edit', $user) }}" class="text-warning"
                                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="Edit">
                                                <button type="submit" class="btn btn-outline-warning"><i
                                                        class="bi bi-pencil-fill"></i></button>

                                            </a>
                                            {{-- @endcan --}}

                                            <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                style="display:inline; " class="text-danger" data-bs-toggle="tooltip"
                                                data-bs-placement="bottom" title="Delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger"><i
                                                        class="bi bi-trash-fill"></i></button>

                                            </form>


                                        </div>
                                        {{-- @endcan --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
@endsection
@push('datatable-scripts')
    <script src="{{ asset('assets/js/datatables/users.index.js') }}"></script>
@endpush
