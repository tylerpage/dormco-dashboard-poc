@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>{{ $user->name }}</h2>
                    <p class="text-muted mb-0">{{ $user->email }}</p>
                </div>
                <div>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Back to Users</a>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit User</a>
                </div>
            </div>

            <div class="row">
                <!-- User Details -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">User Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Name:</strong><br>
                                    {{ $user->name }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Email:</strong><br>
                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Role:</strong><br>
                                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'staff' ? 'primary' : 'info') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Created:</strong><br>
                                    {{ $user->created_at->format('M j, Y g:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assigned Schools -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Assigned Schools ({{ $user->schools->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @if($user->schools->count() > 0)
                                <div class="row">
                                    @foreach($user->schools as $school)
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="card-title">{{ $school->name }}</h6>
                                                <p class="card-text">
                                                    <small class="text-muted">{{ $school->code }}</small><br>
                                                    @if($school->contact_email)
                                                        <a href="mailto:{{ $school->contact_email }}" class="text-decoration-none">
                                                            {{ $school->contact_email }}
                                                        </a>
                                                    @endif
                                                </p>
                                                <a href="{{ route('schools.show', $school) }}" class="btn btn-sm btn-outline-primary">View School</a>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No schools assigned to this user.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Permissions -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Permissions</h5>
                        </div>
                        <div class="card-body">
                            @if($user->permissions && count($user->permissions) > 0)
                                <div class="row">
                                    @foreach($user->permissions as $permission)
                                    <div class="col-md-3">
                                        <span class="badge bg-success">{{ ucfirst($permission) }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No specific permissions assigned.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- User Summary -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">User Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Name:</strong><br>
                                {{ $user->name }}
                            </div>
                            <div class="mb-3">
                                <strong>Email:</strong><br>
                                <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                            </div>
                            <div class="mb-3">
                                <strong>Role:</strong><br>
                                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'staff' ? 'primary' : 'info') }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <strong>Assigned Schools:</strong><br>
                                <span class="badge bg-secondary">{{ $user->schools->count() }} schools</span>
                            </div>
                            <div class="mb-3">
                                <strong>Permissions:</strong><br>
                                <span class="badge bg-info">{{ $user->permissions ? count($user->permissions) : 0 }} permissions</span>
                            </div>
                            <div class="mb-3">
                                <strong>Created:</strong><br>
                                {{ $user->created_at->format('M j, Y g:i A') }}
                            </div>
                            <div class="mb-3">
                                <strong>Last Updated:</strong><br>
                                {{ $user->updated_at->format('M j, Y g:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
