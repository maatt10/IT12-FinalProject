@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1>Order Details</h1>
        <p>View the complete information for this online bouquet order.</p>
    </div>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
        Back to Orders
    </a>
</div>

<div class="card">

    <div class="card-header">
        <h2>Order Information</h2>
    </div>

    <div class="form-row">

        <div class="form-group">
            <label>Customer</label>
            <p>{{ $order->customer->full_name }}</p>
        </div>

        <div class="form-group">
            <label>Recorded By</label>
            <p>{{ $order->user->full_name }}</p>
        </div>

        <div class="form-group">
            <label>Order Date</label>
            <p>{{ $order->order_date->format('M d, Y h:i A') }}</p>
        </div>

    </div>

    <div class="form-row">

        <div class="form-group">
            <label>Order Type</label>
            <p>
                {{ $order->order_type === 'ready_made'
                    ? 'Ready-Made'
                    : 'Customized' }}
            </p>
        </div>

        <div class="form-group">
            <label for="order_status">
                Order Status
            </label>

            <form
                action="{{ route('orders.status.update', $order) }}"
                method="POST">
                @csrf
                @method('PUT')

                <select
                    id="order_status"
                    name="order_status"
                    required>
                    <option
                        value="pending"
                        {{ $order->order_status === 'pending' ? 'selected' : '' }}>
                        Pending
                    </option>

                    <option
                        value="confirmed"
                        {{ $order->order_status === 'confirmed' ? 'selected' : '' }}>
                        Confirmed
                    </option>

                    <option
                        value="preparing"
                        {{ $order->order_status === 'preparing' ? 'selected' : '' }}>
                        Preparing
                    </option>

                    <option
                        value="ready"
                        {{ $order->order_status === 'ready' ? 'selected' : '' }}>
                        Ready
                    </option>

                    <option
                        value="completed"
                        {{ $order->order_status === 'completed' ? 'selected' : '' }}>
                        Completed
                    </option>

                    <option
                        value="cancelled"
                        {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>
                        Cancelled
                    </option>
                </select>

                <button
                    type="submit"
                    class="btn btn-primary"
                    style="margin-top: 8px;">
                    Update Status
                </button>
            </form>
        </div>

        <div class="form-group">
            <label>Fulfillment</label>
            <p>{{ ucfirst($order->fulfillment_type) }}</p>
        </div>

    </div>

</div>

<div class="card">

    <div class="card-header">
        <h2>Receiver Information</h2>
    </div>

    <div class="form-row">

        <div class="form-group">
            <label>Receiver Name</label>
            <p>{{ $order->receiver_full_name }}</p>
        </div>

        <div class="form-group">
            <label>Contact Number</label>
            <p>{{ $order->receiver_contact }}</p>
        </div>

    </div>

    <div class="form-row">

        <div class="form-group">
            <label>Delivery / Pickup Timing</label>
            <p>{{ $order->delivery_timing }}</p>
        </div>

        <div class="form-group">
            <label>Delivery Address</label>
            <p>{{ $order->delivery_address ?: '—' }}</p>
        </div>

    </div>

</div>

<div class="card">

    <div class="card-header">
        <h2>Payment and Fulfillment</h2>
    </div>

    <div class="form-row">

        <div class="form-group">
            <label>Fulfillment Type</label>
            <p>{{ ucfirst($order->fulfillment_type) }}</p>
        </div>

        <div class="form-group">
            <label>Delivery Fee</label>
            <p>
                ₱{{ number_format($order->delivery_fee, 2) }}
            </p>
        </div>

        <div class="form-group">
            <label>Payment Proof Reference</label>
            <p>
                {{ $order->payment_proof_reference ?: '—' }}
            </p>
        </div>

    </div>

</div>

<div class="card">

    <div class="card-header">
        <h2>Order Items</h2>
    </div>

    <div class="table-wrapper">
        <table class="data-table">

            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Customization</th>
                    <th>Line Total</th>
                </tr>
            </thead>

            <tbody>

                @foreach($order->items as $item)

                <tr>

                    <td>
                        {{ $item->product->display_name }}
                    </td>

                    <td>
                        {{ $item->quantity }}
                    </td>

                    <td>
                        ₱{{ number_format($item->unit_price, 2) }}
                    </td>

                    <td>
                        {{ $item->customization_details ?: '—' }}
                    </td>

                    <td>
                        ₱{{ number_format($item->line_total, 2) }}
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>
    </div>

    <div class="order-total">

        <strong>
            Delivery Fee:
            ₱{{ number_format($order->delivery_fee, 2) }}
        </strong>

        <br>

        <strong>
            Total:
            ₱{{ number_format($order->total_amount, 2) }}
        </strong>

    </div>

</div>

@endsection