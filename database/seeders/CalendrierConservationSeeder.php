<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CalendrierConservation;
use App\Models\PlanClassement;

class CalendrierConservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regles = [
            [
                'NO_regle' => 'R001',
                'delais_legaux' => 10,
                'nature_dossier' => 'Déclarations import marchandises générales',
                'reference' => 'Code des Douanes - Article 78',
                'plan_classement_code' => 1,
                'sort_final' => 'E',
                'archive_courant' => 3,
                'archive_intermediaire' => 7,
                'observation' => 'Conservation de 10 ans à compter de la date de dédouanement'
            ],
            [
                'NO_regle' => 'R002',
                'delais_legaux' => 10,
                'nature_dossier' => 'Déclarations export marchandises générales',
                'reference' => 'Code des Douanes - Article 78',
                'plan_classement_code' => 1,
                'sort_final' => 'E',
                'archive_courant' => 3,
                'archive_intermediaire' => 7,
                'observation' => 'Conservation de 10 ans à compter de la date de dédouanement'
            ],
            [
                'NO_regle' => 'R003',
                'delais_legaux' => 5,
                'nature_dossier' => 'Carnets TIR internationaux',
                'reference' => 'Convention TIR - Annexe 8',
                'plan_classement_code' => 2,
                'sort_final' => 'E',
                'archive_courant' => 2,
                'archive_intermediaire' => 3,
                'observation' => 'Conservation limitée selon convention internationale'
            ],
            [
                'NO_regle' => 'R004',
                'delais_legaux' => 30,
                'nature_dossier' => 'Dossiers contentieux - Infractions graves',
                'reference' => 'Code de Procédure Pénale - Article 10',
                'plan_classement_code' => 3,
                'sort_final' => 'C',
                'archive_courant' => 5,
                'archive_intermediaire' => 25,
                'observation' => 'Conservation permanente pour infractions graves'
            ],
            [
                'NO_regle' => 'R005',
                'delais_legaux' => 15,
                'nature_dossier' => 'Dossiers contentieux - Infractions mineures',
                'reference' => 'Code des Douanes - Article 285',
                'plan_classement_code' => 3,
                'sort_final' => 'E',
                'archive_courant' => 5,
                'archive_intermediaire' => 10,
                'observation' => 'Conservation de 15 ans puis élimination'
            ],
            [
                'NO_regle' => 'R006',
                'delais_legaux' => 20,
                'nature_dossier' => 'Admission temporaire - Matériel industriel',
                'reference' => 'Code des Douanes - Article 161',
                'plan_classement_code' => 4,
                'sort_final' => 'E',
                'archive_courant' => 3,
                'archive_intermediaire' => 17,
                'observation' => 'Conservation jusqu\'à apurement complet'
            ],
            [
                'NO_regle' => 'R007',
                'delais_legaux' => 10,
                'nature_dossier' => 'Entrepôts de stockage',
                'reference' => 'Code des Douanes - Article 145',
                'plan_classement_code' => 4,
                'sort_final' => 'E',
                'archive_courant' => 4,
                'archive_intermediaire' => 6,
                'observation' => 'Conservation de 10 ans après fermeture'
            ],
            [
                'NO_regle' => 'R008',
                'delais_legaux' => 10,
                'nature_dossier' => 'Contrôles a posteriori - Vérifications',
                'reference' => 'Code des Douanes - Article 95',
                'plan_classement_code' => 5,
                'sort_final' => 'E',
                'archive_courant' => 3,
                'archive_intermediaire' => 7,
                'observation' => 'Conservation standard de 10 ans'
            ],
            [
                'NO_regle' => 'R009',
                'delais_legaux' => 5,
                'nature_dossier' => 'Recours administratifs',
                'reference' => 'Code de Procédure Administrative',
                'plan_classement_code' => 6,
                'sort_final' => 'E',
                'archive_courant' => 2,
                'archive_intermediaire' => 3,
                'observation' => 'Conservation de 5 ans après décision finale'
            ],
            [
                'NO_regle' => 'R010',
                'delais_legaux' => 30,
                'nature_dossier' => 'Recours judiciaires',
                'reference' => 'Code de Procédure Civile - Article 85',
                'plan_classement_code' => 6,
                'sort_final' => 'C',
                'archive_courant' => 5,
                'archive_intermediaire' => 25,
                'observation' => 'Conservation permanente pour recours judiciaires'
            ],
            [
                'NO_regle' => 'R011',
                'delais_legaux' => 50,
                'nature_dossier' => 'Dossiers individuels du personnel',
                'reference' => 'Statut de la Fonction Publique',
                'plan_classement_code' => 7,
                'sort_final' => 'T',
                'archive_courant' => 10,
                'archive_intermediaire' => 40,
                'observation' => 'Tri sélectif après 50 ans - Conservation échantillon'
            ],
            [
                'NO_regle' => 'R012',
                'delais_legaux' => 30,
                'nature_dossier' => 'Budgets et comptabilité générale',
                'reference' => 'Loi Organique des Finances',
                'plan_classement_code' => 8,
                'sort_final' => 'C',
                'archive_courant' => 5,
                'archive_intermediaire' => 25,
                'observation' => 'Conservation permanente - Mémoire budgétaire'
            ],
            [
                'NO_regle' => 'R013',
                'delais_legaux' => 50,
                'nature_dossier' => 'Accords internationaux bilatéraux',
                'reference' => 'Droit International - Convention de Vienne',
                'plan_classement_code' => 9,
                'sort_final' => 'C',
                'archive_courant' => 10,
                'archive_intermediaire' => 40,
                'observation' => 'Conservation permanente - Valeur historique'
            ],
            [
                'NO_regle' => 'R014',
                'delais_legaux' => 10,
                'nature_dossier' => 'Programmes de formation continue',
                'reference' => 'Règlement interne ADII',
                'plan_classement_code' => 10,
                'sort_final' => 'E',
                'archive_courant' => 3,
                'archive_intermediaire' => 7,
                'observation' => 'Conservation de 10 ans puis élimination'
            ],
            [
                'NO_regle' => 'R015',
                'delais_legaux' => 15,
                'nature_dossier' => 'Documentation technique BADR/PORTNET',
                'reference' => 'Politique de Sécurité Informatique',
                'plan_classement_code' => 11,
                'sort_final' => 'T',
                'archive_courant' => 5,
                'archive_intermediaire' => 10,
                'observation' => 'Tri sélectif - Conservation versions importantes'
            ],
            [
                'NO_regle' => 'R016',
                'delais_legaux' => 100,
                'nature_dossier' => 'Textes législatifs et réglementaires',
                'reference' => 'Archives Nationales du Maroc',
                'plan_classement_code' => 12,
                'sort_final' => 'C',
                'archive_courant' => 10,
                'archive_intermediaire' => 90,
                'observation' => 'Conservation permanente - Valeur juridique et historique'
            ],
            [
                'NO_regle' => 'R017',
                'delais_legaux' => 30,
                'nature_dossier' => 'Statistiques commerce extérieur',
                'reference' => 'Office des Changes - Instruction 01/2020',
                'plan_classement_code' => 13,
                'sort_final' => 'C',
                'archive_courant' => 5,
                'archive_intermediaire' => 25,
                'observation' => 'Conservation permanente - Données stratégiques'
            ],
            [
                'NO_regle' => 'R018',
                'delais_legaux' => 20,
                'nature_dossier' => 'Rapports de sécurité et surveillance',
                'reference' => 'Code de Sécurité Nationale',
                'plan_classement_code' => 14,
                'sort_final' => 'T',
                'archive_courant' => 5,
                'archive_intermediaire' => 15,
                'observation' => 'Tri sélectif selon classification sécuritaire'
            ],
            [
                'NO_regle' => 'R019',
                'delais_legaux' => 10,
                'nature_dossier' => 'Publications et communiqués de presse',
                'reference' => 'Politique de Communication ADII',
                'plan_classement_code' => 15,
                'sort_final' => 'T',
                'archive_courant' => 2,
                'archive_intermediaire' => 8,
                'observation' => 'Tri sélectif - Conservation publications importantes'
            ],
            [
                'NO_regle' => 'R020',
                'delais_legaux' => 15,
                'nature_dossier' => 'Rapports d\'audit interne',
                'reference' => 'Normes Internationales d\'Audit Interne',
                'plan_classement_code' => 16,
                'sort_final' => 'C',
                'archive_courant' => 5,
                'archive_intermediaire' => 10,
                'observation' => 'Conservation permanente - Traçabilité des contrôles'
            ]
        ];

        foreach ($regles as $regle) {
            // Find the plan classement by code
            $planClassement = PlanClassement::where('code_classement', $regle['plan_classement_code'])->first();
            
            if ($planClassement) {
                CalendrierConservation::updateOrCreate(
                    ['NO_regle' => $regle['NO_regle']],
                    [
                        'delais_legaux' => $regle['delais_legaux'],
                        'nature_dossier' => $regle['nature_dossier'],
                        'reference' => $regle['reference'],
                        'plan_classement_id' => $planClassement->id,
                        'sort_final' => $regle['sort_final'],
                        'archive_courant' => $regle['archive_courant'],
                        'archive_intermediaire' => $regle['archive_intermediaire'],
                        'observation' => $regle['observation']
                    ]
                );
            }
        }
    }
}