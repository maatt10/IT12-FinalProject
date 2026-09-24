@extends('layouts.app')

@section('title', 'Purchases')

@section('content')

<div class="page-header">
    <div>
        <h1>Purchases</h1>
        <p>View recorded purchases and restocking transactions.</p>
    </div>

    <a href="{{ route('purchases.create') }}" class="btn btn-primary">
        + Record Purchase
    </a>
</div>

<div class="card">

    @if($purchases->isEmpty())

    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <h3>No purchases yet</h3>
        <p>Start by recording your first restock.</p>
        <a href="{{ route('purchases.create') }}" class="btn btn-primary">
            + Record First Purchase
        </a>
    </div>

    @else

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Date &amp; Time</th>
                    <th>Supplier</th>
                    <th>Recorded By</th>
                    <th style="text-align: right;">Total Amount</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($purchases as $purchase)
                <tr>
                    <td style="color: #64748B; white-space: nowrap;">
                        {{ $purchase->purchase_date->format('M d, Y h:i A') }}
                    </td>

                    <td style="font-weight: 500; color: #212121;">
                        {{ $purchase->supplier_name ?: '—' }}
                    </td>

                    <td style="color: #64748B;">
                        {{ $purchase->user->full_name ?? 'Unknown' }}
                    </td>

                    <td style="text-align: right; font-weight: 700; color: #2E5A3B;">
                        ₱{{ number_format($purchase->total_amount, 2) }}
                    </td>

                    <td style="text-align: right;">
                        <a href="{{ route('purchases.show', $purchase) }}" class="action-btn view">
                            View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @endif

</div>

<style>
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