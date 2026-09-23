@extends('layouts.app')

@section('content')

<div class="page-header">
    <div>
        <h1>Inventory</h1>
        <p>View available retail and production stock.</p>
    </div>
</div>

@if(session('success'))
<div class="alert success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert error">
    {{ session('error') }}
</div>
@endif

<div class="card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Variation</th>
                <th>Retail Stock</th>
                <th>Production Stock</th>
                <th>Stock Unit</th>
                <th>Last Updated</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse($products as $product)

            @php
            $retailInventory = $product->inventory
            ->firstWhere('reserve_type', 'retail');

            $productionInventory = $product->inventory
            ->firstWhere('reserve_type', 'production');

            $lastUpdated = $product->inventory
            ->filter(fn ($item) => $item->last_updated)
            ->sortByDesc('last_updated')
            ->first();
            @endphp

            <tr>
                <td>
                    {{ $product->name }}
                </td>

                <td>
                    {{ $product->variation ?: '—' }}
                </td>

                <td>
                    {{ $retailInventory
                            ? number_format($retailInventory->current_quantity, 2)
                            : '—' }}
                </td>

                <td>
                    {{ $productionInventory
                            ? number_format($productionInventory->current_quantity, 2)
                            : '—' }}
                </td>

                <td>
                    {{ $product->stock_unit }}
                </td>

                <td>
                    {{ $lastUpdated
                            ? $lastUpdated->last_updated->format('M d, Y h:i A')
                            : '—' }}
                </td>

                <td>
                    @if($product->inventory->isEmpty())
                    <a href="{{ route('inventory.initial-stock', $product) }}"
                        class="btn btn-primary">
                        Set Initial Stock
                    </a>
                    @else
                    <a href="{{ route('inventory.adjustment', $product) }}"
                        class="btn btn-primary">
                        Adjust Stock
                    </a>

                    <a href="{{ route('inventory.history', $product) }}"
                        class="btn btn-secondary">
                        View History
                    </a>
                    @endif
                </td>
            </tr>

            @empty
            <tr>
                <td colspan="7" style="text-align: center;">
                    No products found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection