<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Bebidas', 'color' => '#3B82F6', 'icon' => '🥤'],
            ['name' => 'Snacks', 'color' => '#F59E0B', 'icon' => '🍿'],
            ['name' => 'Lácteos', 'color' => '#10B981', 'icon' => '🥛'],
            ['name' => 'Golosinas', 'color' => '#EC4899', 'icon' => '🍫'],
            ['name' => 'Panadería', 'color' => '#8B5CF6', 'icon' => '🍞'],
            ['name' => 'Cigarros', 'color' => '#EF4444', 'icon' => '🚬'],
            ['name' => 'Aseo', 'color' => '#06B6D4', 'icon' => '🧼'],
            ['name' => 'Otros', 'color' => '#6B7280', 'icon' => '📦'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}