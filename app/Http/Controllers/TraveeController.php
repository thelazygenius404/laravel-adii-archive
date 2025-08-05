<?php

namespace App\Http\Controllers;

use App\Models\Travee;
use App\Models\Salle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TraveeController extends Controller
{
    /**
     * Display a listing of the travees.
     */
    public function index(Request $request)
    {
        $query = Travee::with(['salle.organisme']);

        if ($request->filled('search')) {
            $query->where('nom', 'LIKE', "%{$request->search}%");
        }

        if ($request->filled('salle_id')) {
            $query->where('salle_id', $request->salle_id);
        }

        $travees = $query->withCount('tablettes')
                        ->orderBy('nom')
                        ->paginate($request->get('per_page', 15))
                        ->withQueryString();

        $salles = Salle::with('organisme')->orderBy('nom')->get();

        return view('admin.travees.index', compact('travees', 'salles'));
    }

    /**
     * Show the form for creating a new travee.
     */
    public function create()
    {
        $salles = Salle::with('organisme')->orderBy('nom')->get();
        return view('admin.travees.create', compact('salles'));
    }

    /**
     * Store a newly created travee in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'salle_id' => 'required|exists:salles,id',
        ], [
            'nom.required' => 'Le nom de la travée est obligatoire.',
            'salle_id.required' => 'La salle est obligatoire.',
        ]);

        Travee::create($validated);

        return redirect()->route('admin.travees.index')
                        ->with('success', 'Travée créée avec succès.');
    }

    /**
     * Display the specified travee.
     */
    public function show(Travee $travee)
    {
        $travee->load(['salle.organisme', 'tablettes.positions']);
        
        $stats = [
            'total_tablettes' => $travee->tablettes()->count(),
            'total_positions' => $travee->positions()->count(),
            'positions_occupees' => $travee->positions_occupees,
            'utilisation_percentage' => $travee->utilisation_percentage,
        ];

        return view('admin.travees.show', compact('travee', 'stats'));
    }

    /**
     * Show the form for editing the specified travee.
     */
    public function edit(Travee $travee)
    {
        $salles = Salle::with('organisme')->orderBy('nom')->get();
        return view('admin.travees.edit', compact('travee', 'salles'));
    }

    /**
     * Update the specified travee in storage.
     */
    public function update(Request $request, Travee $travee)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'salle_id' => 'required|exists:salles,id',
        ], [
            'nom.required' => 'Le nom de la travée est obligatoire.',
            'salle_id.required' => 'La salle est obligatoire.',
        ]);

        $travee->update($validated);

        return redirect()->route('admin.travees.index')
                        ->with('success', 'Travée modifiée avec succès.');
    }

    /**
     * Remove the specified travee from storage.
     */
    public function destroy(Travee $travee)
    {
        if ($travee->tablettes()->count() > 0) {
            return redirect()->route('admin.travees.index')
                            ->with('error', 'Impossible de supprimer cette travée car elle contient des tablettes.');
        }

        $travee->delete();

        return redirect()->route('admin.travees.index')
                        ->with('success', 'Travée supprimée avec succès.');
    }

    /**
     * Get travees by salle for API/AJAX requests.
     */
    public function bySalle(Request $request, $salleId)
    {
        $travees = Travee::where('salle_id', $salleId)
                        ->select('id', 'nom')
                        ->withCount('tablettes')
                        ->orderBy('nom')
                        ->get();
        
        return response()->json($travees);
    }
       
    public function bulkAction(Request $request)
    {
        // Log de debug détaillé
        Log::info('=== TRAVEE BULK ACTION DEBUG ===', [
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'all_data' => $request->all(),
            'files' => $request->files->all(),
        ]);

        try {
            // CORRECTION 1: Validation plus permissive
            $validated = $request->validate([
                'action' => 'required|string|in:delete,export,move,optimize',
                'travee_ids' => 'required', // Accepter string ou array
                'new_salle_id' => 'nullable|integer|exists:salles,id'
            ], [
                'action.required' => 'L\'action est obligatoire.',
                'action.in' => 'Action non valide. Actions autorisées: delete, export, move, optimize',
                'travee_ids.required' => 'Au moins une travée doit être sélectionnée.',
                'new_salle_id.exists' => 'La salle sélectionnée n\'existe pas.'
            ]);

            Log::info('Validation réussie:', $validated);

            // CORRECTION 2: Gestion flexible des IDs
            $traveeIds = $validated['travee_ids'];
            
            // Si c'est une string JSON, la décoder
            if (is_string($traveeIds)) {
                $traveeIds = json_decode($traveeIds, true);
                Log::info('IDs décodés depuis JSON:', ['ids' => $traveeIds]);
            }
            
            // Vérifier que c'est un array valide
            if (!is_array($traveeIds) || empty($traveeIds)) {
                Log::error('IDs invalides:', ['travee_ids' => $validated['travee_ids']]);
                return response()->json([
                    'success' => false,
                    'message' => 'Format des IDs de travées invalide. Reçu: ' . gettype($validated['travee_ids'])
                ], 400);
            }

            // CORRECTION 3: Validation des IDs numériques
            $traveeIds = array_filter($traveeIds, function($id) {
                return is_numeric($id) && $id > 0;
            });

            if (empty($traveeIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun ID de travée valide fourni.'
                ], 400);
            }

            Log::info('IDs traités:', ['travee_ids' => $traveeIds]);

            $action = $validated['action'];

            // Exécuter l'action
            switch ($action) {
                case 'export':
                    return $this->bulkExport($traveeIds);
                
                case 'delete':
                    return $this->bulkDelete($traveeIds);
                
                case 'move':
                    return $this->bulkMove($traveeIds, $validated['new_salle_id'] ?? null);
                
                case 'optimize':
                    return $this->bulkOptimize($traveeIds);
                
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
            Log::error('Travee bulk action error: ' . $e->getMessage(), [
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

    // ... autres méthodes privées (bulkDelete, bulkExport, etc.) restent identiques
    
    /**
     * Bulk delete multiple travees.
     */
    private function bulkDelete($traveeIds)
    {
        try {
            Log::info('Bulk delete travees', ['ids' => $traveeIds]);
            
            $travees = Travee::whereIn('id', $traveeIds)->get();
            
            if ($travees->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune travée trouvée avec les IDs fournis.'
                ], 404);
            }
            
            $errors = [];
            $deleted = 0;

            foreach ($travees as $travee) {
                // Vérifier si la travée contient des tablettes
                if ($travee->tablettes()->count() > 0) {
                    $errors[] = "La travée '{$travee->nom}' contient des tablettes et ne peut pas être supprimée.";
                    continue;
                }

                $travee->delete();
                $deleted++;
            }

            $message = $deleted > 0 ? "{$deleted} travée(s) supprimée(s) avec succès." : 'Aucune travée supprimée.';
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
            Log::error('Bulk delete travees error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk export specific travees.
     */
    private function bulkExport($traveeIds)
    {
        try {
            Log::info('Bulk export travees', ['ids' => $traveeIds]);
            
            $travees = Travee::with(['salle.organisme'])
                            ->withCount(['tablettes'])
                            ->whereIn('id', $traveeIds)
                            ->orderBy('nom')
                            ->get();

            if ($travees->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune travée trouvée pour l\'export.'
                ], 404);
            }

            $filename = 'travees_selection_' . date('Y-m-d_H-i-s') . '.csv';

            return response()->streamDownload(function() use ($travees) {
                $file = fopen('php://output', 'w');
                
                // Add UTF-8 BOM for proper Excel encoding
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // CSV headers
                fputcsv($file, [
                    'ID',
                    'Nom',
                    'Salle',
                    'Organisme',
                    'Nombre Tablettes',
                    'Date de création'
                ], ';');
                
                foreach ($travees as $travee) {
                    fputcsv($file, [
                        $travee->id,
                        $travee->nom,
                        $travee->salle->nom,
                        $travee->salle->organisme->nom_org,
                        $travee->tablettes_count ?? 0,
                        $travee->created_at->format('d/m/Y H:i:s')
                    ], ';');
                }
                
                fclose($file);
            }, $filename, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"'
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk export travees error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'export: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk move (version simplifiée)
     */
     private function bulkMove($traveeIds, $newSalleId)
    {
        try {
            // Validate new salle ID
            if (!$newSalleId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salle de destination requise pour le déplacement.'
                ], 400);
            }

            // Check if the new salle exists
            $newSalle = Salle::with('organisme')->find($newSalleId);
            if (!$newSalle) {
                return response()->json([
                    'success' => false,
                    'message' => 'La salle de destination n\'existe pas.'
                ], 404);
            }

            // Get travees to move
            $travees = Travee::with('salle')->whereIn('id', $traveeIds)->get();
            
            if ($travees->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune travée trouvée avec les IDs fournis.'
                ], 404);
            }

            $moved = 0;
            $errors = [];
            $movedTravees = [];

            // Use database transaction for data integrity
            DB::beginTransaction();

            foreach ($travees as $travee) {
                try {
                    // Skip if travee is already in the target salle
                    if ($travee->salle_id == $newSalleId) {
                        $errors[] = "La travée '{$travee->nom}' est déjà dans la salle '{$newSalle->nom}'.";
                        continue;
                    }

                    // Check for naming conflicts in the new salle
                    $existingTravee = Travee::where('salle_id', $newSalleId)
                                           ->where('nom', $travee->nom)
                                           ->where('id', '!=', $travee->id)
                                           ->first();
                    
                    if ($existingTravee) {
                        $errors[] = "Une travée nommée '{$travee->nom}' existe déjà dans la salle '{$newSalle->nom}'.";
                        continue;
                    }

                    // Store old salle info for logging
                    $oldSalle = $travee->salle;
                    
                    // Update the travee's salle
                    $travee->update(['salle_id' => $newSalleId]);
                    
                    $moved++;
                    $movedTravees[] = [
                        'id' => $travee->id,
                        'nom' => $travee->nom,
                        'old_salle' => $oldSalle->nom,
                        'new_salle' => $newSalle->nom,
                        'tablettes_count' => $travee->tablettes()->count()
                    ];

                } catch (\Exception $e) {
                    Log::error("Error moving travee {$travee->id}: " . $e->getMessage());
                    $errors[] = "Erreur lors du déplacement de la travée '{$travee->nom}': " . $e->getMessage();
                    continue;
                }
            }

            // Commit the transaction if at least one travee was moved
            if ($moved > 0) {
                DB::commit();
                
                // Log the successful moves
                Log::info("Bulk move travees completed", [
                    'moved_count' => $moved,
                    'target_salle_id' => $newSalleId,
                    'target_salle_name' => $newSalle->nom,
                    'moved_travees' => $movedTravees
                ]);
            } else {
                DB::rollback();
            }

            // Prepare response message
            $message = '';
            if ($moved > 0) {
                $message = "{$moved} travée(s) déplacée(s) avec succès vers la salle '{$newSalle->nom}' ({$newSalle->organisme->nom_org}).";
                
                // Add details about moved travees if not too many
                if (count($movedTravees) <= 3) {
                    $traveeNames = collect($movedTravees)->pluck('nom')->implode(', ');
                    $message .= " Travées déplacées: {$traveeNames}.";
                }
            } else {
                $message = 'Aucune travée n\'a pu être déplacée.';
            }

            if (!empty($errors)) {
                $message .= ' Erreurs rencontrées: ' . implode(' ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= " (et " . (count($errors) - 3) . " autres erreurs)";
                }
            }

            return response()->json([
                'success' => $moved > 0,
                'message' => $message,
                'moved' => $moved,
                'errors' => $errors,
                'details' => [
                    'target_salle' => [
                        'id' => $newSalle->id,
                        'nom' => $newSalle->nom,
                        'organisme' => $newSalle->organisme->nom_org ?? 'N/A'
                    ],
                    'moved_travees' => $movedTravees,
                    'total_requested' => count($traveeIds),
                    'total_moved' => $moved,
                    'total_errors' => count($errors)
                ]
            ]);

        } catch (\Exception $e) {
            // Rollback transaction on any major error
            DB::rollback();
            
            Log::error('Bulk move travees failed: ' . $e->getMessage(), [
                'travee_ids' => $traveeIds,
                'new_salle_id' => $newSalleId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du déplacement des travées: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk optimize (version simplifiée)
     */
    private function bulkOptimize($traveeIds)
    {
        // Version simplifiée pour test
        return response()->json([
            'success' => true,
            'message' => 'Optimisation simulée réussie pour ' . count($traveeIds) . ' travée(s).',
            'optimized' => count($traveeIds)
        ]);
    }

}