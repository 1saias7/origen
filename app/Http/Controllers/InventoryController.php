<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $categories = Category::where('is_active', true)->get();
        
        $query = Product::with(['category', 'stocks' => function($q) use ($user) {
            $q->where('branch_id', $user->branch_id);
        }])->where('is_active', true);

        // Filtrar por categoría si se especifica
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        $products = $query->paginate(20);

        return view('inventory.index', compact('products', 'categories'));
    }

    public function all()
    {
        $user = Auth::user();
        
        if ($user->role !== 'admin') {
            abort(403, 'No tienes permiso para ver el inventario global');
        }

        $products = Product::with(['category', 'stocks.branch'])
            ->where('is_active', true)
            ->paginate(20);

        return view('inventory.all', compact('products'));
    }
}