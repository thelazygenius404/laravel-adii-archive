<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\EntiteProductrice;

class UtilisateursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some entite productrices for assignment
        $adiiDG = EntiteProductrice::where('code_entite', 'ADII-DG')->first();
        $adiiDOD = EntiteProductrice::where('code_entite', 'ADII-DOD')->first();
        $adiiDC = EntiteProductrice::where('code_entite', 'ADII-DC')->first();
        $adiiDI = EntiteProductrice::where('code_entite', 'ADII-DI')->first();

        // Admin user
        User::updateOrCreate(['email' => 'admin@example.com'], [
            'nom' => 'Admin',
            'prenom' => 'System',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'id_entite_productrices' => $adiiDG?->id,
        ]);

        // Gestionnaire archives user
        User::updateOrCreate(['email' => 'gestionnaire@example.com'], [
            'nom' => 'Gestionnaire',
            'prenom' => 'Archives',
            'email' => 'gestionnaire@example.com',
            'role' => 'gestionnaire_archives',
            'password' => Hash::make('gestionnaire123'),
            'email_verified_at' => now(),
            'id_entite_productrices' => $adiiDI?->id,
        ]);

        // Service producteurs user
        User::updateOrCreate(['email' => 'producteurs@example.com'], [
            'nom' => 'Service',
            'prenom' => 'Producteurs',
            'email' => 'producteurs@example.com',
            'role' => 'service_producteurs',
            'password' => Hash::make('producteurs123'),
            'email_verified_at' => now(),
            'id_entite_productrices' => $adiiDOD?->id,
        ]);
       
        // Specific users for ADII

        User::updateOrCreate(['email' => 'chef.dedouanement@adii.gov.ma'], [
            'nom' => 'Idrissi',
            'prenom' => 'Aicha',
            'email' => 'chef.dedouanement@adii.gov.ma',
            'role' => 'service_producteurs',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'id_entite_productrices' => EntiteProductrice::where('code_entite', 'ADII-DOD-SD')->first()?->id,
        ]);

        User::updateOrCreate(['email' => 'responsable.contentieux@adii.gov.ma'], [
            'nom' => 'Chakib',
            'prenom' => 'Omar',
            'email' => 'responsable.contentieux@adii.gov.ma',
            'role' => 'gestionnaire_archives',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'id_entite_productrices' => EntiteProductrice::where('code_entite', 'ADII-DOD-SC')->first()?->id,
        ]);

        User::updateOrCreate(['email' => 'chef.brigade@adii.gov.ma'], [
            'nom' => 'Fassi',
            'prenom' => 'Youssef',
            'email' => 'chef.brigade@adii.gov.ma',
            'role' => 'service_producteurs',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'id_entite_productrices' => EntiteProductrice::where('code_entite', 'ADII-DC-BM')->first()?->id,
        ]);
        User::updateOrCreate(['email' => 'ichrak.laadimi@adii.gov.ma'], [
            'nom' => 'Laadimi',
            'prenom' => 'Ichrak',
            'email' => 'ichrak.laadimi@adii.gov.ma',
            'role' => 'user',
            'password' => Hash::make('Ichrak123'),
            'email_verified_at' => now(),
            'id_entite_productrices' => $adiiDOD?->id,
        ]);
        User::updateOrCreate(['email' => 'bilal.elakry@adii.gov.ma'], [
            'nom' => 'Elakry',
            'prenom' => 'Bilal',
            'email' => 'bilal.elakry@adii.gov.ma',
            'role' => 'user',
            'password' => Hash::make('bilal123'),
            'email_verified_at' => now(),
            'id_entite_productrices' => EntiteProductrice::where('code_entite', 'ADII-DC-SE')->first()?->id,
        ]);
    }
}