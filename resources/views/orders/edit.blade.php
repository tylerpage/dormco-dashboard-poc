@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Edit Order: {{ $order->order_number }}</h2>
                    <p class="text-muted mb-0">Update order information</p>
                </div>
                <div>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Back to Orders</a>
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary">View Order</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Order Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('orders.update', $order) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    @if(auth()->user()->role !== 'school')
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="order_number" class="form-label">Order Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('order_number') is-invalid @enderror" 
                                                   id="order_number" name="order_number" value="{{ old('order_number', $order->order_number) }}" required>
                                            @error('order_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Order Number</label>
                                            <input type="text" class="form-control" value="{{ $order->order_number }}" readonly>
                                        </div>
                                    </div>
                                    @endif
                                    @if(auth()->user()->role !== 'school')
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="school_id" class="form-label">School</label>
                                            <select class="form-select @error('school_id') is-invalid @enderror" id="school_id" name="school_id">
                                                <option value="">No School Assigned</option>
                                                @foreach($schools as $school)
                                                    <option value="{{ $school->id }}" {{ old('school_id', $order->school_id) == $school->id ? 'selected' : '' }}>
                                                        {{ $school->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('school_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">School</label>
                                            <input type="text" class="form-control" value="{{ $order->school ? $order->school->name : 'No School Assigned' }}" readonly>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                                   id="customer_name" name="customer_name" value="{{ old('customer_name', $order->customer_name) }}" required>
                                            @error('customer_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @if(auth()->user()->role !== 'school')
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="customer_email" class="form-label">Customer Email <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                                   id="customer_email" name="customer_email" value="{{ old('customer_email', $order->customer_email) }}" required>
                                            @error('customer_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Customer Email</label>
                                            <input type="email" class="form-control" value="{{ $order->customer_email }}" readonly>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <div class="row">
                                    @if(auth()->user()->role !== 'school')
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                <option value="pending" {{ old('status', $order->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="picked" {{ old('status', $order->status) === 'picked' ? 'selected' : '' }}>Picked</option>
                                                <option value="packed" {{ old('status', $order->status) === 'packed' ? 'selected' : '' }}>Packed</option>
                                                <option value="shipped" {{ old('status', $order->status) === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                <option value="delivered" {{ old('status', $order->status) === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Status</label>
                                            <input type="text" class="form-control" value="{{ ucfirst($order->status) }}" readonly>
                                        </div>
                                    </div>
                                    @endif
                                    @if(auth()->user()->role !== 'school')
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tracking_number" class="form-label">Tracking Number</label>
                                            <input type="text" class="form-control @error('tracking_number') is-invalid @enderror" 
                                                   id="tracking_number" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}">
                                            @error('tracking_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @else
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tracking Number</label>
                                            <input type="text" class="form-control" value="{{ $order->tracking_number ?: 'Not set' }}" readonly>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <div class="row">
                                    @if(auth()->user()->role !== 'school')
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="pallet_number" class="form-label">Pallet Number</label>
                                            <select class="form-select @error('pallet_number') is-invalid @enderror" id="pallet_number" name="pallet_number">
                                                <option value="">No Pallet Assigned</option>
                                                @foreach($pallets as $pallet)
                                                    <option value="{{ $pallet->pallet_number }}" {{ old('pallet_number', $order->pallet_number) == $pallet->pallet_number ? 'selected' : '' }}>
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
                                    </div>
                                    @else
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Pallet Number</label>
                                            <input type="text" class="form-control" value="{{ $order->pallet_number ?: 'Not assigned' }}" readonly>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Shipping Address <span class="text-danger">*</span></label>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="shipping_address_1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('shipping_address_1') is-invalid @enderror" 
                                                   id="shipping_address_1" name="shipping_address_1" value="{{ old('shipping_address_1', $order->shipping_address_1) }}" required>
                                            @error('shipping_address_1')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="shipping_address_2" class="form-label">Address Line 2</label>
                                            <input type="text" class="form-control @error('shipping_address_2') is-invalid @enderror" 
                                                   id="shipping_address_2" name="shipping_address_2" value="{{ old('shipping_address_2', $order->shipping_address_2) }}">
                                            @error('shipping_address_2')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="shipping_address_3" class="form-label">Address Line 3</label>
                                            <input type="text" class="form-control @error('shipping_address_3') is-invalid @enderror" 
                                                   id="shipping_address_3" name="shipping_address_3" value="{{ old('shipping_address_3', $order->shipping_address_3) }}">
                                            @error('shipping_address_3')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="shipping_city" class="form-label">City <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('shipping_city') is-invalid @enderror" 
                                                   id="shipping_city" name="shipping_city" value="{{ old('shipping_city', $order->shipping_city) }}" required>
                                            @error('shipping_city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="shipping_state" class="form-label">State <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('shipping_state') is-invalid @enderror" 
                                                   id="shipping_state" name="shipping_state" value="{{ old('shipping_state', $order->shipping_state) }}" required>
                                            @error('shipping_state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="shipping_zip" class="form-label">ZIP Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('shipping_zip') is-invalid @enderror" 
                                                   id="shipping_zip" name="shipping_zip" value="{{ old('shipping_zip', $order->shipping_zip) }}" required>
                                            @error('shipping_zip')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                @if(auth()->user()->role !== 'school')
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Additional notes about this order...">{{ old('notes', $order->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @else
                                <div class="mb-3">
                                    <label class="form-label">Notes</label>
                                    <textarea class="form-control" rows="3" readonly>{{ $order->notes ?: 'No notes' }}</textarea>
                                </div>
                                @endif

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="verified" name="verified" value="1" {{ old('verified', $order->verified) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="verified">
                                            Mark as Verified
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Update Order</button>
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary">Cancel</a>
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
