@extends('layouts.app')

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

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="card">

    <div class="card-header">
        <h2>Customer List</h2>
    </div>

    @if($customers->count())

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Contact Number</th>
                        <th>Address</th>
                        <th>Regular Customer</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($customers as $customer)
                        <tr>
                            <td>
                                <strong>{{ $customer->full_name }}</strong>
                            </td>

                            <td>
                                {{ $customer->contact_number }}
                            </td>

                            <td>
                                {{ $customer->address ?: '—' }}
                            </td>

                            <td>
                                @if($customer->is_regular)
                                    <span class="badge badge-success">
                                        Yes
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        No
                                    </span>
                                @endif
                            </td>

                            <td>
                                <a
                                    href="{{ route('customers.edit', $customer) }}"
                                    class="btn btn-secondary btn-sm"
                                >
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
            <h3>No customers yet</h3>
            <p>
                Add a customer to start building your customer records.
            </p>

            <a
                href="{{ route('customers.create') }}"
                class="btn btn-primary"
            >
                + Add Customer
            </a>
        </div>

    @endif

</div>

@endsection