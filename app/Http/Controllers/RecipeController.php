<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RecipeController extends Controller
{

    public function index(MenuItem $menuItem): View
    {
        $menuItem->load('recipes.ingredient');
        $availableIngredients = Ingredient::orderBy('name')->get();
        return view('recipes.index', compact('menuItem', 'availableIngredients'));
    }

    public function store(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $validated = $request->validate([
            'ingredient_id'    => 'required|exists:ingredients,id',
            'quantity_required' => 'required|numeric|min:0.001',
        ]);

        $exists = Recipe::where('menu_item_id', $menuItem->id)
                        ->where('ingredient_id', $validated['ingredient_id'])
                        ->exists();

        if ($exists) {
            return back()->with('error', 'This ingredient is already in the recipe. Please update its quantity instead.');
        }

        Recipe::create([
            'menu_item_id'     => $menuItem->id,
            'ingredient_id'    => $validated['ingredient_id'],
            'quantity_required' => $validated['quantity_required'],
        ]);

        return back()->with('success', 'Ingredient added to recipe.');
    }

    public function update(Request $request, MenuItem $menuItem, Recipe $recipe): RedirectResponse
    {
        abort_unless($recipe->menu_item_id === $menuItem->id, 403);

        $validated = $request->validate([
            'quantity_required' => 'required|numeric|min:0.001',
        ]);

        $recipe->update($validated);

        return back()->with('success', 'Recipe updated.');
    }

    public function destroy(MenuItem $menuItem, Recipe $recipe): RedirectResponse
    {
        abort_unless($recipe->menu_item_id === $menuItem->id, 403);

        $recipe->delete();

        return back()->with('success', 'Ingredient removed from recipe.');
    }
}
