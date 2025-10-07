@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Update Shipping Information - Order #{{ $order->order_number }}</h3>
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Order
                    </a>
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

                    <form action="{{ route('orders.update-shipping.store', $order) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="tracking_number" class="form-label">Tracking Number</label>
                            <input type="text" class="form-control @error('tracking_number') is-invalid @enderror" 
                                   id="tracking_number" name="tracking_number" 
                                   value="{{ old('tracking_number', $order->tracking_number) }}">
                            @error('tracking_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="pallet_number" class="form-label">Pallet Number</label>
                            <select class="form-select @error('pallet_number') is-invalid @enderror" 
                                    id="pallet_number" name="pallet_number">
                                <option value="">No Pallet Assigned</option>
                                @foreach($pallets as $pallet)
                                    <option value="{{ $pallet->pallet_number }}" 
                                            {{ old('pallet_number', $order->pallet_number) == $pallet->pallet_number ? 'selected' : '' }}>
                                        {{ $pallet->pallet_number }} 
                                        @if($pallet->school)
                                            - {{ $pallet->school->name }}
                                        @endif
                                        ({{ ucfirst($pallet->status) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('pallet_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Shipping Address</label>
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="shipping_address_1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('shipping_address_1') is-invalid @enderror" 
                                           id="shipping_address_1" name="shipping_address_1" 
                                           value="{{ old('shipping_address_1', $order->shipping_address_1) }}" required>
                                    @error('shipping_address_1')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="shipping_address_2" class="form-label">Address Line 2</label>
                                    <input type="text" class="form-control @error('shipping_address_2') is-invalid @enderror" 
                                           id="shipping_address_2" name="shipping_address_2" 
                                           value="{{ old('shipping_address_2', $order->shipping_address_2) }}">
                                    @error('shipping_address_2')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label for="shipping_address_3" class="form-label">Address Line 3</label>
                                    <input type="text" class="form-control @error('shipping_address_3') is-invalid @enderror" 
                                           id="shipping_address_3" name="shipping_address_3" 
                                           value="{{ old('shipping_address_3', $order->shipping_address_3) }}">
                                    @error('shipping_address_3')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 col-12 mb-3">
                                    <label for="shipping_city" class="form-label">City <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('shipping_city') is-invalid @enderror" 
                                           id="shipping_city" name="shipping_city" 
                                           value="{{ old('shipping_city', $order->shipping_city) }}" required>
                                    @error('shipping_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 col-12 mb-3">
                                    <label for="shipping_state" class="form-label">State <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('shipping_state') is-invalid @enderror" 
                                           id="shipping_state" name="shipping_state" 
                                           value="{{ old('shipping_state', $order->shipping_state) }}" required>
                                    @error('shipping_state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 col-12 mb-3">
                                    <label for="shipping_zip" class="form-label">ZIP Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('shipping_zip') is-invalid @enderror" 
                                           id="shipping_zip" name="shipping_zip" 
                                           value="{{ old('shipping_zip', $order->shipping_zip) }}" required>
                                    @error('shipping_zip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update Shipping</button>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
