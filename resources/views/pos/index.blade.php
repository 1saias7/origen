<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🛒 Punto de Venta - {{ Auth::user()->branch->name }}
        </h2>
    </x-slot>

    <div class="py-4" x-data="posSystem" x-cloak>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 h-[calc(100vh-200px)]">
                
                <!-- Panel Izquierdo: Productos (8 columnas) -->
                <div class="lg:col-span-8 flex flex-col">
                    <div class="bg-white rounded-lg shadow-lg flex-1 flex flex-col">
                        
                        <!-- Buscador -->
                        <div class="p-4 border-b">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    x-model="barcodeInput"
                                    @keyup.enter="searchByBarcode"
                                    placeholder="🔍 Escanear código de barras o buscar..."
                                    class="w-full text-lg px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200"
                                    autofocus
                                >
                            </div>
                        </div>

                        <!-- Categorías -->
                        <div class="p-4 border-b bg-gray-50">
                            <div class="flex gap-2 overflow-x-auto pb-2">
                                <button 
                                    @click="selectedCategory = null" 
                                    :class="selectedCategory === null ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border-2 border-gray-300'"
                                    class="px-6 py-2 rounded-lg text-sm font-bold whitespace-nowrap hover:shadow-md transition"
                                >
                                    Todos
                                </button>
                                @foreach($products->pluck('category')->unique('id') as $category)
                                    <button 
                                        @click="selectedCategory = {{ $category->id }}" 
                                        :class="selectedCategory === {{ $category->id }} ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 border-2 border-gray-300'"
                                        class="px-6 py-2 rounded-lg text-sm font-bold whitespace-nowrap hover:shadow-md transition"
                                    >
                                        {{ $category->icon }} {{ $category->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Grid de Productos -->
                        <div class="flex-1 overflow-y-auto p-4">
                            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3">
                                @foreach($products as $product)
                                    @php
                                        $stock = $product->stocks->first();
                                    @endphp
                                    <button 
                                        x-show="selectedCategory === null || selectedCategory === {{ $product->category_id }}"
                                        @click="addToCart({{ json_encode([
                                            'id' => $product->id,
                                            'name' => $product->name,
                                            'price' => $product->price,
                                            'stock' => $stock ? $stock->stock : 0,
                                            'barcode' => $product->barcode
                                        ]) }})"
                                        class="bg-white border-2 rounded-xl p-4 text-left hover:border-blue-500 hover:shadow-lg transition transform hover:scale-105 active:scale-95"
                                        :class="{ 'opacity-50 cursor-not-allowed': {{ $stock && $stock->stock > 0 ? 'false' : 'true' }} }"
                                        :disabled="{{ $stock && $stock->stock > 0 ? 'false' : 'true' }}"
                                    >
                                        <div class="text-2xl mb-2">{{ $product->category->icon }}</div>
                                        <div class="font-bold text-sm mb-2 line-clamp-2 min-h-[40px]">{{ $product->name }}</div>
                                        <div class="text-xl font-bold text-blue-600 mb-1">${{ number_format($product->price, 0, ',', '.') }}</div>
                                        <div class="text-xs {{ $stock && $stock->stock <= 5 ? 'text-red-600 font-bold' : 'text-gray-500' }}">
                                            Stock: {{ $stock ? $stock->stock : 0 }}
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel Derecho: Carrito (4 columnas) -->
                <div class="lg:col-span-4 flex flex-col">
                    <div class="bg-white rounded-lg shadow-lg flex flex-col h-full">
                        
                        <!-- Header -->
                        <div class="p-4 border-b bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-t-lg">
                            <h3 class="text-lg font-bold">🧾 TICKET DE VENTA</h3>
                            <p class="text-sm opacity-90">{{ now()->format('d/m/Y H:i') }}</p>
                        </div>

                        <!-- Items del Carrito (con scroll) -->
                        <div class="flex-1 overflow-y-auto p-4 bg-gray-50">
                            <template x-if="cart.length === 0">
                                <div class="text-center py-12">
                                    <div class="text-6xl mb-4">🛒</div>
                                    <p class="text-gray-500 font-semibold">Carrito vacío</p>
                                    <p class="text-sm text-gray-400">Escanea productos para empezar</p>
                                </div>
                            </template>

                            <div class="space-y-2">
                                <template x-for="(item, index) in cart" :key="index">
                                    <div class="bg-white rounded-lg p-3 border-2 border-gray-200 hover:border-blue-300 transition">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1 pr-2">
                                                <p class="font-bold text-sm" x-text="item.name"></p>
                                                <p class="text-xs text-gray-500">$<span x-text="formatNumber(item.price)"></span> c/u</p>
                                            </div>
                                            <button @click="removeFromCart(index)" class="text-red-600 hover:text-red-800 text-xl">
                                                🗑️
                                            </button>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <button @click="decreaseQuantity(index)" class="w-10 h-10 bg-gray-200 rounded-full hover:bg-gray-300 font-bold text-lg active:scale-90 transition">
                                                    −
                                                </button>
                                                <span class="w-12 text-center font-bold text-lg" x-text="item.quantity"></span>
                                                <button @click="increaseQuantity(index)" class="w-10 h-10 bg-blue-600 text-white rounded-full hover:bg-blue-700 font-bold text-lg active:scale-90 transition">
                                                    +
                                                </button>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold text-lg text-blue-600">$<span x-text="formatNumber(item.price * item.quantity)"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Panel Inferior FIJO (Total y Pago) -->
                        <div class="border-t-4 border-gray-200 bg-white rounded-b-lg">
                            
                            <!-- Total -->
                            <div class="p-4 bg-gradient-to-r from-green-50 to-blue-50 border-b">
                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-bold text-gray-700">TOTAL:</span>
                                    <span class="text-3xl font-bold text-blue-600">$<span x-text="formatNumber(total)"></span></span>
                                </div>
                            </div>

                            <!-- Método de Pago -->
                            <div class="p-4 border-b bg-gray-50">
                                <label class="block text-sm font-bold text-gray-700 mb-2">💳 MÉTODO DE PAGO</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <button 
                                        @click="paymentMethod = 'cash'; showNumpad = true"
                                        :class="paymentMethod === 'cash' ? 'bg-green-600 text-white ring-4 ring-green-300' : 'bg-white text-gray-700 border-2 border-gray-300'"
                                        class="py-4 px-4 rounded-lg font-bold text-lg hover:shadow-lg transition active:scale-95"
                                    >
                                        💵 Efectivo
                                    </button>
                                    <button 
                                        @click="paymentMethod = 'card'; showNumpad = false; paidAmount = total"
                                        :class="paymentMethod === 'card' ? 'bg-blue-600 text-white ring-4 ring-blue-300' : 'bg-white text-gray-700 border-2 border-gray-300'"
                                        class="py-4 px-4 rounded-lg font-bold text-lg hover:shadow-lg transition active:scale-95"
                                    >
                                        💳 Tarjeta
                                    </button>
                                </div>
                            </div>

                            <!-- Numpad Virtual (solo para efectivo) -->
                            <div x-show="paymentMethod === 'cash' && showNumpad" class="p-4 bg-white border-b">
                                <label class="block text-sm font-bold text-gray-700 mb-2">💰 RECIBIDO</label>
                                
                                <!-- Display del monto -->
                                <div class="mb-3 text-center">
                                    <div class="text-3xl font-bold text-green-600 bg-green-50 rounded-lg py-3 border-2 border-green-200">
                                        $<span x-text="formatNumber(paidAmount)"></span>
                                    </div>
                                </div>

                                <!-- Botones rápidos de billetes -->
                                <div class="grid grid-cols-3 gap-2 mb-3">
                                    <button @click="paidAmount = 1000" class="bg-gradient-to-r from-green-100 to-green-200 hover:from-green-200 hover:to-green-300 text-green-800 font-bold py-3 rounded-lg border-2 border-green-300 active:scale-95 transition">
                                        $1.000
                                    </button>
                                    <button @click="paidAmount = 2000" class="bg-gradient-to-r from-blue-100 to-blue-200 hover:from-blue-200 hover:to-blue-300 text-blue-800 font-bold py-3 rounded-lg border-2 border-blue-300 active:scale-95 transition">
                                        $2.000
                                    </button>
                                    <button @click="paidAmount = 5000" class="bg-gradient-to-r from-purple-100 to-purple-200 hover:from-purple-200 hover:to-purple-300 text-purple-800 font-bold py-3 rounded-lg border-2 border-purple-300 active:scale-95 transition">
                                        $5.000
                                    </button>
                                    <button @click="paidAmount = 10000" class="bg-gradient-to-r from-red-100 to-red-200 hover:from-red-200 hover:to-red-300 text-red-800 font-bold py-3 rounded-lg border-2 border-red-300 active:scale-95 transition">
                                        $10.000
                                    </button>
                                    <button @click="paidAmount = 20000" class="bg-gradient-to-r from-orange-100 to-orange-200 hover:from-orange-200 hover:to-orange-300 text-orange-800 font-bold py-3 rounded-lg border-2 border-orange-300 active:scale-95 transition">
                                        $20.000
                                    </button>
                                    <button @click="paidAmount = total" class="bg-gradient-to-r from-gray-700 to-gray-800 hover:from-gray-800 hover:to-gray-900 text-white font-bold py-3 rounded-lg active:scale-95 transition">
                                        Exacto
                                    </button>
                                </div>

                                <!-- Teclado numérico -->
                                <div class="grid grid-cols-3 gap-2">
                                    <template x-for="n in [1,2,3,4,5,6,7,8,9]">
                                        <button 
                                            @click="addToAmount(n)" 
                                            class="bg-white hover:bg-gray-100 text-gray-800 font-bold text-xl py-4 rounded-lg border-2 border-gray-300 active:scale-95 transition"
                                            x-text="n"
                                        ></button>
                                    </template>
                                    <button @click="clearAmount" class="bg-red-100 hover:bg-red-200 text-red-800 font-bold text-lg py-4 rounded-lg border-2 border-red-300 active:scale-95 transition">
                                        ⌫
                                    </button>
                                    <button @click="addToAmount(0)" class="bg-white hover:bg-gray-100 text-gray-800 font-bold text-xl py-4 rounded-lg border-2 border-gray-300 active:scale-95 transition">
                                        0
                                    </button>
                                    <button @click="addToAmount('00')" class="bg-white hover:bg-gray-100 text-gray-800 font-bold text-xl py-4 rounded-lg border-2 border-gray-300 active:scale-95 transition">
                                        00
                                    </button>
                                </div>

                                <!-- Vuelto -->
                                <div class="mt-3">
                                    <div x-show="change >= 0" class="p-4 bg-green-100 rounded-lg border-2 border-green-300">
                                        <p class="text-sm font-bold text-green-800 mb-1">💵 VUELTO:</p>
                                        <p class="text-2xl font-bold text-green-900">$<span x-text="formatNumber(change)"></span></p>
                                    </div>
                                    <div x-show="change < 0" class="p-4 bg-red-100 rounded-lg border-2 border-red-300">
                                        <p class="text-sm font-bold text-red-800 mb-1">⚠️ FALTA:</p>
                                        <p class="text-2xl font-bold text-red-900">$<span x-text="formatNumber(Math.abs(change))"></span></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="p-4 space-y-2">
                                <button 
                                    @click="completeSale"
                                    :disabled="cart.length === 0 || (paymentMethod === 'cash' && change < 0)"
                                    :class="cart.length === 0 || (paymentMethod === 'cash' && change < 0) ? 'bg-gray-400 cursor-not-allowed' : 'bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 active:scale-95'"
                                    class="w-full text-white font-bold py-5 px-4 rounded-xl text-xl shadow-lg transition"
                                >
                                    ✅ COMPLETAR VENTA
                                </button>
                                <button 
                                    @click="clearCart"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl active:scale-95 transition"
                                >
                                    🗑️ Limpiar Carrito
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('posSystem', () => ({
                cart: [],
                selectedCategory: null,
                barcodeInput: '',
                paymentMethod: 'cash',
                paidAmount: 0,
                showNumpad: true,

                get total() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
                },

                get change() {
                    return this.paidAmount - this.total;
                },

                addToCart(product) {
                    if (product.stock <= 0) {
                        alert('❌ Producto sin stock');
                        return;
                    }

                    const existingItem = this.cart.find(item => item.id === product.id);
                    
                    if (existingItem) {
                        if (existingItem.quantity < product.stock) {
                            existingItem.quantity++;
                        } else {
                            alert('❌ Stock insuficiente');
                        }
                    } else {
                        this.cart.push({
                            ...product,
                            quantity: 1
                        });
                    }

                    this.barcodeInput = '';
                },

                increaseQuantity(index) {
                    const item = this.cart[index];
                    if (item.quantity < item.stock) {
                        item.quantity++;
                    } else {
                        alert('❌ Stock insuficiente');
                    }
                },

                decreaseQuantity(index) {
                    if (this.cart[index].quantity > 1) {
                        this.cart[index].quantity--;
                    } else {
                        this.removeFromCart(index);
                    }
                },

                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },

                clearCart() {
                    if (confirm('¿Estás seguro de limpiar el carrito?')) {
                        this.cart = [];
                        this.paidAmount = 0;
                        this.barcodeInput = '';
                    }
                },

                addToAmount(digit) {
                    if (this.paidAmount === 0) {
                        this.paidAmount = parseInt(digit);
                    } else {
                        this.paidAmount = parseInt(String(this.paidAmount) + String(digit));
                    }
                },

                clearAmount() {
                    this.paidAmount = Math.floor(this.paidAmount / 10);
                },

                async searchByBarcode() {
                    if (!this.barcodeInput) return;

                    try {
                        const response = await fetch('{{ route("pos.search-barcode") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ barcode: this.barcodeInput })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.addToCart(data);
                        } else {
                            alert('❌ ' + (data.error || 'Producto no encontrado'));
                        }
                    } catch (error) {
                        alert('❌ Error al buscar producto');
                    }

                    this.barcodeInput = '';
                },

                async completeSale() {
                    if (this.cart.length === 0) {
                        alert('❌ El carrito está vacío');
                        return;
                    }

                    if (this.paymentMethod === 'cash' && this.change < 0) {
                        alert('❌ Monto pagado insuficiente');
                        return;
                    }

                    try {
                        const response = await fetch('{{ route("pos.complete-sale") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                items: this.cart.map(item => ({
                                    product_id: item.id,
                                    quantity: item.quantity
                                })),
                                payment_method: this.paymentMethod,
                                paid_amount: this.paymentMethod === 'cash' ? this.paidAmount : this.total
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            if (this.paymentMethod === 'cash' && data.change_amount > 0) {
                                alert(`✅ VENTA COMPLETADA!\n\n💵 Vuelto: $${this.formatNumber(data.change_amount)}`);
                            } else {
                                alert('✅ Venta completada exitosamente!');
                            }

                            this.cart = [];
                            this.paidAmount = 0;
                            this.barcodeInput = '';
                            
                            window.location.reload();
                        } else {
                            alert('❌ ' + (data.error || 'Error al completar la venta'));
                        }
                    } catch (error) {
                        alert('❌ Error al procesar la venta');
                        console.error(error);
                    }
                },

                formatNumber(num) {
                    return new Intl.NumberFormat('es-CL').format(num);
                }
            }))
        });
    </script>
</x-app-layout>