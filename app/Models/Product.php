<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'reference_code',
        'name',
        'variation',
        'image_path',
        'item_type',
        'is_active',
        'is_sellable',
        'stock_purpose',
        'low_stock_threshold',
        'selling_price',
        'stock_unit',
        'purchase_unit',
        'units_per_purchase',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_sellable' => 'boolean',
            'selling_price' => 'decimal:2',
            'units_per_purchase' => 'decimal:2',
        ];
    }

    /* ============================================
       RELATIONSHIPS
       ============================================ */
    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'product_id', 'product_id');
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class, 'product_id', 'product_id');
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class, 'product_id', 'product_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'product_id');
    }

    public function orderComponents()
    {
        return $this->hasMany(OrderComponent::class, 'product_id', 'product_id');
    }

    public function productions()
    {
        return $this->hasMany(Production::class, 'product_id', 'product_id');
    }

    public function parentComponents()
    {
        return $this->hasMany(
            ProductComponent::class,
            'parent_product_id',
            'product_id'
        );
    }

    public function usedAsComponent()
    {
        return $this->hasMany(
            ProductComponent::class,
            'material_product_id',
            'product_id'
        );
    }

    /* ============================================
       ACCESSORS
       ============================================ */
    public function getDisplayNameAttribute()
    {
        return $this->variation
            ? $this->name . ' - ' . $this->variation
            : $this->name;
    }

    public function getImageUrlAttribute()
    {
        return $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : null;
    }
}