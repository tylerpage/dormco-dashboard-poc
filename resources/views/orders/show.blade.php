@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
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

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Order #{{ $order->order_number }}</h2>
                    <p class="text-muted mb-0">{{ $order->customer_name }} - {{ $order->customer_email }}</p>
                </div>
                <div>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Back to Orders</a>
                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-primary">Edit Order</a>
                    @if(auth()->user()->role === 'school' && auth()->user()->assigned_schools && !in_array($order->school_id, auth()->user()->assigned_schools))
                        <!-- School user cannot verify this order -->
                    @else
                    <form action="{{ route('orders.toggle-verification', $order) }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="verified" value="{{ $order->verified ? '0' : '1' }}">
                        <button type="submit" class="btn btn-{{ $order->verified ? 'warning' : 'success' }}">
                            <i class="fas fa-{{ $order->verified ? 'times' : 'check' }}"></i>
                            {{ $order->verified ? 'Unverify' : 'Verify' }} Order
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            <div class="row">
                <!-- Order Details -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Status:</strong>
                                    <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'shipped' ? 'primary' : 'warning') }} ms-2">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Verified:</strong>
                                    @if($order->verified)
                                        <span class="badge bg-success ms-2">
                                            <i class="fas fa-check"></i> Verified
                                        </span>
                                        @if($order->verified_at)
                                            <br><small class="text-muted">Verified on {{ $order->verified_at->format('M j, Y g:i A') }}</small>
                                        @endif
                                        @if($order->verifiedBy)
                                            <br><small class="text-muted">by {{ $order->verifiedBy->name }}</small>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary ms-2">
                                            <i class="fas fa-times"></i> Not Verified
                                        </span>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <strong>School:</strong>
                                    @if($order->school)
                                        <span class="badge bg-info ms-2">{{ $order->school->name }}</span>
                                    @else
                                        <span class="badge bg-warning ms-2">No School Assigned</span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Tracking Number:</strong>
                                    <span class="ms-2">{{ $order->tracking_number ?: 'Not set' }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Pallet Number:</strong>
                                    @if($order->pallet_number)
                                        <a href="{{ route('pallets.show', $order->pallet_number) }}" class="ms-2 text-decoration-none">
                                            {{ $order->pallet_number }} <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @else
                                        <span class="ms-2">Not set</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Shipping Address</h5>
                            @if(auth()->user()->role !== 'school')
                            <a href="{{ route('orders.update-shipping', $order) }}" class="btn btn-sm btn-outline-primary">
                                Update Shipping
                            </a>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="shipping-address">
                                @if($order->shipping_address_1)
                                    <div>{{ $order->shipping_address_1 }}</div>
                                @endif
                                @if($order->shipping_address_2)
                                    <div>{{ $order->shipping_address_2 }}</div>
                                @endif
                                @if($order->shipping_address_3)
                                    <div>{{ $order->shipping_address_3 }}</div>
                                @endif
                                @if($order->shipping_city || $order->shipping_state || $order->shipping_zip)
                                    <div>
                                        @if($order->shipping_city){{ $order->shipping_city }}@endif
                                        @if($order->shipping_city && ($order->shipping_state || $order->shipping_zip)), @endif
                                        @if($order->shipping_state){{ $order->shipping_state }}@endif
                                        @if($order->shipping_state && $order->shipping_zip) @endif
                                        @if($order->shipping_zip){{ $order->shipping_zip }}@endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Order Photos -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Order Photos ({{ $order->photos->count() }})</h5>
                            @if(auth()->user()->role !== 'school')
                            <a href="{{ route('orders.upload-photos', $order) }}" class="btn btn-sm btn-outline-primary">
                                Upload Photos
                            </a>
                            @endif
                        </div>
                        <div class="card-body">
                            @forelse($order->photos as $photo)
                            <div class="row border-bottom py-3">
                                <div class="col-md-3">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Order Photo" 
                                         class="img-thumbnail" style="max-width: 150px; max-height: 150px; cursor: pointer;"
                                         data-bs-toggle="modal" data-bs-target="#imageModal{{ $photo->id }}">
                                </div>
                                <div class="col-md-9">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>Uploaded by:</strong> {{ $photo->uploadedBy->name }}<br>
                                            <strong>Date:</strong> {{ $photo->created_at->format('M j, Y g:i A') }}
                                            @if($photo->notes)
                                                <br><strong>Notes:</strong> {{ $photo->notes }}
                                            @endif
                                        </div>
                                        @if(auth()->user()->role !== 'school')
                                        <div>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deletePhoto({{ $photo->id }}, '{{ $photo->photo_path }}')"
                                                    title="Delete this photo">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Image Modal for {{ $photo->id }} -->
                            <div class="modal fade" id="imageModal{{ $photo->id }}" tabindex="-1" aria-labelledby="imageModal{{ $photo->id }}Label" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Order Photo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                                                 alt="Order Photo" class="img-fluid" style="max-height: 70vh;">
                                            <div class="mt-3">
                                                <strong>Uploaded by:</strong> {{ $photo->uploadedBy->name }}<br>
                                                <strong>Date:</strong> {{ $photo->created_at->format('M j, Y g:i A') }}
                                                @if($photo->notes)
                                                    <br><strong>Notes:</strong> {{ $photo->notes }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            @if(auth()->user()->role !== 'school')
                                            <button type="button" class="btn btn-danger" 
                                                    onclick="deletePhoto({{ $photo->id }}, '{{ $photo->photo_path }}')">
                                                <i class="fas fa-trash"></i> Delete Photo
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">No photos uploaded for this order.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Order Items ({{ $order->items->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @forelse($order->items as $item)
                            <div class="row border-bottom py-3">
                                <div class="col-md-8">
                                    <h6 class="mb-1">{{ $item->item_name }}</h6>
                                    @if($item->description)
                                        <p class="text-muted small mb-1">{{ $item->description }}</p>
                                    @endif
                                    <small class="text-muted">Quantity: {{ $item->quantity }}</small>
                                </div>
                                <div class="col-md-4 text-end">
                                    @if($item->photo_path)
                                        <img src="{{ asset('storage/' . $item->photo_path) }}" alt="{{ $item->item_name }}" 
                                             class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                    @else
                                        <span class="text-muted">No photo</span>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">No items found for this order.</p>
                            @endforelse
                        </div>
                    </div>

                    @if(auth()->user()->role !== 'school')
                    <!-- Order Actions History -->
                    <div class="card">
                        <div class="card-header">
                            <button class="btn btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target="#actionsHistory" aria-expanded="false">
                                <h5 class="mb-0">Order Actions History <i class="fas fa-chevron-down"></i></h5>
                            </button>
                        </div>
                        <div class="collapse" id="actionsHistory">
                            <div class="card-body">
                                @forelse($order->actions as $action)
                                <div class="border-bottom py-2">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $action->description }}</strong>
                                        <small class="text-muted">{{ $action->created_at->format('M j, Y g:i A') }}</small>
                                    </div>
                                    <div class="text-muted small">
                                        Performed by: {{ $action->performedBy->name }}
                                    </div>
                                    @if($action->old_values || $action->new_values)
                                    <div class="mt-2">
                                        @if($action->old_values)
                                        <small class="text-danger">
                                            <strong>Old:</strong> {{ json_encode($action->old_values) }}
                                        </small>
                                        @endif
                                        @if($action->new_values)
                                        <small class="text-success ms-3">
                                            <strong>New:</strong> {{ json_encode($action->new_values) }}
                                        </small>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                @empty
                                <p class="text-muted">No actions recorded for this order.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Order Summary -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Order Number:</strong><br>
                                <code>{{ $order->order_number }}</code>
                            </div>
                            <div class="mb-3">
                                <strong>Customer:</strong><br>
                                {{ $order->customer_name }}<br>
                                <small class="text-muted">{{ $order->customer_email }}</small>
                            </div>
                            <div class="mb-3">
                                <strong>Total Items:</strong><br>
                                {{ $order->item_count }}
                            </div>
                            <div class="mb-3">
                                <strong>Created:</strong><br>
                                {{ $order->created_at->format('M j, Y g:i A') }}
                            </div>
                            @if($order->notes)
                            <div class="mb-3">
                                <strong>Notes:</strong><br>
                                <small>{{ $order->notes }}</small>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
function deletePhoto(photoId, photoPath) {
    if (confirm('Are you sure you want to delete this photo? This action cannot be undone.')) {
        // Create a form to submit the delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("order-photos.delete", ":photoId") }}'.replace(':photoId', photoId);
        
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
