@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Save Personal View</h3>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Orders
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

                    <form action="{{ route('orders.save-view.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="viewName" class="form-label">View Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="viewName" name="name" value="{{ old('name') }}" required>
                            <div class="form-text">This view will be saved to your personal collection and only visible to you.</div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <input type="hidden" name="filters" value="{{ json_encode($filters) }}">
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save View</button>
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
