<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductComponent extends Model
{
    protected $table = 'product_components';

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = null;

    protected $fillable = [
        'parent_product_id',
        'material_product_id',
        'quantity_required',
    ];

    protected function casts(): array
    {
        return [
            'quantity_required' => 'decimal:2',
        ];
    }

    public function parentProduct()
    {
        return $this->belongsTo(
            Product::class,
            'parent_product_id',
            'product_id'
        );
    }

    public function materialProduct()
    {
        return $this->belongsTo(
            Product::class,
            'material_product_id',
            'product_id'
        );
    }
}