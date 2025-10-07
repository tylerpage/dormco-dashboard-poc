@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
                <div class="mb-3 mb-md-0 flex-grow-1">
                    <h2>Pallet #{{ $pallet->pallet_number }}</h2>
                    <p class="text-muted mb-0">
                        Created by {{ $pallet->creator->name }} on {{ $pallet->created_at->format('M j, Y g:i A') }}
                    </p>
                </div>
                <div class="d-flex flex-column flex-md-row gap-2">
                    <a href="{{ route('pallets.orders', $pallet) }}" class="btn btn-outline-info w-md-auto">
                        <i class="fas fa-list"></i> View Orders
                    </a>
                    <button class="btn btn-outline-success w-md-auto" onclick="printQRCode('{{ $pallet->pallet_number }}')">
                        <i class="fas fa-qrcode"></i> Print QR Code
                    </button>
                    <a href="{{ route('pallets.index') }}" class="btn btn-outline-secondary w-md-auto">Back to Pallets</a>
                    <a href="{{ route('pallets.edit', $pallet) }}" class="btn btn-primary w-md-auto">Edit Pallet</a>
                </div>
            </div>

            <div class="row">
                <!-- Pallet Details -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Pallet Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Status:</strong>
                                    <span class="badge bg-{{ $pallet->status === 'delivered' ? 'success' : ($pallet->status === 'shipped' ? 'primary' : 'warning') }} ms-2">
                                        {{ ucfirst($pallet->status) }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <strong>School:</strong>
                                    @if($pallet->school)
                                        <span class="badge bg-info ms-2">{{ $pallet->school->name }}</span>
                                    @else
                                        <span class="badge bg-warning ms-2">No School Assigned</span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Location:</strong>
                                    <span class="ms-2">{{ $pallet->location ?: 'Not set' }}</span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Lot:</strong>
                                    <span class="ms-2">{{ $pallet->lot ?: 'Not set' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Shipping Address</h5>
                        </div>
                        <div class="card-body">
                            <div class="shipping-address">
                                @if($pallet->shipping_address_1)
                                    <div>{{ $pallet->shipping_address_1 }}</div>
                                @endif
                                @if($pallet->shipping_address_2)
                                    <div>{{ $pallet->shipping_address_2 }}</div>
                                @endif
                                @if($pallet->shipping_address_3)
                                    <div>{{ $pallet->shipping_address_3 }}</div>
                                @endif
                                @if($pallet->shipping_city || $pallet->shipping_state || $pallet->shipping_zip)
                                    <div>
                                        @if($pallet->shipping_city){{ $pallet->shipping_city }}@endif
                                        @if($pallet->shipping_city && ($pallet->shipping_state || $pallet->shipping_zip)), @endif
                                        @if($pallet->shipping_state){{ $pallet->shipping_state }}@endif
                                        @if($pallet->shipping_state && $pallet->shipping_zip) @endif
                                        @if($pallet->shipping_zip){{ $pallet->shipping_zip }}@endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Pallet Photos -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Pallet Photos ({{ $pallet->photos->count() }})</h5>
                            <a href="{{ route('pallets.upload-photo', $pallet) }}" class="btn btn-sm btn-outline-primary">
                                Upload Photo
                            </a>
                        </div>
                        <div class="card-body">
                            @forelse($pallet->photos as $photo)
                            <div class="row border-bottom py-3">
                                <div class="col-md-4">
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Pallet Photo" 
                                         class="img-thumbnail w-100" style="max-height: 200px; object-fit: cover; cursor: pointer;"
                                         data-bs-toggle="modal" data-bs-target="#imageModal{{ $photo->id }}">
                                </div>
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>Uploaded by {{ $photo->uploadedBy->name }}</strong><br>
                                            <small class="text-muted">{{ $photo->created_at->format('M j, Y g:i A') }}</small>
                                            @if($photo->notes)
                                                <br><strong>Notes:</strong> {{ $photo->notes }}
                                            @endif
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deletePhoto({{ $photo->id }}, '{{ $photo->photo_path }}')"
                                                    title="Delete this photo">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Image Modal for {{ $photo->id }} -->
                            <div class="modal fade" id="imageModal{{ $photo->id }}" tabindex="-1" aria-labelledby="imageModal{{ $photo->id }}Label" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Pallet Photo</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                                                 alt="Pallet Photo" class="img-fluid" style="max-height: 70vh;">
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
                                            <button type="button" class="btn btn-danger" 
                                                    onclick="deletePhoto({{ $photo->id }}, '{{ $photo->photo_path }}')">
                                                <i class="fas fa-trash"></i> Delete Photo
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">No photos uploaded for this pallet.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Assigned Orders -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Assigned Orders ({{ $pallet->orders->count() }})</h5>
                        </div>
                        <div class="card-body">
                            @forelse($pallet->orders as $order)
                            <div class="border-bottom py-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>
                                            <a href="{{ route('orders.show', $order) }}" class="text-decoration-none">
                                                Order #{{ $order->order_number }}
                                            </a>
                                        </strong>
                                        <br>
                                        <small class="text-muted">{{ $order->customer_name }} - {{ $order->customer_email }}</small>
                                        @if($order->school)
                                        <br>
                                        <span class="badge bg-info">{{ $order->school->name }}</span>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'shipped' ? 'primary' : 'warning') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $order->created_at->format('M j, Y') }}</small>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-muted">No orders assigned to this pallet.</p>
                            @endforelse
                        </div>
                    </div>

                    @if(auth()->user()->role !== 'school')
                    <!-- Pallet Actions History -->
                    <div class="card">
                        <div class="card-header">
                            <button class="btn btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target="#palletActionsHistory" aria-expanded="false">
                                <h5 class="mb-0">Pallet Actions History <i class="fas fa-chevron-down"></i></h5>
                            </button>
                        </div>
                        <div class="collapse" id="palletActionsHistory">
                            <div class="card-body">
                                @forelse($pallet->actions as $action)
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
                                <p class="text-muted">No actions recorded for this pallet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Pallet Summary -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Pallet Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Pallet Number:</strong><br>
                                <code>{{ $pallet->pallet_number }}</code>
                            </div>
                            <div class="mb-3">
                                <strong>Status:</strong><br>
                                <span class="badge bg-{{ $pallet->status === 'delivered' ? 'success' : ($pallet->status === 'shipped' ? 'primary' : 'warning') }}">
                                    {{ ucfirst($pallet->status) }}
                                </span>
                            </div>
                            <div class="mb-3">
                                <strong>School:</strong><br>
                                @if($pallet->school)
                                    <span class="badge bg-info">{{ $pallet->school->name }}</span>
                                @else
                                    <span class="text-muted">No School Assigned</span>
                                @endif
                            </div>
                            <div class="mb-3">
                                <strong>Location:</strong><br>
                                {{ $pallet->location ?: 'Not set' }}
                            </div>
                            <div class="mb-3">
                                <strong>Lot:</strong><br>
                                {{ $pallet->lot ?: 'Not set' }}
                            </div>
                            <div class="mb-3">
                                <strong>Created:</strong><br>
                                {{ $pallet->created_at->format('M j, Y g:i A') }}
                            </div>
                            <div class="mb-3">
                                <strong>Created By:</strong><br>
                                {{ $pallet->creator->name }}
                            </div>
                            <div class="mb-3">
                                <strong>Photos:</strong><br>
                                <span class="badge bg-secondary">{{ $pallet->photos->count() }} photos</span>
                            </div>
                            @if($pallet->notes)
                            <div class="mb-3">
                                <strong>Notes:</strong><br>
                                <small>{{ $pallet->notes }}</small>
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
        form.action = '{{ route("pallet-photos.delete", ":photoId") }}'.replace(':photoId', photoId);
        
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

