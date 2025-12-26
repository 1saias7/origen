<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            [
                'name' => 'Origen Centro',
                'code' => 'SUC001',
                'address' => 'Av. Libertador Bernardo O\'Higgins 123, Santiago',
                'phone' => '+56912345678',
                'is_active' => true,
            ],
            [
                'name' => 'Origen Providencia',
                'code' => 'SUC002',
                'address' => 'Av. Providencia 456, Providencia',
                'phone' => '+56987654321',
                'is_active' => true,
            ],
            [
                'name' => 'Origen Las Condes',
                'code' => 'SUC003',
                'address' => 'Av. Apoquindo 789, Las Condes',
                'phone' => '+56911223344',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}