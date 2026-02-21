<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Recipe;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        $mainCourse = Category::create(['name' => 'Main Course', 'description' => 'Hearty main dishes']);
        $drinks     = Category::create(['name' => 'Drinks', 'description' => 'Hot and cold beverages']);
        $desserts   = Category::create(['name' => 'Desserts', 'description' => 'Sweet endings']);


        $tomato  = Ingredient::create(['name' => 'Tomato',       'unit' => 'gram',  'current_stock' => 20000, 'minimum_stock' => 2000]);
        $cheese  = Ingredient::create(['name' => 'Cheese',       'unit' => 'gram',  'current_stock' => 10000, 'minimum_stock' => 1000]);
        $bread   = Ingredient::create(['name' => 'Burger Bun',   'unit' => 'piece', 'current_stock' => 100,   'minimum_stock' => 10]);
        $beef    = Ingredient::create(['name' => 'Beef Patty',   'unit' => 'gram',  'current_stock' => 15000, 'minimum_stock' => 2000]);
        $pasta   = Ingredient::create(['name' => 'Pasta',        'unit' => 'gram',  'current_stock' => 8000,  'minimum_stock' => 500]);
        $sauce   = Ingredient::create(['name' => 'Tomato Sauce', 'unit' => 'ml',    'current_stock' => 5000,  'minimum_stock' => 500]);
        $cream   = Ingredient::create(['name' => 'Cream',        'unit' => 'ml',    'current_stock' => 3000,  'minimum_stock' => 300]);
        $pizzaDough = Ingredient::create(['name' => 'Pizza Dough','unit' => 'gram', 'current_stock' => 10000, 'minimum_stock' => 1000]);
        $cocoa   = Ingredient::create(['name' => 'Cocoa Powder', 'unit' => 'gram',  'current_stock' => 1000,  'minimum_stock' => 100]);
        $sugar   = Ingredient::create(['name' => 'Sugar',        'unit' => 'gram',  'current_stock' => 5000,  'minimum_stock' => 500]);


        $burger = MenuItem::create([
            'category_id' => $mainCourse->id,
            'name'        => 'Classic Burger',
            'price'       => 12.99,
            'description' => 'Juicy beef burger with all the trimmings',
        ]);
        Recipe::create(['menu_item_id' => $burger->id, 'ingredient_id' => $beef->id,   'quantity_required' => 200]);
        Recipe::create(['menu_item_id' => $burger->id, 'ingredient_id' => $bread->id,  'quantity_required' => 1]);
        Recipe::create(['menu_item_id' => $burger->id, 'ingredient_id' => $tomato->id, 'quantity_required' => 50]);
        Recipe::create(['menu_item_id' => $burger->id, 'ingredient_id' => $cheese->id, 'quantity_required' => 30]);

        $pasta_dish = MenuItem::create([
            'category_id' => $mainCourse->id,
            'name'        => 'Pasta Arrabbiata',
            'price'       => 10.50,
            'description' => 'Classic Italian spicy tomato pasta',
        ]);
        Recipe::create(['menu_item_id' => $pasta_dish->id, 'ingredient_id' => $pasta->id,  'quantity_required' => 150]);
        Recipe::create(['menu_item_id' => $pasta_dish->id, 'ingredient_id' => $sauce->id,  'quantity_required' => 100]);
        Recipe::create(['menu_item_id' => $pasta_dish->id, 'ingredient_id' => $tomato->id, 'quantity_required' => 80]);

        $pizza = MenuItem::create([
            'category_id' => $mainCourse->id,
            'name'        => 'Margherita Pizza',
            'price'       => 14.00,
            'description' => 'Classic pizza with fresh mozzarella and basil',
        ]);
        Recipe::create(['menu_item_id' => $pizza->id, 'ingredient_id' => $pizzaDough->id, 'quantity_required' => 250]);
        Recipe::create(['menu_item_id' => $pizza->id, 'ingredient_id' => $sauce->id,      'quantity_required' => 80]);
        Recipe::create(['menu_item_id' => $pizza->id, 'ingredient_id' => $cheese->id,     'quantity_required' => 100]);
        Recipe::create(['menu_item_id' => $pizza->id, 'ingredient_id' => $tomato->id,     'quantity_required' => 60]);

        $creamPasta = MenuItem::create([
            'category_id' => $mainCourse->id,
            'name'        => 'Pasta Carbonara',
            'price'       => 11.50,
            'description' => 'Creamy pasta with cheese',
        ]);
        Recipe::create(['menu_item_id' => $creamPasta->id, 'ingredient_id' => $pasta->id,  'quantity_required' => 150]);
        Recipe::create(['menu_item_id' => $creamPasta->id, 'ingredient_id' => $cream->id,  'quantity_required' => 80]);
        Recipe::create(['menu_item_id' => $creamPasta->id, 'ingredient_id' => $cheese->id, 'quantity_required' => 40]);

        $chocolate = MenuItem::create([
            'category_id' => $desserts->id,
            'name'        => 'Chocolate Mousse',
            'price'       => 6.50,
            'description' => 'Rich and creamy chocolate dessert',
        ]);
        Recipe::create(['menu_item_id' => $chocolate->id, 'ingredient_id' => $cocoa->id,  'quantity_required' => 30]);
        Recipe::create(['menu_item_id' => $chocolate->id, 'ingredient_id' => $cream->id,  'quantity_required' => 100]);
        Recipe::create(['menu_item_id' => $chocolate->id, 'ingredient_id' => $sugar->id,  'quantity_required' => 50]);

    }
}
