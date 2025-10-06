@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>{{ $school->name }}</h2>
                    <p class="text-muted mb-0">School Code: {{ $school->code }}</p>
                </div>
                <div>
                    <a href="{{ route('schools.index') }}" class="btn btn-outline-secondary">Back to Schools</a>
                    <a href="{{ route('schools.edit', $school) }}" class="btn btn-primary">Edit School</a>
                </div>
            </div>

            <div class="row">
                <!-- School Details -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">School Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Status:</strong>
                                    @if($school->is_active)
                                        <span class="badge bg-success ms-2">Active</span>
                                    @else
                                        <span class="badge bg-secondary ms-2">Inactive</span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <strong>School Code:</strong>
                                    <code class="ms-2">{{ $school->code }}</code>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Contact Email:</strong>
                                    @if($school->contact_email)
                                        <a href="mailto:{{ $school->contact_email }}" class="ms-2">{{ $school->contact_email }}</a>
                                    @else
                                        <span class="text-muted ms-2">No email provided</span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <strong>Contact Phone:</strong>
                                    @if($school->contact_phone)
                                        <a href="tel:{{ $school->contact_phone }}" class="ms-2">{{ $school->contact_phone }}</a>
                                    @else
                                        <span class="text-muted ms-2">No phone provided</span>
                                    @endif
                                </div>
                            </div>
                            @if($school->address)
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <strong>Address:</strong>
                                    <div class="mt-2">
                                        <pre class="mb-0">{{ $school->address }}</pre>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if($school->notes)
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <strong>Notes:</strong>
                                    <div class="mt-2">
                                        <p class="mb-0">{{ $school->notes }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Orders ({{ $school->orders->count() }})</h5>
                            <a href="{{ route('orders.index', ['school_id' => $school->id]) }}" class="btn btn-sm btn-outline-primary">
                                View All Orders
                            </a>
                        </div>
                        <div class="card-body">
                            @forelse($school->orders->take(5) as $order)
                            <div class="row border-bottom py-2">
                                <div class="col-md-4">
                                    <a href="{{ route('orders.show', $order) }}" class="text-decoration-none">
                                        {{ $order->order_number }}
                                    </a>
                                </div>
                                <div class="col-md-4">{{ $order->customer_name }}</div>
                                <div class="col-md-2">
                                    <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'shipped' ? 'primary' : 'warning') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <div class="col-md-2 text-end">
                                    <small class="text-muted">{{ $order->created_at->format('M j, Y') }}</small>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">No orders found for this school.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Recent Pallets -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Recent Pallets ({{ $school->pallets->count() }})</h5>
                            <a href="{{ route('pallets.index', ['school_id' => $school->id]) }}" class="btn btn-sm btn-outline-primary">
                                View All Pallets
                            </a>
                        </div>
                        <div class="card-body">
                            @forelse($school->pallets->take(5) as $pallet)
                            <div class="row border-bottom py-2">
                                <div class="col-md-4">
                                    <a href="{{ route('pallets.show', $pallet) }}" class="text-decoration-none">
                                        {{ $pallet->pallet_number }}
                                    </a>
                                </div>
                                <div class="col-md-4">{{ $pallet->location ?: 'No location' }}</div>
                                <div class="col-md-2">
                                    <span class="badge bg-{{ $pallet->status === 'delivered' ? 'success' : ($pallet->status === 'shipped' ? 'primary' : 'warning') }}">
                                        {{ ucfirst($pallet->status) }}
                                    </span>
                                </div>
                                <div class="col-md-2 text-end">
                                    <small class="text-muted">{{ $pallet->created_at->format('M j, Y') }}</small>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">No pallets found for this school.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- School Summary -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">School Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>School Name:</strong><br>
                                {{ $school->name }}
                            </div>
                            <div class="mb-3">
                                <strong>School Code:</strong><br>
                                <code>{{ $school->code }}</code>
                            </div>
                            <div class="mb-3">
                                <strong>Status:</strong><br>
                                @if($school->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>
                            <div class="mb-3">
                                <strong>Total Orders:</strong><br>
                                <span class="badge bg-info">{{ $school->orders->count() }}</span>
                            </div>
                            <div class="mb-3">
                                <strong>Total Pallets:</strong><br>
                                <span class="badge bg-warning">{{ $school->pallets->count() }}</span>
                            </div>
                            <div class="mb-3">
                                <strong>Created:</strong><br>
                                {{ $school->created_at->format('M j, Y g:i A') }}
                            </div>
                            <div class="mb-3">
                                <strong>Last Updated:</strong><br>
                                {{ $school->updated_at->format('M j, Y g:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
