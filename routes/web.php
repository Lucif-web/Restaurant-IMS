<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\StockMovementController;
use Illuminate\Support\Facades\Route;


Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('categories', CategoryController::class)->except(['show']);

Route::resource('menu-items', MenuItemController::class);

Route::prefix('menu-items/{menuItem}/recipes')->name('recipes.')->group(function () {
    Route::get('/', [RecipeController::class, 'index'])->name('index');
    Route::post('/', [RecipeController::class, 'store'])->name('store');
    Route::put('/{recipe}', [RecipeController::class, 'update'])->name('update');
    Route::delete('/{recipe}', [RecipeController::class, 'destroy'])->name('destroy');
});

Route::resource('ingredients', IngredientController::class)->except(['show']);
Route::get('ingredients/{ingredient}/stock-in', [IngredientController::class, 'stockIn'])->name('ingredients.stock-in');
Route::post('ingredients/{ingredient}/add-stock', [IngredientController::class, 'addStock'])->name('ingredients.add-stock');

Route::resource('orders', OrderController::class)->except(['edit', 'update']);
Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');

Route::get('stock-movements', [StockMovementController::class, 'index'])->name('stock-movements.index');
