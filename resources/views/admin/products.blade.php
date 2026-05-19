@extends('layouts.app')

@section('title', 'Products')

@section('content')
<a href="{{ route('admin.products.create') }}" class="btn btn-primary mb-3">Add New Product</a>
<table class="table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->title }}</td>
            <td>${{ $product->price }}</td>
            <td>{{ $product->stock }}</td>
            <td>
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" width="50" alt="Image">
                @endif
            </td>
            <td>
                <a href="{{ route('admin.products.show', $product) }}" class="btn btn-info btn-sm">Show</a>
                <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning btn-sm">Edit</a>
                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
