@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1>Purchase Details</h1>
        <p>Review the recorded purchase and its items.</p>
    </div>

    <a href="{{ route('purchases.index') }}" class="btn btn-secondary">
        Back to Purchases
    </a>
</div>

<div class="card">

    <div class="form-grid">

        <div>
            <strong>Purchase Date</strong>
            <p>
                {{ $purchase->purchase_date->format('M d, Y h:i A') }}
            </p>
        </div>

        <div>
            <strong>Supplier</strong>
            <p>
                {{ $purchase->supplier_name ?: 'Not specified' }}
            </p>
        </div>

        <div>
            <strong>Recorded By</strong>
            <p>
                {{ $purchase->user->full_name ?? 'Unknown' }}
            </p>
        </div>

        <div>
            <strong>Total Amount</strong>
            <p>
                ₱{{ number_format($purchase->total_amount, 2) }}
            </p>
        </div>

    </div>

</div>

<div class="card">

    <h2>Purchased Items</h2>

    <div class="table-responsive">

        <table class="table">

            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Purchase Unit</th>
                    <th>Unit Cost</th>
                    <th>Allocation</th>
                    <th>Stock Added</th>
                    <th>Line Total</th>
                </tr>
            </thead>

            <tbody>

                @foreach($purchase->items as $item)

                    @php
                        $product = $item->product;

                        $stockQuantity =
                            (float) $item->quantity *
                            (float) $product->units_per_purchase;
                    @endphp

                    <tr>

                        <td>
                            {{ $product->display_name }}
                        </td>

                        <td>
                            {{ number_format($item->quantity, 2) }}
                        </td>

                        <td>
                            {{ $product->purchase_unit }}
                        </td>

                        <td>
                            ₱{{ number_format($item->unit_cost, 2) }}
                        </td>

                        <td>
                            {{ ucfirst($item->reserve_type) }}
                        </td>

                        <td>
                            {{ number_format($stockQuantity, 2) }}
                            {{ $product->stock_unit }}
                        </td>

                        <td>
                            ₱{{ number_format(
                                $item->quantity * $item->unit_cost,
                                2
                            ) }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection