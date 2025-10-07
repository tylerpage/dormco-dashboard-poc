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
                    <h2>Order Photos</h2>
                    <p class="text-muted mb-0">Order #{{ $order->order_number }} - {{ $order->customer_name }}</p>
                </div>
                <div>
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Order
                    </a>
                </div>
            </div>

            <!-- Photos Grid -->
            @if($order->photos->count() > 0)
                <div class="row">
                    @foreach($order->photos as $photo)
                        <div class="col-md-4 col-lg-3 mb-4">
                            <div class="card">
                                <a href="{{ route('orders.photos.show', [$order, $photo]) }}" class="text-decoration-none">
                                    <img src="{{ $photo->getSignedUrl() }}" 
                                         alt="Order Photo" 
                                         class="card-img-top" 
                                         style="height: 200px; object-fit: cover;">
                                </a>
                                <div class="card-body p-2">
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i> {{ $photo->uploadedBy->name }}<br>
                                        <i class="fas fa-clock"></i> {{ $photo->created_at->format('M j, Y g:i A') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No Photos</h4>
                    <p class="text-muted">No photos have been uploaded for this order yet.</p>
                    <a href="{{ route('orders.upload-photos', $order) }}" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Photos
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
