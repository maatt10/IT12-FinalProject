

<?php $__env->startSection('title', 'Record Production'); ?>

<?php $__env->startSection('content'); ?>
<div class="form-page-wide">

    <?php
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
    ?>

    <div class="page-header">
        <div>
            <h1>Record Production</h1>
            <p>Select a product and enter the quantity to produce. Required materials are calculated from the product's BOM.</p>
        </div>

        <a href="<?php echo e(route('products.index', ['item_type' => 'product'])); ?>" class="btn btn-secondary">
            ← Back to Inventory
        </a>
    </div>

    <form action="<?php echo e(route('production.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        
        <div class="card" style="margin-bottom: 20px;">
            <h2 class="card-heading">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#E85D75" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                </svg>
                Production Details
            </h2>

            <div class="form-grid">

                <div class="form-group">
                    <label for="product_id">Product to Produce <span class="req">*</span></label>
                    <select
                        id="product_id"
                        name="product_id"
                        class="form-control"
                        required>
                        <option value="">— Select a product —</option>
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option
                            value="<?php echo e($product->product_id); ?>"
                            <?php if(old('product_id')==$product->product_id): echo 'selected'; endif; ?>>
                            <?php echo e($product->display_name); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['product_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="quantity_produced">Quantity to Produce <span class="req">*</span></label>
                    <input
                        type="number"
                        id="quantity_produced"
                        name="quantity_produced"
                        class="form-control"
                        value="<?php echo e(old('quantity_produced')); ?>"
                        min="0.01"
                        step="0.01"
                        placeholder="Enter quantity"
                        required>
                    <small style="color: #94A3B8; font-size: 12px;">
                        Quantity is entered using the product's stock unit.
                    </small>
                    <?php $__errorArgs = ['quantity_produced'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="error"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

            </div>
        </div>

        
        <div class="card" id="bom-card" style="display: none; border-top: 4px solid #D4AF37; margin-bottom: 20px;">
            <h2 class="card-heading">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#D4AF37" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Required Materials
            </h2>

            <p style="font-size: 13px; color: #64748B; margin-top: -10px; margin-bottom: 18px;">
                The following quantities will be deducted from production inventory.
            </p>

            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Material</th>
                            <th style="text-align: right;">Per Product</th>
                            <th style="text-align: right;">Total Required</th>
                            <th>Stock Unit</th>
                        </tr>
                    </thead>
                    <tbody id="bom-body"></tbody>
                </table>
            </div>
        </div>

        
        <div class="form-actions">
            <a href="<?php echo e(route('products.index', ['item_type' => 'product'])); ?>" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                Complete Production
            </button>
        </div>

    </form>

    <style>
        .card-heading {
            font-size: 16px;
            font-weight: 700;
            color: #212121;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 18px 24px;
        }

        .req {
            color: #E85D75;
            margin-left: 2px;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 20px;
            border-top: 1px solid #F0E6DD;
        }

        /* Centered wide form pages (Production, Purchases) */
        .form-page-wide {
            max-width: 900px;
            margin: 0 auto;
        }
    </style>

    <script>
        const products = <?php echo json_encode($productionProducts, 15, 512) ?>;

        const productSelect = document.getElementById('product_id');
        const quantityInput = document.getElementById('quantity_produced');
        const bomCard = document.getElementById('bom-card');
        const bomBody = document.getElementById('bom-body');

        // Non-money numbers: strip trailing .00
        function formatQuantity(value) {
            return parseFloat(Number(value).toFixed(2)).toString();
        }

        function updateBomPreview() {
            const selectedId = Number(productSelect.value);
            const quantity = Number(quantityInput.value);

            bomBody.innerHTML = '';

            if (!selectedId || !Number.isFinite(quantity) || quantity <= 0) {
                bomCard.style.display = 'none';
                return;
            }

            const product = products.find(function(item) {
                return Number(item.id) === selectedId;
            });

            if (!product || !product.components || product.components.length === 0) {
                bomCard.style.display = 'none';
                return;
            }

            product.components.forEach(function(component) {
                const totalRequired = component.quantity_required * quantity;

                const row = document.createElement('tr');

                const materialCell = document.createElement('td');
                materialCell.style.fontWeight = '600';
                materialCell.style.color = '#212121';
                materialCell.textContent = component.name;

                const perProductCell = document.createElement('td');
                perProductCell.style.textAlign = 'right';
                perProductCell.style.color = '#64748B';
                perProductCell.textContent = formatQuantity(component.quantity_required);

                const totalCell = document.createElement('td');
                totalCell.style.textAlign = 'right';
                totalCell.style.fontWeight = '600';
                totalCell.style.color = '#2E5A3B';
                totalCell.textContent = formatQuantity(totalRequired);

                const unitCell = document.createElement('td');
                unitCell.style.color = '#64748B';
                unitCell.textContent = component.stock_unit;

                row.appendChild(materialCell);
                row.appendChild(perProductCell);
                row.appendChild(totalCell);
                row.appendChild(unitCell);

                bomBody.appendChild(row);
            });

            bomCard.style.display = 'block';
        }

        productSelect.addEventListener('change', updateBomPreview);
        quantityInput.addEventListener('input', updateBomPreview);

        updateBomPreview();
    </script>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\VICTUS\lf_system\resources\views/production/create.blade.php ENDPATH**/ ?>