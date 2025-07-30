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
use Illuminate\Support\Facades\Log;
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
                    'utilisation_percentage' => $organisme->salles->sum('capacite_max') > 0 ? ($organisme->salles->sum('capacite_actuelle') / $organisme->salles->sum('capacite_max')) * 100 : 0,
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
        if ($request->has('export')) {
            $type = $request->get('type', 'optimization');
            return $this->exportOptimizationReport($request->get('organisme_id'), $type);
        }
        
        $organismeId = $request->get('organisme_id');
        
        try {
            // Find boites with low occupancy
            $boitesPartielle = Boite::active()
                ->whereRaw('nbr_dossiers < capacite * 0.5')
                ->when($organismeId, function ($q) use ($organismeId) {
                    $q->whereHas('position.tablette.travee.salle', function ($query) use ($organismeId) {
                        $query->where('organisme_id', $organismeId);
                    });
                })
                ->with(['position.tablette.travee.salle.organisme', 'dossiers'])
                ->get();

            // Créer un tableau simple
            $positionsOptimisables = [];
            
            foreach ($boitesPartielle as $boite) {
                $positionsOptimisables[] = [
                    'boite' => [
                        'id' => (int) $boite->id,
                        'numero' => (string) ($boite->numero ?? ''),
                        'code_thematique' => (string) ($boite->code_thematique ?? ''),
                    ],
                    'localisation' => (string) ($boite->full_location ?? 'Non définie'),
                    'capacite' => (int) ($boite->capacite ?? 0),
                    'occupation' => (int) ($boite->nbr_dossiers ?? 0),
                    'taux_occupation' => (float) round($boite->utilisation_percentage ?? 0, 1),
                    'dossiers_count' => (int) $boite->dossiers->count(),
                    'suggestions' => $this->generateOptimizationSuggestions($boite),
                ];
            }

            // Statistiques - utiliser count() sur les tableaux
            $totalBoites = $boitesPartielle->count();
            $totalOptimisables = count($positionsOptimisables);
            
            $stats = [
                'total_boites_analysees' => $totalBoites,
                'positions_potentiellement_liberables' => $totalOptimisables,
                'espace_total_recuperable' => 0,
                'taux_optimisation_possible' => 0
            ];
            
            // Calculer l'espace récupérable
            foreach ($positionsOptimisables as $item) {
                $stats['espace_total_recuperable'] += ($item['capacite'] - $item['occupation']);
            }
            
            // Calculer le taux d'optimisation
            if ($totalBoites > 0) {
                $stats['taux_optimisation_possible'] = round(($totalOptimisables / $totalBoites) * 100, 2);
            }

            // Si c'est une requête AJAX, retourner JSON avec tableau
            if ($request->ajax() || $request->has('ajax')) {
                return response()->json([
                    'success' => true,
                    'stats' => $stats,
                    'optimisations' => $positionsOptimisables, // Tableau pour JSON
                ]);
            }

            // Récupérer la liste des organismes pour le filtre
            $organismes = Organisme::orderBy('nom_org')->get();
            $organismeSelectionne = null;
            if ($organismeId) {
                $organismeSelectionne = Organisme::find($organismeId);
            }

            // IMPORTANT: Convertir en collection pour la vue HTML
            return view('admin.stockage.optimize', [
                'stats' => $stats,
                'positionsOptimisables' => collect($positionsOptimisables), // ← Collection pour Blade
                'organismes' => $organismes,
                'organismeSelectionne' => $organismeSelectionne
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur optimisation stockage: ' . $e->getMessage());
            
            if ($request->ajax() || $request->has('ajax')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Erreur lors de l\'analyse d\'optimisation',
                    'message' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => 'Erreur lors de l\'analyse d\'optimisation: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate optimization suggestions for a boite.
     */
    private function generateOptimizationSuggestions($boite)
    {
        $suggestions = [];

        try {
            $utilisationPercentage = $boite->utilisation_percentage ?? 0;
            $nbrDossiers = $boite->nbr_dossiers ?? 0;

            if ($utilisationPercentage < 30) {
                $suggestions[] = 'Considérer la consolidation avec une autre boîte';
            }

            if ($nbrDossiers == 0) {
                $suggestions[] = 'Boîte vide - peut être supprimée';
            }

            if ($utilisationPercentage < 50 && $nbrDossiers > 0) {
                $suggestions[] = 'Optimiser l\'espace en regroupant les dossiers';
            }
            
            if ($utilisationPercentage > 0 && $utilisationPercentage < 25) {
                $suggestions[] = 'Très faible utilisation - action recommandée';
            }

            // Si aucune suggestion spécifique, ajouter une suggestion générale
            if (empty($suggestions)) {
                $suggestions[] = 'Surveillance recommandée';
            }

        } catch (\Exception $e) {
            $suggestions[] = 'Erreur lors de l\'analyse';
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
    private function exportOptimizationReport($organismeId, $type = 'optimization')
{
    $filename = 'rapport_optimisation_' . date('Y-m-d_H-i-s') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function() use ($organismeId) {
        $file = fopen('php://output', 'w');
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($file, [
            'Boîte',
            'Code Thématique', 
            'Localisation',
            'Capacité',
            'Occupation',
            'Utilisation (%)',
            'Organisme',
            'Suggestions'
        ], ';');
        
        // Récupérer les données d'optimisation
        $query = Boite::active()
            ->whereRaw('nbr_dossiers < capacite * 0.5')
            ->when($organismeId, function ($q) use ($organismeId) {
                $q->whereHas('position.tablette.travee.salle', function ($query) use ($organismeId) {
                    $query->where('organisme_id', $organismeId);
                });
            })
            ->with(['position.tablette.travee.salle.organisme']);
            
        foreach ($query->get() as $boite) {
            $suggestions = implode(', ', $this->generateOptimizationSuggestions($boite));
            
            fputcsv($file, [
                $boite->numero,
                $boite->code_thematique,
                $boite->full_location,
                $boite->capacite,
                $boite->nbr_dossiers,
                $boite->utilisation_percentage,
                $boite->position->tablette->travee->salle->organisme->nom_org,
                $suggestions
            ], ';');
        }
        
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
 }
}