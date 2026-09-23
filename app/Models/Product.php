<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'name',
        'variation',
        'is_sellable',
        'selling_price',
        'stock_unit',
        'purchase_unit',
        'units_per_purchase',
    ];

    protected function casts(): array
    {
        return [
            'is_sellable' => 'boolean',
            'selling_price' => 'decimal:2',
            'units_per_purchase' => 'decimal:2',
        ];
    }

    public function inventory()
    {
        return $this->hasMany(
            Inventory::class,
            'product_id',
            'product_id'
        );
    }

    public function saleItems()
    {
        return $this->hasMany(
            SaleItem::class,
            'product_id',
            'product_id'
        );
    }

    public function purchaseItems()
    {
        return $this->hasMany(
            PurchaseItem::class,
            'product_id',
            'product_id'
        );
    }

    public function orderItems()
    {
        return $this->hasMany(
            OrderItem::class,
            'product_id',
            'product_id'
        );
    }

    public function productions()
    {
        return $this->hasMany(
            Production::class,
            'product_id',
            'product_id'
        );
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

    public function getDisplayNameAttribute()
    {
        return $this->variation
            ? $this->name . ' - ' . $this->variation
            : $this->name;
    }
}