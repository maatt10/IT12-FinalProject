@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- ==============================
     WELCOME HEADER
     ============================== --}}
<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <p style="color: #64748B; font-size: 14px; margin-top: 4px;">
            Welcome back, <strong style="color: #E85D75;">{{ auth()->user()->full_name }}</strong>.
        </p>
    </div>
</div>

{{-- ==============================
     STAT CARDS
     ============================== --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; margin-bottom: 24px;">

    {{-- Today's Sales --}}
    <div class="stat-card">
        <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div style="text-align: right;">
            <p class="stat-label">Today's Sales</p>
            <p class="stat-value">₱{{ number_format((float) $todaySales, 2) }}</p>
        </div>
    </div>

    {{-- Today's Transactions --}}
    <div class="stat-card green-accent">
        <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
        </div>
        <div style="text-align: right;">
            <p class="stat-label">Today's Transactions</p>
            <p class="stat-value">{{ $todayTransactions }}</p>
        </div>
    </div>

</div>

{{-- ==============================
     PENDING ONLINE ORDERS (OWNER ONLY)
     ============================== --}}
@if(auth()->user()->role === 'owner')
<div class="card" style="margin-bottom: 24px; border-top: 4px solid #D4AF37;">

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <div>
            <h2 style="font-size: 17px; font-weight: 700; color: #212121; display: flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                Pending Online Orders
            </h2>
            <p style="font-size: 13px; color: #64748B; margin-top: 4px;">
                Orders that still require processing.
            </p>
        </div>
        <a href="{{ route('orders.index') }}" style="font-size: 13px; font-weight: 600; color: #E85D75; text-decoration: none; padding: 8px 14px; border-radius: 8px; background: #FCE4EC;">
            View All →
        </a>
    </div>

    @if($pendingOrders->isEmpty())
    <p style="font-size: 13px; color: #64748B; padding: 20px; text-align: center; background: #FEFCF9; border-radius: 10px;">
        No pending online orders. ✨
    </p>
    @else
    <div style="overflow-x: auto;">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingOrders as $order)
                <tr>
                    <td style="font-weight: 600; color: #E85D75;">#{{ $order->order_id }}</td>
                    <td>{{ $order->customer->full_name ?? 'Unknown Customer' }}</td>
                    <td>
                        <span class="status-badge rose">{{ str_replace('_', ' ', $order->order_status) }}</span>
                    </td>
                    <td style="font-weight: 600;">₱{{ number_format((float) $order->total_amount, 2) }}</td>
                    <td>
                        <a href="{{ route('orders.show', $order) }}" style="color: #2E5A3B; font-weight: 600; text-decoration: none;">
                            View →
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($pendingOrders->hasPages())
    <div class="dashboard-pagination">
        {{ $pendingOrders->links() }}
    </div>
    @endif
    @endif

</div>
@endif

{{-- ==============================
     RECENT SALES
     ============================== --}}
<div class="card" style="margin-bottom: 24px;">

    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
        <div>
            <h2 style="font-size: 17px; font-weight: 700; color: #212121; display: flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Recent Sales
            </h2>
            <p style="font-size: 13px; color: #64748B; margin-top: 4px;">
                Latest recorded sales transactions.
            </p>
        </div>
        <a href="{{ route('sales.create') }}" style="font-size: 13px; font-weight: 600; color: #FFFFFF; text-decoration: none; padding: 8px 16px; border-radius: 8px; background: #E85D75; box-shadow: 0 2px 6px rgba(232, 93, 117, 0.3);">
            + New Sale
        </a>
    </div>

    @if($recentSales->isEmpty())
    <p style="font-size: 13px; color: #64748B; padding: 20px; text-align: center; background: #FEFCF9; border-radius: 10px;">
        No sales transactions have been recorded yet.
    </p>
    @else
    <div style="overflow-x: auto;">
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Date &amp; Time</th>
                    <th>Customer</th>
                    <th>Payment</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentSales as $sale)
                <tr>
                    <td>{{ $sale->sale_date?->format('M d, Y h:i A') }}</td>
                    <td>{{ $sale->customer->full_name ?? 'Walk-in Customer' }}</td>
                    <td>
                        <span class="payment-text">{{ str_replace('_', ' ', $sale->payment_method) }}</span>
                    </td>
                    <td style="font-weight: 700; color: #2E5A3B;">
                        ₱{{ number_format((float) $sale->total_amount, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($recentSales->hasPages())
    <div class="dashboard-pagination">
        {{ $recentSales->links() }}
    </div>
    @endif
    @endif

</div>

{{-- ==============================
     QUICK ACTIONS
     ============================== --}}
<div class="card">
    <h2 style="font-size: 17px; font-weight: 700; color: #212121; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
        </svg>
        Quick Actions
    </h2>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">

        <a href="{{ route('sales.create') }}" class="quick-action">
            <div class="qa-icon" style="background: #FCE4EC; color: #E85D75;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <p class="qa-title">New Sale</p>
            <p class="qa-desc">Record a customer purchase</p>
        </a>

        <a href="{{ route('inventory.index') }}" class="quick-action">
            <div class="qa-icon" style="background: #E8F5E9; color: #2E5A3B;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </div>
            <p class="qa-title">Inventory</p>
            <p class="qa-desc">View current stock</p>
        </a>

        <a href="{{ route('customers.index') }}" class="quick-action">
            <div class="qa-icon" style="background: #FFF8E1; color: #D4AF37;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <p class="qa-title">Customers</p>
            <p class="qa-desc">Manage customer records</p>
        </a>

        <a href="{{ route('purchases.index') }}" class="quick-action">
            <div class="qa-icon" style="background: #FCE4EC; color: #E85D75;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <p class="qa-title">Purchases</p>
            <p class="qa-desc">Record purchases</p>
        </a>

        <a href="{{ route('production.index') }}" class="quick-action">
            <div class="qa-icon" style="background: #E8F5E9; color: #2E5A3B;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
            </div>
            <p class="qa-title">Production</p>
            <p class="qa-desc">Record product production</p>
        </a>

        @if(auth()->user()->role === 'owner')
        <a href="{{ route('orders.index') }}" class="quick-action">
            <div class="qa-icon" style="background: #FFF8E1; color: #D4AF37;">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <p class="qa-title">Online Orders</p>
            <p class="qa-desc">Manage bouquet orders</p>
        </a>
        @endif

    </div>
</div>

<style>
    /* ===============================
       QUICK ACTION CARDS
       =============================== */
    .quick-action {
        display: block;
        padding: 16px;
        border: 1px solid #F0E6DD;
        border-radius: 10px;
        text-decoration: none;
        background: #FFFFFF;
        transition: all 0.2s ease;
    }

    .quick-action:hover {
        background: #FEFCF9;
        border-color: #E85D75;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(232, 93, 117, 0.12);
    }

    .qa-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }

    .qa-title {
        font-size: 14px;
        font-weight: 600;
        color: #212121;
        margin-bottom: 2px;
    }

    .qa-desc {
        font-size: 12px;
        color: #64748B;
    }

    /* ===============================
       STAT CARDS (non-clickable)
       =============================== */
    .stat-card {
        background: #FFFFFF;
        border-radius: 12px;
        padding: 22px;
        border: 1px solid #F0E6DD;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        border-top: 4px solid #E85D75;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        cursor: default;
        user-select: none;
    }

    .stat-card.green-accent {
        border-top-color: #2E5A3B;
    }

    .stat-card .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: #FCE4EC;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #E85D75;
        flex-shrink: 0;
    }

    .stat-card.green-accent .stat-icon {
        background: #E8F5E9;
        color: #2E5A3B;
    }

    .stat-card .stat-label {
        font-size: 11px;
        font-weight: 600;
        color: #94A3B8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .stat-card .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #212121;
        margin-top: 4px;
    }

    /* ===============================
       STATUS BADGES (non-clickable)
       =============================== */
    .status-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        text-transform: capitalize;
        letter-spacing: 0.3px;
        cursor: default;
        user-select: none;
    }

    .status-badge.rose {
        background: #FCE4EC;
        color: #E85D75;
    }

    /* Payment method - plain text */
    .payment-text {
        font-size: 12px;
        font-weight: 600;
        color: #2E5A3B;
        text-transform: capitalize;
        cursor: default;
        user-select: none;
    }

    /* ===============================
       DASHBOARD COMPACT TABLES
       =============================== */
    .dashboard-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .dashboard-table thead {
        background: #FDF2F4;
    }

    .dashboard-table th {
        background: #FDF2F4;
        color: #E85D75;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 10px;
        letter-spacing: 1.2px;
        padding: 12px;
        text-align: left;
        border-bottom: 2px solid #F8BBD0;
    }

    .dashboard-table th:first-child {
        border-top-left-radius: 8px;
    }

    .dashboard-table th:last-child {
        border-top-right-radius: 8px;
    }

    .dashboard-table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #F5EEE4;
        font-size: 13px;
        color: #212121;
    }

    .dashboard-table tbody tr:last-child td {
        border-bottom: none;
    }

    .dashboard-table tbody tr:hover {
        background: #FEFCF9;
    }

    /* ===============================
       DASHBOARD PAGINATION
       =============================== */
    .dashboard-pagination {
        margin-top: 16px;
        display: flex;
        justify-content: flex-end;
    }

    .dashboard-pagination nav {
        display: flex;
        gap: 4px;
    }

    .dashboard-pagination nav>div {
        display: flex;
        gap: 4px;
    }

    .dashboard-pagination a,
    .dashboard-pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 8px;
        background: #FFFFFF;
        color: #64748B;
        border: 1px solid #F0E6DD;
        text-decoration: none;
        cursor: pointer;
    }

    .dashboard-pagination a:hover {
        background: #FCE4EC;
        color: #E85D75;
        border-color: #F8BBD0;
    }

    .dashboard-pagination span[aria-current="page"]>span,
    .dashboard-pagination .active span {
        background: #E85D75;
        color: #FFFFFF;
        border-color: #E85D75;
        font-weight: 700;
    }

    .dashboard-pagination svg {
        width: 14px;
        height: 14px;
    }
</style>

@endsection