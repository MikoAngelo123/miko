@extends('layouts.app')

@section('title', 'Your Orders')

@section('content')
@if($orders->count() > 0)
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $order->created_at->format('Y-m-d') }}</td>
                <td>{{ $order->product->title }}</td>
                <td>{{ $order->quantity }}</td>
                <td>${{ number_format($order->total_price, 2) }}</td>
                <td>
                    @if($order->status == 'pending')
                        <span class="badge bg-warning text-dark">Pending</span>
                    @else
                        <span class="badge bg-success">Complete</span>
                    @endif
                </td>
                <td>
                    @if($order->status == 'pending')
                        <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@else
<p>You have no orders.</p>
@endif
@endsection
