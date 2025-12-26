<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🔓 Abrir Sesión de Caja
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('cash-session.store') }}">
                        @csrf

                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">Información de Apertura</h3>
                            <p class="text-sm text-gray-600">
                                Usuario: <span class="font-semibold">{{ Auth::user()->name }}</span><br>
                                Sucursal: <span class="font-semibold">{{ Auth::user()->branch->name }}</span><br>
                                Fecha y Hora: <span class="font-semibold">{{ now()->format('d/m/Y H:i') }}</span>
                            </p>
                        </div>

                        <div class="mb-6">
                            <label for="opening_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Monto de Apertura (Efectivo Inicial en Caja)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-3 text-gray-500 text-lg">$</span>
                                <input 
                                    type="number" 
                                    name="opening_amount" 
                                    id="opening_amount" 
                                    step="1" 
                                    min="0"
                                    required
                                    class="pl-8 w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg text-lg"
                                    placeholder="0"
                                    value="{{ old('opening_amount', 0) }}"
                                >
                            </div>
                            @error('opening_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            
                            <!-- Botones rápidos -->
                            <div class="mt-3 flex flex-wrap gap-2">
                                <button type="button" onclick="document.getElementById('opening_amount').value = 50000" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">
                                    $50.000
                                </button>
                                <button type="button" onclick="document.getElementById('opening_amount').value = 100000" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">
                                    $100.000
                                </button>
                                <button type="button" onclick="document.getElementById('opening_amount').value = 200000" class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">
                                    $200.000
                                </button>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg">
                                🔓 Abrir Caja
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
</x-app-layout>