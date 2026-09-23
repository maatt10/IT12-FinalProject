@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1>Sales Reports</h1>
        <p>Review sales activity for a selected date range.</p>
    </div>
</div>

<div class="card">

    <form
        action="{{ route('reports.sales') }}"
        method="GET"
    >
        <div class="form-grid">

            <div>
                <label for="from">
                    From
                </label>

                <input
                    type="date"
                    id="from"
                    name="from"
                    value="{{ $from }}"
                    required
                >
            </div>

            <div>
                <label for="to">
                    To
                </label>

                <input
                    type="date"
                    id="to"
                    name="to"
                    value="{{ $to }}"
                    required
                >
            </div>

        </div>

        <button
            type="submit"
            class="btn btn-primary"
            style="margin-top: 12px;"
        >
            Generate Report
        </button>
    </form>

</div>

<div class="stats-grid">

    <div class="card">
        <h3>Total Sales</h3>

        <p class="stat-value">
            ₱{{ number_format($totalSales, 2) }}
        </p>
    </div>

    <div class="card">
        <h3>Transactions</h3>

        <p class="stat-value">
            {{ $transactionCount }}
        </p>
    </div>

    <div class="card">
        <h3>Total Discounts</h3>

        <p class="stat-value">
            ₱{{ number_format($totalDiscounts, 2) }}
        </p>
    </div>

</div>

<div class="card">

    <h2>Payment Methods</h2>

    <div class="table-responsive">

        <table class="table">

            <thead>
                <tr>
                    <th>Payment Method</th>
                    <th>Total Sales</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>Cash</td>
                    <td>
                        ₱{{ number_format(
                            $paymentTotals['cash'],
                            2
                        ) }}
                    </td>
                </tr>

                <tr>
                    <td>GCash</td>
                    <td>
                        ₱{{ number_format(
                            $paymentTotals['gcash'],
                            2
                        ) }}
                    </td>
                </tr>

                <tr>
                    <td>Bank Transfer</td>
                    <td>
                        ₱{{ number_format(
                            $paymentTotals['bank_transfer'],
                            2
                        ) }}
                    </td>
                </tr>

            </tbody>

        </table>

    </div>

</div>

<div class="card">

    <h2>Sales Transactions</h2>

    @if($sales->isEmpty())

        <div class="empty-state">
            <p>
                No sales were recorded during the selected period.
            </p>
        </div>

    @else

        <div class="table-responsive">

            <table class="table">

                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Payment</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Total</th>
                        <th>Recorded By</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($sales as $sale)

                        <tr>

                            <td>
                                {{ $sale->sale_date->format('M d, Y h:i A') }}
                            </td>

                            <td>
                                {{ $sale->customer->full_name ?? 'Walk-in' }}
                            </td>

                            <td>
                                {{ strtoupper(
                                    str_replace(
                                        '_',
                                        ' ',
                                        $sale->payment_method
                                    )
                                ) }}
                            </td>

                            <td>
                                ₱{{ number_format(
                                    $sale->subtotal,
                                    2
                                ) }}
                            </td>

                            <td>
                                ₱{{ number_format(
                                    $sale->discount_amount,
                                    2
                                ) }}
                            </td>

                            <td>
                                ₱{{ number_format(
                                    $sale->total_amount,
                                    2
                                ) }}
                            </td>

                            <td>
                                {{ $sale->user->full_name ?? 'Unknown' }}
                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @endif

</div>

@endsection