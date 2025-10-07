@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Dashboard</h1>
                <div class="text-muted">
                    Welcome back, {{ Auth::user()->name }}!
                </div>
            </div>

            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (in_array(Auth::user()->role, ['admin', 'staff']))
                <!-- Admin/Staff Dashboard -->
                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title">{{ $totalOrders ?? 0 }}</h4>
                                        <p class="card-text">Total Orders</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-shopping-cart fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title">{{ $unverifiedOrders ?? 0 }}</h4>
                                        <p class="card-text">Unverified Orders</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title">{{ App\Models\Pallet::count() }}</h4>
                                        <p class="card-text">Total Pallets</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-boxes fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4 class="card-title">{{ App\Models\School::count() }}</h4>
                                        <p class="card-text">Schools</p>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="fas fa-university fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <a href="{{ route('orders.index') }}" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-list me-2"></i>View Orders
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="{{ route('orders.create') }}" class="btn btn-outline-success w-100">
                                            <i class="fas fa-plus me-2"></i>Create Order
                                        </a>
                                    </div>
                                    @if (Auth::user()->role === 'admin' || (Auth::user()->permissions && in_array('pallets', Auth::user()->permissions)))
                                    <div class="col-md-3 mb-3">
                                        <a href="{{ route('pallets.index') }}" class="btn btn-outline-info w-100">
                                            <i class="fas fa-boxes me-2"></i>View Pallets
                                        </a>
                                    </div>
                                    @endif
                                    @if (Auth::user()->role === 'admin')
                                    <div class="col-md-3 mb-3">
                                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary w-100">
                                            <i class="fas fa-users me-2"></i>Manage Users
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
            @else
                <!-- School User Dashboard -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">How to Verify Orders</h5>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-info-circle me-2"></i>Order Verification Process
                                    </h6>
                                    <p class="mb-0">As a school user, you can verify orders to confirm that the information is accurate and complete.</p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary">
                                            <i class="fas fa-check-circle me-2"></i>Step 1: Access Orders
                                        </h6>
                                        <p>Navigate to the <strong>Orders</strong> section from the main menu to view all orders assigned to your school.</p>
                                        
                                        <h6 class="text-primary">
                                            <i class="fas fa-search me-2"></i>Step 2: Review Order Details
                                        </h6>
                                        <p>Click on any order to view its complete details including customer information, shipping address, and order items.</p>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h6 class="text-primary">
                                            <i class="fas fa-verify me-2"></i>Step 3: Verify the Order
                                        </h6>
                                        <p>Once you've reviewed the order information and confirmed it's accurate, click the <strong>"Verify Order"</strong> button.</p>
                                        
                                        <h6 class="text-primary">
                                            <i class="fas fa-check-double me-2"></i>Step 4: Confirmation
                                        </h6>
                                        <p>The order will be marked as verified, and you'll see a confirmation message. Verified orders are tracked in the system.</p>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <div class="alert alert-warning">
                                        <h6 class="alert-heading">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Important Notes
                                        </h6>
                                        <ul class="mb-0">
                                            <li>Only verify orders that you have personally reviewed and confirmed</li>
                                            <li>You can only verify orders assigned to your school</li>
                                            <li>Verification helps ensure order accuracy and improves processing efficiency</li>
                                            <li>If you need to unverify an order, you can do so by clicking the "Unverify Order" button</li>
                                        </ul>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <a href="{{ route('orders.index') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-list me-2"></i>View Orders to Verify
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
