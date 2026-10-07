@extends('layouts.app')

@section('title', 'Records')

@section('content')

<div class="page-header">
    <div>
        <h1>Records</h1>
        <p>View transaction records and historical data.</p>
    </div>
</div>

{{-- TOP TABS --}}
<div class="record-tabs">
    <a href="{{ route('records.index', ['tab' => 'sales']) }}"
        class="record-tab {{ $tab === 'sales' ? 'active' : '' }}">
        Sales
    </a>
    <a href="{{ route('records.index', ['tab' => 'purchases']) }}"
        class="record-tab {{ $tab === 'purchases' ? 'active' : '' }}">
        Purchases
    </a>
    <a href="{{ route('records.index', ['tab' => 'production']) }}"
        class="record-tab {{ $tab === 'production' ? 'active' : '' }}">
        Production
    </a>
    <a href="{{ route('records.index', ['tab' => 'customers']) }}"
        class="record-tab {{ $tab === 'customers' ? 'active' : '' }}">
        Customers
    </a>
</div>

{{-- SUB TABS (only on Sales) --}}
@if($tab === 'sales')
<div class="record-sub-tabs">
    <a href="{{ route('records.index', ['tab' => 'sales', 'sub' => 'walk-in']) }}"
        class="record-sub-tab {{ $sub === 'walk-in' ? 'active' : '' }}">
        Walk-in Sales
    </a>
    <a href="{{ route('records.index', ['tab' => 'sales', 'sub' => 'online']) }}"
        class="record-sub-tab {{ $sub === 'online' ? 'active' : '' }}">
        Online Orders
    </a>
</div>
@endif

{{-- TAB ACTION BUTTON --}}
@if($tab === 'customers')
<div class="tab-action-row">
    <a href="{{ route('customers.create', ['from' => 'records']) }}" class="btn btn-primary">
        + Add Customer
    </a>
</div>
@endif

<div class="card">

    {{-- WALK-IN SALES --}}
    @if($tab === 'sales' && $sub === 'walk-in')

    @if($sales->isEmpty())
    <div class="empty-state">
        <h3>No walk-in sales yet</h3>
        <p>Sales recorded at the POS will appear here.</p>
    </div>
    @else
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Receipt No.</th>
                    <th>Date &amp; Time</th>
                    <th>Customer</th>
                    <th>Payment</th>
                    <th class="num">Total</th>
                    <th class="num">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sales as $sale)
                <tr>
                    <td style="font-family: 'SF Mono', Consolas, monospace; font-size: 12px; color: #64748B; font-weight: 600;">
                        {{ $sale->reference_code }}
                    </td>
                    <td style="color: #64748B; white-space: nowrap;">
                        {{ $sale->sale_date->format('M d, Y h:i A') }}
                    </td>
                    <td style="font-weight: 500;">
                        {{ $sale->customer->full_name ?? 'Walk-in' }}
                    </td>
                    <td>
                        <span style="font-size: 12px; font-weight: 600; text-transform: capitalize; color: {{ $sale->payment_method === 'cash' ? '#E85D75' : '#2E5A3B' }};">
                            {{ str_replace('_', ' ', $sale->payment_method) }}
                        </span>
                    </td>
                    <td class="num" style="font-weight: 700; color: #2E5A3B;">
                        ₱{{ number_format($sale->total_amount, 2) }}
                    </td>
                    <td class="num">
                        <a href="{{ route('sales.print', $sale) }}?from=records"
                            class="action-btn view">
                            View Receipt
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $sales->appends(['tab' => 'sales', 'sub' => 'walk-in'])->links() }}
    </div>
    @endif

    {{-- ONLINE ORDERS --}}
    @elseif($tab === 'sales' && $sub === 'online')

    @if($orders->isEmpty())
    <div class="empty-state">
        <h3>No online orders yet</h3>
        <p>Orders recorded through Messenger will appear here.</p>
    </div>
    @else
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Order No.</th>
                    <th>Date &amp; Time</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th class="num">Total</th>
                    <th class="num">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td style="font-family: 'SF Mono', Consolas, monospace; font-size: 12px; color: #64748B; font-weight: 600;">
                        {{ $order->reference_code }}
                    </td>
                    <td style="color: #64748B; white-space: nowrap;">
                        {{ $order->order_date->format('M d, Y h:i A') }}
                    </td>
                    <td style="font-weight: 500;">
                        {{ $order->customer->full_name ?? $order->customer_name ?? 'Unregistered' }}
                    </td>
                    <td>
                        <span style="font-size: 12px; font-weight: 600; text-transform: capitalize;
                                        color: @switch($order->order_status)
                                            @case('pending') #B8860B @break
                                            @case('completed') #2E5A3B @break
                                            @case('cancelled') #DC3545 @break
                                            @default #64748B
                                        @endswitch;">
                            {{ str_replace('_', ' ', $order->order_status) }}
                        </span>
                    </td>
                    <td class="num" style="font-weight: 700; color: #2E5A3B;">
                        ₱{{ number_format($order->total_amount, 2) }}
                    </td>
                    <td class="num">
                        <div class="action-group">
                            <a href="{{ route('orders.show', ['order' => $order, 'from' => 'records']) }}" class="action-btn view">
                                View
                            </a>
                            <a href="{{ route('orders.print', $order) }}?from=records" class="action-btn receipt">
                                Receipt
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $orders->appends(['tab' => 'sales', 'sub' => 'online'])->links() }}
    </div>
    @endif

    {{-- PURCHASES --}}
    @elseif($tab === 'purchases')

    @if($purchases->isEmpty())
    <div class="empty-state">
        <h3>No stock-in records yet</h3>
        <p>Restocking entries will appear here.</p>
    </div>
    @else
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Date &amp; Time</th>
                    <th>Supplier</th>
                    <th>Recorded By</th>
                    <th class="num">Total Amount</th>
                    <th class="num">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchases as $purchase)
                <tr>
                    <td style="color: #64748B; white-space: nowrap;">
                        {{ $purchase->purchase_date->format('M d, Y h:i A') }}
                    </td>
                    <td style="font-weight: 500;">
                        {{ $purchase->supplier_name ?: '—' }}
                    </td>
                    <td style="color: #64748B;">
                        {{ $purchase->user->full_name ?? 'Unknown' }}
                    </td>
                    <td class="num" style="font-weight: 700; color: #2E5A3B;">
                        ₱{{ number_format($purchase->total_amount, 2) }}
                    </td>
                    <td class="num">
                        <a href="{{ route('purchases.show', $purchase) }}" class="action-btn view">
                            View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $purchases->appends(['tab' => 'purchases'])->links() }}
    </div>
    @endif

    {{-- PRODUCTION --}}
    @elseif($tab === 'production')

    @if($productions->isEmpty())
    <div class="empty-state">
        <h3>No production records yet</h3>
        <p>Production entries will appear here.</p>
    </div>
    @else
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Date &amp; Time</th>
                    <th>Product</th>
                    <th>Produced By</th>
                    <th class="num">Quantity Produced</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productions as $production)
                <tr>
                    <td style="color: #64748B; white-space: nowrap;">
                        {{ $production->production_date->format('M d, Y h:i A') }}
                    </td>
                    <td style="font-weight: 600;">
                        {{ $production->product->display_name }}
                    </td>
                    <td style="color: #64748B;">
                        {{ $production->producedBy->full_name ?? 'Unknown' }}
                    </td>
                    <td class="num" style="font-weight: 700; color: #2E5A3B;">
                        {{ (float) $production->quantity_produced }} {{ $production->product->stock_unit }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $productions->appends(['tab' => 'production'])->links() }}
    </div>
    @endif

    {{-- CUSTOMERS --}}
    @elseif($tab === 'customers')

    @if($customers->isEmpty())
    <div class="empty-state">
        <h3>No customers yet</h3>
        <p>Click "+ Add Customer" to register the first one.</p>
    </div>
    @else
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact Number</th>
                    <th>Address</th>
                    <th>Regular</th>
                    <th class="num">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                <tr>
                    <td style="font-weight: 600;">
                        {{ $customer->full_name }}
                    </td>
                    <td style="color: #64748B;">
                        {{ $customer->contact_number }}
                    </td>
                    <td style="color: #64748B;">
                        {{ $customer->address ?: '—' }}
                    </td>
                    <td>
                        @if($customer->is_regular)
                        <span style="color: #2E5A3B; font-weight: 600; font-size: 13px;">Yes</span>
                        @else
                        <span style="color: #94A3B8; font-style: italic; font-size: 13px;">No</span>
                        @endif
                    </td>
                    <td class="num">
                        <a href="{{ route('customers.edit', ['customer' => $customer, 'from' => 'records']) }}"
                            class="action-btn view">
                            View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $customers->appends(['tab' => 'customers'])->links() }}
    </div>
    @endif

    @endif

