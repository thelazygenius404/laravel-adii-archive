<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Tablette;
use Illuminate\Http\Request;
use App\Models\Travee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PositionController extends Controller
{
    /**
     * Display a listing of the positions.
     */
    public function index(Request $request)
    {
        $query = Position::with(['tablette.travee.salle.organisme', 'boite']);

        if ($request->filled('search')) {
            $query->where('nom', 'LIKE', "%{$request->search}%");
        }

        if ($request->filled('tablette_id')) {
            $query->where('tablette_id', $request->tablette_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'libre') {
                $query->available();
            } elseif ($request->status === 'occupee') {
                $query->occupied();
            }
        }

        $positions = $query->orderBy('nom')
                          ->paginate($request->get('per_page', 15))
                          ->withQueryString();

        $tablettes = Tablette::with('travee.salle')->orderBy('nom')->get();

        return view('admin.positions.index', compact('positions', 'tablettes'));
    }

    /**
     * Show the form for creating a new position.
     */
    public function create()
    {
        $tablettes = Tablette::with('travee.salle')->orderBy('nom')->get();
        return view('admin.positions.create', compact('tablettes'));
    }

    /**
     * Store a newly created position in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'tablette_id' => 'required|exists:tablettes,id',
        ], [
            'nom.required' => 'Le nom de la position est obligatoire.',
            'tablette_id.required' => 'La tablette est obligatoire.',
        ]);

        $validated['vide'] = true; // New positions are always empty

        Position::create($validated);

        return redirect()->route('admin.positions.index')
                        ->with('success', 'Position créée avec succès.');
    }

    /**
     * Display the specified position.
     */
    public function show(Position $position)
    {
        $position->load(['tablette.travee.salle.organisme', 'boite.dossiers']);

        return view('admin.positions.show', compact('position'));
    }

    /**
     * Show the form for editing the specified position.
     */
    public function edit(Position $position)
    {
        $tablettes = Tablette::with('travee.salle')->orderBy('nom')->get();
        return view('admin.positions.edit', compact('position', 'tablettes'));
    }

    /**
     * Update the specified position in storage.
     */
    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'tablette_id' => 'required|exists:tablettes,id',
        ], [
            'nom.required' => 'Le nom de la position est obligatoire.',
            'tablette_id.required' => 'La tablette est obligatoire.',
        ]);

        $position->update($validated);

        return redirect()->route('admin.positions.index')
                        ->with('success', 'Position modifiée avec succès.');
    }

    /**
     * Remove the specified position from storage.
     */
    public function destroy(Position $position)
    {
        if ($position->boite) {
            return redirect()->route('admin.positions.index')
                            ->with('error', 'Impossible de supprimer cette position car elle contient une boîte.');
        }

        $position->delete();

        return redirect()->route('admin.positions.index')
                        ->with('success', 'Position supprimée avec succès.');
    }

    /**
     * Toggle position status (libre/occupée).
     */
    public function toggleStatus(Position $position)
    {
        if ($position->vide) {
            // Cannot manually mark as occupied without a boite
            return response()->json([
                'success' => false,
                'message' => 'Une position ne peut être marquée comme occupée que par l\'ajout d\'une boîte.'
            ], 400);
        } else {
            // Check if position has a boite
            if ($position->boite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette position contient une boîte. Supprimez d\'abord la boîte.'
                ], 400);
            }
            
            $position->markAsFree();
            
            return response()->json([
                'success' => true,
                'message' => 'Position marquée comme libre.',
                'status' => 'libre'
            ]);
        }
    }

    /**
     * Get positions by tablette for API/AJAX requests.
     */
    public function byTablette(Request $request, $tabletteId)
    {
        $query = Position::where('tablette_id', $tabletteId)->select('id', 'nom', 'vide');
        
        if ($request->filled('available_only')) {
            $query->available();
        }
        
        $positions = $query->orderBy('nom')->get();
        
        return response()->json($positions);
    }

    /**
     * Bulk create positions for a tablette.
     */
    public function bulkCreate(Request $request)
    {
        $validated = $request->validate([
            'travee_id' => 'required|exists:travees,id',
            'positions_per_tablette' => 'required|integer|min:1',
        ]);

        $traveeId = $validated['travee_id'];
        $positionsPerTablette = $validated['positions_per_tablette'];

        try {
            // Récupérer les tablettes de la travée sans positions
            $tablettesSansPositions = Tablette::where('travee_id', $traveeId)
                                            ->doesntHave('positions')
                                            ->get();

            foreach ($tablettesSansPositions as $tablette) {
                for ($i = 1; $i <= $positionsPerTablette; $i++) {
                    Position::create([
                        'tablette_id' => $tablette->id,
                        'nom' => "P{$i}",
                    ]);
                }
            }
            Log::info('Positions générées avec succès pour la travée ID: ' . $traveeId);
            return response()->json(['success' => true, 'message' => 'Positions générées avec succès.']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la génération des positions : ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Erreur lors de la génération des positions.'], 500);
        }
    }

    /**
     * Create positions for a specific tablette.
     */
    private function createPositionsForTablette($tablette, $nombrePositions, $prefix, $startNumber)
    {
        $created = 0;
        
        for ($i = 0; $i < $nombrePositions; $i++) {
            $numero = $startNumber + $i;
            $nom = $prefix . str_pad($numero, 3, '0', STR_PAD_LEFT);
            
            // Vérifier si la position existe déjà
            if (!Position::where('tablette_id', $tablette->id)->where('nom', $nom)->exists()) {
                Position::create([
                    'nom' => $nom,
                    'vide' => true,
                    'tablette_id' => $tablette->id,
                ]);
                $created++;
            }
        }
        
        return $created;
    }

    /**
     * Generate positions for all empty tablettes in a travee.
     */
    public function generateForTravee(Request $request)
    {
        $validated = $request->validate([
            'travee_id' => 'required|exists:travees,id',
            'positions_per_tablette' => 'required|integer|min:1|max:50',
            'prefix' => 'nullable|string|max:10'
        ]);

        try {
            DB::beginTransaction();

            $travee = Travee::find($validated['travee_id']);
            $prefix = $validated['prefix'] ?? 'P';
            $positionsPerTablette = $validated['positions_per_tablette'];
            
            $tablettes = $travee->tablettes()
                            ->whereDoesntHave('positions')
                            ->get();
            
            $totalCreated = 0;
            
            foreach ($tablettes as $tablette) {
                $created = $this->createPositionsForTablette(
                    $tablette, 
                    $positionsPerTablette, 
                    $prefix, 
                    1
                );
                $totalCreated += $created;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$totalCreated} position(s) générée(s) pour {$tablettes->count()} tablette(s).",
                'created' => $totalCreated,
                'tablettes_processed' => $tablettes->count()
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error generating positions for travee', [
                'travee_id' => $validated['travee_id'],
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération: ' . $e->getMessage()
            ], 500);
        }
    }
