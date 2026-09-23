@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">
        Dashboard
    </h1>

```
<p class="text-sm text-gray-500 mt-1">
    Welcome back, {{ auth()->user()->full_name }}.
</p>
```

</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

```
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
    <p class="text-sm text-gray-500">
        Today's Sales
    </p>

    <p class="text-2xl font-bold text-gray-800 mt-2">
        ₱{{ number_format((float) $todaySales, 2) }}
    </p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
    <p class="text-sm text-gray-500">
        Today's Transactions
    </p>

    <p class="text-2xl font-bold text-gray-800 mt-2">
        {{ $todayTransactions }}
    </p>
</div>
```

</div>

@if(auth()->user()->role === 'owner')

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-6">

```
<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">
            Pending Online Orders
        </h2>

        <p class="text-sm text-gray-500">
            Orders that still require processing.
        </p>
    </div>

    <a
        href="{{ route('orders.index') }}"
        class="text-sm font-medium text-green-700 hover:text-green-800"
    >
        View All
    </a>
</div>

@if($pendingOrders->isEmpty())

    <p class="text-sm text-gray-500">
        No pending online orders.
    </p>

@else

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-gray-500">
                    <th class="py-3 pr-4">Order</th>
                    <th class="py-3 pr-4">Customer</th>
                    <th class="py-3 pr-4">Status</th>
                    <th class="py-3 pr-4">Total</th>
                    <th class="py-3">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($pendingOrders as $order)
                    <tr class="border-b last:border-0">
                        <td class="py-3 pr-4 font-medium text-gray-800">
                            #{{ $order->order_id }}
                        </td>

                        <td class="py-3 pr-4 text-gray-700">
                            {{ $order->customer->full_name ?? 'Unknown Customer' }}
                        </td>

                        <td class="py-3 pr-4">
                            <span class="capitalize text-gray-700">
                                {{ str_replace('_', ' ', $order->order_status) }}
                            </span>
                        </td>

                        <td class="py-3 pr-4 text-gray-700">
                            ₱{{ number_format((float) $order->total_amount, 2) }}
                        </td>

                        <td class="py-3">
                            <a
                                href="{{ route('orders.show', $order) }}"
                                class="text-green-700 hover:text-green-800 font-medium"
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
```

</div>

@endif

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-6">

```
<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-lg font-semibold text-gray-800">
            Recent Sales
        </h2>

        <p class="text-sm text-gray-500">
            Latest recorded sales transactions.
        </p>
    </div>

    <a
        href="{{ route('sales.create') }}"
        class="text-sm font-medium text-green-700 hover:text-green-800"
    >
        New Sale
    </a>
</div>

@if($recentSales->isEmpty())

    <p class="text-sm text-gray-500">
        No sales transactions have been recorded yet.
    </p>

@else

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b text-left text-gray-500">
                    <th class="py-3 pr-4">Date</th>
                    <th class="py-3 pr-4">Customer</th>
                    <th class="py-3 pr-4">Payment</th>
                    <th class="py-3">Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach($recentSales as $sale)
                    <tr class="border-b last:border-0">
                        <td class="py-3 pr-4 text-gray-700">
                            {{ $sale->sale_date?->format('M d, Y h:i A') }}
                        </td>

                        <td class="py-3 pr-4 text-gray-700">
                            {{ $sale->customer->full_name ?? 'Walk-in Customer' }}
                        </td>

                        <td class="py-3 pr-4 capitalize text-gray-700">
                            {{ str_replace('_', ' ', $sale->payment_method) }}
                        </td>

                        <td class="py-3 font-medium text-gray-800">
                            ₱{{ number_format((float) $sale->total_amount, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endif
```

</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">

```
<h2 class="text-lg font-semibold text-gray-800 mb-4">
    Quick Actions
</h2>

<div class="grid grid-cols-2 md:grid-cols-3 gap-3">

    <a
        href="{{ route('sales.create') }}"
        class="border rounded-lg p-4 hover:bg-gray-50 transition"
    >
        <p class="font-medium text-gray-800">
            New Sale
        </p>

        <p class="text-xs text-gray-500 mt-1">
            Record a customer purchase
        </p>
    </a>

    <a
        href="{{ route('inventory.index') }}"
        class="border rounded-lg p-4 hover:bg-gray-50 transition"
    >
        <p class="font-medium text-gray-800">
            Inventory
        </p>

        <p class="text-xs text-gray-500 mt-1">
            View current stock
        </p>
    </a>

    <a
        href="{{ route('customers.index') }}"
        class="border rounded-lg p-4 hover:bg-gray-50 transition"
    >
        <p class="font-medium text-gray-800">
            Customers
        </p>

        <p class="text-xs text-gray-500 mt-1">
            Manage customer records
        </p>
    </a>

    <a
        href="{{ route('purchases.index') }}"
        class="border rounded-lg p-4 hover:bg-gray-50 transition"
    >
        <p class="font-medium text-gray-800">
            Purchases
        </p>

        <p class="text-xs text-gray-500 mt-1">
            Record purchases
        </p>
    </a>

    <a
        href="{{ route('production.index') }}"
        class="border rounded-lg p-4 hover:bg-gray-50 transition"
    >
        <p class="font-medium text-gray-800">
            Production
        </p>

        <p class="text-xs text-gray-500 mt-1">
            Record product production
        </p>
    </a>

    @if(auth()->user()->role === 'owner')

        <a
            href="{{ route('orders.index') }}"
            class="border rounded-lg p-4 hover:bg-gray-50 transition"
        >
            <p class="font-medium text-gray-800">
                Online Orders
            </p>

            <p class="text-xs text-gray-500 mt-1">
                Manage bouquet orders
            </p>
        </a>

    @endif

</div>
```

</div>

@endsection
