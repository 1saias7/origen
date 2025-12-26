<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ProductStock;
use App\Models\InventoryMovement;
use App\Models\CashSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Verificar que haya una sesión de caja abierta
        $activeCashSession = CashSession::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if (!$activeCashSession) {
            return redirect()->route('cash-session.create')
                ->with('error', 'Debes abrir una sesión de caja primero');
        }

        $products = Product::with(['category', 'stocks' => function($query) use ($user) {
            $query->where('branch_id', $user->branch_id);
        }])
        ->where('is_active', true)
        ->get();

        return view('pos.index', compact('products', 'activeCashSession'));
    }

    public function searchByBarcode(Request $request)
    {
        $barcode = $request->input('barcode');
        $user = Auth::user();

        $product = Product::where('barcode', $barcode)
            ->with(['stocks' => function($query) use ($user) {
                $query->where('branch_id', $user->branch_id);
            }])
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Producto no encontrado'], 404);
        }

        $stock = $product->stocks->first();
        
        if (!$stock || $stock->stock <= 0) {
            return response()->json(['error' => 'Producto sin stock'], 400);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $stock->stock,
            'barcode' => $product->barcode,
        ]);
    }

    public function completeSale(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,card',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        
        $activeCashSession = CashSession::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if (!$activeCashSession) {
            return response()->json(['error' => 'No hay sesión de caja activa'], 400);
        }

        try {
            DB::beginTransaction();

            // Calcular total
            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $total += $product->price * $item['quantity'];
            }

            // Calcular vuelto
            $paidAmount = $request->payment_method === 'cash' ? $request->paid_amount : $total;
            $changeAmount = $paidAmount - $total;

            if ($changeAmount < 0) {
                return response()->json(['error' => 'Monto pagado insuficiente'], 400);
            }

            // Generar número de venta
            $lastSale = Sale::whereDate('created_at', today())->latest()->first();
            $saleNumber = 'VTA-' . now()->format('Ymd') . '-' . str_pad(($lastSale ? $lastSale->id + 1 : 1), 4, '0', STR_PAD_LEFT);

            // Crear venta
            $sale = Sale::create([
                'sale_number' => $saleNumber,
                'user_id' => $user->id,
                'branch_id' => $user->branch_id,
                'cash_session_id' => $activeCashSession->id,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'paid_amount' => $paidAmount,
                'change_amount' => $changeAmount,
                'status' => 'completed',
            ]);

            // Crear items de venta y actualizar stock
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $quantity = $item['quantity'];

                // Crear item de venta
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total' => $product->price * $quantity,
                ]);

                // Actualizar stock
                $productStock = ProductStock::where('product_id', $product->id)
                    ->where('branch_id', $user->branch_id)
                    ->first();

                if ($productStock) {
                    $stockBefore = $productStock->stock;
                    $productStock->stock -= $quantity;
                    $productStock->save();

                    // Registrar movimiento de inventario
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'branch_id' => $user->branch_id,
                        'user_id' => $user->id,
                        'type' => 'sale',
                        'quantity' => -$quantity,
                        'stock_before' => $stockBefore,
                        'stock_after' => $productStock->stock,
                        'reason' => 'Venta #' . $saleNumber,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'sale' => $sale->load('items.product'),
                'change_amount' => $changeAmount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al procesar la venta: ' . $e->getMessage()], 500);
        }
    }
}