<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organisme;
use App\Models\EntiteProductrice;

class OrganismesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Organismes
        $adii = Organisme::create([
            'nom_org' => 'ADII - Administration des Douanes et Impôts Indirects'
        ]);

        $dgi = Organisme::create([
            'nom_org' => 'DGI - Direction Générale des Impôts'
        ]);

        $tgr = Organisme::create([
            'nom_org' => 'TGR - Trésorerie Générale du Royaume'
        ]);

        $ancfcc = Organisme::create([
            'nom_org' => 'ANCFCC - Agence Nationale de la Conservation Foncière'
        ]);

        // Create Entité Productrices for ADII
        $adiiDirection = EntiteProductrice::create([
            'nom_entite' => 'Direction Générale ADII',
            'code_entite' => 'ADII-DG',
            'id_organisme' => $adii->id,
            'entite_parent' => null
        ]);

        $directionOperations = EntiteProductrice::create([
            'nom_entite' => 'Direction des Opérations Douanières',
            'code_entite' => 'ADII-DOD',
            'id_organisme' => $adii->id,
            'entite_parent' => $adiiDirection->id
        ]);

        $directionControle = EntiteProductrice::create([
            'nom_entite' => 'Direction du Contrôle',
            'code_entite' => 'ADII-DC',
            'id_organisme' => $adii->id,
            'entite_parent' => $adiiDirection->id
        ]);

        $directionInformatique = EntiteProductrice::create([
            'nom_entite' => 'Direction de l\'Informatique',
            'code_entite' => 'ADII-DI',
            'id_organisme' => $adii->id,
            'entite_parent' => $adiiDirection->id
        ]);

        // Sous-entités des opérations douanières
        EntiteProductrice::create([
            'nom_entite' => 'Service Transit',
            'code_entite' => 'ADII-DOD-ST',
            'id_organisme' => $adii->id,
            'entite_parent' => $directionOperations->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Service Dédouanement',
            'code_entite' => 'ADII-DOD-SD',
            'id_organisme' => $adii->id,
            'entite_parent' => $directionOperations->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Service Contentieux',
            'code_entite' => 'ADII-DOD-SC',
            'id_organisme' => $adii->id,
            'entite_parent' => $directionOperations->id
        ]);

        // Sous-entités du contrôle
        EntiteProductrice::create([
            'nom_entite' => 'Brigade Mobile',
            'code_entite' => 'ADII-DC-BM',
            'id_organisme' => $adii->id,
            'entite_parent' => $directionControle->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Service Enquêtes',
            'code_entite' => 'ADII-DC-SE',
            'id_organisme' => $adii->id,
            'entite_parent' => $directionControle->id
        ]);

        // Create some entities for DGI
        $dgiDirection = EntiteProductrice::create([
            'nom_entite' => 'Direction Générale DGI',
            'code_entite' => 'DGI-DG',
            'id_organisme' => $dgi->id,
            'entite_parent' => null
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Direction des Impôts Directs',
            'code_entite' => 'DGI-DID',
            'id_organisme' => $dgi->id,
            'entite_parent' => $dgiDirection->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Direction des Impôts Indirects',
            'code_entite' => 'DGI-DII',
            'id_organisme' => $dgi->id,
            'entite_parent' => $dgiDirection->id
        ]);

        // Create some entities for TGR
        $tgrDirection = EntiteProductrice::create([
            'nom_entite' => 'Direction Générale TGR',
            'code_entite' => 'TGR-DG',
            'id_organisme' => $tgr->id,
            'entite_parent' => null
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Direction du Budget',
            'code_entite' => 'TGR-DB',
            'id_organisme' => $tgr->id,
            'entite_parent' => $tgrDirection->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Direction de la Comptabilité',
            'code_entite' => 'TGR-DC',
            'id_organisme' => $tgr->id,
            'entite_parent' => $tgrDirection->id
        ]);

        // Create some entities for ANCFCC
        $ancfccDirection = EntiteProductrice::create([
            'nom_entite' => 'Direction Générale ANCFCC',
            'code_entite' => 'ANCFCC-DG',
            'id_organisme' => $ancfcc->id,
            'entite_parent' => null
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Direction de la Conservation Foncière',
            'code_entite' => 'ANCFCC-DCF',
            'id_organisme' => $ancfcc->id,
            'entite_parent' => $ancfccDirection->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Direction du Cadastre',
            'code_entite' => 'ANCFCC-DCad',
            'id_organisme' => $ancfcc->id,
            'entite_parent' => $ancfccDirection->id
        ]);
    }
}
