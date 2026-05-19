@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<p class="lead">Welcome, {{ Auth::user()->name }}.</p>
<div class="row">
    <div class="col-md-4">
        <div class="card text-white bg-primary mb-3 text-center">
            <div class="card-header">My Total Orders</div>
            <div class="card-body">
                <h1 class="card-title">{{ $totalOrders }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-success mb-3 text-center">
            <div class="card-header">Total Spent</div>
            <div class="card-body">
                <h1 class="card-title">${{ number_format($totalPaid, 2) }}</h1>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info mb-3 text-center">
            <div class="card-header">Available Products</div>
            <div class="card-body">
                <h1 class="card-title">{{ $totalProducts }}</h1>
            </div>
        </div>
    </div>
</div>
@endsection
