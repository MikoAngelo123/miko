@extends('layouts.app')

@section('title', 'Browse Products')

@section('content')
<div class="row">
    @foreach($products as $product)
    <div class="col-md-4 mb-4">
        <div class="card">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="Product Image">
            @endif
            <div class="card-body">
                <h5 class="card-title">{{ $product->title }}</h5>
                <p class="card-text">${{ $product->price }}</p>
                <p class="card-text">Stock: {{ $product->stock }}</p>
                <form method="POST" action="{{ route('customer.cart.add') }}">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="mb-2">
                        <input type="number" name="quantity" value="1" min="1" class="form-control" style="width: 80px; display: inline;">
                    </div>
                    <button type="submit" class="btn btn-primary">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection
