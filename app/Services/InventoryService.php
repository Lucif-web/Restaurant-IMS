<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Validate that sufficient stock exists for all items in the order.
     * Returns array of errors if insufficient, empty array if OK.
     */
    public function validateStockForOrder(Order $order): array
    {
        $errors = [];

        // Aggregate required quantities per ingredient across all order items
        $requiredStock = $this->calculateRequiredStock($order);

        foreach ($requiredStock as $ingredientId => $data) {
            $ingredient = Ingredient::find($ingredientId);

            if (!$ingredient) {
                $errors[] = "Ingredient ID {$ingredientId} not found.";
                continue;
            }

            if ($ingredient->current_stock < $data['quantity']) {
                $errors[] = sprintf(
                    'Insufficient stock for "%s": need %.3f %s, but only %.3f %s available.',
                    $ingredient->name,
                    $data['quantity'],
                    $ingredient->unit,
                    $ingredient->current_stock,
                    $ingredient->unit
                );
            }
        }

        return $errors;
    }

    /**
     * Deduct stock for a delivered order.
     * Must be called inside a DB transaction.
     * 
     * @throws Exception if stock is insufficient
     */
    public function deductStockForOrder(Order $order): void
    {
        // First validate stock availability
        $errors = $this->validateStockForOrder($order);
        if (!empty($errors)) {
            throw new Exception(implode("\n", $errors));
        }

        // Calculate required quantities per ingredient
        $requiredStock = $this->calculateRequiredStock($order);

        foreach ($requiredStock as $ingredientId => $data) {
            // Lock the row for update to prevent race conditions
            $ingredient = Ingredient::lockForUpdate()->find($ingredientId);

            $stockBefore = $ingredient->current_stock;
            $stockAfter = $stockBefore - $data['quantity'];

            // Final check after locking
            if ($stockAfter < 0) {
                throw new Exception("Stock became insufficient for '{$ingredient->name}' during processing.");
            }

            // Update the ingredient stock
            $ingredient->update(['current_stock' => $stockAfter]);

            // Log the stock movement
            StockMovement::create([
                'ingredient_id' => $ingredientId,
                'order_id'      => $order->id,
                'type'          => 'deduction',
                'quantity'      => -$data['quantity'], // Negative = deducted
                'stock_before'  => $stockBefore,
                'stock_after'   => $stockAfter,
                'notes'         => "Auto-deducted for Order #{$order->order_number}",
            ]);
        }
    }

    /**
     * Manually add stock for an ingredient.
     */
    public function addStock(Ingredient $ingredient, float $quantity, string $notes = ''): StockMovement
    {
        return DB::transaction(function () use ($ingredient, $quantity, $notes) {
            $stockBefore = $ingredient->current_stock;
            $stockAfter = $stockBefore + $quantity;

            $ingredient->update(['current_stock' => $stockAfter]);

            return StockMovement::create([
                'ingredient_id' => $ingredient->id,
                'order_id'      => null,
                'type'          => 'manual_add',
                'quantity'      => $quantity,
                'stock_before'  => $stockBefore,
                'stock_after'   => $stockAfter,
                'notes'         => $notes ?: "Manual stock addition",
            ]);
        });
    }

    /**
     * Calculate the total ingredient quantities required for all order items.
     * Returns: [ingredient_id => ['quantity' => float, 'name' => string]]
     */
    private function calculateRequiredStock(Order $order): array
    {
        $required = [];

        // Eager load the relationships
        $order->load('orderItems.menuItem.recipes.ingredient');

        foreach ($order->orderItems as $orderItem) {
            $menuItem = $orderItem->menuItem;

            if (!$menuItem) {
                continue;
            }

            foreach ($menuItem->recipes as $recipe) {
                $ingredientId = $recipe->ingredient_id;
                $needed = $recipe->quantity_required * $orderItem->quantity;

                if (!isset($required[$ingredientId])) {
                    $required[$ingredientId] = [
                        'quantity' => 0,
                        'name' => $recipe->ingredient->name ?? "ID:{$ingredientId}",
                    ];
                }

                $required[$ingredientId]['quantity'] += $needed;
            }
        }

        return $required;
    }

    /**
     * Get low-stock ingredients.
     */
    public function getLowStockIngredients()
    {
        return Ingredient::whereColumn('current_stock', '<=', 'minimum_stock')->get();
    }
}
