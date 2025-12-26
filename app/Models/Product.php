<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'barcode',
        'sku',
        'name',
        'description',
        'price',
        'image',
        'is_active',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ProductStock::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    // Helper: Obtener stock de una sucursal específica
    public function getStockForBranch($branchId)
    {
        return $this->stocks()->where('branch_id', $branchId)->first()?->stock ?? 0;
    }

    // Helper: Obtener stock total (todas las sucursales)
    public function getTotalStock()
    {
        return $this->stocks()->sum('stock');
    }
}