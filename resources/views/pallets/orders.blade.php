@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0">Orders on Pallet #{{ $pallet->pallet_number }}</h3>
                        <p class="text-muted mb-0">
                            {{ $pallet->orders->count() }} order(s) assigned to this pallet
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('pallets.show', $pallet) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Pallet
                        </a>
                    </div>
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

                    @if($pallet->orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Order & Customer</th>
                                        <th>Verified</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pallet->orders as $order)
                                    @php
                                        $palletOrder = $pallet->palletOrders->where('order_id', $order->id)->first();
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <a href="{{ route('orders.show', $order) }}" class="text-decoration-none fw-bold">
                                                    {{ $order->order_number }}
                                                </a>
                                                <span class="fw-bold">{{ $order->customer_name }}</span>
                                                <a href="mailto:{{ $order->customer_email }}" class="text-decoration-none text-muted small">
                                                    {{ $order->customer_email }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            @if($palletOrder && $palletOrder->verified)
                                                <div class="d-flex flex-column">
                                                    <span class="badge bg-success mb-1">
                                                        <i class="fas fa-check"></i> Verified
                                                    </span>
                                                    @if($palletOrder->verified_at)
                                                        <small class="text-muted">{{ $palletOrder->verified_at->format('M j, Y g:i A') }}</small>
                                                    @endif
                                                    @if($palletOrder->verifiedBy)
                                                        <small class="text-muted">by {{ $palletOrder->verifiedBy->name }}</small>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times"></i> Not Verified
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-grid">
                                                @if($palletOrder && $palletOrder->verified)
                                                    <form action="{{ route('pallets.unverify-order', [$pallet, $order]) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-warning btn-lg w-100">
                                                            <i class="fas fa-times"></i> Unverify
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('pallets.verify-order', [$pallet, $order]) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn btn-success btn-lg w-100">
                                                            <i class="fas fa-check"></i> Verify
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Orders Assigned</h4>
                            <p class="text-muted">This pallet doesn't have any orders assigned to it yet.</p>
                            <a href="{{ route('pallets.show', $pallet) }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Back to Pallet
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
