<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Category;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all()->keyBy('name');
        $branches = Branch::all();

        $products = [
            // BEBIDAS
            [
                'category' => 'Bebidas',
                'items' => [
                    ['barcode' => '7800000000001', 'name' => 'Coca Cola 500ml', 'price' => 990],
                    ['barcode' => '7800000000002', 'name' => 'Coca Cola 1.5L', 'price' => 1790],
                    ['barcode' => '7800000000003', 'name' => 'Coca Cola Zero 500ml', 'price' => 990],
                    ['barcode' => '7800000000004', 'name' => 'Fanta 500ml', 'price' => 890],
                    ['barcode' => '7800000000005', 'name' => 'Sprite 500ml', 'price' => 890],
                    ['barcode' => '7800000000006', 'name' => 'Bilz 1.5L', 'price' => 1490],
                    ['barcode' => '7800000000007', 'name' => 'Pap 1.5L', 'price' => 1490],
                    ['barcode' => '7800000000008', 'name' => 'Agua Mineral sin Gas 500ml', 'price' => 590],
                    ['barcode' => '7800000000009', 'name' => 'Agua Mineral con Gas 500ml', 'price' => 690],
                    ['barcode' => '7800000000010', 'name' => 'Jugo Watts Naranja 1L', 'price' => 1290],
                    ['barcode' => '7800000000011', 'name' => 'Jugo Watts Durazno 1L', 'price' => 1290],
                    ['barcode' => '7800000000012', 'name' => 'Red Bull 250ml', 'price' => 1590],
                    ['barcode' => '7800000000013', 'name' => 'Monster Energy 500ml', 'price' => 1990],
                ]
            ],
            
            // SNACKS
            [
                'category' => 'Snacks',
                'items' => [
                    ['barcode' => '7800000000101', 'name' => 'Ramitas 110g', 'price' => 1090],
                    ['barcode' => '7800000000102', 'name' => 'Super8 Chocolate 25g', 'price' => 390],
                    ['barcode' => '7800000000103', 'name' => 'Lays Clásicas 45g', 'price' => 890],
                    ['barcode' => '7800000000104', 'name' => 'Lays Limón 45g', 'price' => 890],
                    ['barcode' => '7800000000105', 'name' => 'Papas Fritas Marco Polo 200g', 'price' => 1490],
                    ['barcode' => '7800000000106', 'name' => 'Mani King Salado 80g', 'price' => 990],
                    ['barcode' => '7800000000107', 'name' => 'Cheetos 60g', 'price' => 990],
                    ['barcode' => '7800000000108', 'name' => 'Doritos Queso 48g', 'price' => 990],
                ]
            ],

            // LÁCTEOS
            [
                'category' => 'Lácteos',
                'items' => [
                    ['barcode' => '7800000000201', 'name' => 'Leche Entera 1L', 'price' => 990],
                    ['barcode' => '7800000000202', 'name' => 'Leche Descremada 1L', 'price' => 990],
                    ['barcode' => '7800000000203', 'name' => 'Yogurt Natural 1L', 'price' => 1490],
                    ['barcode' => '7800000000204', 'name' => 'Yogurt Frutilla 125g', 'price' => 390],
                    ['barcode' => '7800000000205', 'name' => 'Yogurt Vainilla 125g', 'price' => 390],
                    ['barcode' => '7800000000206', 'name' => 'Mantequilla 250g', 'price' => 1990],
                ]
            ],

            // GOLOSINAS
            [
                'category' => 'Golosinas',
                'items' => [
                    ['barcode' => '7800000000301', 'name' => 'Chocolate Sahne Nuss', 'price' => 890],
                    ['barcode' => '7800000000302', 'name' => 'Chocolate Trencito', 'price' => 490],
                    ['barcode' => '7800000000303', 'name' => 'Chocolate Milky Way', 'price' => 590],
                    ['barcode' => '7800000000304', 'name' => 'Chocolate Snickers', 'price' => 690],
                    ['barcode' => '7800000000305', 'name' => 'Chocolate Kit Kat', 'price' => 690],
                    ['barcode' => '7800000000306', 'name' => 'Chicles Bubbaloo', 'price' => 190],
                    ['barcode' => '7800000000307', 'name' => 'Mentas Halls', 'price' => 390],
                    ['barcode' => '7800000000308', 'name' => 'Gomitas Mogul 80g', 'price' => 790],
                ]
            ],

            // PANADERÍA
            [
                'category' => 'Panadería',
                'items' => [
                    ['barcode' => '7800000000401', 'name' => 'Pan Hallulla unidad', 'price' => 250],
                    ['barcode' => '7800000000402', 'name' => 'Pan Marraqueta unidad', 'price' => 200],
                    ['barcode' => '7800000000403', 'name' => 'Pan de Molde Ideal', 'price' => 1490],
                    ['barcode' => '7800000000404', 'name' => 'Croissant Simple', 'price' => 590],
                ]
            ],

            // CIGARROS
            [
                'category' => 'Cigarros',
                'items' => [
                    ['barcode' => '7800000000501', 'name' => 'Cigarros Kent Box', 'price' => 3990],
                    ['barcode' => '7800000000502', 'name' => 'Cigarros Marlboro Box', 'price' => 4290],
                    ['barcode' => '7800000000503', 'name' => 'Cigarros Lucky Strike', 'price' => 3890],
                ]
            ],

            // ASEO
            [
                'category' => 'Aseo',
                'items' => [
                    ['barcode' => '7800000000601', 'name' => 'Papel Higiénico Elite x4', 'price' => 2490],
                    ['barcode' => '7800000000602', 'name' => 'Jabón Dove 90g', 'price' => 990],
                    ['barcode' => '7800000000603', 'name' => 'Shampoo Sedal 340ml', 'price' => 2490],
                    ['barcode' => '7800000000604', 'name' => 'Pasta Dental Colgate 90g', 'price' => 1590],
                ]
            ],
        ];

        // Crear productos UNA SOLA VEZ
        foreach ($products as $categoryGroup) {
            $category = $categories[$categoryGroup['category']];
            
            foreach ($categoryGroup['items'] as $item) {
                // Crear producto único
                $product = Product::create([
                    'category_id' => $category->id,
                    'barcode' => $item['barcode'],
                    'sku' => 'SKU-' . Str::upper(Str::random(8)),
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'is_active' => true,
                ]);

                // Asignar stock a cada sucursal con cantidades diferentes
                foreach ($branches as $index => $branch) {
                    if ($index === 0) {
                        // Sucursal Centro: más stock
                        $stock = rand(50, 150);
                    } elseif ($index === 1) {
                        // Providencia: stock medio
                        $stock = rand(30, 80);
                    } else {
                        // Las Condes: menos stock
                        $stock = rand(10, 50);
                    }

                    ProductStock::create([
                        'product_id' => $product->id,
                        'branch_id' => $branch->id,
                        'stock' => $stock,
                        'min_stock' => 5,
                    ]);
                }
            }
        }
    }
}