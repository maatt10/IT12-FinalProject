<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\InventoryTransaction;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function create()
    {
        $products = Product::where('is_sellable', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->orderBy('variation')
            ->with([
                'inventory' => function ($query) {
                    $query->where('reserve_type', 'retail');
                },
            ])
            ->get()
            ->map(function ($product) {
                $retailInventory = $product->inventory->first();

                return [
                    'id' => $product->product_id,
                    'name' => $product->display_name,
                    'price' => (float) $product->selling_price,
                    'unit' => $product->stock_unit,
                    'stock' => $retailInventory
                        ? (float) $retailInventory->current_quantity
                        : 0,
                ];
            })
            ->values();

        $customers = Customer::orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->customer_id,
                    'full_name' => $customer->full_name,
                    'label' => $customer->last_name . ', ' . $customer->first_name,
                    'discount_type' => $customer->discount_type ?? 'none',
                    'discount_id_number' => $customer->discount_id_number,
                ];
            })
            ->values();

        return view('sales.create', compact('products', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,customer_id'],
            'payment_method' => ['required', 'in:cash,gcash'],
            'gcash_reference' => [
                'nullable',
                'required_if:payment_method,gcash',
                'digits:13',
            ],
            'discount_type' => ['required', 'in:none,pwd,senior'],
            'discount_name' => ['nullable', 'string', 'max:120'],
            'discount_id_number' => [
                'nullable',
                'required_unless:discount_type,none',
                'string',
                'max:30',
            ],
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,product_id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
        ]);

        $saleId = null;

        try {
            DB::transaction(function () use ($validated, &$saleId) {

                $subtotal = 0;

                foreach ($validated['items'] as $item) {

                    $product = Product::findOrFail($item['product_id']);

                    if (!$product->is_sellable) {
                        throw new \Exception(
                            "{$product->name} is not available for sale."
                        );
                    }

                    $inventory = Inventory::where(
                        'product_id',
                        $product->product_id
                    )
                        ->where('reserve_type', 'retail')
                        ->lockForUpdate()
                        ->first();

                    if (!$inventory) {
                        throw new \Exception(
                            "{$product->name} has no retail inventory."
                        );
                    }

                    $quantity = (float) $item['quantity'];

                    if ($inventory->current_quantity < $quantity) {
                        throw new \Exception(
                            "Insufficient retail stock for {$product->name}."
                        );
                    }

                    $unitPrice = (float) $product->selling_price;
                    $lineTotal = $quantity * $unitPrice;

                    $subtotal += $lineTotal;
                }

                $discount = (float) $validated['discount_amount'];

                if ($discount > $subtotal) {
                    throw new \Exception(
                        'Discount cannot exceed the sale subtotal.'
                    );
                }

                $total = $subtotal - $discount;

                $sale = Sale::create([
                    'customer_id' => $validated['customer_id'] ?? null,
                    'user_id' => auth()->user()->user_id,
                    'sale_date' => now(),
                    'payment_method' => $validated['payment_method'],
                    'gcash_reference' => $validated['gcash_reference'] ?? null,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'discount_type' => $validated['discount_type'],
                    'discount_name' => $validated['discount_name'] ?? null,
                    'discount_id_number' => $validated['discount_id_number'] ?? null,
                    'total_amount' => $total,
                    'receipt_issued' => true,
                ]);

                // Generate the human-readable reference code (DDMMYY-NNNNN)
                $datePart = $sale->sale_date->format('dmy');

                $lastToday = Sale::where('reference_code', 'LIKE', $datePart . '-%')
                    ->orderByDesc('reference_code')
                    ->value('reference_code');

                $nextNumber = 1;
                if ($lastToday) {
                    $parts = explode('-', $lastToday);
                    $nextNumber = intval(end($parts)) + 1;
                }

                $sale->update([
                    'reference_code' => $datePart . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT),
                ]);

                $saleId = $sale->sale_id;

                foreach ($validated['items'] as $item) {

                    $product = Product::findOrFail($item['product_id']);

                    $inventory = Inventory::where(
                        'product_id',
                        $product->product_id
                    )
                        ->where('reserve_type', 'retail')
                        ->lockForUpdate()
                        ->firstOrFail();

                    $quantity = (float) $item['quantity'];
                    $unitPrice = (float) $product->selling_price;
                    $lineTotal = $quantity * $unitPrice;

                    SaleItem::create([
                        'sale_id' => $sale->sale_id,
                        'product_id' => $product->product_id,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'line_total' => $lineTotal,
                    ]);

                    $inventory->update([
                        'current_quantity' =>
                        $inventory->current_quantity - $quantity,
                        'last_updated' => now(),
                    ]);

                    InventoryTransaction::create([
                        'inventory_id' => $inventory->inventory_id,
                        'transaction_type' => 'sale_out',
                        'quantity_change' => -$quantity,
                        'reference_id' => $sale->sale_id,
                        'reference_type' => 'sale',
                        'notes' => null,
                        'transaction_date' => now(),
                        'recorded_by' => auth()->user()->user_id,
                    ]);
                }

                // Extra validation: PWD and Senior ID formats
                if ($validated['discount_type'] === 'pwd') {
                    $raw = preg_replace('/\D/', '', $validated['discount_id_number'] ?? '');
                    if (strlen($raw) !== 16) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'discount_id_number' => 'PWD ID must contain exactly 16 digits (format: RR-PPMM-BBB-NNNNNNN).',
                        ]);
                    }
                }

                if ($validated['discount_type'] === 'senior') {
                    $raw = trim($validated['discount_id_number'] ?? '');
                    if (strlen($raw) < 4 || strlen($raw) > 30) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'discount_id_number' => 'Senior Citizen ID must be between 4 and 30 characters.',
                        ]);
                    }
                }

                $customerName = $sale->customer
                    ? $sale->customer->full_name
                    : 'Walk-in Customer';

                app(AuditLogger::class)->log(
                    'create',
                    'sales',
                    $sale->sale_id,
                    'Sale completed for ' .
                        $customerName .
                        '. Total: ₱' .
                        number_format((float) $sale->total_amount, 2) .
                        ', Payment: ' .
                        strtoupper($sale->payment_method)
                );
            });

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'sale_id' => $saleId,
                    'receipt_url' => route('sales.print', $saleId),
                ]);
            }

            return redirect()->route('sales.print', $saleId);
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function print(Sale $sale)
    {
        $sale->load('items.product', 'customer', 'user');
        return view('sales.print', compact('sale'));
    }
}
