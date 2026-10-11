<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\ProductComponent;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    private int $matCounter = 0;
    private int $prdCounter = 0;
    private int $adminUserId = 1;

    public function run(): void
    {
        if (Product::count() > 0) {
            $this->command->warn('Products already exist. Skipping DemoDataSeeder.');
            return;
        }

        $admin = User::where('role', 'owner')->first();
        if (!$admin) {
            $this->command->error('No owner user found. Run UserSeeder first.');
            return;
        }
        $this->adminUserId = $admin->user_id;

        DB::transaction(function () {
            $this->seedFuzzyWires();
            $this->seedWrappings();
            $bouquets = $this->seedBouquets();
            $this->seedBOMs($bouquets);
        });

        $this->command->info('Demo data seeded successfully.');
    }

    /* ============================================
       FUZZY WIRES  (material, both, ₱1/piece)
       ============================================ */
    private function seedFuzzyWires(): void
    {
        // Color => [production stock, retail stock]
        $colors = [
            'White'         => [1400, 200],
            'Cream'         => [1100, 150],
            'Beige'         => [750,  100],
            'Tan'           => [650,  100],
            'Caramel'       => [550,  80],
            'Dark Brown'    => [450,  80],
            'Peach'         => [650,  100],
            'Blush Pink'    => [850,  120],
            'Soft Pink'     => [950,  150],
            'Hot Pink'      => [750,  120],
            'Rose'          => [550,  100],
            'Sky Blue'      => [650,  100],
            'Baby Blue'     => [550,  80],
            'Lavender'      => [750,  120],
            'Lilac'         => [550,  80],
            'Bright Yellow' => [850,  150],
            'Light Green'   => [450,  80],
            'Dark Green'    => [650,  100],
            'Black'         => [400,  80],
            'Red'           => [650,  100],
            'Orange'        => [450,  80],
        ];

        foreach ($colors as $color => [$prodStock, $retailStock]) {
            $product = Product::create([
                'reference_code' => $this->nextRefCode('material'),
                'name' => 'Fuzzy Wire',
                'variation' => $color,
                'image_path' => null,
                'item_type' => 'material',
                'is_active' => true,
                'is_sellable' => true,
                'stock_purpose' => 'both',
                'low_stock_threshold' => 200,
                'selling_price' => 1.00,
                'stock_unit' => 'piece',
                'purchase_unit' => 'bundle',
                'units_per_purchase' => 100,
            ]);

            $this->seedInventory($product, $retailStock, $prodStock);
        }
    }

    /* ============================================
       WRAPPINGS & AUX  (materials)
       ============================================ */
    private function seedWrappings(): void
    {
        $materials = [
            [
                'name' => 'Kraft Paper',
                'stock_unit' => 'meter',
                'purchase_unit' => 'roll',
                'units_per_purchase' => 10,
                'selling_price' => 15.00,
                'stock_purpose' => 'both',
                'low_stock_threshold' => 5,
                'retail' => 15, 'production' => 30,
            ],
            [
                'name' => 'Printed Wrap',
                'stock_unit' => 'meter',
                'purchase_unit' => 'roll',
                'units_per_purchase' => 10,
                'selling_price' => 15.00,
                'stock_purpose' => 'both',
                'low_stock_threshold' => 5,
                'retail' => 10, 'production' => 25,
            ],
            [
                'name' => 'Tissue Paper',
                'stock_unit' => 'sheet',
                'purchase_unit' => 'pack',
                'units_per_purchase' => 50,
                'selling_price' => 5.00,
                'stock_purpose' => 'production',
                'low_stock_threshold' => 30,
                'retail' => 0, 'production' => 250,
            ],
            [
                'name' => 'Lace Netting',
                'stock_unit' => 'meter',
                'purchase_unit' => 'roll',
                'units_per_purchase' => 10,
                'selling_price' => 25.00,
                'stock_purpose' => 'both',
                'low_stock_threshold' => 3,
                'retail' => 5, 'production' => 20,
            ],
            [
                'name' => 'Satin Ribbon',
                'stock_unit' => 'meter',
                'purchase_unit' => 'roll',
                'units_per_purchase' => 25,
                'selling_price' => 10.00,
                'stock_purpose' => 'both',
                'low_stock_threshold' => 10,
                'retail' => 20, 'production' => 50,
            ],
            [
                'name' => 'Organza Ribbon',
                'stock_unit' => 'meter',
                'purchase_unit' => 'roll',
                'units_per_purchase' => 25,
                'selling_price' => 12.00,
                'stock_purpose' => 'both',
                'low_stock_threshold' => 10,
                'retail' => 15, 'production' => 40,
            ],
            [
                'name' => 'Pearl Beads',
                'stock_unit' => 'piece',
                'purchase_unit' => 'pack',
                'units_per_purchase' => 100,
                'selling_price' => 1.00,
                'stock_purpose' => 'production',
                'low_stock_threshold' => 50,
                'retail' => 0, 'production' => 500,
            ],
            [
                'name' => 'Floral Wire',
                'stock_unit' => 'piece',
                'purchase_unit' => 'bundle',
                'units_per_purchase' => 100,
                'selling_price' => 2.00,
                'stock_purpose' => 'production',
                'low_stock_threshold' => 100,
                'retail' => 0, 'production' => 800,
            ],
        ];

        foreach ($materials as $m) {
            $product = Product::create([
                'reference_code' => $this->nextRefCode('material'),
                'name' => $m['name'],
                'variation' => null,
                'image_path' => null,
                'item_type' => 'material',
                'is_active' => true,
                'is_sellable' => $m['stock_purpose'] === 'both',
                'stock_purpose' => $m['stock_purpose'],
                'low_stock_threshold' => $m['low_stock_threshold'],
                'selling_price' => $m['selling_price'],
                'stock_unit' => $m['stock_unit'],
                'purchase_unit' => $m['purchase_unit'],
                'units_per_purchase' => $m['units_per_purchase'],
            ]);

            $this->seedInventory($product, $m['retail'], $m['production']);
        }
    }

    /* ============================================
       BOUQUETS (made_product, retail only)
       ============================================ */
    private function seedBouquets(): array
    {
        $bouquets = [
            ['name' => 'Autumn Kraft Bouquet',        'price' => 650.00],
            ['name' => 'Sky Blue Tulip Lily Bouquet', 'price' => 850.00],
            ['name' => 'Lavender Dream Bouquet',      'price' => 750.00],
            ['name' => 'Peach Sunrise Bouquet',       'price' => 700.00],
            ['name' => 'Blush Romance Bouquet',       'price' => 950.00],
            ['name' => 'Midnight Noir Bouquet',       'price' => 1100.00],
            ['name' => 'Hot Pink Delight',            'price' => 800.00],
            ['name' => 'Pink Petals Bouquet',         'price' => 700.00],
            ['name' => 'Rainbow Mini Flowers',        'price' => 600.00],
            ['name' => 'Sunshine Yellow Bouquet',     'price' => 650.00],
        ];

        $created = [];

        foreach ($bouquets as $b) {
            $product = Product::create([
                'reference_code' => $this->nextRefCode('made_product'),
                'name' => $b['name'],
                'variation' => null,
                'image_path' => null,
                'item_type' => 'made_product',
                'is_active' => true,
                'is_sellable' => true,
                'stock_purpose' => 'retail',  // ← fixed: retail only, not both
                'low_stock_threshold' => 3,   // bouquets sell by count
                'selling_price' => $b['price'],
                'stock_unit' => 'piece',
                'purchase_unit' => null,
                'units_per_purchase' => null,
            ]);

            // Retail only — no production pool for bouquets
            $this->seedInventory($product, 3, 0);

            $created[$b['name']] = $product;
        }

        return $created;
    }

    /* ============================================
       BOMs (flat — bouquets use wires directly)
       ============================================ */
    private function seedBOMs(array $bouquets): void
    {
        // Build lookup by display_name
        $lookup = [];
        foreach (Product::where('item_type', 'material')->get() as $m) {
            $lookup[$m->display_name] = $m;
        }

        // Bouquet name => [material display_name => quantity]
        $boms = [
            'Autumn Kraft Bouquet' => [
                'Fuzzy Wire - Cream'      => 10,
                'Fuzzy Wire - Tan'        => 8,
                'Fuzzy Wire - Caramel'    => 4,
                'Fuzzy Wire - Dark Brown' => 5,
                'Kraft Paper'             => 3,
                'Satin Ribbon'            => 1.5,
                'Floral Wire'             => 15,
            ],
            'Sky Blue Tulip Lily Bouquet' => [
                'Fuzzy Wire - Sky Blue'   => 18,
                'Fuzzy Wire - White'      => 4,
                'Fuzzy Wire - Dark Green' => 2,
                'Tissue Paper'            => 6,
                'Lace Netting'            => 0.5,
                'Organza Ribbon'          => 1.5,
                'Floral Wire'             => 15,
            ],
            'Lavender Dream Bouquet' => [
                'Fuzzy Wire - Lavender'      => 14,
                'Fuzzy Wire - Lilac'         => 8,
                'Fuzzy Wire - White'         => 4,
                'Fuzzy Wire - Bright Yellow' => 2,
                'Fuzzy Wire - Dark Green'    => 2,
                'Tissue Paper'               => 6,
                'Lace Netting'               => 0.5,
                'Organza Ribbon'             => 1.5,
                'Floral Wire'                => 15,
            ],
            'Peach Sunrise Bouquet' => [
                'Fuzzy Wire - Peach'      => 12,
                'Fuzzy Wire - Cream'      => 8,
                'Fuzzy Wire - Tan'        => 4,
                'Fuzzy Wire - Dark Brown' => 3,
                'Printed Wrap'            => 3,
                'Lace Netting'            => 0.5,
                'Satin Ribbon'            => 1.5,
                'Floral Wire'             => 15,
            ],
            'Blush Romance Bouquet' => [
                'Fuzzy Wire - Blush Pink'    => 16,
                'Fuzzy Wire - Soft Pink'     => 10,
                'Fuzzy Wire - White'         => 5,
                'Fuzzy Wire - Bright Yellow' => 3,
                'Fuzzy Wire - Dark Green'    => 2,
                'Printed Wrap'               => 3,
                'Lace Netting'               => 0.5,
                'Satin Ribbon'               => 2,
                'Floral Wire'                => 18,
            ],
            'Midnight Noir Bouquet' => [
                'Fuzzy Wire - Black'       => 18,
                'Fuzzy Wire - White'       => 6,
                'Fuzzy Wire - Light Green' => 3,
                'Printed Wrap'             => 3,
                'Lace Netting'             => 0.5,
                'Satin Ribbon'             => 2,
                'Floral Wire'              => 18,
            ],
            'Hot Pink Delight' => [
                'Fuzzy Wire - Hot Pink'      => 15,
                'Fuzzy Wire - Soft Pink'     => 8,
                'Fuzzy Wire - White'         => 4,
                'Fuzzy Wire - Bright Yellow' => 3,
                'Fuzzy Wire - Light Green'   => 2,
                'Printed Wrap'               => 3,
                'Lace Netting'               => 0.5,
                'Satin Ribbon'               => 1.5,
                'Floral Wire'                => 15,
            ],
            'Pink Petals Bouquet' => [
                'Fuzzy Wire - Soft Pink'     => 12,
                'Fuzzy Wire - Blush Pink'    => 8,
                'Fuzzy Wire - Hot Pink'      => 4,
                'Fuzzy Wire - White'         => 3,
                'Fuzzy Wire - Bright Yellow' => 2,
                'Fuzzy Wire - Light Green'   => 2,
                'Printed Wrap'               => 3,
                'Lace Netting'               => 0.5,
                'Organza Ribbon'             => 1.5,
                'Floral Wire'                => 15,
            ],
            'Rainbow Mini Flowers' => [
                'Fuzzy Wire - Red'           => 3,
                'Fuzzy Wire - Orange'        => 3,
                'Fuzzy Wire - Bright Yellow' => 3,
                'Fuzzy Wire - Light Green'   => 3,
                'Fuzzy Wire - Sky Blue'      => 3,
                'Fuzzy Wire - Lavender'      => 3,
                'Fuzzy Wire - Hot Pink'      => 3,
                'Fuzzy Wire - White'         => 4,
                'Pearl Beads'                => 20,
                'Tissue Paper'               => 4,
                'Lace Netting'               => 0.5,
                'Satin Ribbon'               => 1.5,
                'Floral Wire'                => 12,
            ],
            'Sunshine Yellow Bouquet' => [
                'Fuzzy Wire - Bright Yellow' => 16,
                'Fuzzy Wire - White'         => 4,
                'Fuzzy Wire - Light Green'   => 2,
                'Printed Wrap'               => 3,
                'Lace Netting'               => 0.5,
                'Satin Ribbon'               => 1.5,
                'Floral Wire'                => 15,
            ],
        ];

        foreach ($boms as $bouquetName => $components) {
            $bouquet = $bouquets[$bouquetName] ?? null;
            if (!$bouquet) {
                $this->command->warn("Bouquet not found: {$bouquetName}");
                continue;
            }

            foreach ($components as $materialDisplayName => $qty) {
                $material = $lookup[$materialDisplayName] ?? null;
                if (!$material) {
                    $this->command->warn("Material not found: {$materialDisplayName}");
                    continue;
                }

                ProductComponent::create([
                    'parent_product_id' => $bouquet->product_id,
                    'material_product_id' => $material->product_id,
                    'quantity_required' => $qty,
                ]);
            }
        }
    }

    /* ============================================
       HELPERS
       ============================================ */
    private function nextRefCode(string $type): string
    {
        if ($type === 'material') {
            $this->matCounter++;
            return 'MAT-' . str_pad((string) $this->matCounter, 5, '0', STR_PAD_LEFT);
        }

        $this->prdCounter++;
        return 'PRD-' . str_pad((string) $this->prdCounter, 5, '0', STR_PAD_LEFT);
    }

    private function seedInventory(Product $product, float $retailQty, float $prodQty): void
    {
        if ($retailQty > 0) {
            $inv = Inventory::create([
                'product_id' => $product->product_id,
                'reserve_type' => 'retail',
                'current_quantity' => $retailQty,
                'last_updated' => now(),
            ]);

            InventoryTransaction::create([
                'inventory_id' => $inv->inventory_id,
                'transaction_type' => 'manual_addition',
                'quantity_change' => $retailQty,
                'reference_id' => null,
                'reference_type' => 'initial_stock',
                'notes' => 'Seeded initial retail stock.',
                'transaction_date' => now(),
                'recorded_by' => $this->adminUserId,
            ]);
        }

        if ($prodQty > 0) {
            $inv = Inventory::create([
                'product_id' => $product->product_id,
                'reserve_type' => 'production',
                'current_quantity' => $prodQty,
                'last_updated' => now(),
            ]);

            InventoryTransaction::create([
                'inventory_id' => $inv->inventory_id,
                'transaction_type' => 'manual_addition',
                'quantity_change' => $prodQty,
                'reference_id' => null,
                'reference_type' => 'initial_stock',
                'notes' => 'Seeded initial production stock.',
                'transaction_date' => now(),
                'recorded_by' => $this->adminUserId,
            ]);
        }
    }
}