@push('scripts')
<!-- QR Code Generation Library - Multiple CDNs for reliability -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"></script>
<script>
// Fallback QR code generation using a simple approach
window.generateQRCode = function(text, size = 200) {
    return new Promise((resolve, reject) => {
        try {
            // Use QR Server API as primary method
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=${size}x${size}&data=${encodeURIComponent(text)}`;
            resolve(qrUrl);
        } catch (error) {
            reject(error);
        }
    });
};
</script>

<script>
function printQRCode(palletNumber) {
    console.log('Starting QR code generation for pallet:', palletNumber);
    
    // Create a new window for printing
    const printWindow = window.open('', '_blank', 'width=800,height=600');
    
    if (!printWindow) {
        alert('Popup blocked. Please allow popups for this site and try again.');
        return;
    }
    
    // Generate QR code for the pallet
    const url = `${window.location.origin}/pallets/${palletNumber}`;
    console.log('Generating QR code for URL:', url);
    
    // Use the reliable QR Server API
    const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(url)}`;
    
    console.log('QR code URL:', qrCodeUrl);
    
    const html = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Pallet QR Code - ${palletNumber}</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 20px; 
                    text-align: center;
                }
                .qr-container { 
                    display: inline-block; 
                    margin: 20px; 
                    text-align: center; 
                    border: 1px solid #ddd;
                    padding: 20px;
                    width: 300px;
                }
                .qr-title { 
                    font-weight: bold; 
                    font-size: 18px; 
                    margin-bottom: 15px;
                    color: #333;
                }
                .qr-code { 
                    margin: 15px 0; 
                }
                .qr-url { 
                    font-size: 12px; 
                    color: #666; 
                    word-break: break-all;
                    margin-top: 15px;
                }
                @media print {
                    body { margin: 0; }
                    .qr-container { margin: 10px; }
                }
            </style>
        </head>
        <body>
            <div class="qr-container">
                <div class="qr-title">DormCoPallet #${palletNumber}</div>
                <div class="qr-code">
                    <img src="${qrCodeUrl}" alt="QR Code for ${palletNumber}" style="max-width: 200px; max-height: 200px;" onload="console.log('QR code image loaded successfully')" onerror="console.error('Failed to load QR code image')">
                </div>
                <!-- <div class="qr-url">${url}</div> -->
            </div>
        </body>
        </html>
    `;
    
    printWindow.document.write(html);
    printWindow.document.close();
    
    // Wait for images to load, then print
    printWindow.onload = function() {
        console.log('Print window loaded');
        setTimeout(() => {
            printWindow.print();
        }, 1000);
    };
}

</script>

@endpush
