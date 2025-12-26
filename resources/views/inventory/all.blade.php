<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🌐 Inventario Global - Todas las Sucursales
            </h2>
            <a href="{{ route('inventory.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold">
                ← Volver a Mi Sucursal
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    
                    <!-- Tabla de Productos con Stocks por Sucursal -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Producto
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Categoría
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Precio
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" colspan="3">
                                        Stock por Sucursal
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total
                                    </th>
                                </tr>
                                <tr class="bg-gray-100">
                                    <th colspan="3"></th>
                                    <th class="px-3 py-2 text-xs font-medium text-gray-600">Centro</th>
                                    <th class="px-3 py-2 text-xs font-medium text-gray-600">Providencia</th>
                                    <th class="px-3 py-2 text-xs font-medium text-gray-600">Las Condes</th>
                                    <th class="px-3 py-2 text-xs font-medium text-gray-600"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($products as $product)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $product->barcode }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                                  style="background-color: {{ $product->category->color }}20; color: {{ $product->category->color }};">
                                                {{ $product->category->icon }} {{ $product->category->name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            ${{ number_format($product->price, 0, ',', '.') }}
                                        </td>
                                        
                                        @php
                                            $stockBySucursal = $product->stocks->keyBy('branch.code');
                                            $totalStock = $product->stocks->sum('stock');
                                        @endphp

                                        <!-- Stock Sucursal Centro -->
                                        <td class="px-3 py-4 text-center">
                                            @if($stockBySucursal->has('SUC001'))
                                                @php $stock = $stockBySucursal->get('SUC001'); @endphp
                                                <span class="font-bold {{ $stock->stock <= $stock->min_stock ? 'text-red-600' : 'text-green-600' }}">
                                                    {{ $stock->stock }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>

                                        <!-- Stock Sucursal Providencia -->
                                        <td class="px-3 py-4 text-center">
                                            @if($stockBySucursal->has('SUC002'))
                                                @php $stock = $stockBySucursal->get('SUC002'); @endphp
                                                <span class="font-bold {{ $stock->stock <= $stock->min_stock ? 'text-red-600' : 'text-green-600' }}">
                                                    {{ $stock->stock }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>

                                        <!-- Stock Sucursal Las Condes -->
                                        <td class="px-3 py-4 text-center">
                                            @if($stockBySucursal->has('SUC003'))
                                                @php $stock = $stockBySucursal->get('SUC003'); @endphp
                                                <span class="font-bold {{ $stock->stock <= $stock->min_stock ? 'text-red-600' : 'text-green-600' }}">
                                                    {{ $stock->stock }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>

                                        <!-- Total -->
                                        <td class="px-6 py-4 text-center">
                                            <span class="text-lg font-bold text-blue-600">
                                                {{ $totalStock }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            No hay productos registrados
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>

                    <!-- Resumen Total -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-blue-600 font-semibold">Total Productos</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $products->total() }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-green-600 font-semibold">Stock Total</p>
                            <p class="text-2xl font-bold text-green-900">
                                {{ $products->sum(function($p) { return $p->stocks->sum('stock'); }) }}
                            </p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-sm text-purple-600 font-semibold">Valor Inventario</p>
                            <p class="text-2xl font-bold text-purple-900">
                                ${{ number_format($products->sum(function($p) { 
                                    return $p->price * $p->stocks->sum('stock'); 
                                }), 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <p class="text-sm text-red-600 font-semibold">Productos Stock Bajo</p>
                            <p class="text-2xl font-bold text-red-900">
                                {{ $products->filter(function($p) {
                                    return $p->stocks->contains(function($s) {
                                        return $s->stock <= $s->min_stock;
                                    });
                                })->count() }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>