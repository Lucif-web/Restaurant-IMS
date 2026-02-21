Restaurant Inventory Management System


## Overview

A complete Inventory Management System for restaurants that manages categories, menu items, ingredients (stock), recipes, and orders — with automatic stock deduction when food is delivered.


## Features

 Module | Features |
 Categories | Create, Edit, Delete, List |
 Menu Items | CRUD, Category assignment, Pricing, Image upload |
 Ingredients | CRUD, Units (kg/gram/piece/liter/ml), Manual Stock-In |
 Recipes | Ingredient-per-item mapping with quantities |
 Orders | Place orders, Status management, Multi-item support |
 Stock Deduction | Auto-deduct on delivery with DB transactions |
 Stock Logs | Full movement history with filters |
 Dashboard | Stats, Low stock alerts, Recent activity |

## Installation

## Prerequisites
- PHP 8.2+
- Composer
- MySQL or SQLite
- Node.js (optional, for assets)

## Step-by-Step Setup


# 1. Clone / copy the project
git clone https://github.com/Lucif-web/Restaurant-IMS.git restaurant-ims
cd restaurant-ims

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Configure database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=restaurant_ims
DB_USERNAME=root
DB_PASSWORD=your_password

# 6. Run migrations
php artisan migrate

# 7. Seed with sample data (optional but recommended)
php artisan db:seed

# 8. Create storage symlink (for image uploads)
php artisan storage:link

# 9. Start the server
php artisan serve


Visit: http://localhost:8000


Database Schema
```
categories
├── id, name, description, timestamps

menu_items
├── id, category_id (FK), name, price, image, description, is_available, timestamps

ingredients
├── id, name, unit (kg/gram/piece/liter/ml), current_stock, minimum_stock, timestamps

recipes                          ← PIVOT TABLE
├── id, menu_item_id (FK), ingredient_id (FK), quantity_required, timestamps

orders
├── id, order_number, status (pending/preparing/delivered/cancelled), total_amount, notes, delivered_at, timestamps

order_items
├── id, order_id (FK), menu_item_id (FK), quantity, price, timestamps

stock_movements                  ← AUDIT LOG
├── id, ingredient_id (FK), order_id (FK nullable), type, quantity, stock_before, stock_after, notes, timestamps
```

---

## Stock Deduction Flow


1. Admin places Order (status: pending)
       ↓
   System validates stock availability at order creation (early warning)
       ↓
2. Admin marks Order as "Delivered"
       ↓
   DB Transaction begins
       ↓
3. InventoryService::deductStockForOrder($order)
       ↓
   Loop through all order items
       → Fetch recipe for each menu item
       → Calculate: quantity_required × ordered_quantity
       → Lock ingredient row (lockForUpdate)
       → Deduct from current_stock
       → Create StockMovement log entry
       ↓
4. Order updated: status=delivered, delivered_at=now()
       ↓
   DB Transaction commits
       ↓
5. Stock logs visible in Stock Movement Logs page



## Architecture

app/
├── Http/Controllers/
│   ├── DashboardController.php        ← Dashboard stats
│   ├── CategoryController.php         ← Category CRUD
│   ├── MenuItemController.php         ← Menu item CRUD + image upload
│   ├── IngredientController.php       ← Stock management
│   ├── RecipeController.php           ← Recipe management
│   ├── OrderController.php            ← Order + status updates
│   └── StockMovementController.php    ← Log viewer
│
├── Models/
│   ├── Category.php                   ← hasMany MenuItem
│   ├── MenuItem.php                   ← belongsTo Category, belongsToMany Ingredient
│   ├── Ingredient.php                 ← belongsToMany MenuItem, hasMany StockMovement
│   ├── Recipe.php                     ← Pivot: MenuItem ↔ Ingredient with quantity
│   ├── Order.php                      ← hasMany OrderItem, StockMovement
│   ├── OrderItem.php                  ← belongsTo Order + MenuItem
│   └── StockMovement.php              ← Audit log
│
├── Services/
│   └── InventoryService.php           ← Core stock deduction logic
│
database/
├── migrations/                        ← 6 migration files
└── seeders/
    └── DatabaseSeeder.php             ← Sample data

resources/views/
├── layouts/app.blade.php              ← Sidebar layout
├── dashboard/index.blade.php
├── categories/                        ← index, create, edit
├── menu-items/                        ← index, create, edit
├── ingredients/                       ← index, create, edit, stock-in
├── recipes/                           ← index (manage ingredients)
├── orders/                            ← index, create, show
└── stock-movements/                   ← index (with filters)

routes/web.php                         ← All routes



## Key Technical Decisions

### 1. Database Transactions
All stock deductions are wrapped in `DB::transaction()` to prevent partial updates:
DB::transaction(function () use ($order) {
    $this->inventoryService->deductStockForOrder($order);
    $order->update(['status' => 'delivered', 'delivered_at' => now()]);
});


### 2. Row-Level Locking
`lockForUpdate()` prevents race conditions when multiple orders are processed simultaneously:

$ingredient = Ingredient::lockForUpdate()->find($ingredientId);


### 3. Service Layer
`InventoryService` contains all business logic, keeping controllers thin and logic testable.

### 4. Stock Validation
Stock is validated both at **order creation** (early warning) and again at **delivery** (with row locking):

In OrderController::store()
$errors = $this->inventoryService->validateStockForOrder($order);

In OrderController::updateStatus() when delivering
$this->inventoryService->deductStockForOrder($order); // throws Exception if insufficient




## Screenshots Guide

| Page | URL |
| Dashboard | `/` |
| Categories | `/categories` |
| Menu Items | `/menu-items` |
| Manage Recipe | `/menu-items/{id}/recipes` |
| Ingredients | `/ingredients` |
| Stock-In | `/ingredients/{id}/stock-in` |
| Orders | `/orders` |
| New Order | `/orders/create` |
| Stock Logs | `/stock-movements` |

## Bonus Features Implemented

-  **Low Stock Alerts** — Dashboard badge + ingredients page warning
-  **Dashboard** — Revenue, order counts, recent activity
-  **Stock Movement Logs** — Filterable history with before/after values
-  **Image Upload** — Menu items support image uploads via storage


## Tech Stack

- **Framework:** Laravel 11+
- **Database:** MySQL / SQLite
- **Frontend:** Blade Templates + Bootstrap 5 + Bootstrap Icons
- **Architecture:** MVC + Service Layer
- **Features:** Eloquent ORM, Form Validation, DB Transactions, File Storage


## Developer Notes

### Adding a new unit type
Edit the `unit` enum in:
1. `ingredients` migration
2. `IngredientController` validation rules
3. `ingredients/create.blade.php` select options

### Extending low-stock notifications
Implement `App\Notifications\LowStockNotification` and dispatch it from `InventoryService::deductStockForOrder()` after checking `$ingredient->isLowStock()`.
