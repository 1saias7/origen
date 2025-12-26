<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            📊 Dashboard - {{ Auth::user()->branch->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Alertas -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Estado de Caja -->
            <div class="mb-6">
                @if($activeCashSession)
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-green-800">✅ Caja Abierta</h3>
                                <p class="text-sm text-green-700">
                                    Apertura: ${{ number_format($activeCashSession->opening_amount, 0, ',', '.') }} CLP
                                    • Desde: {{ $activeCashSession->opened_at->format('H:i') }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('pos.index') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold">
                                    🛒 Ir a Punto de Venta
                                </a>
                                <a href="{{ route('cash-session.close', $activeCashSession) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold">
                                    🔒 Cerrar Caja
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-yellow-800">⚠️ Caja Cerrada</h3>
                                <p class="text-sm text-yellow-700">Debes abrir una sesión de caja para comenzar a vender</p>
                            </div>
                            <a href="{{ route('cash-session.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                                🔓 Abrir Caja
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Estadísticas del Día -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="text-4xl mr-4">💰</div>
                            <div>
                                <p class="text-sm text-gray-500">Ventas del Día</p>
                                <p class="text-2xl font-bold text-gray-900">${{ number_format($todayRevenue, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="text-4xl mr-4">🧾</div>
                            <div>
                                <p class="text-sm text-gray-500">Transacciones</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $todaySales }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="text-4xl mr-4">📊</div>
                            <div>
                                <p class="text-sm text-gray-500">Ticket Promedio</p>
                                <p class="text-2xl font-bold text-gray-900">
                                    ${{ $todaySales > 0 ? number_format($todayRevenue / $todaySales, 0, ',', '.') : '0' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Productos con Stock Bajo -->
            @if($lowStockProducts->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4 text-red-600">⚠️ Productos con Stock Bajo</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Actual</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Mínimo</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($lowStockProducts as $product)
                                        @php
                                            $stock = $product->stocks->first();
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $product->barcode }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    {{ $stock ? $stock->stock : 0 }} unidades
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $stock ? $stock->min_stock : 5 }} unidades
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>