<?php

namespace App\Http\Controllers;

use App\Models\Tablette;
use App\Models\Travee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        try {
            $validated = $request->validate([
                'action' => 'required|string|in:delete,export,move,optimize',
                'tablette_ids' => 'required|array|min:1',
                'tablette_ids.*' => 'integer|exists:tablettes,id',
                'new_travee_id' => 'nullable|integer|exists:travees,id'
            ], [
                'action.required' => 'L\'action est obligatoire.',
                'action.in' => 'Action non valide.',
                'tablette_ids.required' => 'Au moins une tablette doit être sélectionnée.',
                'tablette_ids.array' => 'Format de données invalide pour les tablettes.',
                'tablette_ids.min' => 'Au moins une tablette doit être sélectionnée.',
                'tablette_ids.*.exists' => 'Une ou plusieurs tablettes sélectionnées n\'existent pas.',
                'new_travee_id.exists' => 'La travée sélectionnée n\'existe pas.'
            ]);

            $tabletteIds = $validated['tablette_ids'];
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
            Log::error('Tablette bulk action error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne du serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete multiple tablettes.
     */
    private function bulkDelete($tabletteIds)
    {
        try {
            $tablettes = Tablette::whereIn('id', $tabletteIds)->get();
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
     * Bulk export specific tablettes.
     */
    private function bulkExport($tabletteIds)
    {
        $tablettes = Tablette::with(['travee.salle.organisme'])
                            ->withCount('positions')
                            ->whereIn('id', $tabletteIds)
                            ->orderBy('nom')
                            ->get();

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
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Bulk move multiple tablettes to a new travee.
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
            $moved = 0;
            $errors = [];

            foreach ($tablettes as $tablette) {
                // Vérifier si la tablette contient des positions occupées
                $positionsOccupees = $tablette->positions()->where('vide', false)->count();
                if ($positionsOccupees > 0) {
                    $errors[] = "La tablette '{$tablette->nom}' contient des positions occupées et nécessite une attention particulière.";
                    // On peut quand même la déplacer mais avec un avertissement
                }

                $tablette->update(['travee_id' => $newTraveeId]);
                $moved++;
            }

            $message = $moved > 0 ? "{$moved} tablette(s) déplacée(s) vers la travée '{$newTravee->nom}'." : 'Aucune tablette déplacée.';
            if (!empty($errors)) {
                $message .= ' Avertissements: ' . implode(' ', $errors);
            }

            return response()->json([
                'success' => $moved > 0,
                'message' => $message,
                'moved' => $moved,
                'warnings' => $errors
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk move tablettes error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du déplacement: ' . $e->getMessage()
            ], 500);
        }
    }
}