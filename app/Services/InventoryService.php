<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\OrderReservation;

class InventoryService
{
    /**
     * Physical stock on hand (what's actually in the shop).
     */
    public static function physical(int $productId, string $reserveType): float
    {
        $row = Inventory::where('product_id', $productId)
            ->where('reserve_type', $reserveType)
            ->first();

        return $row ? (float) $row->current_quantity : 0.0;
    }

    /**
     * Quantity currently claimed by active orders.
     */
    public static function reserved(int $productId, string $reserveType): float
    {
        return OrderReservation::reservedFor($productId, $reserveType);
    }

    /**
     * What's actually available to promise/sell right now.
     * Can be negative — indicates "must purchase this amount".
     */
    public static function available(int $productId, string $reserveType): float
    {
        return self::physical($productId, $reserveType)
            - self::reserved($productId, $reserveType);
    }

    /**
     * Quick breakdown for UI display.
     */
    public static function breakdown(int $productId, string $reserveType): array
    {
        $physical = self::physical($productId, $reserveType);
        $reserved = self::reserved($productId, $reserveType);

        return [
            'physical' => $physical,
            'reserved' => $reserved,
            'available' => $physical - $reserved,
        ];
    }

    /**
     * Totals across both reserve types for a product.
     */
    public static function totals(int $productId): array
    {
        return [
            'retail' => self::breakdown($productId, 'retail'),
            'production' => self::breakdown($productId, 'production'),
        ];
    }
}