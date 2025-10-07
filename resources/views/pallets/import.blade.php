@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Import Pallets</h3>
                    <a href="{{ route('pallets.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Pallets
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

                    <form action="{{ route('pallets.import.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="pallet_numbers" class="form-label">Pallet Numbers</label>
                            <textarea class="form-control @error('pallet_numbers') is-invalid @enderror" 
                                      id="pallet_numbers" name="pallet_numbers" rows="8" 
                                      placeholder="Enter pallet numbers, one per line:&#10;PAL-001&#10;PAL-002&#10;PAL-003" required>{{ old('pallet_numbers') }}</textarea>
                            <div class="form-text">Enter one pallet number per line. Each pallet will be created with default settings.</div>
                            @error('pallet_numbers')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="default_status" class="form-label">Default Status</label>
                            <select class="form-select @error('default_status') is-invalid @enderror" 
                                    id="default_status" name="default_status">
                                <option value="packing" {{ old('default_status', 'packing') === 'packing' ? 'selected' : '' }}>Packing</option>
                                <option value="shipped" {{ old('default_status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ old('default_status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            </select>
                            @error('default_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="default_school_id" class="form-label">Default School</label>
                            <select class="form-select @error('default_school_id') is-invalid @enderror" 
                                    id="default_school_id" name="default_school_id">
                                <option value="">No School Assigned</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}" {{ old('default_school_id') == $school->id ? 'selected' : '' }}>
                                        {{ $school->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('default_school_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Import Pallets</button>
                            <a href="{{ route('pallets.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
