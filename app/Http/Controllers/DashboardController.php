<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private InventoryService $inventoryService)
    {}

    public function index(): View
    {
        $stats = [
            'total_categories' => Category::count(),
            'total_menu_items' => MenuItem::count(),
            'total_ingredients' => Ingredient::count(),
            'total_orders'    => Order::count(),
            'pending_orders'  => Order::where('status', 'pending')->count(),
            'delivered_today' => Order::where('status', 'delivered')
                                      ->whereDate('delivered_at', today())
                                      ->count(),
            'revenue_today'   => Order::where('status', 'delivered')
                                      ->whereDate('delivered_at', today())
                                      ->sum('total_amount'),
        ];

        $lowStockIngredients = $this->inventoryService->getLowStockIngredients();

        $recentOrders = Order::with('orderItems.menuItem')
            ->latest()
            ->limit(5)
            ->get();

        $recentMovements = StockMovement::with('ingredient', 'order')
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'lowStockIngredients',
            'recentOrders',
            'recentMovements'
        ));
    }
}
