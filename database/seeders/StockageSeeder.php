<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Organisme;
use App\Models\Salle;
use App\Models\Travee;
use App\Models\Tablette;
use App\Models\Position;
use App\Models\Boite;
use App\Models\Dossier;
use App\Models\CalendrierConservation;

class StockageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get organismes
        $adii = Organisme::where('nom_org', 'LIKE', '%ADII%')->first();
        $dgi = Organisme::where('nom_org', 'LIKE', '%DGI%')->first();

        if (!$adii || !$dgi) {
            $this->command->warn('Les organismes ADII et DGI doivent être créés avant d\'exécuter ce seeder.');
            return;
        }

        // Create salles for ADII
        $salleADII1 = Salle::create([
            'nom' => 'Salle Archives ADII - Niveau 1',
            'capacite_max' => 500,
            'capacite_actuelle' => 0,
            'organisme_id' => $adii->id,
        ]);

        $salleADII2 = Salle::create([
            'nom' => 'Salle Archives ADII - Niveau 2',
            'capacite_max' => 800,
            'capacite_actuelle' => 0,
            'organisme_id' => $adii->id,
        ]);

        // Create salles for DGI
        $salleDGI1 = Salle::create([
            'nom' => 'Salle Archives DGI - Principal',
            'capacite_max' => 600,
            'capacite_actuelle' => 0,
            'organisme_id' => $dgi->id,
        ]);

        // Create structure for ADII Salle 1
        $this->createSalleStructure($salleADII1, 'A', 5, 8, 10);
        
        // Create structure for ADII Salle 2
        $this->createSalleStructure($salleADII2, 'B', 8, 10, 10);
        
        // Create structure for DGI Salle 1
        $this->createSalleStructure($salleDGI1, 'C', 6, 10, 10);

        // Create some boites and dossiers
        $this->createBoitesAndDossiers();

        // Update capacities
        Salle::all()->each(function($salle) {
            $salle->updateCapaciteActuelle();
        });
    }

    /**
     * Create complete structure for a salle.
     */
    private function createSalleStructure($salle, $prefix, $traveeCount, $tablettesPerTravee, $positionsPerTablette)
    {
        for ($t = 1; $t <= $traveeCount; $t++) {
            $travee = Travee::create([
                'nom' => "Travée {$prefix}{$t}",
                'salle_id' => $salle->id,
            ]);

            for ($tab = 1; $tab <= $tablettesPerTravee; $tab++) {
                $tablette = Tablette::create([
                    'nom' => "Tablette {$prefix}{$t}-{$tab}",
                    'travee_id' => $travee->id,
                ]);

                for ($pos = 1; $pos <= $positionsPerTablette; $pos++) {
                    Position::create([
                        'nom' => "Position {$prefix}{$t}-{$tab}-{$pos}",
                        'vide' => true,
                        'tablette_id' => $tablette->id,
                    ]);
                }
            }
        }
    }

    /**
     * Create sample boites and dossiers.
     */
    private function createBoitesAndDossiers()
    {
        // Get some positions
        $positions = Position::with('tablette.travee.salle')->limit(50)->get();
        $calendriers = CalendrierConservation::limit(10)->get();

        if ($calendriers->isEmpty()) {
            $this->command->warn('Aucune règle de conservation trouvée. Créez d\'abord les règles de conservation.');
            return;
        }

        $boiteCounter = 1;
        $dossierCounter = 1;

        foreach ($positions->take(30) as $position) {
            // Create boite
            $boite = Boite::create([
                'numero' => 'BOX-' . str_pad($boiteCounter, 4, '0', STR_PAD_LEFT),
                'code_thematique' => $this->generateCodeThematique(),
                'code_topo' => $this->generateCodeTopo($position),
                'capacite' => rand(10, 50),
                'nbr_dossiers' => 0,
                'detruite' => false,
                'position_id' => $position->id,
            ]);

            // Mark position as occupied
            $position->markAsOccupied();

            // Create dossiers in this boite
            $nbrDossiers = rand(3, min(15, $boite->capacite));
            for ($d = 1; $d <= $nbrDossiers; $d++) {
                $calendrier = $calendriers->random();
                $dateCreation = now()->subDays(rand(30, 3650)); // Between 1 month and 10 years ago
                
                // Calculate elimination date
                $totalYears = $calendrier->archive_courant + $calendrier->archive_intermediaire;
                $dateElimination = $dateCreation->copy()->addYears($totalYears);

                Dossier::create([
                    'numero' => 'DOS-' . str_pad($dossierCounter, 6, '0', STR_PAD_LEFT),
                    'titre' => $this->generateDossierTitle(),
                    'date_creation' => $dateCreation,
                    'cote_classement' => $this->generateCoteClassement($calendrier),
                    'description' => $this->generateDescription(),
                    'mots_cles' => $this->generateMotsCles(),
                    'date_elimination_prevue' => $dateElimination,
                    'statut' => $this->getRandomStatut($dateElimination),
                    'type_piece' => $this->getRandomTypePiece(),
                    'disponible' => true,
                    'boite_id' => $boite->id,
                    'calendrier_conservation_id' => $calendrier->id,
                ]);

                $dossierCounter++;
            }

            // Update boite dossier count
            $boite->updateNbrDossiers();
            $boiteCounter++;
        }
    }

    /**
     * Generate thematic code.
     */
    private function generateCodeThematique()
    {
        $themes = ['DOD', 'CNT', 'ADM', 'FIN', 'JUR', 'TEC', 'RH', 'LOG'];
        return $themes[array_rand($themes)] . '-' . rand(100, 999);
    }

    /**
     * Generate topographic code.
     */
    private function generateCodeTopo($position)
    {
        $salle = $position->tablette->travee->salle;
        $travee = $position->tablette->travee;
        $tablette = $position->tablette;
        
        return substr($salle->nom, -1) . '-' . 
               substr($travee->nom, -1) . '-' . 
               substr($tablette->nom, -1) . '-' . 
               substr($position->nom, -1);
    }

    /**
     * Generate dossier title.
     */
    private function generateDossierTitle()
    {
        $titles = [
            'Déclaration en détail import marchandises',
            'Dossier contentieux infraction douanière',
            'Demande admission temporaire',
            'Contrôle a posteriori entreprise',
            'Recours administratif décision',
            'Dossier formation personnel',
            'Rapport audit interne service',
            'Convention coopération internationale',
            'Statistiques commerce extérieur',
            'Documentation technique système',
            'Procédure disciplinaire agent',
            'Budget exercice comptable',
            'Maintenance équipement informatique',
            'Accord partenariat OEA',
            'Contrôle substances dangereuses'
        ];

        return $titles[array_rand($titles)] . ' - ' . date('Y') . '/' . rand(1000, 9999);
    }

    /**
     * Generate cote classement.
     */
    private function generateCoteClassement($calendrier)
    {
        $planCode = $calendrier->planClassement->formatted_code;
        return $planCode . '/' . date('Y') . '/' . rand(100, 999);
    }

    /**
     * Generate description.
     */
    private function generateDescription()
    {
        $descriptions = [
            'Dossier contenant les pièces justificatives et documents administratifs relatifs à la procédure.',
            'Ensemble des documents de contrôle et de vérification effectués dans le cadre réglementaire.',
            'Pièces du dossier de demande avec tous les justificatifs requis par la réglementation.',
            'Documentation complète de la procédure avec correspondances et décisions prises.',
            'Dossier technique comprenant les spécifications et la documentation associée.'
        ];

        return $descriptions[array_rand($descriptions)];
    }

    /**
     * Generate mots cles.
     */
    private function generateMotsCles()
    {
        $motsCles = [
            ['douane', 'import', 'marchandise', 'déclaration'],
            ['contentieux', 'infraction', 'amende', 'procédure'],
            ['admission', 'temporaire', 'matériel', 'industrie'],
            ['contrôle', 'vérification', 'comptable', 'entreprise'],
            ['formation', 'personnel', 'développement', 'compétence'],
            ['budget', 'finance', 'comptabilité', 'exercice'],
            ['technique', 'informatique', 'système', 'maintenance'],
            ['coopération', 'international', 'accord', 'échange'],
            ['statistique', 'commerce', 'extérieur', 'analyse'],
            ['audit', 'interne', 'contrôle', 'recommandation']
        ];

        $selected = $motsCles[array_rand($motsCles)];
        return implode(', ', $selected);
    }

    /**
     * Get random statut based on elimination date.
     */
    private function getRandomStatut($dateElimination)
    {
        if ($dateElimination->isPast()) {
            return collect(['elimine', 'archive'])->random();
        }

        if ($dateElimination->diffInDays(now()) < 365) {
            return collect(['actif', 'archive', 'en_cours'])->random();
        }

        return collect(['actif', 'en_cours'])->random();
    }

    /**
     * Get random type piece.
     */
    private function getRandomTypePiece()
    {
        $types = [
            'Document administratif',
            'Pièce justificative',
            'Correspondance officielle',
            'Rapport technique',
            'Décision administrative',
            'Procès-verbal',
            'Contrat/Convention',
            'Document comptable',
            'Dossier personnel',
            'Document juridique'
        ];

        return $types[array_rand($types)];
    }
}