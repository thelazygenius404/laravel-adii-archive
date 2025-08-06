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
     * Utilise les données réelles du fichier Excel CCBilal.xlsx
     */
    public function run(): void
    {
        $regles = [
            // Catégorie 100 - Organisation et administration
            [
                'plan_classement_code' => '100.10.1',
                'pieces_constituant' => 'Dahirs, lois, décrets, arrêtés',
                'principal_secondaire' => 'S',
                'delai_legal' => '_',
                'reference_juridique' => '_',
                'archives_courantes' => 'Validité du texte',
                'archives_intermediaires' => '1 an',
                'sort_final' => 'D',
                'observation' => 'Destruction après validité du texte + 1 an'
            ],
            [
                'plan_classement_code' => '100.10.2',
                'pieces_constituant' => 'Circulaires, lettres circulaires, notes circulaires',
                'principal_secondaire' => 'S',
                'delai_legal' => '_',
                'reference_juridique' => '_',
                'archives_courantes' => 'Validité du texte',
                'archives_intermediaires' => '1 an',
                'sort_final' => 'D',
                'observation' => 'Destruction après validité du texte + 1 an'
            ],
            [
                'plan_classement_code' => '100.10.3',
                'pieces_constituant' => 'Instructions, notes de service',
                'principal_secondaire' => 'S',
                'delai_legal' => '_',
                'reference_juridique' => '_',
                'archives_courantes' => 'Validité du texte',
                'archives_intermediaires' => '1 an',
                'sort_final' => 'D',
                'observation' => 'Destruction après validité du texte + 1 an'
            ],
            [
                'plan_classement_code' => '100.10.4',
                'pieces_constituant' => 'Organigrammes',
                'principal_secondaire' => 'S',
                'delai_legal' => '_',
                'reference_juridique' => '_',
                'archives_courantes' => 'Validité de l\'organigramme',
                'archives_intermediaires' => '1 an',
                'sort_final' => 'D',
                'observation' => 'Destruction après validité de l\'organigramme + 1 an'
            ],
            [
                'plan_classement_code' => '100.20.1',
                'pieces_constituant' => 'Chrono courrier arrivée, Registre courrier arrivée, accusé de réception du courrier',
                'principal_secondaire' => 'S',
                'delai_legal' => '_',
                'reference_juridique' => '_',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '2 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans puis destruction'
            ],
            [
                'plan_classement_code' => '100.20.2',
                'pieces_constituant' => 'Chrono courrier départ, Registre courrier départ',
                'principal_secondaire' => 'S',
                'delai_legal' => '_',
                'reference_juridique' => '_',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '2 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans puis destruction'
            ],
            [
                'plan_classement_code' => '100.30',
                'pieces_constituant' => 'PAS',
                'principal_secondaire' => 'S',
                'delai_legal' => '_',
                'reference_juridique' => '_',
                'archives_courantes' => 'Validité du PAS',
                'archives_intermediaires' => '5 ans',
                'sort_final' => 'D',
                'observation' => 'Destruction après validité du PAS + 5 ans'
            ],
            [
                'plan_classement_code' => '100.40',
                'pieces_constituant' => 'Rapports',
                'principal_secondaire' => 'S',
                'delai_legal' => '_',
                'reference_juridique' => '_',
                'archives_courantes' => 'Validité du texte',
                'archives_intermediaires' => '1 an',
                'sort_final' => 'D',
                'observation' => 'Destruction après validité du texte + 1 an'
            ],
            [
                'plan_classement_code' => '100.50',
                'pieces_constituant' => 'PV et comptes rendus',
                'principal_secondaire' => 'S',
                'delai_legal' => '_',
                'reference_juridique' => '_',
                'archives_courantes' => 'Validité du texte',
                'archives_intermediaires' => '1 an',
                'sort_final' => 'D',
                'observation' => 'Destruction après validité du texte + 1 an'
            ],
            [
                'plan_classement_code' => '100.60',
                'pieces_constituant' => 'Statistiques et tableaux de bord',
                'principal_secondaire' => 'S',
                'delai_legal' => '_',
                'reference_juridique' => '_',
                'archives_courantes' => 'Validité du texte',
                'archives_intermediaires' => '1 an',
                'sort_final' => 'D',
                'observation' => 'Destruction après validité du texte + 1 an'
            ],
            [
                'plan_classement_code' => '100.70',
                'pieces_constituant' => 'Plans et programmes',
                'principal_secondaire' => 'S',
                'delai_legal' => '_',
                'reference_juridique' => '_',
                'archives_courantes' => 'Validité du plan',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Destruction après validité du plan + 3 ans'
            ],

            // Catégorie 510 - Régimes économiques douaniers
            [
                'plan_classement_code' => '510.1',
                'pieces_constituant' => 'Dossiers admission temporaire, soumissions, déclarations',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 161',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après apurement'
            ],
            [
                'plan_classement_code' => '510.2.1',
                'pieces_constituant' => 'Demandes autorisation entrepôt, plans, garanties',
                'principal_secondaire' => 'P',
                'delai_legal' => '15 ans',
                'reference_juridique' => 'Code des douanes art. 145',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '10 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 15 ans après fermeture entrepôt'
            ],
            [
                'plan_classement_code' => '510.2.2',
                'pieces_constituant' => 'Registres, comptabilité matières, contrôles',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 145',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après clôture'
            ],
            [
                'plan_classement_code' => '510.3',
                'pieces_constituant' => 'Dossiers perfectionnement passif, autorisations',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 170',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après apurement'
            ],
            [
                'plan_classement_code' => '510.4',
                'pieces_constituant' => 'Dossiers zone franche, autorisations, contrôles',
                'principal_secondaire' => 'P',
                'delai_legal' => '15 ans',
                'reference_juridique' => 'Loi zone franche',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '10 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 15 ans après cessation activité'
            ],
            [
                'plan_classement_code' => '510.5',
                'pieces_constituant' => 'Demandes drawback, justificatifs, liquidations',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 174',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après remboursement'
            ],
            [
                'plan_classement_code' => '510.6',
                'pieces_constituant' => 'Dossiers exportation temporaire, soumissions',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 180',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après réimportation'
            ],
            [
                'plan_classement_code' => '510.7',
                'pieces_constituant' => 'Autres régimes économiques, dossiers divers',
                'principal_secondaire' => 'S',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans selon régime'
            ],

            // Catégorie 520 - Transit et transport
            [
                'plan_classement_code' => '520.1',
                'pieces_constituant' => 'Déclarations transit, acquit-à-caution, bulletins',
                'principal_secondaire' => 'P',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Code des douanes art. 126',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans après apurement'
            ],
            [
                'plan_classement_code' => '520.2',
                'pieces_constituant' => 'Carnets TIR, manifestes internationaux',
                'principal_secondaire' => 'P',
                'delai_legal' => '3 ans',
                'reference_juridique' => 'Convention TIR',
                'archives_courantes' => '1 an',
                'archives_intermediaires' => '2 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 3 ans selon convention'
            ],
            [
                'plan_classement_code' => '520.3',
                'pieces_constituant' => 'Bons de transport sous douane, escortes',
                'principal_secondaire' => 'P',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Code des douanes art. 130',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans après transport'
            ],
            [
                'plan_classement_code' => '520.4',
                'pieces_constituant' => 'Manifestes de chargement, déchargement',
                'principal_secondaire' => 'P',
                'delai_legal' => '3 ans',
                'reference_juridique' => 'Code des douanes art. 65',
                'archives_courantes' => '1 an',
                'archives_intermediaires' => '2 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 3 ans après opération'
            ],
            [
                'plan_classement_code' => '520.5',
                'pieces_constituant' => 'Carnet de passages en douane CPD',
                'principal_secondaire' => 'P',
                'delai_legal' => '2 ans',
                'reference_juridique' => 'Accord international CPD',
                'archives_courantes' => '1 an',
                'archives_intermediaires' => '1 an',
                'sort_final' => 'D',
                'observation' => 'Conservation 2 ans après utilisation'
            ],
            [
                'plan_classement_code' => '520.6',
                'pieces_constituant' => 'Acquit-à-caution, soumissions générales',
                'principal_secondaire' => 'P',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Code des douanes art. 90',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans après apurement'
            ],
            [
                'plan_classement_code' => '520.7',
                'pieces_constituant' => 'Soumissions générales cautionnées',
                'principal_secondaire' => 'P',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Code des douanes art. 92',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans après apurement'
            ],
            [
                'plan_classement_code' => '520.8',
                'pieces_constituant' => 'Bulletins de renseignements douaniers',
                'principal_secondaire' => 'S',
                'delai_legal' => '3 ans',
                'reference_juridique' => 'Instructions douanières',
                'archives_courantes' => '1 an',
                'archives_intermediaires' => '2 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 3 ans'
            ],
            [
                'plan_classement_code' => '520.9',
                'pieces_constituant' => 'Contrôles camions, conteneurs, navires',
                'principal_secondaire' => 'P',
                'delai_legal' => '3 ans',
                'reference_juridique' => 'Procédures de contrôle',
                'archives_courantes' => '1 an',
                'archives_intermediaires' => '2 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 3 ans après contrôle'
            ],
            [
                'plan_classement_code' => '520.10',
                'pieces_constituant' => 'Autorisations magasins, aires de dédouanement',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 58',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après cessation'
            ],

            // Catégorie 530 - Contentieux douanier
            [
                'plan_classement_code' => '530.1',
                'pieces_constituant' => 'PV de constatation, saisies, séquestres',
                'principal_secondaire' => 'P',
                'delai_legal' => '30 ans',
                'reference_juridique' => 'Code de procédure pénale',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '25 ans',
                'sort_final' => 'C',
                'observation' => 'Conservation définitive - valeur juridique'
            ],
            [
                'plan_classement_code' => '530.2',
                'pieces_constituant' => 'Transactions, compromis, amendes',
                'principal_secondaire' => 'P',
                'delai_legal' => '20 ans',
                'reference_juridique' => 'Code des douanes art. 285',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '15 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 20 ans après paiement'
            ],
            [
                'plan_classement_code' => '530.3',
                'pieces_constituant' => 'Dossiers judiciaires, jugements, arrêts',
                'principal_secondaire' => 'P',
                'delai_legal' => '30 ans',
                'reference_juridique' => 'Code de procédure pénale',
                'archives_courantes' => '10 ans',
                'archives_intermediaires' => '20 ans',
                'sort_final' => 'C',
                'observation' => 'Conservation définitive - archives judiciaires'
            ],

            // Catégorie 540 - Recours et réclamations
            [
                'plan_classement_code' => '540.1',
                'pieces_constituant' => 'Réclamations contentieuses, décisions',
                'principal_secondaire' => 'P',
                'delai_legal' => '15 ans',
                'reference_juridique' => 'Code des douanes art. 242',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '10 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 15 ans après décision finale'
            ],
            [
                'plan_classement_code' => '540.2',
                'pieces_constituant' => 'Recours gracieux, réponses administratives',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code de procédure administrative',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après décision'
            ],
            [
                'plan_classement_code' => '540.3',
                'pieces_constituant' => 'Recours hiérarchiques, décisions supérieures',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code de procédure administrative',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après décision'
            ],
            [
                'plan_classement_code' => '540.4',
                'pieces_constituant' => 'Jugements, arrêts, cassations',
                'principal_secondaire' => 'P',
                'delai_legal' => '30 ans',
                'reference_juridique' => 'Code de procédure civile',
                'archives_courantes' => '10 ans',
                'archives_intermediaires' => '20 ans',
                'sort_final' => 'C',
                'observation' => 'Conservation définitive - décisions de justice'
            ],

            // Catégorie 550 - Contrôle et vérification
            [
                'plan_classement_code' => '550.1',
                'pieces_constituant' => 'Rapports de vérification, procès-verbaux de contrôle',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 95',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après vérification'
            ],
            [
                'plan_classement_code' => '550.2',
                'pieces_constituant' => 'Dossiers de contrôle a posteriori',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 95',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après contrôle'
            ],
            [
                'plan_classement_code' => '550.3',
                'pieces_constituant' => 'Audits opérateurs économiques, rapports OEA',
                'principal_secondaire' => 'P',
                'delai_legal' => '15 ans',
                'reference_juridique' => 'Réglementation OEA',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '10 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 15 ans après audit'
            ],
            [
                'plan_classement_code' => '550.4',
                'pieces_constituant' => 'Procès-verbaux contrôle physique marchandises',
                'principal_secondaire' => 'P',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Procédures de contrôle',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans après contrôle'
            ],
            [
                'plan_classement_code' => '550.5',
                'pieces_constituant' => 'Résultats scanner, détection',
                'principal_secondaire' => 'S',
                'delai_legal' => '3 ans',
                'reference_juridique' => 'Procédures techniques',
                'archives_courantes' => '1 an',
                'archives_intermediaires' => '2 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 3 ans puis destruction'
            ],
            [
                'plan_classement_code' => '550.6',
                'pieces_constituant' => 'Rapports laboratoire, analyses',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Normes laboratoire',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après analyse'
            ],
            [
                'plan_classement_code' => '550.7',
                'pieces_constituant' => 'Expertises, contre-expertises',
                'principal_secondaire' => 'P',
                'delai_legal' => '15 ans',
                'reference_juridique' => 'Code de procédure civile',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '10 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 15 ans après expertise'
            ],
            [
                'plan_classement_code' => '550.8',
                'pieces_constituant' => 'Prélèvements échantillons, protocoles',
                'principal_secondaire' => 'P',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Procédures de prélèvement',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans après prélèvement'
            ],
            [
                'plan_classement_code' => '550.9',
                'pieces_constituant' => 'Rapports de contrôle mission',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Instructions de contrôle',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après mission'
            ],
            [
                'plan_classement_code' => '550.10',
                'pieces_constituant' => 'Missions de vérification ciblées',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 95',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après mission'
            ],
            [
                'plan_classement_code' => '550.11',
                'pieces_constituant' => 'Contrôles de prix, valeurs déclarées',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 20',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après contrôle'
            ],

            // Catégorie 560 - Facilitations commerciales
            [
                'plan_classement_code' => '560.1.1',
                'pieces_constituant' => 'Demandes statut OEA, dossiers candidature',
                'principal_secondaire' => 'P',
                'delai_legal' => '20 ans',
                'reference_juridique' => 'Réglementation OEA',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '15 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 20 ans après décision'
            ],
            [
                'plan_classement_code' => '560.1.2',
                'pieces_constituant' => 'Suivi OEA, rapports annuels, renouvellements',
                'principal_secondaire' => 'P',
                'delai_legal' => '15 ans',
                'reference_juridique' => 'Réglementation OEA',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '10 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 15 ans après fin statut'
            ],
            [
                'plan_classement_code' => '560.1.3',
                'pieces_constituant' => 'Contrôles OEA, sanctions, suspensions',
                'principal_secondaire' => 'P',
                'delai_legal' => '20 ans',
                'reference_juridique' => 'Réglementation OEA',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '15 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 20 ans après sanction'
            ],
            [
                'plan_classement_code' => '560.2',
                'pieces_constituant' => 'Circuit vert, facilitations accordées',
                'principal_secondaire' => 'P',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Procédures de facilitation',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans après utilisation'
            ],
            [
                'plan_classement_code' => '560.3',
                'pieces_constituant' => 'Procédures simplifiées, autorisations spéciales',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après autorisation'
            ],
            [
                'plan_classement_code' => '560.4',
                'pieces_constituant' => 'Dédouanement à domicile, autorisations',
                'principal_secondaire' => 'P',
                'delai_legal' => '15 ans',
                'reference_juridique' => 'Code des douanes art. 85',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '10 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 15 ans après cessation'
            ],

            // Catégorie 610 - Dédouanement des marchandises
            [
                'plan_classement_code' => '610.1.1',
                'pieces_constituant' => 'Déclarations import marchandises générales',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 78',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après dédouanement'
            ],
            [
                'plan_classement_code' => '610.1.2',
                'pieces_constituant' => 'Déclarations import première nécessité',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 78',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après dédouanement'
            ],
            [
                'plan_classement_code' => '610.1.3',
                'pieces_constituant' => 'Déclarations import produits pétroliers',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 78',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après dédouanement'
            ],
            [
                'plan_classement_code' => '610.1.4',
                'pieces_constituant' => 'Déclarations import véhicules automobiles',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 78',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après dédouanement'
            ],
            [
                'plan_classement_code' => '610.1.5',
                'pieces_constituant' => 'Déclarations import matériel informatique',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 78',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après dédouanement'
            ],
            [
                'plan_classement_code' => '610.2.1',
                'pieces_constituant' => 'Déclarations export marchandises générales',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 78',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après dédouanement'
            ],
            [
                'plan_classement_code' => '610.2.2',
                'pieces_constituant' => 'Déclarations export produits agricoles',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 78',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après dédouanement'
            ],
            [
                'plan_classement_code' => '610.2.3',
                'pieces_constituant' => 'Déclarations export produits manufacturés',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 78',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après dédouanement'
            ],
            [
                'plan_classement_code' => '610.2.4',
                'pieces_constituant' => 'Déclarations export matières premières',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 78',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après dédouanement'
            ],
            [
                'plan_classement_code' => '610.2.5',
                'pieces_constituant' => 'Déclarations export produits artisanaux',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 78',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après dédouanement'
            ],
            [
                'plan_classement_code' => '610.3.1',
                'pieces_constituant' => 'Liquidations douanières import',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 78',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après liquidation'
            ],
            [
                'plan_classement_code' => '610.3.2',
                'pieces_constituant' => 'Liquidations douanières export',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Code des douanes art. 78',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après liquidation'
            ],
            [
                'plan_classement_code' => '610.4.1',
                'pieces_constituant' => 'Mainlevée marchandises import',
                'principal_secondaire' => 'P',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Code des douanes art. 80',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans après mainlevée'
            ],
            [
                'plan_classement_code' => '610.4.2',
                'pieces_constituant' => 'Mainlevée marchandises export',
                'principal_secondaire' => 'P',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Code des douanes art. 80',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans après mainlevée'
            ],
            [
                'plan_classement_code' => '610.5.1',
                'pieces_constituant' => 'Certificats origine - délivrance',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Accords commerciaux',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après délivrance'
            ],
            [
                'plan_classement_code' => '610.5.2',
                'pieces_constituant' => 'Certificats origine - contrôle',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Accords commerciaux',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après contrôle'
            ],
            [
                'plan_classement_code' => '610.6.1',
                'pieces_constituant' => 'EUR1 et attestations préférentielles',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Accords UE-Maroc',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans selon accord'
            ],
            [
                'plan_classement_code' => '610.6.2',
                'pieces_constituant' => 'Certificats circulation marchandises',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Accords régionaux',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans selon accord'
            ],
            [
                'plan_classement_code' => '610.7.1',
                'pieces_constituant' => 'Admissions franchise diplomatique',
                'principal_secondaire' => 'P',
                'delai_legal' => '15 ans',
                'reference_juridique' => 'Convention Vienne',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '10 ans',
                'sort_final' => 'T',
                'observation' => 'Tri sélectif - conservation échantillon'
            ],
            [
                'plan_classement_code' => '610.7.2',
                'pieces_constituant' => 'Admissions franchise matériel professionnel',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Convention ATA',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après retour'
            ],
            [
                'plan_classement_code' => '610.7.3',
                'pieces_constituant' => 'Admissions franchise usage personnel',
                'principal_secondaire' => 'S',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Code des douanes',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans puis destruction'
            ],
            [
                'plan_classement_code' => '610.8.1',
                'pieces_constituant' => 'Prohibitions restrictions - contrôle',
                'principal_secondaire' => 'P',
                'delai_legal' => '15 ans',
                'reference_juridique' => 'Lois spéciales',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '10 ans',
                'sort_final' => 'C',
                'observation' => 'Conservation définitive - sécurité nationale'
            ],
            [
                'plan_classement_code' => '610.8.2',
                'pieces_constituant' => 'Licences import/export',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Réglementation commerce extérieur',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après expiration'
            ],
            [
                'plan_classement_code' => '610.8.3',
                'pieces_constituant' => 'Autorisations spéciales marchandises',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Réglementations sectorielles',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après autorisation'
            ],
            [
                'plan_classement_code' => '610.9.1',
                'pieces_constituant' => 'Classification tarifaire - décisions',
                'principal_secondaire' => 'P',
                'delai_legal' => '15 ans',
                'reference_juridique' => 'Tarif douanier',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '10 ans',
                'sort_final' => 'T',
                'observation' => 'Tri sélectif - conservation décisions importantes'
            ],
            [
                'plan_classement_code' => '610.9.2',
                'pieces_constituant' => 'Classification tarifaire - contestations',
                'principal_secondaire' => 'P',
                'delai_legal' => '15 ans',
                'reference_juridique' => 'Code des douanes art. 242',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '10 ans',
                'sort_final' => 'C',
                'observation' => 'Conservation définitive - jurisprudence'
            ],
            [
                'plan_classement_code' => '610.10.1',
                'pieces_constituant' => 'Valeur douane - détermination',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Accord OMC',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après détermination'
            ],
            [
                'plan_classement_code' => '610.10.2',
                'pieces_constituant' => 'Valeur douane - contestations',
                'principal_secondaire' => 'P',
                'delai_legal' => '15 ans',
                'reference_juridique' => 'Code des douanes art. 242',
                'archives_courantes' => '5 ans',
                'archives_intermediaires' => '10 ans',
                'sort_final' => 'C',
                'observation' => 'Conservation définitive - jurisprudence'
            ],
            [
                'plan_classement_code' => '610.11.1',
                'pieces_constituant' => 'Origine marchandises - détermination',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Accords commerciaux',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après détermination'
            ],
            [
                'plan_classement_code' => '610.11.2',
                'pieces_constituant' => 'Origine marchandises - preuves',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Accords commerciaux',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans après vérification'
            ],
            [
                'plan_classement_code' => '610.12.1',
                'pieces_constituant' => 'Bagages accompagnés voyageurs',
                'principal_secondaire' => 'S',
                'delai_legal' => '3 ans',
                'reference_juridique' => 'Code des douanes art. 15',
                'archives_courantes' => '1 an',
                'archives_intermediaires' => '2 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 3 ans puis destruction'
            ],
            [
                'plan_classement_code' => '610.12.2',
                'pieces_constituant' => 'Bagages non accompagnés',
                'principal_secondaire' => 'S',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Code des douanes art. 15',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans puis destruction'
            ],
            [
                'plan_classement_code' => '610.13.1',
                'pieces_constituant' => 'Envois postaux import',
                'principal_secondaire' => 'P',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Convention postale universelle',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans selon convention'
            ],
            [
                'plan_classement_code' => '610.13.2',
                'pieces_constituant' => 'Envois postaux export',
                'principal_secondaire' => 'P',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Convention postale universelle',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans selon convention'
            ],
            [
                'plan_classement_code' => '610.14.1',
                'pieces_constituant' => 'Messagerie express - dédouanement',
                'principal_secondaire' => 'P',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Procédures express',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans après dédouanement'
            ],
            [
                'plan_classement_code' => '610.14.2',
                'pieces_constituant' => 'Messagerie express - contrôle',
                'principal_secondaire' => 'P',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Procédures contrôle',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans après contrôle'
            ],
            [
                'plan_classement_code' => '610.15.1',
                'pieces_constituant' => 'Commerce électronique plateformes',
                'principal_secondaire' => 'P',
                'delai_legal' => '10 ans',
                'reference_juridique' => 'Loi commerce électronique',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 10 ans - évolution réglementaire'
            ],
            [
                'plan_classement_code' => '610.15.2',
                'pieces_constituant' => 'Commerce électronique particuliers',
                'principal_secondaire' => 'S',
                'delai_legal' => '5 ans',
                'reference_juridique' => 'Procédures simplifiées',
                'archives_courantes' => '2 ans',
                'archives_intermediaires' => '3 ans',
                'sort_final' => 'D',
                'observation' => 'Conservation 5 ans puis destruction'
            ],
            [
                'plan_classement_code' => '610.16.1',
                'pieces_constituant' => 'Statistiques commerce extérieur',
                'principal_secondaire' => 'P',
                'delai_legal' => '50 ans',
                'reference_juridique' => 'Office des Changes',
                'archives_courantes' => '10 ans',
                'archives_intermediaires' => '40 ans',
                'sort_final' => 'C',
                'observation' => 'Conservation définitive - valeur historique et économique'
            ]
        ];

        foreach ($regles as $regle) {
            // Vérifier que le plan de classement existe
            $planExists = PlanClassement::where('code_classement', $regle['plan_classement_code'])->exists();
            
            if ($planExists) {
                CalendrierConservation::updateOrCreate(
                    ['plan_classement_code' => $regle['plan_classement_code']],
                    $regle
                );
            }
        }
    }
}