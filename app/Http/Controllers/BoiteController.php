<?php

namespace App\Http\Controllers;

use App\Models\Boite;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BoiteController extends Controller
{
    /**
     * Display a listing of the boites.
     */
    /**
 * Display a listing of the boites.
 */
public function index(Request $request)
{
    $query = Boite::with(['position.tablette.travee.salle.organisme']);

    if ($request->filled('search')) {
        $query->search($request->search);
    }

    if ($request->filled('status')) {
        if ($request->status === 'active') {
            $query->active();
        } elseif ($request->status === 'destroyed') {
            $query->destroyed();
        }
    }

    if ($request->filled('organisme_id')) {
        $query->whereHas('position.tablette.travee.salle', function ($q) use ($request) {
            $q->where('organisme_id', $request->organisme_id);
        });
    }

    $boites = $query->withCount('dossiers')
                   ->orderBy('numero')
                   ->paginate($request->get('per_page', 15))
                   ->withQueryString();

    // Fetch available positions for the dropdown
    $positions = Position::with(['tablette.travee.salle.organisme'])
                        ->available()
                        ->orderBy('nom')
                        ->get();

    return view('admin.boites.index', compact('boites', 'positions'));
}

    /**
     * Show the form for creating a new boite.
     */
    public function create()
    {
        $positions = Position::with(['tablette.travee.salle.organisme'])
                            ->available()
                            ->orderBy('nom')
                            ->get();
        
        // Generate next boite number
        $lastBoite = Boite::orderBy('numero', 'desc')->first();
        $nextNumber = $this->generateNextNumber($lastBoite);

        return view('admin.boites.create', compact('positions', 'nextNumber'));
    }

    /**
     * Store a newly created boite in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|string|max:255|unique:boites',
            'code_thematique' => 'nullable|string|max:255',
            'code_topo' => 'nullable|string|max:255',
            'capacite' => 'required|integer|min:1',
            'position_id' => 'required|exists:positions,id',
        ], [
            'numero.required' => 'Le numéro de la boîte est obligatoire.',
            'numero.unique' => 'Ce numéro de boîte existe déjà.',
            'capacite.required' => 'La capacité est obligatoire.',
            'capacite.min' => 'La capacité doit être d\'au moins 1.',
            'position_id.required' => 'La position est obligatoire.',
        ]);

        // Check if position is available
        $position = Position::find($validated['position_id']);
        if (!$position->vide) {
            return back()->withErrors([
                'position_id' => 'Cette position est déjà occupée.'
            ])->withInput();
        }

        $validated['nbr_dossiers'] = 0;
        $validated['detruite'] = false;

        $boite = Boite::create($validated);

        // Mark position as occupied
        $position->markAsOccupied();

        return redirect()->route('admin.boites.index')
                        ->with('success', 'Boîte créée avec succès.');
    }

    /**
     * Display the specified boite.
     */
    public function show(Boite $boite)
    {
        $boite->load([
            'position.tablette.travee.salle.organisme',
            'dossiers.calendrierConservation'
        ]);

        $stats = [
            'total_dossiers' => $boite->dossiers()->count(),
            'dossiers_actifs' => $boite->dossiers()->byStatus('actif')->count(),
            'dossiers_archives' => $boite->dossiers()->byStatus('archive')->count(),
            'dossiers_elimines' => $boite->dossiers()->byStatus('elimine')->count(),
            'utilisation_percentage' => $boite->utilisation_percentage,
            'capacite_restante' => $boite->capacite_restante,
        ];

        return view('admin.boites.show', compact('boite', 'stats'));
    }

    /**
     * Show the form for editing the specified boite.
     */
    public function edit(Boite $boite)
    {
        $positions = Position::with(['tablette.travee.salle.organisme'])
                            ->where(function($query) use ($boite) {
                                $query->available()->orWhere('id', $boite->position_id);
                            })
                            ->orderBy('nom')
                            ->get();

        return view('admin.boites.edit', compact('boite', 'positions'));
    }

    /**
     * Update the specified boite in storage.
     */
    public function update(Request $request, Boite $boite)
    {
        $validated = $request->validate([
            'numero' => ['required', 'string', 'max:255', Rule::unique('boites')->ignore($boite->id)],
            'code_thematique' => 'nullable|string|max:255',
            'code_topo' => 'nullable|string|max:255',
            'capacite' => 'required|integer|min:1',
            'position_id' => 'required|exists:positions,id',
        ], [
            'numero.required' => 'Le numéro de la boîte est obligatoire.',
            'numero.unique' => 'Ce numéro de boîte existe déjà.',
            'capacite.required' => 'La capacité est obligatoire.',
            'capacite.min' => 'La capacité doit être d\'au moins 1.',
            'position_id.required' => 'La position est obligatoire.',
        ]);

        // Check if new capacity is not less than current dossiers count
        if ($validated['capacite'] < $boite->nbr_dossiers) {
            return back()->withErrors([
                'capacite' => 'La capacité ne peut pas être inférieure au nombre de dossiers actuels (' . $boite->nbr_dossiers . ').'
            ])->withInput();
        }

        $oldPositionId = $boite->position_id;

        // Check if new position is available (if position changed)
        if ($validated['position_id'] != $oldPositionId) {
            $newPosition = Position::find($validated['position_id']);
            if (!$newPosition->vide) {
                return back()->withErrors([
                    'position_id' => 'Cette position est déjà occupée.'
                ])->withInput();
            }
        }

        $boite->update($validated);

        // Update positions if changed
        if ($validated['position_id'] != $oldPositionId) {
            Position::find($oldPositionId)->markAsFree();
            Position::find($validated['position_id'])->markAsOccupied();
        }

        return redirect()->route('admin.boites.index')
                        ->with('success', 'Boîte modifiée avec succès.');
    }

    /**
     * Remove the specified boite from storage (physical deletion).
     */
    public function destroy(Boite $boite)
    {
        // Check if boite has dossiers
        if ($boite->dossiers()->count() > 0) {
            return redirect()->route('admin.boites.index')
                            ->with('error', 'Impossible de supprimer cette boîte car elle contient des dossiers.');
        }

        $positionId = $boite->position_id;
        
        $boite->delete();

        // Mark position as free
        Position::find($positionId)->markAsFree();

        return redirect()->route('admin.boites.index')
                        ->with('success', 'Boîte supprimée avec succès.');
    }

    /**
     * Mark boite as destroyed (logical deletion).
     */
    public function destroyBox(Boite $boite)
    {
        try {
            $boite->markAsDestroyed();
            
            return response()->json([
                'success' => true,
                'message' => 'Boîte marquée comme détruite avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la destruction de la boîte: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore a destroyed boite.
     */
    public function restoreBox(Boite $boite)
    {
        try {
            $boite->restoreFromDestroyed();
            
            return response()->json([
                'success' => true,
                'message' => 'Boîte restaurée avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la restauration: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get boites by position for API/AJAX requests.
     */
    public function byPosition(Request $request, $positionId)
    {
        $boite = Boite::where('position_id', $positionId)
                     ->with('dossiers')
                     ->first();
        
        return response()->json($boite);
    }

    /**
     * Export boites to CSV.
     */
    public function export(Request $request)
    {
        $query = Boite::with(['position.tablette.travee.salle.organisme']);

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'destroyed') {
                $query->destroyed();
            }
        }

        if ($request->filled('organisme_id')) {
            $query->whereHas('position.tablette.travee.salle', function ($q) use ($request) {
                $q->where('organisme_id', $request->organisme_id);
            });
        }

        $boites = $query->withCount('dossiers')->orderBy('numero')->get();

        $filename = 'boites_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($boites) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, [
                'Numéro',
                'Code Thématique',
                'Code Topographique',
                'Capacité',
                'Nombre Dossiers',
                'Utilisation (%)',
                'Localisation Complète',
                'Organisme',
                'Salle',
                'Statut',
                'Date de Création'
            ], ';');
            
            foreach ($boites as $boite) {
                fputcsv($file, [
                    $boite->numero,
                    $boite->code_thematique,
                    $boite->code_topo,
                    $boite->capacite,
                    $boite->nbr_dossiers,
                    $boite->utilisation_percentage,
                    $boite->full_location,
                    $boite->position->tablette->travee->salle->organisme->nom_org,
                    $boite->position->tablette->travee->salle->nom,
                    $boite->detruite ? 'Détruite' : 'Active',
                    $boite->created_at->format('d/m/Y H:i:s')
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk operations on multiple boites.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,destroy,export,move',
            'boite_ids' => 'required|array',
            'boite_ids.*' => 'exists:boites,id',
            'new_position_id' => 'nullable|exists:positions,id',
        ]);

        $boiteIds = $validated['boite_ids'];
        $action = $validated['action'];

        switch ($action) {
            case 'delete':
                return $this->bulkDelete($boiteIds);
            
            case 'destroy':
                return $this->bulkDestroy($boiteIds);
            
            case 'export':
                return $this->bulkExport($boiteIds);
            
            case 'move':
                return $this->bulkMove($boiteIds, $validated['new_position_id'] ?? null);
            
            default:
                return response()->json(['success' => false, 'message' => 'Action non reconnue.'], 400);
        }
    }

    /**
     * Bulk delete multiple boites (physical deletion).
     */
    private function bulkDelete($boiteIds)
    {
        $boites = Boite::whereIn('id', $boiteIds)->get();
        $deleted = 0;
        $errors = [];

        foreach ($boites as $boite) {
            if ($boite->dossiers()->count() > 0) {
                $errors[] = "La boîte '{$boite->numero}' contient des dossiers.";
                continue;
            }

            $positionId = $boite->position_id;
            $boite->delete();
            Position::find($positionId)->markAsFree();
            $deleted++;
        }

        $message = $deleted > 0 ? "{$deleted} boîte(s) supprimée(s) avec succès." : '';
        if (!empty($errors)) {
            $message .= ' Erreurs: ' . implode(' ', $errors);
        }

        return redirect()->route('admin.boites.index')
                        ->with($deleted > 0 ? 'success' : 'error', $message);
    }

    /**
     * Bulk destroy multiple boites (logical deletion).
     */
    private function bulkDestroy($boiteIds)
    {
        $boites = Boite::whereIn('id', $boiteIds)->get();
        $destroyed = 0;
        $errors = [];

        foreach ($boites as $boite) {
            try {
                $boite->markAsDestroyed();
                $destroyed++;
            } catch (\Exception $e) {
                $errors[] = "Erreur avec la boîte '{$boite->numero}': " . $e->getMessage();
            }
        }

        $message = $destroyed > 0 ? "{$destroyed} boîte(s) marquée(s) comme détruites." : '';
        if (!empty($errors)) {
            $message .= ' Erreurs: ' . implode(' ', $errors);
        }

        return redirect()->route('admin.boites.index')
                        ->with($destroyed > 0 ? 'success' : 'error', $message);
    }

    /**
     * Bulk export specific boites.
     */
    private function bulkExport($boiteIds)
    {
        $boites = Boite::with(['position.tablette.travee.salle.organisme'])
                      ->withCount('dossiers')
                      ->whereIn('id', $boiteIds)
                      ->orderBy('numero')
                      ->get();

        $filename = 'boites_selection_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($boites) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, [
                'Numéro',
                'Code Thématique',
                'Code Topographique',
                'Capacité',
                'Nombre Dossiers',
                'Utilisation (%)',
                'Localisation Complète',
                'Organisme',
                'Statut'
            ], ';');
            
            foreach ($boites as $boite) {
                fputcsv($file, [
                    $boite->numero,
                    $boite->code_thematique,
                    $boite->code_topo,
                    $boite->capacite,
                    $boite->nbr_dossiers,
                    $boite->utilisation_percentage,
                    $boite->full_location,
                    $boite->position->tablette->travee->salle->organisme->nom_org,
                    $boite->detruite ? 'Détruite' : 'Active'
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk move multiple boites.
     */
    private function bulkMove($boiteIds, $newPositionId)
    {
        if (!$newPositionId) {
            return response()->json([
                'success' => false,
                'message' => 'Position de destination requise pour le déplacement.'
            ], 400);
        }

        // This would require a more complex implementation for moving multiple boites
        // For now, we'll return an error suggesting individual moves
        return response()->json([
            'success' => false,
            'message' => 'Le déplacement en lot n\'est pas encore supporté. Veuillez déplacer les boîtes individuellement.'
        ], 400);
    }

    /**
     * Generate next boite number.
     */
    private function generateNextNumber($lastBoite)
    {
        if (!$lastBoite) {
            return 'BOX-0001';
        }
        
        // Extract number from last boite (assuming format like BOX-0001, BOX-0002, etc.)
        $lastNumber = (int) substr($lastBoite->numero, 4);
        $nextNumber = $lastNumber + 1;
        
        return 'BOX-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get boites with low occupancy for optimization.
     */
    public function lowOccupancy(Request $request)
    {
        $threshold = $request->get('threshold', 50); // Default 50% threshold
        
        $boites = Boite::active()
                      ->with(['position.tablette.travee.salle.organisme'])
                      ->whereRaw("(nbr_dossiers * 100 / capacite) < ?", [$threshold])
                      ->orderBy('numero')
                      ->get();

        return response()->json($boites->map(function ($boite) {
            return [
                'id' => $boite->id,
                'numero' => $boite->numero,
                'capacite' => $boite->capacite,
                'nbr_dossiers' => $boite->nbr_dossiers,
                'utilisation_percentage' => $boite->utilisation_percentage,
                'localisation' => $boite->full_location,
                'organisme' => $boite->position->tablette->travee->salle->organisme->nom_org,
            ];
        }));
    }

    /**
     * Get statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total_boites' => Boite::count(),
            'boites_actives' => Boite::active()->count(),
            'boites_detruites' => Boite::destroyed()->count(),
            'capacite_totale' => Boite::active()->sum('capacite'),
            'dossiers_stockes' => Boite::active()->sum('nbr_dossiers'),
            'utilisation_moyenne' => Boite::active()->avg('nbr_dossiers'),
            'boites_pleines' => Boite::active()->whereRaw('nbr_dossiers >= capacite')->count(),
            'boites_vides' => Boite::active()->where('nbr_dossiers', 0)->count(),
            'boites_faible_occupation' => Boite::active()->whereRaw('(nbr_dossiers * 100 / capacite) < 30')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Find available space for new dossiers.
     */
    public function findAvailableSpace(Request $request)
    {
        $nombreDossiers = $request->get('nombre_dossiers', 1);
        $organismeId = $request->get('organisme_id');

        $query = Boite::active()->whereRaw('nbr_dossiers < capacite');

        if ($organismeId) {
            $query->whereHas('position.tablette.travee.salle', function ($q) use ($organismeId) {
                $q->where('organisme_id', $organismeId);
            });
        }

        $boites = $query->with(['position.tablette.travee.salle.organisme'])
                       ->orderByRaw('(capacite - nbr_dossiers) DESC')
                       ->limit(10)
                       ->get();

        return response()->json($boites->map(function ($boite) use ($nombreDossiers) {
            $espaceDisponible = $boite->capacite_restante;
            return [
                'id' => $boite->id,
                'numero' => $boite->numero,
                'localisation' => $boite->full_location,
                'capacite_restante' => $espaceDisponible,
                'peut_accueillir' => $espaceDisponible >= $nombreDossiers,
                'organisme' => $boite->position->tablette->travee->salle->organisme->nom_org,
            ];
        }));
    }
}