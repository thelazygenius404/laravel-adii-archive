<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use App\Models\Travee;
use App\Models\Tablette;
use App\Models\Position;
use App\Models\Boite;
use App\Models\Dossier;
use App\Models\Organisme;
use Illuminate\Http\Request;

class StockageController extends Controller
{
    /**
     * Display storage overview dashboard.
     */
    public function index()
    {
        // Global statistics
        $stats = [
            'total_salles' => Salle::count(),
            'total_positions' => Position::count(),
            'positions_occupees' => Position::occupied()->count(),
            'positions_libres' => Position::available()->count(),
            'total_boites' => Boite::active()->count(),
            'total_dossiers' => Dossier::count(),
            'dossiers_actifs' => Dossier::byStatus('actif')->count(),
            'dossiers_due_elimination' => Dossier::dueForElimination()->count(),
        ];

        // Utilization by organisme
        $utilisationParOrganisme = Organisme::with('salles')
            ->get()
            ->map(function ($organisme) {
                return [
                    'nom' => $organisme->nom_org,
                    'capacite_max' => $organisme->salles->sum('capacite_max'),
                    'capacite_actuelle' => $organisme->salles->sum('capacite_actuelle'),
                    'utilisation_percentage' => $organisme->utilisation_percentage,
                ];
            });

        // Recent activities
        $activitesRecentes = [
            'nouveaux_dossiers' => Dossier::latest()->limit(5)->get(),
            'boites_pleines' => Boite::active()->whereRaw('nbr_dossiers >= capacite')->limit(5)->get(),
            'dossiers_elimination' => Dossier::nearElimination(30)->limit(5)->get(),
        ];

        return view('admin.stockage.dashboard', compact('stats', 'utilisationParOrganisme', 'activitesRecentes'));
    }

    /**
     * Show storage hierarchy for a specific organisme.
     */
    public function hierarchy(Request $request, $organismeId = null)
    {
        $organisme = null;
        if ($organismeId) {
            $organisme = Organisme::findOrFail($organismeId);
        }

        $query = Salle::with(['travees.tablettes.positions.boite', 'organisme']);
        
        if ($organisme) {
            $query->where('organisme_id', $organisme->id);
        }

        $salles = $query->get();
        $organismes = Organisme::orderBy('nom_org')->get();

        return view('admin.stockage.hierarchy', compact('salles', 'organismes', 'organisme'));
    }

    /**
     * Find available positions for new boites.
     */
    public function findAvailablePositions(Request $request)
    {
        $organismeId = $request->get('organisme_id');
        $limit = $request->get('limit', 20);

        $query = Position::with(['tablette.travee.salle.organisme'])
                        ->available();

        if ($organismeId) {
            $query->whereHas('tablette.travee.salle', function ($q) use ($organismeId) {
                $q->where('organisme_id', $organismeId);
            });
        }

        $positions = $query->limit($limit)->get();

        return response()->json($positions->map(function ($position) {
            return [
                'id' => $position->id,
                'nom' => $position->nom,
                'full_path' => $position->full_path,
                'salle' => $position->tablette->travee->salle->nom,
                'organisme' => $position->tablette->travee->salle->organisme->nom_org,
            ];
        }));
    }

