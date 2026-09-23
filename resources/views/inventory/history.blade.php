@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1>Inventory History</h1>

        <p>
            Transaction history for
            {{ $product->name }}

            @if($product->variation)
                - {{ $product->variation }}
            @endif
        </p>
    </div>

    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
        Back to Inventory
    </a>
</div>

<div class="card">

    <h2 style="margin-bottom: 20px;">
        {{ $product->name }}

        @if($product->variation)
            - {{ $product->variation }}
        @endif
    </h2>

    <table class="data-table">
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Stock Allocation</th>
                <th>Transaction</th>
                <th>Quantity Change</th>
                <th>Recorded By</th>
                <th>Notes</th>
            </tr>
        </thead>

        <tbody>
            @forelse($transactions as $transaction)

            <tr>
                <td>
                    {{ $transaction->transaction_date
                        ? $transaction->transaction_date->format('M d, Y h:i A')
                        : '—' }}
                </td>

                <td>
                    {{ ucfirst($transaction->reserve_type) }}
                </td>

                <td>
                    {{ ucwords(str_replace('_', ' ', $transaction->transaction_type)) }}
                </td>

                <td>
                    @if($transaction->quantity_change > 0)
                        +{{ number_format($transaction->quantity_change, 2) }}
                    @else
                        {{ number_format($transaction->quantity_change, 2) }}
                    @endif

                    {{ $product->stock_unit }}
                </td>

                <td>
                    {{ $transaction->recordedBy
                        ? $transaction->recordedBy->first_name . ' ' .
                          $transaction->recordedBy->last_name
                        : '—' }}
                </td>

                <td>
                    {{ $transaction->notes ?: '—' }}
                </td>
            </tr>

            @empty

            <tr>
                <td colspan="6" style="text-align: center;">
                    No inventory transactions found.
                </td>
            </tr>

            @endforelse
        </tbody>
    </table>

</div>

@endsection