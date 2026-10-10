@extends('layouts.app')

@php
$typeLabel = $itemType === 'material' ? 'Material' : 'Product';
$tabParam = $itemType === 'material' ? 'material' : 'product';

$stockUnitOptions = ['piece', 'stem', 'bundle', 'pack', 'set', 'roll', 'meter', 'box'];
$purchaseUnitOptions = ['bundle', 'box', 'pack', 'roll', 'case', 'carton', 'bag', 'sack', 'piece'];
@endphp

@section('title', 'Add ' . $typeLabel)

@section('content')

<div class="form-page-wide">

    <div class="page-header">
        <div>
            <h1>Add {{ $typeLabel }}</h1>
            <p>Add a new {{ strtolower($typeLabel) }} to the system.</p>
        </div>

        <a href="{{ route('products.index', ['item_type' => $tabParam]) }}" class="btn btn-secondary">
            ← Back to Inventory
        </a>
    </div>

    <div class="card">
        <form action="{{ route('products.store') }}" method="POST" id="product-form" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="item_type" value="{{ $itemType }}">

            <div class="form-grid">

                <div class="form-group">
                    <label for="name">{{ $typeLabel }} Name <span class="req">*</span></label>
                    <input type="text" id="name" name="name" class="form-control"
                        value="{{ old('name') }}"
                        placeholder="{{ $itemType === 'material' ? 'e.g. Fuzzy Wire, Red Rose Stem' : 'e.g. Red Rose Bouquet' }}"
                        required>
                    @error('name') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="variation">Variation</label>
                    <input type="text" id="variation" name="variation" class="form-control"
                        value="{{ old('variation') }}"
                        placeholder="e.g. Red, Blue, Small, Large">
                    @error('variation') <div class="error">{{ $message }}</div> @enderror
                </div>
                @if($itemType === 'made_product')
                <div class="form-group full-width" id="image-field" style="display: none;">
                    <label for="image">Reference Image</label>
                    <div class="image-upload-wrap">
                        <div class="image-preview" id="image-preview">
                            <div class="image-preview-empty" id="image-preview-empty">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>No image selected</span>
                            </div>
                            <img id="image-preview-img" src="" alt="" style="display: none;">
                        </div>
                        <div class="image-upload-controls">
                            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                                class="form-control file-input" onchange="previewImage(this)">
                            <small class="field-help">
                                Optional. Used as a visual reference when recording orders. JPG, PNG, or WebP. Max 4 MB.
                            </small>
                            @error('image') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                @endif

                <div class="form-group full-width">
                    <label for="stock_purpose">Stock Purpose <span class="req">*</span></label>
                    <select id="stock_purpose" name="stock_purpose" class="form-control" required>
                        <option value="">— Select how this item is used —</option>
                        <option value="retail" {{ old('stock_purpose') === 'retail' ? 'selected' : '' }}>Retail only — sold directly to customers</option>
                        <option value="production" {{ old('stock_purpose') === 'production' ? 'selected' : '' }}>Production only — used as a component</option>
                        <option value="both" {{ old('stock_purpose') === 'both' ? 'selected' : '' }}>Both — sold directly AND used as a component</option>
                    </select>
                    <small class="field-help">
                        Determines whether this item appears in POS as sellable, in BOM as a component, or both.
                    </small>
                    @error('stock_purpose') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group" id="selling-price-field" style="display: none;">
                    <label for="selling_price">Selling Price</label>
                    <div class="input-with-prefix">
                        <span class="prefix">₱</span>
                        <input type="number" id="selling_price" name="selling_price" class="form-control"
                            value="{{ old('selling_price') }}" min="0" step="0.01" placeholder="0.00">
                    </div>
                    @error('selling_price') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="stock_unit">Inventory Unit <span class="req">*</span></label>
                    <select id="stock_unit" name="stock_unit" class="form-control" required>
                        <option value="">— Select inventory unit —</option>
                        @foreach($stockUnitOptions as $unit)
                        <option value="{{ $unit }}" {{ old('stock_unit') === $unit ? 'selected' : '' }}>
                            {{ ucfirst($unit) }}
                        </option>
                        @endforeach
                    </select>
                    <small class="field-help">The unit used when counting this item's quantity.</small>
                    @error('stock_unit') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="low_stock_threshold">Low Stock Threshold</label>
                    <input type="number" id="low_stock_threshold" name="low_stock_threshold" class="form-control"
                        value="{{ old('low_stock_threshold', 10) }}" min="0" step="0.01">
                    <small class="field-help">
                        Alert when stock drops to this amount or below. Applied to both retail and production stock.
                    </small>
                    @error('low_stock_threshold') <div class="error">{{ $message }}</div> @enderror
                </div>

                @if($itemType === 'material')
                <div class="purchase-section full-width">
                    <div class="section-heading">
                        <strong>Purchase Information</strong>
                        <span>How this material is bought from suppliers.</span>
                    </div>
                    <div class="purchase-grid">
                        <div class="form-group">
                            <label for="purchase_unit">How it is Purchased</label>
                            <select id="purchase_unit" name="purchase_unit" class="form-control">
                                <option value="">— Select purchase unit —</option>
                                @foreach($purchaseUnitOptions as $unit)
                                <option value="{{ $unit }}" {{ old('purchase_unit') === $unit ? 'selected' : '' }}>
                                    {{ ucfirst($unit) }}
                                </option>
                                @endforeach
                            </select>
                            @error('purchase_unit') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label for="units_per_purchase">Quantity per Purchase <span class="req" id="qpp-req" style="display: none;">*</span></label>
                            <input type="number" id="units_per_purchase" name="units_per_purchase" class="form-control"
                                value="{{ old('units_per_purchase') }}" min="1" step="1" placeholder="e.g. 100">
                            <small class="field-help">How many inventory units are in one purchase. Example: 1 bundle contains 100 pieces.</small>
                            @error('units_per_purchase') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
                @endif

            </div>

            @if($itemType === 'made_product')
            <div class="bom-section">
                <div class="section-heading-row">
                    <div>
                        <strong>Bill of Materials</strong>
                        <span class="section-sub">Materials required to make one unit of this product.</span>
                    </div>
                    <button type="button" class="btn-add-bom" onclick="addBomRow()">+ Add Material</button>
                </div>

                @if($materials->isEmpty())
                <p style="color: #94A3B8; font-size: 13px; padding: 20px 0;">
                    No materials available. Add materials with "Production" or "Both" stock purpose first.
                </p>
                @else
                <div id="bom-rows"></div>
                <p id="bom-empty" style="color: #94A3B8; font-size: 13px; padding: 10px 0;">
                    No materials added yet.
                </p>
                @endif
            </div>
            @endif

            <div class="form-actions">
                <a href="{{ route('products.index', ['item_type' => $tabParam]) }}" class="action-btn-secondary">Cancel</a>
                <button type="submit" class="action-btn-primary">Save {{ $typeLabel }}</button>
            </div>
        </form>
    </div>

