<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class IngredientController extends Controller
{
    public function __construct(private InventoryService $inventoryService)
    {}

    public function index(): View
    {
        $ingredients = Ingredient::latest()->paginate(20);
        $lowStock = $this->inventoryService->getLowStockIngredients();
        return view('ingredients.index', compact('ingredients', 'lowStock'));
    }

    public function create(): View
    {
        return view('ingredients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:150',
            'unit'          => 'required|in:kg,gram,piece,liter,ml',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        Ingredient::create($validated);

        return redirect()->route('ingredients.index')
                         ->with('success', 'Ingredient added successfully.');
    }

    public function edit(Ingredient $ingredient): View
    {
        return view('ingredients.edit', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:150',
            'unit'          => 'required|in:kg,gram,piece,liter,ml',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ]);

        $ingredient->update($validated);

        return redirect()->route('ingredients.index')
                         ->with('success', 'Ingredient updated successfully.');
    }

    public function destroy(Ingredient $ingredient): RedirectResponse
    {
        // Check if ingredient is used in any recipe
        if ($ingredient->menuItems()->exists()) {
            return back()->with('error', 'Cannot delete ingredient used in recipes.');
        }

        $ingredient->delete();

        return redirect()->route('ingredients.index')
                         ->with('success', 'Ingredient deleted successfully.');
    }

    /**
     * Show the manual stock-in form.
     */
    public function stockIn(Ingredient $ingredient): View
    {
        return view('ingredients.stock-in', compact('ingredient'));
    }

    /**
     * Process manual stock addition.
     */
    public function addStock(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.001',
            'notes'    => 'nullable|string|max:300',
        ]);

        $this->inventoryService->addStock(
            $ingredient,
            $validated['quantity'],
            $validated['notes'] ?? ''
        );

        return redirect()->route('ingredients.index')
                         ->with('success', "Added {$validated['quantity']} {$ingredient->unit} to {$ingredient->name}.");
    }
}
