@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Data Exports</h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <!-- Orders Export -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Export Orders</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('exports.orders') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="school_id" class="form-label">School</label>
                                            <select class="form-select" id="school_id" name="school_id">
                                                <option value="">All Schools</option>
                                                <option value="none">No School Assigned</option>
                                                @foreach(\App\Models\School::where('is_active', true)->get() as $school)
                                                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="">All Statuses</option>
                                                <option value="pending">Pending</option>
                                                <option value="picked">Picked</option>
                                                <option value="packed">Packed</option>
                                                <option value="shipped">Shipped</option>
                                                <option value="delivered">Delivered</option>
                                            </select>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="date_from" class="form-label">From Date</label>
                                                    <input type="date" class="form-control" id="date_from" name="date_from">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="date_to" class="form-label">To Date</label>
                                                    <input type="date" class="form-control" id="date_to" name="date_to">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Export Orders CSV</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Pallets Export -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Export Pallets</h5>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('exports.pallets') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="school_id" class="form-label">School</label>
                                            <select class="form-select" id="school_id" name="school_id">
                                                <option value="">All Schools</option>
                                                @foreach(\App\Models\School::where('is_active', true)->get() as $school)
                                                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select class="form-select" id="status" name="status">
                                                <option value="">All Statuses</option>
                                                <option value="packing">Packing</option>
                                                <option value="shipped">Shipped</option>
                                                <option value="delivered">Delivered</option>
                                            </select>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="date_from" class="form-label">From Date</label>
                                                    <input type="date" class="form-control" id="date_from" name="date_from">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="date_to" class="form-label">To Date</label>
                                                    <input type="date" class="form-control" id="date_to" name="date_to">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Export Pallets CSV</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