public function bulkAction(Request $request)
    {
        try {
            $validated = $request->validate([
                'action' => 'required|string|in:delete,export,move,toggle_status',
                'position_ids' => 'required|array|min:1',
                'position_ids.*' => 'integer|exists:positions,id',
                'new_tablette_id' => 'nullable|integer|exists:tablettes,id'
            ], [
                'action.required' => 'L\'action est obligatoire.',
                'action.in' => 'Action non valide.',
                'position_ids.required' => 'Au moins une position doit être sélectionnée.',
                'position_ids.array' => 'Format de données invalide pour les positions.',
                'position_ids.min' => 'Au moins une position doit être sélectionnée.',
                'position_ids.*.exists' => 'Une ou plusieurs positions sélectionnées n\'existent pas.',
                'new_tablette_id.exists' => 'La tablette sélectionnée n\'existe pas.'
            ]);

            $positionIds = $validated['position_ids'];
            $action = $validated['action'];

            // Exécuter l'action
            switch ($action) {
                case 'export':
                    return $this->bulkExport($positionIds);
                
                case 'delete':
                    return $this->bulkDelete($positionIds);
                
                case 'move':
                    return $this->bulkMove($positionIds, $validated['new_tablette_id'] ?? null);
                
                case 'toggle_status':
                    return $this->bulkToggleStatus($positionIds);
                
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Action non reconnue.'
                    ], 400);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Position bulk action error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne du serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete multiple positions.
     */
    private function bulkDelete($positionIds)
    {
        try {
            $positions = Position::whereIn('id', $positionIds)->get();
            $errors = [];
            $deleted = 0;

            foreach ($positions as $position) {
                // Vérifier si la position est occupée
                if (!$position->vide) {
                    $errors[] = "La position '{$position->nom}' est occupée et ne peut pas être supprimée.";
                    continue;
                }

                $position->delete();
                $deleted++;
            }

            $message = $deleted > 0 ? "{$deleted} position(s) supprimée(s) avec succès." : 'Aucune position supprimée.';
            if (!empty($errors)) {
                $message .= ' Erreurs: ' . implode(' ', $errors);
            }

            return response()->json([
                'success' => $deleted > 0,
                'message' => $message,
                'deleted' => $deleted,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk delete positions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk export specific positions.
     */
    private function bulkExport($positionIds)
    {
        $positions = Position::with(['tablette.travee.salle.organisme', 'boite'])
                            ->whereIn('id', $positionIds)
                            ->orderBy('nom')
                            ->get();

        $filename = 'positions_selection_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function() use ($positions) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Nom',
                'Tablette',
                'Travée',
                'Salle',
                'Organisme',
                'Statut',
                'Boîte',
                'Date de création'
            ], ';');
            
            foreach ($positions as $position) {
                fputcsv($file, [
                    $position->id,
                    $position->nom,
                    $position->tablette->nom,
                    $position->tablette->travee->nom,
                    $position->tablette->travee->salle->nom,
                    $position->tablette->travee->salle->organisme->nom_org,
                    $position->vide ? 'Libre' : 'Occupée',
                    $position->boite ? $position->boite->numero : '-',
                    $position->created_at->format('d/m/Y H:i:s')
                ], ';');
            }
            
            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Bulk move multiple positions to a new tablette.
     */
    private function bulkMove($positionIds, $newTabletteId)
    {
        if (!$newTabletteId) {
            return response()->json([
                'success' => false,
                'message' => 'Tablette de destination requise pour le déplacement.'
            ], 400);
        }

        try {
            $newTablette = Tablette::find($newTabletteId);
            if (!$newTablette) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tablette de destination introuvable.'
                ], 404);
            }

            $positions = Position::whereIn('id', $positionIds)->get();
            $moved = 0;
            $errors = [];

            foreach ($positions as $position) {
                // Vérifier si la position est occupée
                if (!$position->vide) {
                    $errors[] = "La position '{$position->nom}' est occupée et ne peut pas être déplacée.";
                    continue;
                }

                $position->update(['tablette_id' => $newTabletteId]);
                $moved++;
            }

            $message = $moved > 0 ? "{$moved} position(s) déplacée(s) vers la tablette '{$newTablette->nom}'." : 'Aucune position déplacée.';
            if (!empty($errors)) {
                $message .= ' Erreurs: ' . implode(' ', $errors);
            }

            return response()->json([
                'success' => $moved > 0,
                'message' => $message,
                'moved' => $moved,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk move positions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du déplacement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk toggle status of positions.
     */
    private function bulkToggleStatus($positionIds)
    {
        try {
            $positions = Position::whereIn('id', $positionIds)->get();
            $toggled = 0;
            $errors = [];

            foreach ($positions as $position) {
                // Ne peut basculer que les positions libres vers occupées manuellement
                if (!$position->vide) {
                    $errors[] = "La position '{$position->nom}' est occupée et ne peut pas être modifiée.";
                    continue;
                }

                // Basculer vers occupé (réservé)
                $position->update(['vide' => false]);
                $toggled++;
            }

            $message = $toggled > 0 ? "{$toggled} position(s) marquée(s) comme occupées." : 'Aucune position modifiée.';
            if (!empty($errors)) {
                $message .= ' Erreurs: ' . implode(' ', $errors);
            }

            return response()->json([
                'success' => $toggled > 0,
                'message' => $message,
                'toggled' => $toggled,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk toggle positions error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification: ' . $e->getMessage()
            ], 500);
        }
    }
}