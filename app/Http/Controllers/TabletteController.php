<?php

namespace App\Http\Controllers;

use App\Models\Tablette;
use App\Models\Travee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Position;

class TabletteController extends Controller
{
    /**
     * Display a listing of the tablettes.
     */
    public function index(Request $request)
    {
        $query = Tablette::with(['travee.salle.organisme']);

        if ($request->filled('search')) {
            $query->where('nom', 'LIKE', "%{$request->search}%");
        }

        if ($request->filled('travee_id')) {
            $query->where('travee_id', $request->travee_id);
        }

        $tablettes = $query->withCount('positions')
                          ->orderBy('nom')
                          ->paginate($request->get('per_page', 15))
                          ->withQueryString();

        $travees = Travee::with('salle')->orderBy('nom')->get();

        return view('admin.tablettes.index', compact('tablettes', 'travees'));
    }

    /**
     * Show the form for creating a new tablette.
     */
    public function create()
    {
        $travees = Travee::with('salle')->orderBy('nom')->get();
        return view('admin.tablettes.create', compact('travees'));
    }

    /**
     * Store a newly created tablette in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'travee_id' => 'required|exists:travees,id',
        ], [
            'nom.required' => 'Le nom de la tablette est obligatoire.',
            'travee_id.required' => 'La travée est obligatoire.',
        ]);

        Tablette::create($validated);

        return redirect()->route('admin.tablettes.index')
                        ->with('success', 'Tablette créée avec succès.');
    }

    /**
     * Display the specified tablette.
     */
    public function show(Tablette $tablette)
    {
        $tablette->load(['travee.salle.organisme', 'positions.boite']);
        
        $stats = [
            'total_positions' => $tablette->positions()->count(),
            'positions_occupees' => $tablette->positions_occupees,
            'positions_libres' => $tablette->total_positions - $tablette->positions_occupees,
            'utilisation_percentage' => $tablette->utilisation_percentage,
        ];

        return view('admin.tablettes.show', compact('tablette', 'stats'));
    }

    /**
     * Show the form for editing the specified tablette.
     */
    public function edit(Tablette $tablette)
    {
        $travees = Travee::with('salle')->orderBy('nom')->get();
        return view('admin.tablettes.edit', compact('tablette', 'travees'));
    }

