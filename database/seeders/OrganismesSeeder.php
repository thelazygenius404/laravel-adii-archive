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
        // ===== DIRECTION RÉGIONALE DE TANGER TÉTOUAN AL-HOCEIMA =====
        $dirRegionaleTTA = Organisme::create([
            'nom_org' => 'Direction Régionale de Tanger Tétouan Al-Hoceima'
        ]);

        // Entités productrices pour Tanger Tétouan Al-Hoceima
        EntiteProductrice::create([
            'nom_entite' => 'Direction Préfectorale de Tanger',
            'code_entite' => 'TTA-DPT',
            'id_organisme' => $dirRegionaleTTA->id,
            'entite_parent' =>  null
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Direction Provinciale de Tétouan',
            'code_entite' => 'TTA-DPTE',
            'id_organisme' => $dirRegionaleTTA->id,
            'entite_parent' => null
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Direction Provinciale d\'Al-Hoceima',
            'code_entite' => 'TTA-DPAH',
            'id_organisme' => $dirRegionaleTTA->id,
            'entite_parent' => null
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Direction des MEAD et ZAL de Tanger',
            'code_entite' => 'TTA-DMEAD',
            'id_organisme' => $dirRegionaleTTA->id,
            'entite_parent' => null
        ]);
        
        // ===== DIRECTION RÉGIONALE DE RABAT SALÉ KÉNITRA =====
        $dirRegionaleRSK = Organisme::create([
            'nom_org' => 'Direction Régionale de Rabat Salé Kénitra'
        ]);

        // Entités productrices pour Rabat Salé Kénitra
        EntiteProductrice::create([
            'nom_entite' => 'Direction Préfectorale de Rabat-Salé',
            'code_entite' => 'RSK-DPRS',
            'id_organisme' => $dirRegionaleRSK->id,
            'entite_parent' => null
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Direction Provinciale de Kénitra',
            'code_entite' => 'RSK-DPK',
            'id_organisme' => $dirRegionaleRSK->id,
            'entite_parent' => null
        ]);

        // ===== DIRECTION RÉGIONALE DE CASABLANCA SETTAT =====
        $dirRegionaleCS = Organisme::create([
            'nom_org' => 'Direction Régionale de Casablanca Settat'
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Direction Préfectorale de Casablanca',
            'code_entite' => 'CS-DPC',
            'id_organisme' => $dirRegionaleCS->id,
            'entite_parent' => null
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Direction des MEAD de Casablanca',
            'code_entite' => 'CS-DMEAD',
            'id_organisme' => $dirRegionaleCS->id,
            'entite_parent' => null
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Direction Provinciale de Nouasser',
            'code_entite' => 'CS-DPN',
            'id_organisme' => $dirRegionaleCS->id,
            'entite_parent' => null
        ]);
        
         // ===== DIRECTION CENTRALE =====
        $directionCentrale = Organisme::create([
            'nom_org' => 'Direction Centrale'
        ]);

        // Division de l'Audit et de l'Inspection (sous le Directeur Général)
        $divisionAudit = EntiteProductrice::create([
            'nom_entite' => 'Division de l\'Audit et de l\'Inspection',
            'code_entite' => 'DC-DAI',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => null
        ]);

        // Services sous Division de l'Audit et de l'Inspection
        EntiteProductrice::create([
            'nom_entite' => 'Sce des Audits Comptables et Financiers',
            'code_entite' => 'DC-DAI-SACF',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionAudit->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce des Audits Thématiques',
            'code_entite' => 'DC-DAI-SAT',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionAudit->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de l\'Audit des Structures',
            'code_entite' => 'DC-DAI-SAS',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionAudit->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de l\'Audit de la Filière de Surveillance',
            'code_entite' => 'DC-DAI-SAFS',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionAudit->id
        ]);

        // Direction des Etudes et de la Coopération Internationale
        $dirEtudes = EntiteProductrice::create([
            'nom_entite' => 'Direction des Etudes et de la Coopération Internationale',
            'code_entite' => 'DC-DECI',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => null
        ]);

        // Divisions sous Direction des Etudes
        $divisionEtudes = EntiteProductrice::create([
            'nom_entite' => 'Division des Etudes',
            'code_entite' => 'DC-DECI-DE',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $dirEtudes->id
        ]);

        $divisionCooperation = EntiteProductrice::create([
            'nom_entite' => 'Division de la Coopération Internationale',
            'code_entite' => 'DC-DECI-DCI',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $dirEtudes->id
        ]);

        $divisionTaxation = EntiteProductrice::create([
            'nom_entite' => 'Division de la Taxation',
            'code_entite' => 'DC-DECI-DT',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $dirEtudes->id
        ]);

        // Services sous Division des Etudes
        EntiteProductrice::create([
            'nom_entite' => 'Sce des Etudes Législatives et Réglementaires',
            'code_entite' => 'DC-DECI-DE-SELR',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionEtudes->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce des Etudes Tarifaires',
            'code_entite' => 'DC-DECI-DE-SET',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionEtudes->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce des Impôts Indirects',
            'code_entite' => 'DC-DECI-DE-SII',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionEtudes->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce des Statistiques et de la Veille Stratégique',
            'code_entite' => 'DC-DECI-DE-SSVS',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionEtudes->id
        ]);

        // Services sous Division de la Coopération Internationale
        EntiteProductrice::create([
            'nom_entite' => 'Sce des Relations avec les Organisations Internationales',
            'code_entite' => 'DC-DECI-DCI-SROI',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionCooperation->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce des Relations avec l\'Europe et l\'Amérique',
            'code_entite' => 'DC-DECI-DCI-SREA',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionCooperation->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce des Relations avec le Monde Arabe, l\'Afrique, l\'Asie et l\'Océanie',
            'code_entite' => 'DC-DECI-DCI-SRMAAO',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionCooperation->id
        ]);

        // Services sous Division de la Taxation
        EntiteProductrice::create([
            'nom_entite' => 'Sce de la Nomenclature',
            'code_entite' => 'DC-DECI-DT-SN',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionTaxation->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce des Règles d\'Origine',
            'code_entite' => 'DC-DECI-DT-SRO',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionTaxation->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce des Régimes Economiques en douane',
            'code_entite' => 'DC-DECI-DT-SRED',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionTaxation->id
        ]);

        // Direction de la Facilitation et de l'Informatique
        $dirFacilitation = EntiteProductrice::create([
            'nom_entite' => 'Direction de la Facilitation et de l\'Informatique',
            'code_entite' => 'DC-DFI',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => null
        ]);

        // Divisions sous Direction de la Facilitation
        $divisionFacilitation = EntiteProductrice::create([
            'nom_entite' => 'Division de la Facilitation des Procédures et des Investissements',
            'code_entite' => 'DC-DFI-DFPI',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $dirFacilitation->id
        ]);

        $divisionSystemes = EntiteProductrice::create([
            'nom_entite' => 'Division des Systèmes d\'Information',
            'code_entite' => 'DC-DFI-DSI',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $dirFacilitation->id
        ]);

        // Services sous Division de la Facilitation des Procédures
        EntiteProductrice::create([
            'nom_entite' => 'Sce des Procédures et des Méthodes',
            'code_entite' => 'DC-DFI-DFPI-SPM',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionFacilitation->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce des Investissements et des Régimes Particuliers',
            'code_entite' => 'DC-DFI-DFPI-SIRP',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionFacilitation->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce du Partenariat avec le Secteur Privé et de l\'Accompagnement des Politiques Sectorielles',
            'code_entite' => 'DC-DFI-DFPI-SPPAPS',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionFacilitation->id
        ]);

        // Services sous Division des Systèmes d'Information
        EntiteProductrice::create([
            'nom_entite' => 'Sce du Développement du Système de Dédouanement',
            'code_entite' => 'DC-DFI-DSI-SDSD',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionSystemes->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de la production informatique',
            'code_entite' => 'DC-DFI-DSI-SPI',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionSystemes->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de l\'Urbanisation et de la Performance du Système d\'Information',
            'code_entite' => 'DC-DFI-DSI-SUPSI',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionSystemes->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce du Développement des Applications Web et du Système de Décisionnel',
            'code_entite' => 'DC-DFI-DSI-SDAWSD',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionSystemes->id
        ]);
        
        EntiteProductrice::create([
            'nom_entite' => 'Sce des Réseaux et de la Sécurité du Système d\'Information',
            'code_entite' => 'DC-DFI-DSI-SRSSI',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionSystemes->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de la Bureautique et de la Gestion des Utilisateurs',
            'code_entite' => 'DC-DFI-DSI-SBGU',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionSystemes->id
        ]);

        // Direction de la Prévention et du Contentieux
        $dirPrevention = EntiteProductrice::create([
            'nom_entite' => 'Direction de la Prévention et du Contentieux',
            'code_entite' => 'DC-DPC',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => null
        ]);

        // Divisions sous Direction de la Prévention
        $divisionPrevention = EntiteProductrice::create([
            'nom_entite' => 'Division de la Prévention',
            'code_entite' => 'DC-DPC-DP',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $dirPrevention->id
        ]);

        $divisionContentieux = EntiteProductrice::create([
            'nom_entite' => 'Division du Contentieux',
            'code_entite' => 'DC-DPC-DC',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $dirPrevention->id
        ]);

        $divisionControle = EntiteProductrice::create([
            'nom_entite' => 'Division du Contrôle',
            'code_entite' => 'DC-DPC-DCO',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $dirPrevention->id
        ]);

        // Services sous Division de la Prévention
        EntiteProductrice::create([
            'nom_entite' => 'Sce de l\'analyse et du Risque',
            'code_entite' => 'DC-DPC-DP-SAR',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionPrevention->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce du Renseignement',
            'code_entite' => 'DC-DPC-DP-SR',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionPrevention->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de la Coordination des Contrôles sur Frontières',
            'code_entite' => 'DC-DPC-DP-SCCF',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionPrevention->id
        ]);

        // Services sous Division du Contentieux
        EntiteProductrice::create([
            'nom_entite' => 'Sce des Règlements Transactionnels',
            'code_entite' => 'DC-DPC-DC-SRT',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionContentieux->id
        ]);
        
        EntiteProductrice::create([
            'nom_entite' => 'Sce des Etudes et Suivi des Règlements Judiciaires',
            'code_entite' => 'DC-DPC-DC-SESRJ',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionContentieux->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de l\'Execution Juridique et du Contentieux de Recouvrement',
            'code_entite' => 'DC-DPC-DC-SEJCR',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionContentieux->id
        ]);

        // Services sous Division du Contrôle
        EntiteProductrice::create([
            'nom_entite' => 'Sce du Contrôle des Opérations Commerciales',
            'code_entite' => 'DC-DPC-DCO-SCOC',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionControle->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce du Contrôle de la Valeur',
            'code_entite' => 'DC-DPC-DCO-SCV',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionControle->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de la Lutte contre la Contrebande',
            'code_entite' => 'DC-DPC-DCO-SLCC',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionControle->id
        ]);

        // Direction des Ressources et de la Programmation
        $dirRessources = EntiteProductrice::create([
            'nom_entite' => 'Direction des Ressources et de la Programmation',
            'code_entite' => 'DC-DRP',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => null
        ]);

        // Divisions sous Direction des Ressources
        $divisionCommunication = EntiteProductrice::create([
            'nom_entite' => 'Division de la Communication et de la Programmation',
            'code_entite' => 'DC-DRP-DCP',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $dirRessources->id
        ]);

        $divisionBudget = EntiteProductrice::create([
            'nom_entite' => 'Division du Budget et des Equipements',
            'code_entite' => 'DC-DRP-DBE',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $dirRessources->id
        ]);

        $divisionRH = EntiteProductrice::create([
            'nom_entite' => 'Division des Ressources Humaines',
            'code_entite' => 'DC-DRP-DRH',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $dirRessources->id
        ]);

        $institutFormation = EntiteProductrice::create([
            'nom_entite' => 'Institut de Formation Douanière',
            'code_entite' => 'DC-DRP-IFD',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $dirRessources->id
        ]);

        // Services sous Division de la Communication
        EntiteProductrice::create([
            'nom_entite' => 'Sce de la Planification et du Contrôle de Gestion',
            'code_entite' => 'DC-DRP-DCP-SPCG',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionCommunication->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de la Communication',
            'code_entite' => 'DC-DRP-DCP-SC',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionCommunication->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de la Gestion de l\'Information et de l\'Accueil',
            'code_entite' => 'DC-DRP-DCP-SGIA',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionCommunication->id
        ]);

        // Services sous Division du Budget
        EntiteProductrice::create([
            'nom_entite' => 'Sce du Budget',
            'code_entite' => 'DC-DRP-DBE-SB',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionBudget->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de la Centralisation Comptable et du Suivi du Recouvrement',
            'code_entite' => 'DC-DRP-DBE-SCCSR',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionBudget->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de la Gestion du Patrimoine',
            'code_entite' => 'DC-DRP-DBE-SGP',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionBudget->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de Reprographie et de Diffusion',
            'code_entite' => 'DC-DRP-DBE-SRD',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionBudget->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce des Equipements et des Fournitures',
            'code_entite' => 'DC-DRP-DBE-SEF',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionBudget->id
        ]);

        // Services sous Division des Ressources Humaines
        EntiteProductrice::create([
            'nom_entite' => 'Sce de l\'Organisation et de la GPRH',
            'code_entite' => 'DC-DRP-DRH-SOGPRH',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionRH->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de la Gestion Administrative du Personnel',
            'code_entite' => 'DC-DRP-DRH-SGAP',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionRH->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de l\'Action Sociale',
            'code_entite' => 'DC-DRP-DRH-SAS',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionRH->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de l\'Animation et de la Coordination des Brigades',
            'code_entite' => 'DC-DRP-DRH-SACB',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $divisionRH->id
        ]);

        // Services sous Institut de Formation Douanière
        EntiteProductrice::create([
            'nom_entite' => 'Sce la conception, de la production et de l\'évaluation de la formation',
            'code_entite' => 'DC-DRP-IFD-SCPEF',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $institutFormation->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de la mise en œuvre et du suivi de la formation',
            'code_entite' => 'DC-DRP-IFD-SMOSF',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $institutFormation->id
        ]);

        EntiteProductrice::create([
            'nom_entite' => 'Sce de la gestion logistique',
            'code_entite' => 'DC-DRP-IFD-SGL',
            'id_organisme' => $directionCentrale->id,
            'entite_parent' => $institutFormation->id
        ]);
    }
}