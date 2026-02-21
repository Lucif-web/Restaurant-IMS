<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'unit',
        'current_stock',
        'minimum_stock',
    ];

    protected $casts = [
        'current_stock' => 'decimal:3',
        'minimum_stock' => 'decimal:3',
    ];

    /**
     * An ingredient belongs to many menu items through recipes.
     */
    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'recipes')
                    ->withPivot('quantity_required')
                    ->withTimestamps();
    }

    /**
     * An ingredient has many stock movement logs.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Check if current stock is below the minimum threshold.
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    /**
     * Check if sufficient stock exists for a given quantity.
     */
    public function hasSufficientStock(float $required): bool
    {
        return $this->current_stock >= $required;
    }
}
