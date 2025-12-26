<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\CashSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        // Sesión de caja activa
        $activeCashSession = CashSession::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        // Estadísticas del día
        $today = now()->startOfDay();
        
        $todaySales = Sale::where('branch_id', $branchId)
            ->where('created_at', '>=', $today)
            ->where('status', 'completed')
            ->count();

        $todayRevenue = Sale::where('branch_id', $branchId)
            ->where('created_at', '>=', $today)
            ->where('status', 'completed')
            ->sum('total');

        // Productos con stock bajo
        $lowStockProducts = Product::whereHas('stocks', function($query) use ($branchId) {
            $query->where('branch_id', $branchId)
                  ->whereRaw('stock <= min_stock');
        })->with(['stocks' => function($query) use ($branchId) {
            $query->where('branch_id', $branchId);
        }])->take(5)->get();

        return view('dashboard', compact(
            'activeCashSession',
            'todaySales',
            'todayRevenue',
            'lowStockProducts'
        ));
    }
}