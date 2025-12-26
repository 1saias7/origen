<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();

        // Admin principal
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@origen.cl',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'branch_id' => $branches->first()->id,
        ]);

        // Usuario Fabian (Admin personal)
        User::create([
            'name' => 'Fabian',
            'email' => 'fabian@origen.cl',
            'password' => Hash::make('fabyblind'),
            'role' => 'admin',
            'branch_id' => $branches->first()->id,
        ]);

        // Cajeros por sucursal
        foreach ($branches as $index => $branch) {
            User::create([
                'name' => 'Cajero ' . $branch->name,
                'email' => 'cajero' . ($index + 1) . '@origen.cl',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'branch_id' => $branch->id,
            ]);
        }
    }
}