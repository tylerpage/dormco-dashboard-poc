@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Schools</h3>
                    <a href="{{ route('schools.create') }}" class="btn btn-primary">Add School</a>
                </div>
                
                <div class="card-body">
                    <!-- Schools Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Contact Email</th>
                                    <th>Contact Phone</th>
                                    <th>Status</th>
                                    <th>Orders</th>
                                    <th>Pallets</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schools as $school)
                                <tr>
                                    <td>
                                        <a href="{{ route('schools.show', $school) }}" class="text-decoration-none">
                                            {{ $school->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <code>{{ $school->code }}</code>
                                    </td>
                                    <td>
                                        @if($school->contact_email)
                                            <a href="mailto:{{ $school->contact_email }}" class="text-decoration-none">
                                                {{ $school->contact_email }}
                                            </a>
                                        @else
                                            <span class="text-muted">No email</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($school->contact_phone)
                                            <a href="tel:{{ $school->contact_phone }}" class="text-decoration-none">
                                                {{ $school->contact_phone }}
                                            </a>
                                        @else
                                            <span class="text-muted">No phone</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($school->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $school->orders_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning">{{ $school->pallets_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('schools.show', $school) }}" class="btn btn-outline-primary">View</a>
                                            <a href="{{ route('schools.edit', $school) }}" class="btn btn-outline-secondary">Edit</a>
                                            <form action="{{ route('schools.destroy', $school) }}" method="POST" class="d-inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this school?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">No schools found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $schools->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
