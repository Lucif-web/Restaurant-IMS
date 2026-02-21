<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(Request $request): View
    {
        $query = StockMovement::with('ingredient', 'order')->latest();

        if ($request->ingredient_id) {
            $query->where('ingredient_id', $request->ingredient_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->paginate(30)->withQueryString();
        $ingredients = Ingredient::orderBy('name')->get();

        return view('stock-movements.index', compact('movements', 'ingredients'));
    }
}
