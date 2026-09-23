<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'contact_number' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_regular' => ['required', 'boolean'],
        ]);

        $customer = Customer::create($validated);

        app(AuditLogger::class)->log(
            'create',
            'customers',
            $customer->customer_id,
            'Customer created: ' . $customer->full_name
        );

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer added successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'contact_number' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
            'is_regular' => ['required', 'boolean'],
        ]);

        $customer->update($validated);

        app(AuditLogger::class)->log(
            'update',
            'customers',
            $customer->customer_id,
            'Customer updated: ' . $customer->full_name
        );

        return redirect()
            ->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }
}