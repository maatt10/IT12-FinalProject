@extends('layouts.app')

@php
    $typeLabel = $product->item_type === 'material' ? 'Material' : 'Product';
    $tabParam = $product->item_type === 'material' ? 'material' : 'product';
@endphp

@section('title', 'Edit ' . $typeLabel)

@section('content')

<div class="form-page-wide">

    <div class="page-header">
        <div>
            <h1>Edit {{ $typeLabel }}</h1>
            <p>Update the information for <strong style="color: #E85D75;">{{ $product->name }}</strong>.</p>
        </div>

        <a href="{{ route('products.index', ['item_type' => $tabParam]) }}" class="btn btn-secondary">
            ← Back to Inventory
        </a>
    </div>

    <div class="card">
        <form action="{{ route('products.update', $product) }}" method="POST" id="product-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="item_type" value="{{ $product->item_type }}">

            <div class="form-grid">

                <div class="form-group">
                    <label>Reference Code</label>
                    <input type="text" class="form-control readonly-field" value="{{ $product->reference_code }}" readonly>
                    <small class="field-help">System-generated. Cannot be changed.</small>
                </div>

                <div class="form-group">
                    <label>{{ $typeLabel }} Name <span class="req">*</span></label>
                    <input type="text" id="name" name="name" class="form-control"
                           value="{{ old('name', $product->name) }}" required>
                    @error('name') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="variation">Variation</label>
                    <input type="text" id="variation" name="variation" class="form-control"
                           value="{{ old('variation', $product->variation) }}">
                    @error('variation') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group full-width">
                    <label for="stock_purpose">Stock Purpose <span class="req">*</span></label>
                    <select id="stock_purpose" name="stock_purpose" class="form-control" required>
                        <option value="retail" {{ old('stock_purpose', $product->stock_purpose) === 'retail' ? 'selected' : '' }}>Retail only — sold directly to customers</option>
                        <option value="production" {{ old('stock_purpose', $product->stock_purpose) === 'production' ? 'selected' : '' }}>Production only — used as a component</option>
                        <option value="both" {{ old('stock_purpose', $product->stock_purpose) === 'both' ? 'selected' : '' }}>Both — sold directly AND used as a component</option>
                    </select>
                    @error('stock_purpose') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group" id="selling-price-field">
                    <label for="selling_price">Selling Price</label>
                    <div class="input-with-prefix">
                        <span class="prefix">₱</span>
                        <input type="number" id="selling_price" name="selling_price" class="form-control"
                               value="{{ old('selling_price', $product->selling_price) }}" min="0" step="0.01">
                    </div>
                    @error('selling_price') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="stock_unit">Inventory Unit <span class="req">*</span></label>
                    <input type="text" id="stock_unit" name="stock_unit" class="form-control"
                           value="{{ old('stock_unit', $product->stock_unit) }}" required>
                    @error('stock_unit') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="low_stock_threshold">Low Stock Threshold</label>
                    <input type="number" id="low_stock_threshold" name="low_stock_threshold" class="form-control"
                           value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" min="0" step="0.01">
                    <small class="field-help">Alert when stock drops to this amount or below.</small>
                    @error('low_stock_threshold') <div class="error">{{ $message }}</div> @enderror
                </div>

                @if($product->item_type === 'material')
                    <div class="purchase-section full-width">
                        <div class="section-heading">
                            <strong>Purchase Information</strong>
                            <span>How this material is bought from suppliers.</span>
                        </div>
                        <div class="purchase-grid">
                            <div class="form-group">
                                <label for="purchase_unit">How it is Purchased</label>
                                <input type="text" id="purchase_unit" name="purchase_unit" class="form-control"
                                       value="{{ old('purchase_unit', $product->purchase_unit) }}">
                                @error('purchase_unit') <div class="error">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label for="units_per_purchase">Quantity per Purchase</label>
                                <input type="number" id="units_per_purchase" name="units_per_purchase" class="form-control"
                                       value="{{ old('units_per_purchase', $product->units_per_purchase) }}" min="0.01" step="0.01">
                                @error('units_per_purchase') <div class="error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            @if($product->item_type === 'made_product')
                <div class="bom-section full-width">
                    <div class="section-heading-row">
                        <div>
                            <strong>Bill of Materials</strong>
                            <span class="section-sub">Materials required to make one unit of this product.</span>
                        </div>
                        <button type="button" class="btn-add-bom" onclick="addBomRow()">+ Add Material</button>
                    </div>

                    @if($materials->isEmpty() && $product->parentComponents->isEmpty())
                        <p style="color: #94A3B8; font-size: 13px; padding: 20px 0;">
                            No materials available. Add materials with "Production" or "Both" stock purpose first.
                        </p>
                    @else
                        <div id="bom-rows"></div>
                        <p id="bom-empty" style="color: #94A3B8; font-size: 13px; padding: 10px 0; display: none;">
                            No materials added yet.
                        </p>
                    @endif
                </div>
            @endif

            <div class="form-actions">
                <a href="{{ route('products.index', ['item_type' => $tabParam]) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update {{ $typeLabel }}</button>
            </div>
        </form>
    </div>

</div>

