@extends('layouts.app')

@section('title', 'Customers')

@section('content')

<div class="page-header">
    <div>
        <h1>Customers</h1>
        <p>Manage customer information and regular customer records.</p>
    </div>

    <a href="{{ route('customers.create') }}" class="btn btn-primary">
        + Add Customer
    </a>
</div>

<div class="card">

    @if($customers->count())

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Contact Number</th>
                        <th>Address</th>
                        <th>Regular</th>
                        <th style="text-align: right;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($customers as $customer)
                        <tr>
                            <td style="font-weight: 600; color: #212121;">
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
                                    <span class="sellable-yes">✓ Yes</span>
                                @else
                                    <span class="sellable-no">No</span>
                                @endif
                            </td>

                            <td style="text-align: right;">
                                <a href="{{ route('customers.edit', $customer) }}" class="action-btn edit">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @else

        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3>No customers yet</h3>
            <p>Add a customer to start building your customer records.</p>
            <a href="{{ route('customers.create') }}" class="btn btn-primary">
                + Add Customer
            </a>
        </div>

    @endif

</div>

<style>
    /* Sellable/Regular indicator — plain text, clearly not clickable */
    .sellable-yes {
        color: #2E5A3B;
        font-weight: 600;
        font-size: 13px;
        cursor: default;
        user-select: none;
    }
    .sellable-no {
        color: #94A3B8;
        font-weight: 500;
        font-size: 13px;
        font-style: italic;
        cursor: default;
        user-select: none;
    }

    /* Compact action buttons */
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

    .action-btn.edit {
        background: #FCE4EC;
        color: #E85D75;
    }
    .action-btn.edit:hover {
        background: #F8BBD0;
        color: #D14A62;
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
        margin-bottom: 20px;
    }
</style>

@endsection