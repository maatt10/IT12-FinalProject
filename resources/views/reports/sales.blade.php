@extends('layouts.app')

@section('title', 'Sales Reports')

@section('content')

<div class="page-header">
    <div>
        <h1>Sales Reports</h1>
        <p>Review sales activity for a selected date range.</p>
    </div>
</div>

{{-- ============================================
     DATE RANGE FILTER
     ============================================ --}}
<div class="card" style="margin-bottom: 24px;">
    <h2 class="card-heading">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        Report Period
    </h2>

    <form action="{{ route('reports.sales') }}" method="GET">
        <div class="filter-row">

            <div class="form-group" style="margin-bottom: 0;">
                <label for="from">From</label>
                <input
                    type="date"
                    id="from"
                    name="from"
                    class="form-control"
                    value="{{ $from }}"
                    required>
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="to">To</label>
                <input
                    type="date"
                    id="to"
                    name="to"
                    class="form-control"
                    value="{{ $to }}"
                    required>
            </div>

            <button type="submit" class="btn btn-primary filter-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Generate Report
            </button>

        </div>
    </form>
</div>

{{-- ============================================
     STAT CARDS
     ============================================ --}}
<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div style="text-align: right;">
            <p class="stat-label">Total Sales</p>
            <p class="stat-value">₱{{ number_format($totalSales, 2) }}</p>
        </div>
    </div>

    <div class="stat-card green-accent">
        <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </div>
        <div style="text-align: right;">
            <p class="stat-label">Transactions</p>
            <p class="stat-value">{{ $transactionCount }}</p>
        </div>
    </div>

    <div class="stat-card gold-accent">
        <div class="stat-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
            </svg>
        </div>
        <div style="text-align: right;">
            <p class="stat-label">Total Discounts</p>
            <p class="stat-value">₱{{ number_format($totalDiscounts, 2) }}</p>
        </div>
    </div>

</div>

{{-- ============================================
     PAYMENT METHODS BREAKDOWN
     ============================================ --}}
<div class="card" style="margin-bottom: 24px;">
    <h2 class="card-heading">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2E5A3B" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
        </svg>
        Payment Methods
    </h2>

    <div class="payment-breakdown">

        <div class="payment-row">
            <div class="payment-info">
                <div class="payment-icon cash">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="payment-name">Cash</span>
            </div>
            <span class="payment-value">₱{{ number_format($paymentTotals['cash'], 2) }}</span>
        </div>

        <div class="payment-row">
            <div class="payment-info">
                <div class="payment-icon gcash">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <span class="payment-name">GCash</span>
            </div>
            <span class="payment-value">₱{{ number_format($paymentTotals['gcash'], 2) }}</span>
        </div>

        <div class="payment-row">
            <div class="payment-info">
                <div class="payment-icon bank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                </div>
                <span class="payment-name">Bank Transfer</span>
            </div>
            <span class="payment-value">₱{{ number_format($paymentTotals['bank_transfer'], 2) }}</span>
        </div>

    </div>
</div>

{{-- ============================================
     SALES TRANSACTIONS TABLE
     ============================================ --}}
<div class="card">
    <h2 class="card-heading">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        Sales Transactions
    </h2>

    @if($sales->isEmpty())

    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3>No sales recorded</h3>
        <p>No sales were recorded during the selected period.</p>
    </div>

    @else

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Date &amp; Time</th>
                    <th>Customer</th>
                    <th>Payment</th>
                    <th style="text-align: right;">Subtotal</th>
                    <th style="text-align: right;">Discount</th>
                    <th style="text-align: right;">Total</th>
                    <th>Recorded By</th>
                </tr>
            </thead>

            <tbody>
                @foreach($sales as $sale)
                <tr>
                    <td style="color: #64748B; white-space: nowrap;">
                        {{ $sale->sale_date->format('M d, Y h:i A') }}
                    </td>

                    <td style="font-weight: 500;">
                        {{ $sale->customer->full_name ?? 'Walk-in' }}
                    </td>

                    <td>
                        <span class="pay-tag pay-{{ $sale->payment_method }}">
                            {{ str_replace('_', ' ', $sale->payment_method) }}
                        </span>
                    </td>

                    <td style="text-align: right; color: #64748B;">
                        ₱{{ number_format($sale->subtotal, 2) }}
                    </td>

                    <td style="text-align: right; color: {{ $sale->discount_amount > 0 ? '#B8860B' : '#94A3B8' }}; font-weight: 500;">
                        {{ $sale->discount_amount > 0 ? '− ₱' . number_format($sale->discount_amount, 2) : '—' }}
                    </td>

                    <td style="text-align: right; font-weight: 700; color: #2E5A3B;">
                        ₱{{ number_format($sale->total_amount, 2) }}
                    </td>

                    <td style="color: #64748B;">
                        {{ $sale->user->full_name ?? 'Unknown' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @endif
</div>

<style>
    /* Card headings */
    .card-heading {
        font-size: 16px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Filter row */
    .filter-row {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 16px;
        align-items: end;
    }

    @media (max-width: 640px) {
        .filter-row {
            grid-template-columns: 1fr;
        }

        .filter-btn {
            width: 100%;
            justify-content: center;
        }
    }

    .filter-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 20px;
        font-size: 14px;
    }

    /* Stats grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
        margin-bottom: 24px;
    }

    /* Stat cards — non-clickable */
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

    .stat-card.gold-accent {
        border-top-color: #D4AF37;
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
        cursor: default;
        user-select: none;
    }

    .stat-card.green-accent .stat-icon {
        background: #E8F5E9;
        color: #2E5A3B;
    }

    .stat-card.gold-accent .stat-icon {
        background: #FFF8E1;
        color: #D4AF37;
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

    /* Payment breakdown */
    .payment-breakdown {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .payment-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        background: #FEFCF9;
        border: 1px solid #F5EEE4;
        border-radius: 10px;
    }

    .payment-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .payment-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        cursor: default;
        user-select: none;
    }

    .payment-icon.cash {
        background: #FCE4EC;
        color: #E85D75;
    }

    .payment-icon.gcash {
        background: #E8F5E9;
        color: #2E5A3B;
    }

    .payment-icon.bank {
        background: #FFF8E1;
        color: #B8860B;
    }

    .payment-name {
        font-size: 14px;
        font-weight: 600;
        color: #212121;
    }

    .payment-value {
        font-size: 16px;
        font-weight: 700;
        color: #2E5A3B;
        cursor: default;
        user-select: none;
    }

    /* Payment tags (in table) — non-clickable */
    .pay-tag {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        text-transform: capitalize;
        letter-spacing: 0.3px;
        cursor: default;
        user-select: none;
    }

    .pay-cash {
        color: #E85D75;
    }

    .pay-gcash {
        color: #2E5A3B;
    }

    .pay-bank_transfer {
        color: #B8860B;
    }

    /* Empty state */
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
    }
</style>

@endsection