<style>
    .form-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 18px 24px;
        margin-bottom: 24px;
    }
    .form-grid > .form-group {
        flex: 1 1 260px;
        min-width: 0;
        margin-bottom: 0;
    }
    .form-grid > .form-group.full-width {
        flex-basis: 100%;
    }

    .req { color: #E85D75; margin-left: 2px; }

    .field-help {
        display: block; margin-top: 6px;
        color: #94A3B8; font-size: 12px; line-height: 1.4;
    }

    .readonly-field {
        background: #FEFCF9 !important;
        color: #64748B; cursor: default;
        font-family: 'SF Mono', Consolas, monospace;
        font-weight: 600; letter-spacing: 0.5px;
    }

    .input-with-prefix { position: relative; }
    .input-with-prefix .prefix {
        position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
        color: #94A3B8; font-weight: 600; font-size: 14px; pointer-events: none;
    }
    .input-with-prefix .form-control { padding-left: 32px; }

    .purchase-section, .bom-section {
        flex-basis: 100%; padding: 18px;
        background: #FAF7F3; border: 1px solid #F0E6DD;
        border-radius: 10px; margin-bottom: 20px;
    }

    .section-heading, .section-heading-row > div {
        display: flex; flex-direction: column; gap: 4px; margin-bottom: 16px;
    }
    .section-heading-row {
        display: flex; justify-content: space-between; align-items: flex-start;
        gap: 12px; margin-bottom: 16px;
    }
    .section-heading strong, .section-heading-row strong {
        color: #212121; font-size: 14px;
    }
    .section-heading span, .section-sub { color: #94A3B8; font-size: 12px; }

    .purchase-grid {
        display: grid; grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px 24px;
    }

    .btn-add-bom {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 14px; border-radius: 8px;
        background: #E85D75; color: #FFFFFF; border: none;
        font-size: 13px; font-weight: 600; cursor: pointer; font-family: inherit;
    }
    .btn-add-bom:hover { background: #D14A62; }

    .bom-row {
        display: grid; grid-template-columns: 1fr 140px 40px; gap: 10px;
        align-items: center; padding: 10px;
        background: #FFFFFF; border: 1px solid #F0E6DD;
        border-radius: 8px; margin-bottom: 8px;
    }
    .bom-row select, .bom-row input {
        padding: 8px 10px; border: 1.5px solid #F0E6DD;
        border-radius: 6px; font-size: 13px; font-family: inherit; background: #FFFFFF;
    }
    .bom-row select:focus, .bom-row input:focus { outline: none; border-color: #E85D75; }
    .bom-remove {
        width: 32px; height: 32px; background: #FDECEA; color: #DC3545;
        border: none; border-radius: 6px; cursor: pointer;
        font-size: 16px; font-weight: 700; font-family: inherit;
    }
    .bom-remove:hover { background: #F8D7DA; }

    .form-actions {
        display: flex; justify-content: flex-end; gap: 10px;
        padding-top: 20px; border-top: 1px solid #F0E6DD;
    }

    @media (max-width: 650px) {
        .purchase-grid { grid-template-columns: 1fr; }
        .bom-row { grid-template-columns: 1fr 80px 40px; }
    }
</style>

<script>
    const MATERIALS = @json($materials->map(fn($m) => ['id' => $m->product_id, 'name' => $m->display_name]));
    const EXISTING_BOM = @json($product->parentComponents->map(fn($c) => ['id' => $c->material_product_id, 'qty' => (float) $c->quantity_required]));

    document.addEventListener('DOMContentLoaded', function () {
        const stockPurpose = document.getElementById('stock_purpose');
        const sellingPriceField = document.getElementById('selling-price-field');

        function updateFieldVisibility() {
            const purpose = stockPurpose.value;
            const hasRetail = purpose === 'retail' || purpose === 'both';
            sellingPriceField.style.display = hasRetail ? 'block' : 'none';
        }
        stockPurpose.addEventListener('change', updateFieldVisibility);
        updateFieldVisibility();

        @if($product->item_type === 'made_product')
            if (EXISTING_BOM.length > 0) {
                EXISTING_BOM.forEach(row => addBomRow(row.id, row.qty));
            } else {
                addBomRow();
            }
        @endif
    });

    function addBomRow(materialId = '', qty = '') {
        const container = document.getElementById('bom-rows');
        if (!container) return;
        const index = container.children.length;

        const row = document.createElement('div');
        row.className = 'bom-row';
        row.innerHTML = `
            <select name="bom[${index}][material_product_id]" required onchange="refreshBomOptions()">
                <option value="">— Select material —</option>
                ${MATERIALS.map(m => `<option value="${m.id}" ${m.id == materialId ? 'selected' : ''}>${m.name}</option>`).join('')}
            </select>
            <input type="number" name="bom[${index}][quantity_required]" min="0.01" step="0.01" placeholder="Qty" value="${qty}" required>
            <button type="button" class="bom-remove" onclick="removeBomRow(this)">×</button>
        `;
        container.appendChild(row);

        const emptyMsg = document.getElementById('bom-empty');
        if (emptyMsg) emptyMsg.style.display = 'none';
        refreshBomOptions();
    }

    function removeBomRow(btn) {
        btn.parentElement.remove();
        reindexBom();
        refreshBomOptions();
    }

    function refreshBomOptions() {
        const rows = document.querySelectorAll('.bom-row');
        if (!rows.length) return;

        const selectedIds = new Set();
        rows.forEach(row => {
            const sel = row.querySelector('select');
            if (sel.value) selectedIds.add(sel.value);
        });

        rows.forEach(row => {
            const sel = row.querySelector('select');
            const currentValue = sel.value;

            Array.from(sel.options).forEach(opt => {
                if (!opt.value) return;
                if (opt.value === currentValue) opt.disabled = false;
                else if (selectedIds.has(opt.value)) opt.disabled = true;
                else opt.disabled = false;
            });
        });
    }

    function reindexBom() {
        const container = document.getElementById('bom-rows');
        if (!container) return;
        const rows = container.querySelectorAll('.bom-row');
        rows.forEach((row, i) => {
            row.querySelector('select').name = `bom[${i}][material_product_id]`;
            row.querySelector('input').name = `bom[${i}][quantity_required]`;
        });
    }
</script>

@endsection