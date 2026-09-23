@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1>Add Customer</h1>
        <p>Enter the customer's information.</p>
    </div>

    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
        Back to Customers
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Please correct the following:</strong>

        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">

    <div class="card-header">
        <h2>Customer Information</h2>
    </div>

    <form
        action="{{ route('customers.store') }}"
        method="POST"
        class="form-container"
    >
        @csrf

        <div class="form-row">

            <div class="form-group">
                <label for="first_name">
                    First Name <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="first_name"
                    name="first_name"
                    value="{{ old('first_name') }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="middle_name">
                    Middle Name
                </label>

                <input
                    type="text"
                    id="middle_name"
                    name="middle_name"
                    value="{{ old('middle_name') }}"
                >
            </div>

            <div class="form-group">
                <label for="last_name">
                    Last Name <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="last_name"
                    name="last_name"
                    value="{{ old('last_name') }}"
                    required
                >
            </div>

        </div>

        <div class="form-row">

            <div class="form-group">
                <label for="contact_number">
                    Contact Number <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="contact_number"
                    name="contact_number"
                    value="{{ old('contact_number') }}"
                    required
                >
            </div>

            <div class="form-group">
                <label for="address">
                    Address
                </label>

                <input
                    type="text"
                    id="address"
                    name="address"
                    value="{{ old('address') }}"
                >
            </div>

        </div>

        <div class="form-group checkbox-group">

            <label>
                <input
                    type="hidden"
                    name="is_regular"
                    value="0"
                >

                <input
                    type="checkbox"
                    name="is_regular"
                    value="1"
                    {{ old('is_regular') ? 'checked' : '' }}
                >

                Regular Customer
            </label>

            <small>
                Mark this customer if they are considered a regular customer
                of the shop.
            </small>

        </div>

        <div class="form-actions">

            <a
                href="{{ route('customers.index') }}"
                class="btn btn-secondary"
            >
                Cancel
            </a>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Save Customer
            </button>

        </div>

    </form>

</div>

@endsection