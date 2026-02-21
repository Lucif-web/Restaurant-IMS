<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index(): View
    {
        $menuItems = MenuItem::with('category')->latest()->paginate(15);
        return view('menu-items.index', compact('menuItems'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        return view('menu-items.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'name'         => 'required|string|max:150',
            'price'        => 'required|numeric|min:0',
            'description'  => 'nullable|string|max:500',
            'is_available' => 'boolean',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $validated['is_available'] = $request->boolean('is_available', true);

        MenuItem::create($validated);

        return redirect()->route('menu-items.index')
                         ->with('success', 'Menu item created successfully.');
    }

    public function show(MenuItem $menuItem): View
    {
        $menuItem->load('category', 'recipes.ingredient');
        return view('menu-items.show', compact('menuItem'));
    }

    public function edit(MenuItem $menuItem): View
    {
        $categories = Category::orderBy('name')->get();
        return view('menu-items.edit', compact('menuItem', 'categories'));
    }

    public function update(Request $request, MenuItem $menuItem): RedirectResponse
    {
        $validated = $request->validate([
            'category_id'  => 'required|exists:categories,id',
            'name'         => 'required|string|max:150',
            'price'        => 'required|numeric|min:0',
            'description'  => 'nullable|string|max:500',
            'is_available' => 'boolean',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }
            $validated['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $validated['is_available'] = $request->boolean('is_available');

        $menuItem->update($validated);

        return redirect()->route('menu-items.index')
                         ->with('success', 'Menu item updated successfully.');
    }

    public function destroy(MenuItem $menuItem): RedirectResponse
    {
        // Check if item is part of any non-delivered order
        $hasActiveOrders = $menuItem->orderItems()
            ->whereHas('order', fn($q) => $q->whereNotIn('status', ['delivered', 'cancelled']))
            ->exists();

        if ($hasActiveOrders) {
            return back()->with('error', 'Cannot delete a menu item with active orders.');
        }

        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }

        $menuItem->delete();

        return redirect()->route('menu-items.index')
                         ->with('success', 'Menu item deleted successfully.');
    }
}
