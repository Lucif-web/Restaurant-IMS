<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderController extends Controller
{
    public function __construct(private InventoryService $inventoryService)
    {}

    public function index(): View
    {
        $orders = Order::with('orderItems.menuItem')->latest()->paginate(20);
        return view('orders.index', compact('orders'));
    }

    public function create(): View
    {
        $menuItems = MenuItem::with('category')
            ->where('is_available', true)
            ->orderBy('name')
            ->get();
        return view('orders.create', compact('menuItems'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'notes'              => 'nullable|string|max:500',
            'items'              => 'required|array|min:1',
            'items.*.menu_item_id' => 'required|exists:menu_items,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'status'       => 'pending',
                    'notes'        => $validated['notes'] ?? null,
                    'total_amount' => 0,
                ]);

                $total = 0;

                foreach ($validated['items'] as $item) {
                    $menuItem = MenuItem::findOrFail($item['menu_item_id']);

                    OrderItem::create([
                        'order_id'    => $order->id,
                        'menu_item_id' => $menuItem->id,
                        'quantity'    => $item['quantity'],
                        'price'       => $menuItem->price,
                    ]);

                    $total += $menuItem->price * $item['quantity'];
                }

                $order->update(['total_amount' => $total]);

                $errors = $this->inventoryService->validateStockForOrder($order);
                if (!empty($errors)) {
                    throw new Exception("Stock validation failed:\n" . implode("\n", $errors));
                }
            });

        } catch (Exception $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('orders.index')
                         ->with('success', 'Order created successfully.');
    }

    public function show(Order $order): View
    {
        $order->load('orderItems.menuItem.recipes.ingredient', 'stockMovements.ingredient');
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,preparing,delivered,cancelled',
        ]);

        $newStatus = $validated['status'];
        if (in_array($order->status, ['delivered', 'cancelled'])) {
            return back()->with('error', 'Cannot modify a delivered or cancelled order.');
        }

        try {
            DB::transaction(function () use ($order, $newStatus) {

                if ($newStatus === 'delivered') {
                    $this->inventoryService->deductStockForOrder($order);

                    $order->update([
                        'status'       => 'delivered',
                        'delivered_at' => now(),
                    ]);

                } else {
                    $order->update(['status' => $newStatus]);
                }
            });

        } catch (Exception $e) {
            return back()->with('error', 'Failed to update order: ' . $e->getMessage());
        }

        $message = $newStatus === 'delivered'
            ? 'Order marked as delivered and stock has been deducted.'
            : 'Order status updated to ' . $newStatus . '.';

        return redirect()->route('orders.show', $order)
                         ->with('success', $message);
    }

    public function destroy(Order $order): RedirectResponse
    {
        if ($order->status === 'delivered') {
            return back()->with('error', 'Cannot delete a delivered order.');
        }

        $order->delete();

        return redirect()->route('orders.index')
                         ->with('success', 'Order deleted.');
    }
}
