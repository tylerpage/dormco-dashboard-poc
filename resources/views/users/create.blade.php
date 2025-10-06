@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Create New User</h2>
                    <p class="text-muted mb-0">Add a new user to the system</p>
                </div>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Back to Users</a>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">User Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('users.store') }}" method="POST">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" value="{{ old('email') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" name="password" required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" 
                                                   id="password_confirmation" name="password_confirmation" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                                <option value="">Select Role</option>
                                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                                <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff</option>
                                                <option value="school" {{ old('role') === 'school' ? 'selected' : '' }}>School</option>
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Assigned Schools</label>
                                    <div class="row">
                                        @foreach($schools as $school)
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="school_{{ $school->id }}" name="assigned_schools[]" 
                                                       value="{{ $school->id }}" 
                                                       {{ in_array($school->id, old('assigned_schools', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="school_{{ $school->id }}">
                                                    {{ $school->name }}
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Permissions</label>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="perm_orders" name="permissions[]" value="orders"
                                                       {{ in_array('orders', old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_orders">Orders</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="perm_pallets" name="permissions[]" value="pallets"
                                                       {{ in_array('pallets', old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_pallets">Pallets</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="perm_schools" name="permissions[]" value="schools"
                                                       {{ in_array('schools', old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_schools">Schools</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="perm_users" name="permissions[]" value="users"
                                                       {{ in_array('users', old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_users">Users</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" 
                                                       id="perm_exports" name="permissions[]" value="exports"
                                                       {{ in_array('exports', old('permissions', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_exports">Exports</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Create User</button>
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
