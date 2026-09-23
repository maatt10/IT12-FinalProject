@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1>Purchases</h1>
        <p>View recorded purchases and restocking transactions.</p>
    </div>

    <a href="{{ route('purchases.create') }}" class="btn btn-primary">
        Record Purchase
    </a>
</div>

<div class="card">

    @if($purchases->isEmpty())

        <div class="empty-state">
            <p>No purchases have been recorded yet.</p>

            <a
                href="{{ route('purchases.create') }}"
                class="btn btn-primary"
            >
                Record First Purchase
            </a>
        </div>

    @else

        <div class="table-responsive">
            <table class="table">

                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Supplier</th>
                        <th>Recorded By</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($purchases as $purchase)

                        <tr>

                            <td>
                                {{ $purchase->purchase_date->format('M d, Y h:i A') }}
                            </td>

                            <td>
                                {{ $purchase->supplier_name ?: 'Not specified' }}
                            </td>

                            <td>
                                {{ $purchase->user->full_name ?? 'Unknown' }}
                            </td>

                            <td>
                                ₱{{ number_format($purchase->total_amount, 2) }}
                            </td>

                            <td>
                                <a
                                    href="{{ route('purchases.show', $purchase) }}"
                                    class="btn btn-secondary"
                                >
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

@endsection