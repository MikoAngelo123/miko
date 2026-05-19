@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ $product->title }}</h5>
        <p class="card-text">Price: ${{ $product->price }}</p>
        <p class="card-text">Stock: {{ $product->stock }}</p>
        @if($product->image)
            <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid" alt="Product Image">
        @endif
    </div>
</div>
@endsection
