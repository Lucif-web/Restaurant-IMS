<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Category Management
Route::resource('categories', CategoryController::class)->except(['show']);

// Menu Item Management
Route::resource('menu-items', MenuItemController::class);

// Recipe Management (nested under menu items)
Route::prefix('menu-items/{menuItem}/recipes')->name('recipes.')->group(function () {
    Route::get('/', [RecipeController::class, 'index'])->name('index');
    Route::post('/', [RecipeController::class, 'store'])->name('store');
    Route::put('/{recipe}', [RecipeController::class, 'update'])->name('update');
    Route::delete('/{recipe}', [RecipeController::class, 'destroy'])->name('destroy');
});

// Ingredient / Stock Management
Route::resource('ingredients', IngredientController::class)->except(['show']);
Route::get('ingredients/{ingredient}/stock-in', [IngredientController::class, 'stockIn'])->name('ingredients.stock-in');
Route::post('ingredients/{ingredient}/add-stock', [IngredientController::class, 'addStock'])->name('ingredients.add-stock');

// Order Management
Route::resource('orders', OrderController::class)->except(['edit', 'update']);
Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

// Stock Movement Logs
Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