</div>

{{-- ============================================
     CONFIRMATION MODAL
     ============================================ --}}
<div class="modal-overlay" id="confirm-modal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 class="modal-title" id="modal-title">Confirm New {{ $typeLabel }}</h3>
            <p class="modal-subtitle">Review the details below before saving.</p>
        </div>

        <div class="modal-body" id="modal-body"></div>

        <div class="modal-footer">
            <button type="button" class="modal-btn modal-btn-secondary" onclick="closeConfirmModal()">
                Cancel
            </button>
            <button type="button" class="modal-btn modal-btn-primary" id="modal-confirm-btn">
                Save {{ $typeLabel }}
            </button>
        </div>
    </div>
</div>

{{-- ============================================
     ERROR MODAL
     ============================================ --}}
<div class="modal-overlay" id="error-modal">
    <div class="modal-box modal-box-error">
        <div class="modal-error-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h3 class="modal-error-title">Something's not right</h3>
        <p class="modal-error-message" id="error-modal-message"></p>
        <div class="modal-error-footer">
            <button type="button" class="modal-btn modal-btn-primary" onclick="closeErrorModal()">
                Got it
            </button>
        </div>
    </div>
</div>

<style>
    .form-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 18px 24px;
        margin-bottom: 24px;
    }

    .form-grid>.form-group {
        flex: 1 1 260px;
        min-width: 0;
        margin-bottom: 0;
    }

    .form-grid>.form-group.full-width {
        flex-basis: 100%;
    }

    .req {
        color: #6B5B95;
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

    /* IMAGE UPLOAD */
    .image-upload-wrap {
        display: flex;
        gap: 20px;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .image-preview {
        width: 140px;
        height: 140px;
        border: 2px dashed #D5C9E8;
        border-radius: 12px;
        background: #FDFBFF;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    .image-preview-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        color: #94A3B8;
        font-size: 11px;
        text-align: center;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-upload-controls {
        flex: 1 1 260px;
        min-width: 0;
    }

    .file-input {
        padding: 8px 12px;
    }

    .purchase-section,
    .bom-section {
        flex-basis: 100%;
        padding: 18px;
        background: #FAF7F3;
        border: 1px solid #F0E6DD;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .section-heading,
    .section-heading-row>div {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-bottom: 16px;
    }

    .section-heading-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 16px;
    }

    .section-heading strong,
    .section-heading-row strong {
        color: #212121;
        font-size: 14px;
    }

    .section-heading span,
    .section-sub {
        color: #94A3B8;
        font-size: 12px;
    }

    .purchase-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px 24px;
    }

    .btn-add-bom {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 8px;
        background: #6B5B95;
        color: #FFFFFF;
        border: none;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
    }

    .btn-add-bom:hover {
        background: #594B7D;
    }

    .bom-row {
        display: grid;
        grid-template-columns: 1fr 140px 40px;
        gap: 10px;
        align-items: center;
        padding: 10px;
        background: #FFFFFF;
        border: 1px solid #F0E6DD;
        border-radius: 8px;
        margin-bottom: 8px;
    }

    .bom-row select,
    .bom-row input {
        padding: 8px 10px;
        border: 1.5px solid #F0E6DD;
        border-radius: 6px;
        font-size: 13px;
        font-family: inherit;
        background: #FFFFFF;
    }

    .bom-row select:focus,
    .bom-row input:focus {
        outline: none;
        border-color: #6B5B95;
    }

    .bom-remove {
        width: 32px;
        height: 32px;
        background: #FDECEA;
        color: #DC3545;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 700;
        font-family: inherit;
    }

    .bom-remove:hover {
        background: #F8D7DA;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        padding-top: 20px;
        border-top: 1px solid #F0E6DD;
        align-items: center;
    }

    .form-actions .action-btn-primary,
    .form-actions .action-btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        height: 44px;
        min-height: 44px;
        min-width: 140px;
        padding: 0 22px;
        font-size: 14px;
        font-weight: 600;
        line-height: 1;
        border-radius: 8px;
        text-decoration: none;
        border: none;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s ease;
        white-space: nowrap;
    }

    .action-btn-primary {
        background: #6B5B95;
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(107, 91, 149, 0.3);
    }

    .action-btn-primary:hover {
        background: #594B7D;
        box-shadow: 0 4px 10px rgba(107, 91, 149, 0.4);
    }

    .action-btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .action-btn-secondary {
        background: #F0E6DD;
        color: #212121;
    }

    .action-btn-secondary:hover {
        background: #E5D5C5;
    }

    /* MODALS (unchanged) */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(33, 33, 33, 0.55);
        backdrop-filter: blur(3px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 20px;
        animation: fade-in 0.15s ease;
    }

    .modal-overlay.open {
        display: flex;
    }

    @keyframes fade-in {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .modal-box {
        background: #FFFFFF;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
        width: 100%;
        max-width: 520px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        animation: slide-up 0.2s ease;
    }

    @keyframes slide-up {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-header {
        padding: 22px 26px 16px;
        border-bottom: 1px solid #F0E6DD;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: #212121;
        margin: 0;
    }

    .modal-subtitle {
        font-size: 13px;
        color: #64748B;
        margin-top: 4px;
    }

    .modal-body {
        padding: 20px 26px;
        overflow-y: auto;
        flex: 1;
        min-height: 0;
    }

    .modal-section {
        margin-bottom: 18px;
    }

    .modal-section:last-child {
        margin-bottom: 0;
    }

    .modal-section-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94A3B8;
        margin-bottom: 10px;
        padding-bottom: 6px;
        border-bottom: 1px dashed #F0E6DD;
    }

    .modal-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 14px;
        border-bottom: 1px dashed #F0E6DD;
        gap: 12px;
    }

    .modal-row:last-child {
        border-bottom: none;
    }

    .modal-row-label {
        color: #64748B;
    }

    .modal-row-value {
        color: #212121;
        font-weight: 600;
        text-align: right;
    }

    .modal-bom-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .modal-bom-item {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 12px;
        padding: 8px 12px;
        background: #FDFBFF;
        border-radius: 6px;
        font-size: 13px;
        align-items: center;
    }

    .modal-bom-name {
        color: #212121;
        font-weight: 600;
    }

    .modal-bom-qty {
        color: #2E5A3B;
        font-weight: 700;
        white-space: nowrap;
    }

    .modal-footer {
        padding: 16px 26px 22px;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        border-top: 1px solid #F0E6DD;
        background: #FEFCF9;
    }

    .modal-btn {
        padding: 11px 22px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        border: none;
        transition: all 0.15s ease;
        min-width: 120px;
    }

    .modal-btn-primary {
        background: #6B5B95;
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(107, 91, 149, 0.3);
    }

    .modal-btn-primary:hover {
        background: #594B7D;
    }

    .modal-btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .modal-btn-secondary {
        background: #F0E6DD;
        color: #212121;
    }

    .modal-btn-secondary:hover {
        background: #E5D5C5;
    }

    .modal-box-error {
        max-width: 420px;
        text-align: center;
        padding: 30px 30px 24px;
    }

    .modal-error-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #FDECEA;
        color: #DC3545;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }

    .modal-error-title {
        font-size: 18px;
        font-weight: 700;
        color: #212121;
        margin-bottom: 8px;
    }

    .modal-error-message {
        font-size: 14px;
        color: #64748B;
        line-height: 1.5;
        margin-bottom: 24px;
        word-break: break-word;
        white-space: pre-line;
    }

    .modal-error-footer {
        display: flex;
        justify-content: center;
    }

    .modal-error-footer .modal-btn {
        min-width: 120px;
    }

    @media (max-width: 650px) {
        .purchase-grid {
            grid-template-columns: 1fr;
        }

        .bom-row {
            grid-template-columns: 1fr 80px 40px;
        }

        .modal-box {
            max-width: 100%;
        }

        .form-actions .action-btn-primary,
        .form-actions .action-btn-secondary {
            flex: 1;
            min-width: 0;
        }

        .image-upload-wrap {
            flex-direction: column;
        }
    }
</style>

<script>
    const MATERIALS = @json($materials -> map(fn($m) => ['id' => $m -> product_id, 'name' => $m -> display_name]));
    const ITEM_TYPE = @json($itemType);
    const TYPE_LABEL = @json($typeLabel);

    /* ============================================
       IMAGE PREVIEW
       ============================================ */
    function previewImage(input) {
        const preview = document.getElementById('image-preview-img');
        const empty = document.getElementById('image-preview-empty');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                empty.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            preview.style.display = 'none';
            empty.style.display = 'flex';
        }
    }

    /* ============================================
       MODAL HELPERS
       ============================================ */
    const confirmModal = document.getElementById('confirm-modal');
    const confirmBody = document.getElementById('modal-body');
    const confirmBtn = document.getElementById('modal-confirm-btn');
    const errorModal = document.getElementById('error-modal');
    const errorMessage = document.getElementById('error-modal-message');

    window.closeConfirmModal = function() {
        confirmModal.classList.remove('open');
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Save ' + TYPE_LABEL;
    };
    window.closeErrorModal = function() {
        errorModal.classList.remove('open');
    };

    function showError(message) {
        errorMessage.textContent = message;
        errorModal.classList.add('open');
    }

    function row(label, value) {
        return `<div class="modal-row">
            <span class="modal-row-label">${label}</span>
            <span class="modal-row-value">${value}</span>
        </div>`;
    }

    confirmModal.addEventListener('click', function(e) {
        if (e.target === confirmModal) closeConfirmModal();
    });
    errorModal.addEventListener('click', function(e) {
        if (e.target === errorModal) closeErrorModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeConfirmModal();
            closeErrorModal();
        }
    });

    /* ============================================
       PAGE INIT
       ============================================ */
    document.addEventListener('DOMContentLoaded', function() {
        const stockPurpose = document.getElementById('stock_purpose');
        const sellingPriceField = document.getElementById('selling-price-field');

        function updateFieldVisibility() {
            const purpose = stockPurpose.value;
            const hasRetail = purpose === 'retail' || purpose === 'both';
            sellingPriceField.style.display = hasRetail ? 'block' : 'none';

            // Show reference image only for sellable bouquets
            const imageField = document.getElementById('image-field');
            if (imageField && ITEM_TYPE === 'made_product') {
                imageField.style.display = hasRetail ? 'block' : 'none';
            }
        }
        stockPurpose.addEventListener('change', updateFieldVisibility);
        updateFieldVisibility();

        // Quantity per purchase — required when purchase unit set
        const purchaseUnit = document.getElementById('purchase_unit');
        const unitsPerPurchase = document.getElementById('units_per_purchase');
        const qppReq = document.getElementById('qpp-req');

        function updateQppRequired() {
            if (!purchaseUnit || !unitsPerPurchase || !qppReq) return;
            const hasUnit = purchaseUnit.value !== '';
            qppReq.style.display = hasUnit ? 'inline' : 'none';
            unitsPerPurchase.required = hasUnit;
        }
        if (purchaseUnit) {
            purchaseUnit.addEventListener('change', updateQppRequired);
            updateQppRequired();
        }

        @if($itemType === 'made_product' && !$materials -> isEmpty())
        addBomRow();
        @endif
    });

    /* ============================================
       BOM ROWS
       ============================================ */
    function addBomRow() {
        const container = document.getElementById('bom-rows');
        const emptyMsg = document.getElementById('bom-empty');
        if (!container) return;

        const index = container.children.length;

        const row = document.createElement('div');
        row.className = 'bom-row';
        row.innerHTML = `
            <select name="bom[${index}][material_product_id]" required onchange="refreshBomOptions()">
                <option value="">— Select material —</option>
                ${MATERIALS.map(m => `<option value="${m.id}">${m.name}</option>`).join('')}
            </select>
            <input type="number" name="bom[${index}][quantity_required]" min="0.01" step="0.01" placeholder="Qty required" required>
            <button type="button" class="bom-remove" onclick="removeBomRow(this)">×</button>
        `;
        container.appendChild(row);
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
                if (opt.value === currentValue) {
                    opt.disabled = false;
                } else if (selectedIds.has(opt.value)) {
                    opt.disabled = true;
                } else {
                    opt.disabled = false;
                }
            });
        });
    }

    function reindexBom() {
        const container = document.getElementById('bom-rows');
        if (!container) return;
        const rows = container.querySelectorAll('.bom-row');
        const emptyMsg = document.getElementById('bom-empty');
        rows.forEach((row, i) => {
            row.querySelector('select').name = `bom[${i}][material_product_id]`;
            row.querySelector('input').name = `bom[${i}][quantity_required]`;
        });
        if (emptyMsg) emptyMsg.style.display = rows.length === 0 ? 'block' : 'none';
    }

    /* ============================================
       SUBMIT → VALIDATE → CONFIRM
       ============================================ */
    document.getElementById('product-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const name = document.getElementById('name').value.trim();
        const variation = document.getElementById('variation').value.trim();
        const stockPurpose = document.getElementById('stock_purpose').value;
        const sellingPrice = document.getElementById('selling_price').value;
        const stockUnit = document.getElementById('stock_unit').value;
        const lowStock = document.getElementById('low_stock_threshold').value;

        const purchaseUnit = document.getElementById('purchase_unit');
        const unitsPerPurchase = document.getElementById('units_per_purchase');

        if (!name) {
            showError('Please enter the item name.');
            document.getElementById('name').focus();
            return;
        }
        if (!stockPurpose) {
            showError('Please select a stock purpose.');
            document.getElementById('stock_purpose').focus();
            return;
        }
        if (!stockUnit) {
            showError('Please select an inventory unit.');
            document.getElementById('stock_unit').focus();
            return;
        }

        const hasRetail = stockPurpose === 'retail' || stockPurpose === 'both';
        if (hasRetail && !sellingPrice) {
            showError('Please enter a selling price for this sellable item.');
            document.getElementById('selling_price').focus();
            return;
        }

        if (purchaseUnit && purchaseUnit.value && !unitsPerPurchase.value) {
            showError('Please enter the quantity per purchase for the selected purchase unit.');
            unitsPerPurchase.focus();
            return;
        }
        if (unitsPerPurchase && unitsPerPurchase.value && parseFloat(unitsPerPurchase.value) < 1) {
            showError('Quantity per purchase must be at least 1.');
            unitsPerPurchase.focus();
            return;
        }

        const bomRows = [];
        if (ITEM_TYPE === 'made_product') {
            const rows = document.querySelectorAll('.bom-row');
            let hasBom = false;
            for (const row of rows) {
                const sel = row.querySelector('select');
                const qty = row.querySelector('input');
                if (sel.value && qty.value) {
                    hasBom = true;
                    bomRows.push({
                        name: sel.options[sel.selectedIndex].text.trim(),
                        qty: parseFloat(qty.value)
                    });
                }
            }
            if (!hasBom) {
                showError('Please add at least one material to the Bill of Materials.');
                return;
            }
        }

        let bodyHtml = `<div class="modal-section">`;
        bodyHtml += row('Type', TYPE_LABEL);
        bodyHtml += row('Name', name);
        if (variation) bodyHtml += row('Variation', variation);
        bodyHtml += row('Stock Purpose',
            stockPurpose === 'retail' ? 'Retail only' :
            stockPurpose === 'production' ? 'Production only' :
            'Both');
        bodyHtml += row('Inventory Unit', stockUnit.charAt(0).toUpperCase() + stockUnit.slice(1));
        if (lowStock) bodyHtml += row('Low Stock Threshold', lowStock);
        if (hasRetail && sellingPrice) {
            bodyHtml += row('Selling Price', '₱' + parseFloat(sellingPrice).toFixed(2));
        }
        bodyHtml += `</div>`;

        if (ITEM_TYPE === 'material' && purchaseUnit && purchaseUnit.value) {
            bodyHtml += `<div class="modal-section">
                <div class="modal-section-title">Purchase Information</div>
                ${row('Purchase Unit', purchaseUnit.options[purchaseUnit.selectedIndex].text.trim())}
                ${unitsPerPurchase.value ? row('Quantity per Purchase', unitsPerPurchase.value + ' units') : ''}
            </div>`;
        }

        if (bomRows.length > 0) {
            bodyHtml += `<div class="modal-section">
                <div class="modal-section-title">Bill of Materials (per unit)</div>
                <div class="modal-bom-list">`;
            bomRows.forEach(b => {
                bodyHtml += `<div class="modal-bom-item">
                    <span class="modal-bom-name">${b.name}</span>
                    <span class="modal-bom-qty">× ${b.qty}</span>
                </div>`;
            });
            bodyHtml += `</div></div>`;
        }

        confirmBody.innerHTML = bodyHtml;
        confirmModal.classList.add('open');
    });

    confirmBtn.addEventListener('click', function() {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Saving...';
        document.getElementById('product-form').submit();
    });
</script>

@endsection