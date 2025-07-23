<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
class UtilisateursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         User::updateOrCreate([
            'nom' => 'Admin',
            'prenom' => '',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
         User::updateOrCreate([
            'nom' => 'Gestionnaire',
            'prenom' => 'Archives',
            'email' => 'gestionnaire@example.com',
            'role' => 'gestionnaire_archives',
            'password' => Hash::make('gestionnaire123'),
            'email_verified_at' => now(),
        ]);
          User::updateOrCreate([
            'nom' => 'Service',
            'prenom' => 'Producteurs',
            'email' => 'producteurs@example.com',
            'role' => 'service_producteurs',
            'password' => Hash::make('producteurs123'),
            'email_verified_at' => now(),
        ]);
    }
}