</div>

<style>
    .record-tabs {
        display: flex;
        gap: 28px;
        margin-bottom: 20px;
        border-bottom: 1.5px solid #F0E6DD;
    }

    .record-tab {
        display: inline-flex;
        align-items: center;
        padding: 10px 2px;
        text-decoration: none;
        font-size: 15px;
        font-weight: 600;
        color: #94A3B8;
        border-bottom: 2px solid transparent;
        margin-bottom: -1.5px;
        transition: all 0.15s ease;
    }

    .record-tab:hover {
        color: #E85D75;
    }

    .record-tab.active {
        color: #E85D75;
        border-bottom-color: #E85D75;
    }

    .record-sub-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        background: #FFFFFF;
        padding: 4px;
        border-radius: 10px;
        border: 1px solid #F0E6DD;
        width: fit-content;
    }

    .record-sub-tab {
        padding: 8px 18px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        color: #64748B;
        transition: all 0.15s ease;
    }

    .record-sub-tab:hover {
        background: #FEFCF9;
        color: #E85D75;
    }

    .record-sub-tab.active {
        background: #FCE4EC;
        color: #E85D75;
    }

    .tab-action-row {
        display: flex;
        justify-content: flex-start;
        margin-bottom: 16px;
    }

    .action-group {
        display: inline-flex;
        gap: 6px;
        justify-content: flex-end;
    }

    .action-btn {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.15s ease;
        white-space: nowrap;
        font-family: inherit;
    }

    .action-btn.view {
        background: #FCE4EC;
        color: #E85D75;
    }

    .action-btn.view:hover {
        background: #F8BBD0;
        color: #D14A62;
    }

    .action-btn.receipt {
        background: #F1F5F9;
        color: #475569;
    }

    .action-btn.receipt:hover {
        background: #E2E8F0;
        color: #1E293B;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748B;
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

    .pagination-wrap {
        margin-top: 20px;
        display: flex;
        justify-content: center;
    }

    @media (max-width: 640px) {
        .record-tabs {
            gap: 20px;
            overflow-x: auto;
        }

        .record-tab {
            font-size: 13px;
            white-space: nowrap;
        }
    }
</style>

@endsection