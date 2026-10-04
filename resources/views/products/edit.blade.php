@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')

<div class="page-header">
    <div>
        <h1>Edit Product</h1>
        <p>
            Update the information for
            <strong style="color: #E85D75;">{{ $product->name }}</strong>.
        </p>
    </div>

```
<a href="{{ route('products.index') }}" class="btn btn-secondary">
    ← Back to Products
</a>
```

</div>

<div class="card" style="max-width: 900px;">
    <form action="{{ route('products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')

```
    <div class="form-grid">

        <div class="form-group">
            <label for="item_type">
                Item Type <span class="req">*</span>
            </label>

            <select id="item_type" name="item_type" class="form-control" required>
                <option value="material"
                    {{ old('item_type', $product->item_type) === 'material' ? 'selected' : '' }}>
                    Material
                </option>

                <option value="retail_product"
                    {{ old('item_type', $product->item_type) === 'retail_product' ? 'selected' : '' }}>
                    Retail Product
                </option>

                <option value="made_product"
                    {{ old('item_type', $product->item_type) === 'made_product' ? 'selected' : '' }}>
                    Made Product
                </option>
            </select>

            <small class="field-help">
                Choose whether this item is a material, a purchased retail item, or a product made by the shop.
            </small>

            @error('item_type')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="name">
                Product Name <span class="req">*</span>
            </label>

            <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                value="{{ old('name', $product->name) }}"
                required>

            @error('name')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="variation">Variation</label>

            <input
                type="text"
                id="variation"
                name="variation"
                class="form-control"
                value="{{ old('variation', $product->variation) }}"
                placeholder="e.g. Red, Blue, Small, Large">

            @error('variation')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="is_sellable">
                Sellable <span class="req">*</span>
            </label>

            <select id="is_sellable" name="is_sellable" class="form-control" required>
                <option value="1"
                    {{ old('is_sellable', $product->is_sellable) == '1' ? 'selected' : '' }}>
                    Yes — can be sold to customers
                </option>

                <option value="0"
                    {{ old('is_sellable', $product->is_sellable) == '0' ? 'selected' : '' }}>
                    No — not sold directly
                </option>
            </select>

            @error('is_sellable')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="selling_price">Selling Price</label>

            <div class="input-with-prefix">
                <span class="prefix">₱</span>

                <input
                    type="number"
                    id="selling_price"
                    name="selling_price"
                    class="form-control"
                    value="{{ old('selling_price', $product->selling_price) }}"
                    min="0"
                    step="0.01"
                    placeholder="0.00">
            </div>

            @error('selling_price')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="stock_unit">
                Inventory Unit <span class="req">*</span>
            </label>

            <input
                type="text"
                id="stock_unit"
                name="stock_unit"
                class="form-control"
                value="{{ old('stock_unit', $product->stock_unit) }}"
                required>

            <small class="field-help">
                The unit used when counting this item's quantity in the shop.
            </small>

            @error('stock_unit')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div id="purchase-fields" class="purchase-section">

            <div class="section-heading">
                <strong>Purchase Information</strong>
                <span>Only used for materials and retail products.</span>
            </div>

            <div class="purchase-grid">

                <div class="form-group">
                    <label for="purchase_unit">
                        How it is Purchased
                    </label>

                    <input
                        type="text"
                        id="purchase_unit"
                        name="purchase_unit"
                        class="form-control"
                        value="{{ old('purchase_unit', $product->purchase_unit) }}"
                        placeholder="e.g. bundle, box, piece">

                    @error('purchase_unit')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="units_per_purchase">
                        Quantity per Purchase
                    </label>

                    <input
                        type="number"
                        id="units_per_purchase"
                        name="units_per_purchase"
                        class="form-control"
                        value="{{ old('units_per_purchase', $product->units_per_purchase) }}"
                        min="0.01"
                        step="0.01">

                    <small class="field-help">
                        Example: 1 bundle contains 100 pieces.
                    </small>

                    @error('units_per_purchase')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

            </div>
        </div>

    </div>

    <div class="form-actions">
        <a href="{{ route('products.index') }}" class="btn btn-secondary">
            Cancel
        </a>

        <button type="submit" class="btn btn-primary">
            Update Product
        </button>
    </div>
</form>
```

</div>

<style>
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 18px 24px;
        margin-bottom: 24px;
    }

    .req {
        color: #E85D75;
        margin-left: 2px;
    }

    .field-help {
        display: block;
        margin-top: 6px;
        color: #94A3B8;
        font-size: 12px;
        line-height: 1.4;
    }

    .input-with-prefix {
        position: relative;
    }

    .input-with-prefix .prefix {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        font-weight: 600;
        font-size: 14px;
        pointer-events: none;
    }

    .input-with-prefix .form-control {
        padding-left: 32px;
    }

    .purchase-section {
        grid-column: 1 / -1;
        padding: 18px;
        background: #FAF7F3;
        border: 1px solid #F0E6DD;
        border-radius: 8px;
    }

    .section-heading {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-bottom: 16px;
    }

    .section-heading strong {
        color: #212121;
        font-size: 14px;
    }

    .section-heading span {
        color: #94A3B8;
        font-size: 12px;
    }

    .purchase-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px 24px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 20px;
        border-top: 1px solid #F0E6DD;
    }

    @media (max-width: 650px) {
        .purchase-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemType = document.getElementById('item_type');
        const purchaseFields = document.getElementById('purchase-fields');
        const purchaseUnit = document.getElementById('purchase_unit');
        const unitsPerPurchase = document.getElementById('units_per_purchase');

        function updatePurchaseFields() {
            const isMadeProduct = itemType.value === 'made_product';

            purchaseFields.style.display = isMadeProduct ? 'none' : 'block';

            if (isMadeProduct) {
                purchaseUnit.value = '';
                unitsPerPurchase.value = '';
            }
        }

        itemType.addEventListener('change', updatePurchaseFields);

        updatePurchaseFields();
    });
</script>

@endsection