    /**
     * Update the specified tablette in storage.
     */
    public function update(Request $request, Tablette $tablette)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'travee_id' => 'required|exists:travees,id',
        ], [
            'nom.required' => 'Le nom de la tablette est obligatoire.',
            'travee_id.required' => 'La travée est obligatoire.',
        ]);

        $tablette->update($validated);

        return redirect()->route('admin.tablettes.index')
                        ->with('success', 'Tablette modifiée avec succès.');
    }

    /**
     * Remove the specified tablette from storage.
     */
    public function destroy(Tablette $tablette)
    {
        if ($tablette->positions()->count() > 0) {
            return redirect()->route('admin.tablettes.index')
                            ->with('error', 'Impossible de supprimer cette tablette car elle contient des positions.');
        }

        $tablette->delete();

        return redirect()->route('admin.tablettes.index')
                        ->with('success', 'Tablette supprimée avec succès.');
    }

    /**
     * Get tablettes by travee for API/AJAX requests.
     */
    public function byTravee(Request $request, $traveeId)
    {
        $tablettes = Tablette::where('travee_id', $traveeId)
                            ->select('id', 'nom')
                            ->withCount('positions')
                            ->orderBy('nom')
                            ->get();
        
        return response()->json($tablettes);
    }
        public function export(Request $request)
    {
        $query = Tablette::with(['travee.salle.organisme']);

        // Apply filters
        if ($request->filled('search')) {
            $query->where('nom', 'LIKE', "%{$request->search}%");
        }

        if ($request->filled('travee_id')) {
            $query->where('travee_id', $request->travee_id);
        }

        $tablettes = $query->withCount('positions')
                        ->orderBy('nom')
                        ->get();

        $filename = 'tablettes_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function() use ($tablettes) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Nom',
                'Travée',
                'Salle',
                'Organisme',
                'Nombre Positions',
                'Positions Occupées',
                'Utilisation (%)',
                'Date de Création'
            ], ';');
            
            foreach ($tablettes as $tablette) {
                $positionsOccupees = $tablette->positions()->where('vide', false)->count();
                $utilisation = $tablette->positions_count > 0 ? 
                            ($positionsOccupees / $tablette->positions_count) * 100 : 0;
                
                fputcsv($file, [
                    $tablette->id,
                    $tablette->nom,
                    $tablette->travee->nom,
                    $tablette->travee->salle->nom,
                    $tablette->travee->salle->organisme->nom_org,
                    $tablette->positions_count ?? 0,
                    $positionsOccupees,
                    number_format($utilisation, 1),
                    $tablette->created_at->format('d/m/Y H:i:s')
                ], ';');
            }
            
            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
     public function bulkAction(Request $request)
{
    // Log de debug détaillé
    Log::info('=== TABLETTE BULK ACTION DEBUG ===', [
        'method' => $request->method(),
        'headers' => $request->headers->all(),
        'all_data' => $request->all(),
        'files' => $request->files->all(),
    ]);

    try {
        // CORRECTION 1: Validation plus permissive
        $validated = $request->validate([
            'action' => 'required|string|in:delete,export,move,optimize',
            'tablette_ids' => 'required', // Accepter string ou array
            'new_travee_id' => 'nullable|integer|exists:travees,id'
        ], [
            'action.required' => 'L\'action est obligatoire.',
            'action.in' => 'Action non valide. Actions autorisées: delete, export, move, optimize',
            'tablette_ids.required' => 'Au moins une tablette doit être sélectionnée.',
            'new_travee_id.exists' => 'La travée sélectionnée n\'existe pas.'
        ]);

        Log::info('Validation réussie:', $validated);

        // CORRECTION 2: Gestion flexible des IDs
        $tabletteIds = $validated['tablette_ids'];
        
        // Si c'est une string JSON, la décoder
        if (is_string($tabletteIds)) {
            $tabletteIds = json_decode($tabletteIds, true);
            Log::info('IDs décodés depuis JSON:', ['ids' => $tabletteIds]);
        }
        
        // Vérifier que c'est un array valide
        if (!is_array($tabletteIds) || empty($tabletteIds)) {
            Log::error('IDs invalides:', ['tablette_ids' => $validated['tablette_ids']]);
            return response()->json([
                'success' => false,
                'message' => 'Format des IDs de tablettes invalide. Reçu: ' . gettype($validated['tablette_ids'])
            ], 400);
        }

        // CORRECTION 3: Validation des IDs numériques
        $tabletteIds = array_filter($tabletteIds, function($id) {
            return is_numeric($id) && $id > 0;
        });

        if (empty($tabletteIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun ID de tablette valide fourni.'
            ], 400);
        }

        Log::info('IDs traités:', ['tablette_ids' => $tabletteIds]);

        $action = $validated['action'];

        // Exécuter l'action
        switch ($action) {
            case 'export':
                return $this->bulkExport($tabletteIds);
            
            case 'delete':
                return $this->bulkDelete($tabletteIds);
            
            case 'move':
                return $this->bulkMove($tabletteIds, $validated['new_travee_id'] ?? null);
            
            case 'optimize':
                return $this->bulkOptimize($tabletteIds);
            
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Action non reconnue: ' . $action
                ], 400);
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Erreur de validation:', [
            'errors' => $e->errors(),
            'input' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur de validation.',
            'errors' => $e->errors(),
            'received_data' => $request->all() // DEBUG
        ], 422);
        
    } catch (\Exception $e) {
        Log::error('Tablette bulk action error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'input' => $request->all()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Erreur interne du serveur: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Test endpoint pour debug
 */
public function testBulkAction(Request $request)
{
    return response()->json([
        'success' => true,
        'message' => 'Test endpoint fonctionne',
        'received_data' => $request->all(),
        'method' => $request->method(),
        'headers' => $request->headers->all()
    ]);
}

/**
 * Bulk delete multiple tablettes - Version améliorée
 */
private function bulkDelete($tabletteIds)
{
    try {
        Log::info('Bulk delete tablettes', ['ids' => $tabletteIds]);
        
        $tablettes = Tablette::whereIn('id', $tabletteIds)->get();
        
        if ($tablettes->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune tablette trouvée avec les IDs fournis.'
            ], 404);
        }
        
        $errors = [];
        $deleted = 0;

        foreach ($tablettes as $tablette) {
            // Vérifier si la tablette contient des positions
            if ($tablette->positions()->count() > 0) {
                $errors[] = "La tablette '{$tablette->nom}' contient des positions et ne peut pas être supprimée.";
                continue;
            }

            $tablette->delete();
            $deleted++;
        }

        $message = $deleted > 0 ? "{$deleted} tablette(s) supprimée(s) avec succès." : 'Aucune tablette supprimée.';
        if (!empty($errors)) {
            $message .= ' Erreurs: ' . implode(' ', $errors);
        }

        Log::info('Bulk delete result', ['deleted' => $deleted, 'errors' => $errors]);

        return response()->json([
            'success' => $deleted > 0,
            'message' => $message,
            'deleted' => $deleted,
            'errors' => $errors
        ]);

    } catch (\Exception $e) {
        Log::error('Bulk delete tablettes error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Bulk export specific tablettes - Version améliorée
 */
private function bulkExport($tabletteIds)
{
    try {
        Log::info('Bulk export tablettes', ['ids' => $tabletteIds]);
        
        $tablettes = Tablette::with(['travee.salle.organisme'])
                            ->withCount('positions')
                            ->whereIn('id', $tabletteIds)
                            ->orderBy('nom')
                            ->get();

        if ($tablettes->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune tablette trouvée pour l\'export.'
            ], 404);
        }

        $filename = 'tablettes_selection_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function() use ($tablettes) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Nom',
                'Travée',
                'Salle',
                'Organisme',
                'Nombre Positions',
                'Positions Occupées',
                'Utilisation (%)',
                'Date de création'
            ], ';');
            
            foreach ($tablettes as $tablette) {
                $positionsOccupees = $tablette->positions()->where('vide', false)->count();
                $utilisation = $tablette->positions_count > 0 ? 
                            ($positionsOccupees / $tablette->positions_count) * 100 : 0;
                
                fputcsv($file, [
                    $tablette->id,
                    $tablette->nom,
                    $tablette->travee->nom,
                    $tablette->travee->salle->nom,
                    $tablette->travee->salle->organisme->nom_org,
                    $tablette->positions_count ?? 0,
                    $positionsOccupees,
                    number_format($utilisation, 1),
                    $tablette->created_at->format('d/m/Y H:i:s')
                ], ';');
            }
            
            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ]);

    } catch (\Exception $e) {
        Log::error('Bulk export tablettes error', [
            'message' => $e->getMessage(),
            'tablette_ids' => $tabletteIds
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'export: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Bulk move multiple tablettes to a new travee - Version améliorée
 */
private function bulkMove($tabletteIds, $newTraveeId)
{
    if (!$newTraveeId) {
        return response()->json([
            'success' => false,
            'message' => 'Travée de destination requise pour le déplacement.'
        ], 400);
    }

    try {
        $newTravee = Travee::find($newTraveeId);
        if (!$newTravee) {
            return response()->json([
                'success' => false,
                'message' => 'Travée de destination introuvable.'
            ], 404);
        }

        $tablettes = Tablette::whereIn('id', $tabletteIds)->get();
        
        if ($tablettes->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune tablette trouvée avec les IDs fournis.'
            ], 404);
        }
        
        $moved = 0;
        $warnings = [];

        foreach ($tablettes as $tablette) {
            // Vérifier si la tablette contient des positions occupées
            $positionsOccupees = $tablette->positions()->where('vide', false)->count();
            if ($positionsOccupees > 0) {
                $warnings[] = "La tablette '{$tablette->nom}' contient {$positionsOccupees} position(s) occupée(s).";
            }

            $oldTraveeId = $tablette->travee_id;
            $tablette->update(['travee_id' => $newTraveeId]);
            
            // Mettre à jour les capacités si nécessaire
            if ($oldTraveeId != $newTraveeId) {
                // Mettre à jour la capacité de la nouvelle salle
                $newTravee->salle->updateCapaciteActuelle();
                
                // Mettre à jour la capacité de l'ancienne salle
                $oldTravee = Travee::find($oldTraveeId);
                if ($oldTravee) {
                    $oldTravee->salle->updateCapaciteActuelle();
                }
            }
            
            $moved++;
        }

        $message = $moved > 0 ? "{$moved} tablette(s) déplacée(s) vers la travée '{$newTravee->nom}'." : 'Aucune tablette déplacée.';
        if (!empty($warnings)) {
            $message .= ' Avertissements: ' . implode(' ', $warnings);
        }

        Log::info('Bulk move result', ['moved' => $moved, 'warnings' => $warnings]);

        return response()->json([
            'success' => $moved > 0,
            'message' => $message,
            'moved' => $moved,
            'warnings' => $warnings
        ]);

    } catch (\Exception $e) {
        Log::error('Bulk move tablettes error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors du déplacement: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Bulk optimize multiple tablettes - Version améliorée
 */
private function bulkOptimize($tabletteIds)
{
    try {
        Log::info('Bulk optimize tablettes', ['ids' => $tabletteIds]);
        
        $tablettes = Tablette::whereIn('id', $tabletteIds)->get();
        
        if ($tablettes->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune tablette trouvée avec les IDs fournis.'
            ], 404);
        }
        
        $optimized = 0;
        $results = [];

        foreach ($tablettes as $tablette) {
            // Optimisation: mise à jour des compteurs et nettoyage
            $positionsCount = $tablette->positions()->count();
            $positionsOccupees = $tablette->positions()->where('vide', false)->count();
            $positionsLibres = $tablette->positions()->where('vide', true)->count();
            
            // Vérifier la cohérence des statuts
            $inconsistentPositions = 0;
            $tablette->positions()->each(function ($position) use (&$inconsistentPositions) {
                if (!$position->vide && !$position->boite) {
                    $position->update(['vide' => true]);
                    $inconsistentPositions++;
                }
            });
            
            // Calculer l'efficacité de la tablette
            $utilisation = $positionsCount > 0 ? 
                         ($positionsOccupees / $positionsCount) * 100 : 0;
            
            $efficacite = 'Excellente';
            if ($utilisation < 30) {
                $efficacite = 'Faible';
            } elseif ($utilisation < 60) {
                $efficacite = 'Moyenne';
            } elseif ($utilisation < 85) {
                $efficacite = 'Bonne';
            }
            
            $results[] = [
                'tablette' => $tablette->nom,
                'positions_total' => $positionsCount,
                'positions_occupees' => $positionsOccupees,
                'positions_libres' => $positionsLibres,
                'positions_corrigees' => $inconsistentPositions,
                'utilisation' => round($utilisation, 1),
                'efficacite' => $efficacite
            ];
            
            $optimized++;
        }

        Log::info('Bulk optimize result', ['optimized' => $optimized, 'results' => $results]);

        return response()->json([
            'success' => true,
            'message' => "{$optimized} tablette(s) optimisée(s) avec succès.",
            'optimized' => $optimized,
            'results' => $results
        ]);

    } catch (\Exception $e) {
        Log::error('Bulk optimize tablettes error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'optimisation: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Bulk create tablettes for a travee.
     */
    public function bulkCreateTablettes(Request $request)
    {
        Log::info('Bulk create tablettes called', $request->all());

        try {
            $validated = $request->validate([
                'travee_id' => 'required|exists:travees,id',
                'nombre_tablettes' => 'required|integer|min:1|max:50',
                'prefix' => 'nullable|string|max:10',
                'start_number' => 'nullable|integer|min:1',
                'positions_per_tablette' => 'nullable|integer|min:0|max:100'
            ]);

            $travee = Travee::find($validated['travee_id']);
            if (!$travee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Travée introuvable.'
                ], 404);
            }

            $nombreTablettes = $validated['nombre_tablettes'];
            $prefix = $validated['prefix'] ?? 'E';
            $startNumber = $validated['start_number'] ?? 1;
            $positionsPerTablette = $validated['positions_per_tablette'] ?? 0;

            DB::beginTransaction();

            $created = 0;
            $errors = [];

            for ($i = 0; $i < $nombreTablettes; $i++) {
                $numero = $startNumber + $i;
                $nom = $prefix . str_pad($numero, 2, '0', STR_PAD_LEFT);
                
                // Vérifier si la tablette existe déjà
                if (Tablette::where('travee_id', $travee->id)->where('nom', $nom)->exists()) {
                    $errors[] = "Tablette {$nom} existe déjà";
                    continue;
                }

                $tablette = Tablette::create([
                    'nom' => $nom,
                    'travee_id' => $travee->id,
                ]);
                
                // Créer les positions si demandé
                if ($positionsPerTablette > 0) {
                    for ($j = 1; $j <= $positionsPerTablette; $j++) {
                        Position::create([
                            'nom' => 'P' . str_pad($j, 3, '0', STR_PAD_LEFT),
                            'vide' => true,
                            'tablette_id' => $tablette->id,
                        ]);
                    }
                }
                
                $created++;
            }

            DB::commit();

            $message = "{$created} tablette(s) créée(s) avec succès.";
            if ($positionsPerTablette > 0) {
                $totalPositions = $created * $positionsPerTablette;
                $message .= " {$totalPositions} position(s) créée(s).";
            }
            if (!empty($errors)) {
                $message .= ' Erreurs: ' . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'created' => $created,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Bulk create tablettes error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création: ' . $e->getMessage()
            ], 500);
        }
    }
    
}