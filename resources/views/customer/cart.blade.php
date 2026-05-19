@extends('layouts.app')

@section('title', 'Your Cart')

@section('content')
@if($items->count() > 0)
<table class="table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Total</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
        <tr>
            <td>{{ $item->product->title }}</td>
            <td>
                <form action="{{ route('customer.cart.update', $item->id) }}" method="POST" class="d-flex align-items-center">
                    @csrf
                    @method('PATCH')
                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="form-control form-control-sm me-2" style="width: 70px;">
                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                </form>
            </td>
            <td>${{ $item->product->price }}</td>
            <td>${{ $item->quantity * $item->product->price }}</td>
            <td>
                <form action="{{ route('customer.cart.remove', $item->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="mt-3 d-flex justify-content-between">
    <a href="{{ route('customer.products') }}" class="btn btn-primary">Continue Shopping</a>
    <form method="POST" action="{{ route('customer.checkout') }}">
        @csrf
        <button type="submit" class="btn btn-success">Checkout</button>
    </form>
</div>
@else
<p>Your cart is empty.</p>
<a href="{{ route('customer.products') }}" class="btn btn-primary">Continue Shopping</a>
@endif
@endsection