    /**
     * Get storage statistics by organisme.
     */
    public function statisticsByOrganisme(Request $request, $organismeId)
    {
        $organisme = Organisme::findOrFail($organismeId);

        $stats = [
            'organisme' => $organisme->nom_org,
            'salles' => $organisme->salles()->count(),
            'capacite_totale' => $organisme->total_capacity,
            'capacite_utilisee' => $organisme->current_utilization,
            'utilisation_percentage' => $organisme->utilisation_percentage,
            'positions_libres' => Position::available()
                ->whereHas('tablette.travee.salle', function ($q) use ($organismeId) {
                    $q->where('organisme_id', $organismeId);
                })->count(),
            'boites_actives' => Boite::active()
                ->whereHas('position.tablette.travee.salle', function ($q) use ($organismeId) {
                    $q->where('organisme_id', $organismeId);
                })->count(),
            'dossiers_total' => Dossier::whereHas('boite.position.tablette.travee.salle', function ($q) use ($organismeId) {
                $q->where('organisme_id', $organismeId);
            })->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Optimize storage by suggesting reorganization.
     */
    public function optimizeStorage(Request $request)
    {
        $organismeId = $request->get('organisme_id');
        
        // Find boites with low occupancy
        $boitesPartielle = Boite::active()
            ->whereRaw('nbr_dossiers < capacite * 0.5')
            ->when($organismeId, function ($q) use ($organismeId) {
                $q->whereHas('position.tablette.travee.salle', function ($query) use ($organismeId) {
                    $query->where('organisme_id', $organismeId);
                });
            })
            ->with(['position.tablette.travee.salle', 'dossiers'])
            ->get();

        // Find positions that could be freed
        $positionsOptimisables = $boitesPartielle->map(function ($boite) {
            return [
                'boite' => $boite->numero,
                'localisation' => $boite->full_location,
                'capacite' => $boite->capacite,
                'occupation' => $boite->nbr_dossiers,
                'taux_occupation' => $boite->utilisation_percentage,
                'dossiers' => $boite->dossiers->count(),
                'suggestions' => $this->generateOptimizationSuggestions($boite),
            ];
        });

        return response()->json([
            'total_boites_analysees' => $boitesPartielle->count(),
            'positions_potentiellement_liberables' => $positionsOptimisables->count(),
            'optimisations' => $positionsOptimisables,
        ]);
    }

    /**
     * Generate optimization suggestions for a boite.
     */
    private function generateOptimizationSuggestions($boite)
    {
        $suggestions = [];

        if ($boite->utilisation_percentage < 30) {
            $suggestions[] = 'Considérer la consolidation avec une autre boîte';
        }

        if ($boite->nbr_dossiers == 0) {
            $suggestions[] = 'Boîte vide - peut être supprimée';
        }

        if ($boite->utilisation_percentage < 50 && $boite->nbr_dossiers > 0) {
            $suggestions[] = 'Optimiser l\'espace en regroupant les dossiers';
        }

        return $suggestions;
    }

    /**
     * Search across all storage entities.
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        $type = $request->get('type', 'all');

        $results = [];

        if ($type === 'all' || $type === 'boites') {
            $results['boites'] = Boite::search($search)
                ->with('position.tablette.travee.salle')
                ->limit(10)
                ->get()
                ->map(function ($boite) {
                    return [
                        'type' => 'boite',
                        'id' => $boite->id,
                        'numero' => $boite->numero,
                        'localisation' => $boite->full_location,
                        'occupation' => "{$boite->nbr_dossiers}/{$boite->capacite}",
                    ];
                });
        }

        if ($type === 'all' || $type === 'dossiers') {
            $results['dossiers'] = Dossier::search($search)
                ->with('boite.position.tablette.travee.salle')
                ->limit(10)
                ->get()
                ->map(function ($dossier) {
                    return [
                        'type' => 'dossier',
                        'id' => $dossier->id,
                        'numero' => $dossier->numero,
                        'titre' => $dossier->titre,
                        'localisation' => $dossier->full_location,
                        'statut' => $dossier->status_display,
                    ];
                });
        }

        if ($type === 'all' || $type === 'salles') {
            $results['salles'] = Salle::search($search)
                ->with('organisme')
                ->limit(10)
                ->get()
                ->map(function ($salle) {
                    return [
                        'type' => 'salle',
                        'id' => $salle->id,
                        'nom' => $salle->nom,
                        'organisme' => $salle->organisme->nom_org,
                        'utilisation' => $salle->utilisation_percentage . '%',
                    ];
                });
        }

        return response()->json($results);
    }

    /**
     * Export storage report.
     */
    public function exportReport(Request $request)
    {
        $type = $request->get('type', 'complete');
        $organismeId = $request->get('organisme_id');

        switch ($type) {
            case 'utilisation':
                return $this->exportUtilizationReport($organismeId);
            case 'inventory':
                return $this->exportInventoryReport($organismeId);
            case 'elimination':
                return $this->exportEliminationReport($organismeId);
            default:
                return $this->exportCompleteReport($organismeId);
        }
    }

    /**
     * Export utilization report.
     */
    private function exportUtilizationReport($organismeId)
    {
        $query = Salle::with(['organisme', 'travees.tablettes.positions']);
        
        if ($organismeId) {
            $query->where('organisme_id', $organismeId);
        }

        $salles = $query->get();

        $filename = 'rapport_utilisation_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($salles) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'Organisme',
                'Salle',
                'Capacité Max',
                'Capacité Actuelle',
                'Utilisation (%)',
                'Travées',
                'Tablettes',
                'Positions Totales',
                'Positions Occupées',
                'Positions Libres'
            ], ';');
            
            foreach ($salles as $salle) {
                $totalPositions = $salle->positions()->count();
                $positionsOccupees = $salle->positions()->occupied()->count();
                
                fputcsv($file, [
                    $salle->organisme->nom_org,
                    $salle->nom,
                    $salle->capacite_max,
                    $salle->capacite_actuelle,
                    $salle->utilisation_percentage,
                    $salle->travees()->count(),
                    $salle->tablettes()->count(),
                    $totalPositions,
                    $positionsOccupees,
                    $totalPositions - $positionsOccupees
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export inventory report.
     */
    private function exportInventoryReport($organismeId)
    {
        $query = Boite::active()->with(['position.tablette.travee.salle.organisme', 'dossiers']);
        
        if ($organismeId) {
            $query->whereHas('position.tablette.travee.salle', function ($q) use ($organismeId) {
                $q->where('organisme_id', $organismeId);
            });
        }

        $boites = $query->get();

        $filename = 'inventaire_boites_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($boites) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'Numéro Boîte',
                'Code Thématique',
                'Code Topographique',
                'Localisation Complète',
                'Capacité',
                'Nombre Dossiers',
                'Utilisation (%)',
                'Organisme',
                'Salle',
                'Statut'
            ], ';');
            
            foreach ($boites as $boite) {
                fputcsv($file, [
                    $boite->numero,
                    $boite->code_thematique,
                    $boite->code_topo,
                    $boite->full_location,
                    $boite->capacite,
                    $boite->nbr_dossiers,
                    $boite->utilisation_percentage,
                    $boite->position->tablette->travee->salle->organisme->nom_org,
                    $boite->position->tablette->travee->salle->nom,
                    $boite->detruite ? 'Détruite' : 'Active'
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export elimination report.
     */
    private function exportEliminationReport($organismeId)
    {
        $query = Dossier::with(['boite.position.tablette.travee.salle.organisme', 'calendrierConservation'])
                        ->where(function ($q) {
                            $q->dueForElimination()->orWhere(function ($subQ) {
                                $subQ->nearElimination(180); // 6 months
                            });
                        });
        
        if ($organismeId) {
            $query->whereHas('boite.position.tablette.travee.salle', function ($q) use ($organismeId) {
                $q->where('organisme_id', $organismeId);
            });
        }

        $dossiers = $query->orderBy('date_elimination_prevue')->get();

        $filename = 'rapport_elimination_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($dossiers) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'Numéro Dossier',
                'Titre',
                'Date Création',
                'Date Élimination Prévue',
                'Jours Restants',
                'Statut',
                'Règle Conservation',
                'Localisation',
                'Organisme',
                'Action Recommandée'
            ], ';');
            
            foreach ($dossiers as $dossier) {
                $joursRestants = $dossier->days_until_elimination;
                $actionRecommandee = $joursRestants <= 0 ? 'À éliminer immédiatement' : 
                                   ($joursRestants <= 30 ? 'Préparation élimination' : 'Surveillance');
                
                fputcsv($file, [
                    $dossier->numero,
                    $dossier->titre,
                    $dossier->date_creation->format('d/m/Y'),
                    $dossier->date_elimination_prevue?->format('d/m/Y'),
                    $joursRestants,
                    $dossier->status_display,
                    $dossier->calendrierConservation->NO_regle,
                    $dossier->full_location,
                    $dossier->boite->position->tablette->travee->salle->organisme->nom_org,
                    $actionRecommandee
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export complete report.
     */
    private function exportCompleteReport($organismeId)
    {
        // This would be a comprehensive report combining all data
        // Implementation would be similar to above methods but more comprehensive
        return $this->exportUtilizationReport($organismeId);
    }
}