<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🔒 Cerrar Sesión de Caja
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    
                    <!-- Resumen de la Sesión -->
                    <div class="mb-6 bg-blue-50 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-3">📊 Resumen de la Sesión</h3>
                        
                        @php
                            $totalSales = $cashSession->sales()->where('status', 'completed')->count();
                            $cashSales = $cashSession->sales()->where('payment_method', 'cash')->where('status', 'completed')->sum('total');
                            $cardSales = $cashSession->sales()->where('payment_method', 'card')->where('status', 'completed')->sum('total');
                            $expectedCash = $cashSession->opening_amount + $cashSales;
                        @endphp

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Apertura:</p>
                                <p class="font-semibold">${{ number_format($cashSession->opening_amount, 0, ',', '.') }} CLP</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Hora Apertura:</p>
                                <p class="font-semibold">{{ $cashSession->opened_at->format('H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Total Ventas:</p>
                                <p class="font-semibold">{{ $totalSales }} transacciones</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Ventas Efectivo:</p>
                                <p class="font-semibold">${{ number_format($cashSales, 0, ',', '.') }} CLP</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Ventas Tarjeta:</p>
                                <p class="font-semibold">${{ number_format($cardSales, 0, ',', '.') }} CLP</p>
                            </div>
                            <div class="col-span-2 mt-2 pt-2 border-t border-blue-200">
                                <p class="text-gray-600">Efectivo Esperado en Caja:</p>
                                <p class="text-xl font-bold text-blue-600">${{ number_format($expectedCash, 0, ',', '.') }} CLP</p>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('cash-session.store-close', $cashSession) }}" x-data="cashClosing">
                        @csrf

                        <div class="mb-6">
                            <label for="closing_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Efectivo Real en Caja (Contar todo el efectivo)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-500 text-lg">$</span>
                                <input 
                                    type="number" 
                                    name="closing_amount" 
                                    id="closing_amount" 
                                    step="1" 
                                    min="0"
                                    required
                                    x-model="closingAmount"
                                    class="pl-8 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-lg"
                                    placeholder="0"
                                >
                            </div>
                            @error('closing_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <!-- Diferencia -->
                            <div class="mt-3 p-3 rounded-lg" :class="difference === 0 ? 'bg-green-100' : 'bg-red-100'">
                                <p class="text-sm font-semibold" :class="difference === 0 ? 'text-green-800' : 'text-red-800'">
                                    Diferencia: $<span x-text="formatNumber(Math.abs(difference))"></span>
                                    <span x-show="difference > 0">(Sobrante)</span>
                                    <span x-show="difference < 0">(Faltante)</span>
                                    <span x-show="difference === 0">✅ (Cuadra perfecto)</span>
                                </p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Notas (Opcional)
                            </label>
                            <textarea 
                                name="notes" 
                                id="notes" 
                                rows="3"
                                class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg"
                                placeholder="Agregar observaciones sobre la sesión..."
                            >{{ old('notes') }}</textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-lg">
                                🔒 Cerrar Caja
                            </button>
                            <a href="{{ route('dashboard') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-4 rounded-lg text-center">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cashClosing', () => ({
                closingAmount: 0,
                expectedAmount: {{ $expectedCash }},
                
                get difference() {
                    return this.closingAmount - this.expectedAmount;
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('es-CL').format(num);
                }
            }))
        });
    </script>
</x-app-layout>