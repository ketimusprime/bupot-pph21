<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use App\Models\Supplier;
use App\Models\PartTimer;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
        ]);

        // Create sample company
        Company::create([
            'name' => 'PT CONTOH INDONESIA',
            'npwp' => '012345678901234',
            'address' => 'Jl. Contoh No. 123, Jakarta',
            'phone' => '021-12345678',
            'email' => 'info@contoh.co.id',
            'director_name' => 'John Doe',
            'is_active' => true,
        ]);

        // Create sample suppliers
        Supplier::create([
            'name' => 'CV Mitra Sejahtera',
            'npwp' => '123456789012345',
            'address' => 'Jl. Supplier No. 456',
            'phone' => '021-87654321',
            'email' => 'mitra@example.com',
            'is_active' => true,
        ]);

        // Create sample part timers
        PartTimer::create([
            'name' => 'Budi Santoso',
            'nik' => '3201010101010001',
            'npwp' => '234567890123456',
            'address' => 'Jl. Freelancer No. 789',
            'phone' => '081234567890',
            'is_active' => true,
        ]);
    }
}
