@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1>Online Orders</h1>
        <p>View and manage recorded bouquet orders.</p>
    </div>

    <a href="{{ route('orders.create') }}" class="btn btn-primary">
        + Record Online Order
    </a>
</div>

<div class="card">

    <div class="card-header">
        <h2>Order List</h2>
    </div>

    @if($orders->count())

    <div class="table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order Date</th>
                    <th>Customer</th>
                    <th>Order Type</th>
                    <th>Fulfillment</th>
                    <th>Delivery Timing</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td>
                        {{ $order->order_date->format('M d, Y h:i A') }}
                    </td>

                    <td>
                        <strong>
                            {{ $order->customer->full_name }}
                        </strong>
                    </td>

                    <td>
                        {{ $order->order_type === 'ready_made'
                                    ? 'Ready-Made'
                                    : 'Customized' }}
                    </td>

                    <td>
                        {{ ucfirst($order->fulfillment_type) }}
                    </td>

                    <td>
                        {{ $order->delivery_timing }}
                    </td>

                    <td>
                        ₱{{ number_format($order->total_amount, 2) }}
                    </td>

                    <td>
                        <span class="badge">
                            {{ ucfirst($order->order_status) }}
                        </span>
                    </td>
                    <td>
                        <a
                            href="{{ route('orders.show', $order) }}"
                            class="btn btn-secondary btn-sm">
                            View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @else

    <div class="empty-state">
        <h3>No online orders yet</h3>

        <p>
            Record a bouquet order received through Messenger
            to retain it in the system.
        </p>

        <a
            href="{{ route('orders.create') }}"
            class="btn btn-primary">
            + Record Online Order
        </a>
    </div>

    @endif

</div>

@endsection