@extends('layouts.app')

@section('title', 'Online Orders')

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

    @if($orders->count())

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Order Date &amp; Time</th>
                    <th>Customer</th>
                    <th>Type</th>
                    <th>Fulfillment</th>
                    <th>Delivery Timing</th>
                    <th style="text-align: right;">Total</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td style="color: #64748B; white-space: nowrap;">
                        {{ $order->order_date->format('M d, Y h:i A') }}
                    </td>

                    <td style="font-weight: 600; color: #212121;">
                        {{ $order->customer->full_name }}
                    </td>

                    <td style="color: #64748B;">
                        {{ $order->order_type === 'ready_made' ? 'Ready-Made' : 'Customized' }}
                    </td>

                    <td style="color: #64748B;">
                        {{ ucfirst($order->fulfillment_type) }}
                    </td>

                    <td style="color: #64748B; white-space: nowrap;">
                        {{ $order->delivery_timing }}
                    </td>

                    <td style="text-align: right; font-weight: 700; color: #2E5A3B;">
                        ₱{{ number_format($order->total_amount, 2) }}
                    </td>

                    <td>
                        <span class="status-text status-{{ $order->order_status }}">
                            {{ ucfirst($order->order_status) }}
                        </span>
                    </td>

                    <td style="text-align: right;">
                        <a href="{{ route('orders.show', $order) }}" class="action-btn view">
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
        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <h3>No online orders yet</h3>
        <p>Record a bouquet order received through Messenger to retain it in the system.</p>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            + Record Online Order
        </a>
    </div>

    @endif

</div>

<style>
    /* Status — plain colored text, non-clickable */
    .status-text {
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        cursor: default;
        user-select: none;
    }

    .status-pending {
        color: #64748B;
    }

    .status-confirmed {
        color: #E85D75;
    }

    .status-preparing {
        color: #B8860B;
    }

    .status-ready {
        color: #D14A62;
    }

    .status-completed {
        color: #2E5A3B;
    }

    .status-cancelled {
        color: #DC3545;
    }

    .action-btn {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .action-btn.view {
        background: #F1F5F9;
        color: #475569;
    }

    .action-btn.view:hover {
        background: #E2E8F0;
        color: #1E293B;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748B;
    }

    .empty-state svg {
        color: #D4AF37;
        margin-bottom: 16px;
        opacity: 0.6;
    }

    .empty-state h3 {
        font-size: 18px;
        color: #212121;
        margin-bottom: 6px;
        font-weight: 700;
    }

    .empty-state p {
        font-size: 14px;
        margin-bottom: 20px;
    }
</style>

@endsection