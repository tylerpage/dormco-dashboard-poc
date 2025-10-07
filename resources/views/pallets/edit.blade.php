@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Edit Pallet: {{ $pallet->pallet_number }}</h2>
                    <p class="text-muted mb-0">Update pallet information</p>
                </div>
                <div>
                    <a href="{{ route('pallets.index') }}" class="btn btn-outline-secondary">Back to Pallets</a>
                    <a href="{{ route('pallets.show', $pallet) }}" class="btn btn-outline-primary">View Pallet</a>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Pallet Information</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('pallets.update', $pallet) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="pallet_number" class="form-label">Pallet Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('pallet_number') is-invalid @enderror" 
                                                   id="pallet_number" name="pallet_number" value="{{ old('pallet_number', $pallet->pallet_number) }}" required>
                                            @error('pallet_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                                <option value="packing" {{ old('status', $pallet->status) === 'packing' ? 'selected' : '' }}>Packing</option>
                                                <option value="shipped" {{ old('status', $pallet->status) === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                                <option value="delivered" {{ old('status', $pallet->status) === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="school_id" class="form-label">School</label>
                                            <select class="form-select @error('school_id') is-invalid @enderror" id="school_id" name="school_id">
                                                <option value="">No School Assigned</option>
                                                @foreach($schools as $school)
                                                    <option value="{{ $school->id }}" {{ old('school_id', $pallet->school_id) == $school->id ? 'selected' : '' }}>
                                                        {{ $school->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('school_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="location" class="form-label">Location</label>
                                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                                   id="location" name="location" value="{{ old('location', $pallet->location) }}">
                                            @error('location')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="lot" class="form-label">Lot</label>
                                            <input type="text" class="form-control @error('lot') is-invalid @enderror" 
                                                   id="lot" name="lot" value="{{ old('lot', $pallet->lot) }}">
                                            @error('lot')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Shipping Address <span class="text-danger">*</span></label>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="shipping_address_1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('shipping_address_1') is-invalid @enderror" 
                                                   id="shipping_address_1" name="shipping_address_1" value="{{ old('shipping_address_1', $pallet->shipping_address_1) }}" required>
                                            @error('shipping_address_1')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="shipping_address_2" class="form-label">Address Line 2</label>
                                            <input type="text" class="form-control @error('shipping_address_2') is-invalid @enderror" 
                                                   id="shipping_address_2" name="shipping_address_2" value="{{ old('shipping_address_2', $pallet->shipping_address_2) }}">
                                            @error('shipping_address_2')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label for="shipping_address_3" class="form-label">Address Line 3</label>
                                            <input type="text" class="form-control @error('shipping_address_3') is-invalid @enderror" 
                                                   id="shipping_address_3" name="shipping_address_3" value="{{ old('shipping_address_3', $pallet->shipping_address_3) }}">
                                            @error('shipping_address_3')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4 col-12 mb-3">
                                            <label for="shipping_city" class="form-label">City <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('shipping_city') is-invalid @enderror" 
                                                   id="shipping_city" name="shipping_city" value="{{ old('shipping_city', $pallet->shipping_city) }}" required>
                                            @error('shipping_city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 col-12 mb-3">
                                            <label for="shipping_state" class="form-label">State <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('shipping_state') is-invalid @enderror" 
                                                   id="shipping_state" name="shipping_state" value="{{ old('shipping_state', $pallet->shipping_state) }}" required>
                                            @error('shipping_state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4 col-12 mb-3">
                                            <label for="shipping_zip" class="form-label">ZIP Code <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('shipping_zip') is-invalid @enderror" 
                                                   id="shipping_zip" name="shipping_zip" value="{{ old('shipping_zip', $pallet->shipping_zip) }}" required>
                                            @error('shipping_zip')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Additional notes about this pallet...">{{ old('notes', $pallet->notes) }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Update Pallet</button>
                                    <a href="{{ route('pallets.show', $pallet) }}" class="btn btn-outline-secondary">Cancel</a>
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
