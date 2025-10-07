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

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Order Photo</h2>
                    <p class="text-muted mb-0">Order #{{ $order->order_number }} - {{ $order->customer_name }}</p>
                </div>
                <div>
                    <a href="{{ route('orders.photos', $order) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Photos
                    </a>
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-eye"></i> View Order
                    </a>
                </div>
            </div>

            <!-- Photo Navigation -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-primary">{{ $currentIndex + 1 }} of {{ $photos->count() }}</span>
                        </div>
                        <div>
                            @if($currentIndex > 0)
                                <a href="{{ route('orders.photos.show', [$order, $photos[$currentIndex - 1]]) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            @endif
                            
                            @if($currentIndex < $photos->count() - 1)
                                <a href="{{ route('orders.photos.show', [$order, $photos[$currentIndex + 1]]) }}" 
                                   class="btn btn-outline-primary">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Photo Display -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body p-0">
                            <img src="{{ $photo->getSignedUrl() }}" 
                                 alt="Order Photo" 
                                 class="img-fluid w-100" 
                                 style="max-height: 70vh; object-fit: contain;">
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Photo Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Uploaded by:</strong><br>
                                {{ $photo->uploadedBy->name }}
                            </div>
                            <div class="mb-3">
                                <strong>Date:</strong><br>
                                {{ $photo->created_at->format('M j, Y g:i A') }}
                            </div>
                            @if($photo->notes)
                                <div class="mb-3">
                                    <strong>Notes:</strong><br>
                                    <small>{{ $photo->notes }}</small>
                                </div>
                            @endif
                            
                            @if(auth()->user()->role !== 'school')
                                <div class="d-grid">
                                    <button type="button" class="btn btn-danger" 
                                            onclick="deletePhoto({{ $photo->id }}, '{{ $photo->photo_path }}')">
                                        <i class="fas fa-trash"></i> Delete Photo
                                    </button>
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
