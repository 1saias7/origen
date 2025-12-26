<?php

namespace App\Http\Controllers;

use App\Models\CashSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashSessionController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        
        // Verificar si ya hay una sesión abierta
        $activeCashSession = CashSession::where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if ($activeCashSession) {
            return redirect()->route('dashboard')
                ->with('info', 'Ya tienes una sesión de caja abierta');
        }

        return view('cash-session.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'opening_amount' => 'required|numeric|min:0',
        ]);

        $user = Auth::user();

        CashSession::create([
            'user_id' => $user->id,
            'branch_id' => $user->branch_id,
            'opening_amount' => $request->opening_amount,
            'status' => 'open',
            'opened_at' => now(),
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Sesión de caja abierta exitosamente');
    }

    public function close(CashSession $cashSession)
    {
        if ($cashSession->user_id !== Auth::id()) {
            abort(403);
        }

        return view('cash-session.close', compact('cashSession'));
    }

    public function storeClose(Request $request, CashSession $cashSession)
    {
        $request->validate([
            'closing_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($cashSession->user_id !== Auth::id()) {
            abort(403);
        }

        // Calcular monto esperado (apertura + ventas en efectivo)
        $cashSales = $cashSession->sales()
            ->where('payment_method', 'cash')
            ->where('status', 'completed')
            ->sum('total');

        $expectedAmount = $cashSession->opening_amount + $cashSales;
        $difference = $request->closing_amount - $expectedAmount;

        $cashSession->update([
            'closing_amount' => $request->closing_amount,
            'expected_amount' => $expectedAmount,
            'difference' => $difference,
            'status' => 'closed',
            'closed_at' => now(),
            'notes' => $request->notes,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Sesión de caja cerrada exitosamente');
    }
}