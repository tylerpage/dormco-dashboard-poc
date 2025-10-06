@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Orders</h3>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#saveViewModal">
                            Save Current View
                        </button>
                        <!-- <a href="{{ route('orders.create') }}" class="btn btn-primary">Add Order</a> -->
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('orders.index') }}" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="search" class="form-label">Search</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Name, email, or order number">
                            </div>
                            <div class="col-md-3">
                                <label for="school_id" class="form-label">School</label>
                                <select class="form-select" id="school_id" name="school_id">
                                    <option value="">All Schools</option>
                                    <option value="none" {{ request('school_id') === 'none' ? 'selected' : '' }}>No School Assigned</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                                            {{ $school->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">All Statuses</option>
                                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="picked" {{ request('status') === 'picked' ? 'selected' : '' }}>Picked</option>
                                    <option value="packed" {{ request('status') === 'packed' ? 'selected' : '' }}>Packed</option>
                                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="verified" class="form-label">Verified</label>
                                <select class="form-select" id="verified" name="verified">
                                    <option value="">All Orders</option>
                                    <option value="yes" {{ request('verified') === 'yes' ? 'selected' : '' }}>Verified Only</option>
                                    <option value="no" {{ request('verified') === 'no' ? 'selected' : '' }}>Not Verified</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Clear</a>
                            </div>
                        </div>
                    </form>

                    <!-- Saved Views Tabs -->
                    @if($savedViews->count() > 0)
                    <div class="mb-3">
                        <small class="text-muted">Your Personal Views:</small>
                        <ul class="nav nav-pills" id="savedViewsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="all-tab" data-bs-toggle="pill" data-bs-target="#all" type="button" role="tab">
                                    All Orders <span class="badge bg-secondary ms-1">{{ $orders->total() }}</span>
                                </button>
                            </li>
                            @foreach($savedViews as $view)
                            <li class="nav-item" role="presentation">
                                <div class="d-flex align-items-center">
                                    <a class="nav-link" href="{{ route('orders.load-view', $view) }}">
                                        {{ $view->name }} <span class="badge bg-primary ms-1">{{ $view->orders_count ?? 0 }}</span>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger ms-2" 
                                            onclick="deleteView({{ $view->id }}, '{{ $view->name }}')"
                                            title="Delete this view">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <!-- Orders Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>School</th>
                                    <th>Items</th>
                                    <th>Status</th>
                                    <th>Verified</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="text-decoration-none">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->customer_email }}</td>
                                    <td>
                                        @if($order->school)
                                            <span class="badge bg-info">{{ $order->school->name }}</span>
                                        @else
                                            <span class="badge bg-warning">No School</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $order->item_count }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'shipped' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($order->verified)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Verified
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-times"></i> Not Verified
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $order->created_at->format('M j, Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary">View</a>
                                            <a href="{{ route('orders.edit', $order) }}" class="btn btn-outline-secondary">Edit</a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">No orders found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save View Modal -->
<div class="modal fade" id="saveViewModal" tabindex="-1" aria-labelledby="saveViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('orders.save-view') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Save Personal View</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="viewName" class="form-label">View Name</label>
                        <input type="text" class="form-control" id="viewName" name="name" required>
                        <div class="form-text">This view will be saved to your personal collection and only visible to you.</div>
                    </div>
                    <input type="hidden" name="filters" value="{{ json_encode(request()->all()) }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save View</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function deleteView(viewId, viewName) {
    if (confirm('Are you sure you want to delete the view "' + viewName + '"? This action cannot be undone.')) {
        // Create a form to submit the delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("orders.delete-view", ":viewId") }}'.replace(':viewId', viewId);
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add method override for DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Submit the form
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

@endsection
