<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PlanClassement;

class PlanClassementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'code_classement' => 1,
                'objet_classement' => 'Dossiers de dédouanement des marchandises - Déclarations en détail Import/Export'
            ],
            [
                'code_classement' => 2,
                'objet_classement' => 'Dossiers de transit international - Carnet TIR et documents de transit douanier'
            ],
            [
                'code_classement' => 3,
                'objet_classement' => 'Dossiers de contentieux douanier - Infractions, amendes et poursuites judiciaires'
            ],
            [
                'code_classement' => 4,
                'objet_classement' => 'Dossiers de régimes économiques en douane - Admission temporaire, entrepôt, zone franche'
            ],
            [
                'code_classement' => 5,
                'objet_classement' => 'Dossiers de contrôle a posteriori - Vérifications comptables et contrôles documentaires'
            ],
            [
                'code_classement' => 6,
                'objet_classement' => 'Dossiers de recours et réclamations - Contestations de décisions douanières'
            ],
            [
                'code_classement' => 7,
                'objet_classement' => 'Dossiers de personnel et ressources humaines - Gestion administrative du personnel'
            ],
            [
                'code_classement' => 8,
                'objet_classement' => 'Dossiers financiers et comptables - Budgets, factures, paiements et recouvrement'
            ],
            [
                'code_classement' => 9,
                'objet_classement' => 'Dossiers de coopération internationale - Accords douaniers et échanges d\'informations'
            ],
            [
                'code_classement' => 10,
                'objet_classement' => 'Dossiers de formation et développement - Programmes de formation du personnel douanier'
            ],
            [
                'code_classement' => 11,
                'objet_classement' => 'Dossiers techniques et informatiques - Maintenance des systèmes douaniers, BADR, PORTNET'
            ],
            [
                'code_classement' => 12,
                'objet_classement' => 'Dossiers juridiques et réglementaires - Textes législatifs, circulaires et notes de service'
            ],
            [
                'code_classement' => 13,
                'objet_classement' => 'Dossiers de statistiques et études - Données du commerce extérieur et analyses économiques'
            ],
            [
                'code_classement' => 14,
                'objet_classement' => 'Dossiers de sécurité et protection - Contrôles de sécurité, lutte contre la contrebande'
            ],
            [
                'code_classement' => 15,
                'objet_classement' => 'Dossiers de communication et relations publiques - Publications, communiqués et événements'
            ],
            [
                'code_classement' => 16,
                'objet_classement' => 'Dossiers d\'audit interne - Missions d\'audit, recommandations et suivi des actions correctives'
            ],
            [
                'code_classement' => 17,
                'objet_classement' => 'Dossiers de partenariat avec le secteur privé - Opérateurs économiques agréés (OEA)'
            ],
            [
                'code_classement' => 18,
                'objet_classement' => 'Dossiers environnementaux - Contrôle des substances dangereuses et protection de l\'environnement'
            ],
            [
                'code_classement' => 19,
                'objet_classement' => 'Dossiers de modernisation et réforme - Projets de modernisation des services douaniers'
            ],
            [
                'code_classement' => 20,
                'objet_classement' => 'Dossiers d\'archives et documentation - Gestion documentaire et conservation des archives'
            ]
        ];

        foreach ($plans as $plan) {
            PlanClassement::updateOrCreate(
                ['code_classement' => $plan['code_classement']],
                $plan
            );
        }
    }
}