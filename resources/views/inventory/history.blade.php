@extends('layouts.app')

@section('title', 'Inventory History')

@section('content')

<div class="page-header">
    <div>
        <h1>Inventory History</h1>
        <p>
            Transaction history for
            <strong style="color: #E85D75;">{{ $product->name }}</strong>
            @if($product->variation)
            — {{ $product->variation }}
            @endif
        </p>
    </div>

    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
        ← Back to Inventory
    </a>
</div>

<div class="card">

    @if($transactions->isEmpty())

    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <h3>No transactions yet</h3>
        <p>This product has no inventory history.</p>
    </div>

    @else

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Date &amp; Time</th>
                    <th>Stock Allocation</th>
                    <th>Transaction</th>
                    <th style="text-align: right;">Quantity Change</th>
                    <th>Recorded By</th>
                    <th>Notes</th>
                </tr>
            </thead>

            <tbody>
                @foreach($transactions as $transaction)
                @php
                $change = (float) $transaction->quantity_change;
                @endphp

                <tr>
                    <td style="color: #64748B; white-space: nowrap;">
                        {{ $transaction->transaction_date ? $transaction->transaction_date->format('M d, Y h:i A') : '—' }}
                    </td>

                    <td>
                        <span class="alloc-tag alloc-{{ $transaction->reserve_type }}">
                            {{ ucfirst($transaction->reserve_type) }}
                        </span>
                    </td>

                    <td style="color: #64748B;">
                        {{ ucwords(str_replace('_', ' ', $transaction->transaction_type)) }}
                    </td>

                    <td style="text-align: right; font-weight: 700; color: {{ $change > 0 ? '#2E5A3B' : ($change < 0 ? '#DC3545' : '#94A3B8') }};">
                        {{ $change > 0 ? '+' : '' }}{{ (float) $change }} {{ $product->stock_unit }}
                    </td>

                    <td style="color: #64748B;">
                        {{ $transaction->recordedBy ? $transaction->recordedBy->first_name . ' ' . $transaction->recordedBy->last_name : '—' }}
                    </td>

                    <td style="color: #64748B; max-width: 240px;">
                        {{ $transaction->notes ?: '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @endif

</div>

<style>
    /* Allocation tag — plain colored text, non-clickable */
    .alloc-tag {
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        cursor: default;
        user-select: none;
    }

    .alloc-retail {
        color: #E85D75;
    }

    .alloc-production {
        color: #B8860B;
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
    }
</style>

@endsection