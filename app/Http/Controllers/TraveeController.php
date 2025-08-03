<?php

namespace App\Http\Controllers;

use App\Models\Travee;
use App\Models\Salle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        try {
            $validated = $request->validate([
                'action' => 'required|string|in:delete,export,move,optimize',
                'travee_ids' => 'required|array|min:1',
                'travee_ids.*' => 'integer|exists:travees,id',
                'new_salle_id' => 'nullable|integer|exists:salles,id'
            ], [
                'action.required' => 'L\'action est obligatoire.',
                'action.in' => 'Action non valide.',
                'travee_ids.required' => 'Au moins une travée doit être sélectionnée.',
                'travee_ids.array' => 'Format de données invalide pour les travées.',
                'travee_ids.min' => 'Au moins une travée doit être sélectionnée.',
                'travee_ids.*.exists' => 'Une ou plusieurs travées sélectionnées n\'existent pas.',
                'new_salle_id.exists' => 'La salle sélectionnée n\'existe pas.'
            ]);

            $traveeIds = $validated['travee_ids'];
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
            Log::error('Travee bulk action error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur interne du serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete multiple travees.
     */
    private function bulkDelete($traveeIds)
    {
        try {
            $travees = Travee::whereIn('id', $traveeIds)->get();
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
        $travees = Travee::with(['salle.organisme'])
                        ->withCount(['tablettes', 'positions'])
                        ->whereIn('id', $traveeIds)
                        ->orderBy('nom')
                        ->get();

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
                'Nombre Positions',
                'Utilisation (%)',
                'Date de création'
            ], ';');
            
            foreach ($travees as $travee) {
                $utilisation = $travee->positions_count > 0 ? 
                             ($travee->positions_occupees / $travee->positions_count) * 100 : 0;
                
                fputcsv($file, [
                    $travee->id,
                    $travee->nom,
                    $travee->salle->nom,
                    $travee->salle->organisme->nom_org,
                    $travee->tablettes_count ?? 0,
                    $travee->positions_count ?? 0,
                    number_format($utilisation, 1),
                    $travee->created_at->format('d/m/Y H:i:s')
                ], ';');
            }
            
            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Bulk move multiple travees to a new salle.
     */
    private function bulkMove($traveeIds, $newSalleId)
    {
        if (!$newSalleId) {
            return response()->json([
                'success' => false,
                'message' => 'Salle de destination requise pour le déplacement.'
            ], 400);
        }

        try {
            $newSalle = Salle::find($newSalleId);
            if (!$newSalle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Salle de destination introuvable.'
                ], 404);
            }

            $travees = Travee::whereIn('id', $traveeIds)->get();
            $moved = 0;
            $errors = [];

            foreach ($travees as $travee) {
                // Vérifier la capacité de la nouvelle salle
                $positionsCount = $travee->positions()->count();
                if (($newSalle->capacite_actuelle + $positionsCount) > $newSalle->capacite_max) {
                    $errors[] = "La travée '{$travee->nom}' ne peut pas être déplacée - capacité insuffisante dans la salle de destination.";
                    continue;
                }

                $travee->update(['salle_id' => $newSalleId]);
                $moved++;
            }

            // Mettre à jour les capacités des salles
            if ($moved > 0) {
                $newSalle->updateCapaciteActuelle();
                
                // Mettre à jour les anciennes salles
                $oldSalles = Salle::whereIn('id', $travees->pluck('salle_id')->unique())->get();
                foreach ($oldSalles as $oldSalle) {
                    $oldSalle->updateCapaciteActuelle();
                }
            }

            $message = $moved > 0 ? "{$moved} travée(s) déplacée(s) vers la salle '{$newSalle->nom}'." : 'Aucune travée déplacée.';
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
            Log::error('Bulk move travees error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du déplacement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk optimize multiple travees.
     */
    private function bulkOptimize($traveeIds)
    {
        try {
            $travees = Travee::whereIn('id', $traveeIds)->get();
            $optimized = 0;

            foreach ($travees as $travee) {
                // Optimisation: mise à jour des compteurs et nettoyage
                $travee->tablettes()->each(function ($tablette) {
                    $tablette->positions()->each(function ($position) {
                        // Vérifier la cohérence des statuts
                        if (!$position->vide && !$position->boite) {
                            $position->update(['vide' => true]);
                        }
                    });
                });
                
                $optimized++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$optimized} travée(s) optimisée(s) avec succès.",
                'optimized' => $optimized
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk optimize travees error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'optimisation: ' . $e->getMessage()
            ], 500);
        }
    }
}