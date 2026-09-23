@extends('layouts.app')

@section('title', 'Record Production')

@section('content')

@php
$productionProducts = $products->map(function ($product) {
return [
'id' => $product->product_id,
'name' => $product->display_name,
'components' => $product->parentComponents->map(function ($component) {
return [
'name' => $component->materialProduct->display_name,
'quantity_required' => (float) $component->quantity_required,
'stock_unit' => $component->materialProduct->stock_unit,
];
})->values()->all(),
];
})->values()->all();
@endphp

<div class="max-w-4xl space-y-6">

```
<div>
    <h1 class="text-2xl font-bold text-gray-800">
        Record Production
    </h1>

    <p class="mt-1 text-sm text-gray-600">
        Select a product and enter the quantity to produce. Required materials are calculated from the product's BOM.
    </p>
</div>

<form
    action="{{ route('production.store') }}"
    method="POST"
    class="space-y-6"
>
    @csrf

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">

        <div>
            <label
                for="product_id"
                class="block text-sm font-medium text-gray-700 mb-2"
            >
                Product to Produce
            </label>

            <select
                id="product_id"
                name="product_id"
                required
                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
            >
                <option value="">
                    Select a product
                </option>

                @foreach($products as $product)

                    <option
                        value="{{ $product->product_id }}"
                        @selected(old('product_id') == $product->product_id)
                    >
                        {{ $product->display_name }}
                    </option>

                @endforeach

            </select>

            @error('product_id')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div>
            <label
                for="quantity_produced"
                class="block text-sm font-medium text-gray-700 mb-2"
            >
                Quantity to Produce
            </label>

            <input
                type="number"
                id="quantity_produced"
                name="quantity_produced"
                value="{{ old('quantity_produced') }}"
                min="0.01"
                step="0.01"
                required
                class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                placeholder="Enter quantity"
            >

            <p class="mt-1 text-xs text-gray-500">
                Quantity is entered using the product's stock unit.
            </p>

            @error('quantity_produced')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

    </div>

    {{-- BOM Preview --}}
    <div
        id="bom-card"
        class="hidden bg-white rounded-lg shadow-sm border border-gray-200 p-6"
    >

        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-800">
                Required Materials
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                The following quantities will be deducted from production inventory.
            </p>
        </div>

        <div class="overflow-x-auto">

            <table class="w-full text-sm">

                <thead>
                    <tr class="border-b border-gray-200 text-left">

                        <th class="px-4 py-3 font-semibold text-gray-700">
                            Material
                        </th>

                        <th class="px-4 py-3 font-semibold text-gray-700">
                            Per Product
                        </th>

                        <th class="px-4 py-3 font-semibold text-gray-700">
                            Total Required
                        </th>

                        <th class="px-4 py-3 font-semibold text-gray-700">
                            Stock Unit
                        </th>

                    </tr>
                </thead>

                <tbody id="bom-body"></tbody>

            </table>

        </div>

    </div>

    <div class="flex flex-col-reverse sm:flex-row gap-3">

        <a
            href="{{ route('production.index') }}"
            class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-gray-100 text-gray-700 font-medium hover:bg-gray-200 transition"
        >
            Cancel
        </a>

        <button
            type="submit"
            class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-green-600 text-white font-medium hover:bg-green-700 transition"
        >
            Complete Production
        </button>

    </div>

</form>
```

</div>

<script>
    const products = @json($productionProducts);

    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity_produced');
    const bomCard = document.getElementById('bom-card');
    const bomBody = document.getElementById('bom-body');

    function updateBomPreview() {
        const selectedId = Number(productSelect.value);
        const quantity = Number(quantityInput.value);

        bomBody.innerHTML = '';

        if (
            !selectedId ||
            !Number.isFinite(quantity) ||
            quantity <= 0
        ) {
            bomCard.classList.add('hidden');
            return;
        }

        const product = products.find(function (item) {
            return Number(item.id) === selectedId;
        });

        if (
            !product ||
            !product.components ||
            product.components.length === 0
        ) {
            bomCard.classList.add('hidden');
            return;
        }

        product.components.forEach(function (component) {
            const totalRequired =
                component.quantity_required * quantity;

            const row = document.createElement('tr');

            row.className = 'border-b border-gray-100';

            const materialCell = document.createElement('td');
            materialCell.className = 'px-4 py-3 text-gray-800';
            materialCell.textContent = component.name;

            const perProductCell = document.createElement('td');
            perProductCell.className = 'px-4 py-3 text-gray-600';
            perProductCell.textContent =
                formatQuantity(component.quantity_required);

            const totalCell = document.createElement('td');
            totalCell.className =
                'px-4 py-3 font-medium text-gray-800';
            totalCell.textContent =
                formatQuantity(totalRequired);

            const unitCell = document.createElement('td');
            unitCell.className = 'px-4 py-3 text-gray-600';
            unitCell.textContent = component.stock_unit;

            row.appendChild(materialCell);
            row.appendChild(perProductCell);
            row.appendChild(totalCell);
            row.appendChild(unitCell);

            bomBody.appendChild(row);
        });

        bomCard.classList.remove('hidden');
    }

    function formatQuantity(value) {
        return Number(value).toLocaleString(undefined, {
            minimumFractionDigits: 0,
            maximumFractionDigits: 2
        });
    }

    productSelect.addEventListener(
        'change',
        updateBomPreview
    );

    quantityInput.addEventListener(
        'input',
        updateBomPreview
    );

    updateBomPreview();
</script>

@endsection