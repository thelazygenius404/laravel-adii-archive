<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PlanClassement;

class PlanClassementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Utilise les données du fichier Excel CCBilal.xlsx
     */
    public function run(): void
    {
        $plans = [
            // Catégorie 100 - Organisation et administration
            ['code_classement' => '100.10.1', 'objet_classement' => 'Textes de lois'],
            ['code_classement' => '100.10.2', 'objet_classement' => 'Circulaires, lettres circulaires, notes circulaires'],
            ['code_classement' => '100.10.3', 'objet_classement' => 'Instructions, notes de service'],
            ['code_classement' => '100.10.4', 'objet_classement' => 'Organigrammes'],
            ['code_classement' => '100.20.1', 'objet_classement' => 'Courrier arrivée'],
            ['code_classement' => '100.20.2', 'objet_classement' => 'Courrier départ'],
            ['code_classement' => '100.30', 'objet_classement' => 'Plans d\'Action Stratégique'],
            ['code_classement' => '100.40', 'objet_classement' => 'Rapports d\'activité'],
            ['code_classement' => '100.50', 'objet_classement' => 'PV et comptes rendus de réunions'],
            ['code_classement' => '100.60', 'objet_classement' => 'Tableaux de bord, états et situations'],
            ['code_classement' => '100.70', 'objet_classement' => 'Planification et programmation'],

            // Catégorie 510 - Régimes économiques douaniers
            ['code_classement' => '510.1', 'objet_classement' => 'Admission temporaire pour perfectionnement actif'],
            ['code_classement' => '510.2.1', 'objet_classement' => 'Entrepôt de stockage - Demandes d\'autorisation'],
            ['code_classement' => '510.2.2', 'objet_classement' => 'Entrepôt de stockage - Suivi et contrôle'],
            ['code_classement' => '510.3', 'objet_classement' => 'Admission temporaire pour perfectionnement passif'],
            ['code_classement' => '510.4', 'objet_classement' => 'Zone franche'],
            ['code_classement' => '510.5', 'objet_classement' => 'Drawback'],
            ['code_classement' => '510.6', 'objet_classement' => 'Exportation temporaire'],
            ['code_classement' => '510.7', 'objet_classement' => 'Régimes économiques - Divers'],

            // Catégorie 520 - Transit et transport
            ['code_classement' => '520.1', 'objet_classement' => 'Transit ordinaire'],
            ['code_classement' => '520.2', 'objet_classement' => 'Transit international (TIR)'],
            ['code_classement' => '520.3', 'objet_classement' => 'Transport sous douane'],
            ['code_classement' => '520.4', 'objet_classement' => 'Manifestes de transit'],
            ['code_classement' => '520.5', 'objet_classement' => 'Carnet de passages en douane'],
            ['code_classement' => '520.6', 'objet_classement' => 'Acquit-à-caution'],
            ['code_classement' => '520.7', 'objet_classement' => 'Soumissions générales'],
            ['code_classement' => '520.8', 'objet_classement' => 'Bulletins de renseignements'],
            ['code_classement' => '520.9', 'objet_classement' => 'Contrôle des moyens de transport'],
            ['code_classement' => '520.10', 'objet_classement' => 'Magasins et aires de dédouanement'],

            // Catégorie 530 - Contentieux douanier
            ['code_classement' => '530.1', 'objet_classement' => 'Procès-verbaux de constatation'],
            ['code_classement' => '530.2', 'objet_classement' => 'Transactions et compromis'],
            ['code_classement' => '530.3', 'objet_classement' => 'Poursuites judiciaires'],

            // Catégorie 540 - Recours et réclamations
            ['code_classement' => '540.1', 'objet_classement' => 'Réclamations contentieuses'],
            ['code_classement' => '540.2', 'objet_classement' => 'Recours gracieux'],
            ['code_classement' => '540.3', 'objet_classement' => 'Recours hiérarchiques'],
            ['code_classement' => '540.4', 'objet_classement' => 'Décisions de justice'],

            // Catégorie 550 - Contrôle et vérification
            ['code_classement' => '550.1', 'objet_classement' => 'Vérification des déclarations'],
            ['code_classement' => '550.2', 'objet_classement' => 'Contrôle a posteriori'],
            ['code_classement' => '550.3', 'objet_classement' => 'Audit des opérateurs'],
            ['code_classement' => '550.4', 'objet_classement' => 'Contrôle physique des marchandises'],
            ['code_classement' => '550.5', 'objet_classement' => 'Scanner et moyens de détection'],
            ['code_classement' => '550.6', 'objet_classement' => 'Laboratoires d\'analyses'],
            ['code_classement' => '550.7', 'objet_classement' => 'Expertise et contre-expertise'],
            ['code_classement' => '550.8', 'objet_classement' => 'Prélèvements d\'échantillons'],
            ['code_classement' => '550.9', 'objet_classement' => 'Rapports de contrôle'],
            ['code_classement' => '550.10', 'objet_classement' => 'Missions de vérification'],
            ['code_classement' => '550.11', 'objet_classement' => 'Contrôle des prix'],

            // Catégorie 560 - Facilitations commerciales
            ['code_classement' => '560.1.1', 'objet_classement' => 'Opérateurs économiques agréés (OEA) - Demandes'],
            ['code_classement' => '560.1.2', 'objet_classement' => 'Opérateurs économiques agréés (OEA) - Suivi'],
            ['code_classement' => '560.1.3', 'objet_classement' => 'Opérateurs économiques agréés (OEA) - Contrôle'],
            ['code_classement' => '560.2', 'objet_classement' => 'Circuit vert - Facilitations'],
            ['code_classement' => '560.3', 'objet_classement' => 'Procédures simplifiées'],
            ['code_classement' => '560.4', 'objet_classement' => 'Dédouanement à domicile'],

            // Catégorie 610 - Dédouanement des marchandises (37 sous-catégories principales)
            ['code_classement' => '610.1.1', 'objet_classement' => 'Déclarations import - Marchandises générales'],
            ['code_classement' => '610.1.2', 'objet_classement' => 'Déclarations import - Marchandises de première nécessité'],
            ['code_classement' => '610.1.3', 'objet_classement' => 'Déclarations import - Produits pétroliers'],
            ['code_classement' => '610.1.4', 'objet_classement' => 'Déclarations import - Véhicules automobiles'],
            ['code_classement' => '610.1.5', 'objet_classement' => 'Déclarations import - Matériel informatique'],
            ['code_classement' => '610.2.1', 'objet_classement' => 'Déclarations export - Marchandises générales'],
            ['code_classement' => '610.2.2', 'objet_classement' => 'Déclarations export - Produits agricoles'],
            ['code_classement' => '610.2.3', 'objet_classement' => 'Déclarations export - Produits manufacturés'],
            ['code_classement' => '610.2.4', 'objet_classement' => 'Déclarations export - Matières premières'],
            ['code_classement' => '610.2.5', 'objet_classement' => 'Déclarations export - Produits artisanaux'],
            ['code_classement' => '610.3.1', 'objet_classement' => 'Liquidations douanières - Import'],
            ['code_classement' => '610.3.2', 'objet_classement' => 'Liquidations douanières - Export'],
            ['code_classement' => '610.4.1', 'objet_classement' => 'Mainlevée des marchandises - Import'],
            ['code_classement' => '610.4.2', 'objet_classement' => 'Mainlevée des marchandises - Export'],
            ['code_classement' => '610.5.1', 'objet_classement' => 'Certificats d\'origine - Délivrance'],
            ['code_classement' => '610.5.2', 'objet_classement' => 'Certificats d\'origine - Contrôle'],
            ['code_classement' => '610.6.1', 'objet_classement' => 'EUR1 et attestations préférentielles'],
            ['code_classement' => '610.6.2', 'objet_classement' => 'Certificats de circulation'],
            ['code_classement' => '610.7.1', 'objet_classement' => 'Admissions en franchise - Diplomatique'],
            ['code_classement' => '610.7.2', 'objet_classement' => 'Admissions en franchise - Matériel professionnel'],
            ['code_classement' => '610.7.3', 'objet_classement' => 'Admissions en franchise - Usage personnel'],
            ['code_classement' => '610.8.1', 'objet_classement' => 'Prohibitions et restrictions - Contrôle'],
            ['code_classement' => '610.8.2', 'objet_classement' => 'Prohibitions et restrictions - Licences'],
            ['code_classement' => '610.8.3', 'objet_classement' => 'Prohibitions et restrictions - Autorisations'],
            ['code_classement' => '610.9.1', 'objet_classement' => 'Classification tarifaire - Décisions'],
            ['code_classement' => '610.9.2', 'objet_classement' => 'Classification tarifaire - Contestations'],
            ['code_classement' => '610.10.1', 'objet_classement' => 'Valeur en douane - Détermination'],
            ['code_classement' => '610.10.2', 'objet_classement' => 'Valeur en douane - Contestations'],
            ['code_classement' => '610.11.1', 'objet_classement' => 'Origine des marchandises - Détermination'],
            ['code_classement' => '610.11.2', 'objet_classement' => 'Origine des marchandises - Preuves'],
            ['code_classement' => '610.12.1', 'objet_classement' => 'Bagages accompagnés - Voyageurs'],
            ['code_classement' => '610.12.2', 'objet_classement' => 'Bagages non accompagnés'],
            ['code_classement' => '610.13.1', 'objet_classement' => 'Envois postaux - Import'],
            ['code_classement' => '610.13.2', 'objet_classement' => 'Envois postaux - Export'],
            ['code_classement' => '610.14.1', 'objet_classement' => 'Messagerie express - Dédouanement'],
            ['code_classement' => '610.14.2', 'objet_classement' => 'Messagerie express - Contrôle'],
            ['code_classement' => '610.15.1', 'objet_classement' => 'Commerce électronique - Plateformes'],
            ['code_classement' => '610.15.2', 'objet_classement' => 'Commerce électronique - Particuliers'],
            ['code_classement' => '610.16.1', 'objet_classement' => 'Statistiques du commerce extérieur'],
        ];

        foreach ($plans as $plan) {
            PlanClassement::updateOrCreate(
                ['code_classement' => $plan['code_classement']],
                $plan
            );
        }
    }
}