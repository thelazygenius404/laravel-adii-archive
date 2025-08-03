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
        $validated = $request->validate([
            'action' => 'required|in:delete,export,move,optimize',
            'travee_ids' => 'required|array',
            'travee_ids.*' => 'exists:travees,id',
            'new_salle_id' => 'nullable|exists:salles,id'
        ]);

        $traveeIds = $validated['travee_ids'];
        $action = $validated['action'];

        try {
            switch ($action) {
                case 'delete':
                    return $this->bulkDeleteTravees($traveeIds);
                case 'export':
                    return $this->bulkExportTravees($traveeIds);
                case 'move':
                    return $this->bulkMoveTravees($traveeIds, $validated['new_salle_id'] ?? null);
                case 'optimize':
                    return $this->bulkOptimizeTravees($traveeIds);
                default:
                    return response()->json(['success' => false, 'message' => 'Action non reconnue.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Travee bulk action error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'exécution: ' . $e->getMessage()
            ], 500);
        }
    }

    private function bulkDeleteTravees($traveeIds)
    {
        $travees = Travee::whereIn('id', $traveeIds)->get();
        $errors = [];
        $deleted = 0;

        foreach ($travees as $travee) {
            if ($travee->tablettes()->count() > 0) {
                $errors[] = "La travée '{$travee->nom}' contient des tablettes.";
                continue;
            }
            $travee->delete();
            $deleted++;
        }

        return response()->json([
            'success' => $deleted > 0,
            'message' => "{$deleted} travée(s) supprimée(s).",
            'errors' => $errors
        ]);
    }
    private function bulkExportTravees($traveeIds)
    {
        $travees = Travee::whereIn('id', $traveeIds)->get();
        $exportData = $travees->map(function ($travee) {
            return [
                'id' => $travee->id,
                'nom' => $travee->nom,
                'salle' => $travee->salle->nom,
                'organisme' => $travee->salle->organisme->nom,
                'tablettes_count' => $travee->tablettes_count,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $exportData
        ]);
    }
    
    private function bulkMoveTravees($traveeIds, $newSalleId)
    {
        if (!$newSalleId) {
            return response()->json(['success' => false, 'message' => 'ID de la
    nouvelle salle requis.'], 400);
        }
        
        $newSalle = Salle::find($newSalleId);       
        if (!$newSalle) {
            return response()->json(['success' => false, 'message' => 'Salle non trouvée.'], 404);
        }
        
        $travees = Travee::whereIn('id', $traveeIds)->get();
        $moved = 0;
        $errors = [];
        foreach ($travees as $travee) {
            if ($travee->tablettes()->count() > 0) {
                $errors[] = "La travée '{$travee->nom}' contient des tablettes.";
                continue;
            }
            $travee->salle_id = $newSalleId;
            $travee->save();
            $moved++;
        }
        
        return response()->json([
            'success' => $moved > 0,
            'message' => "{$moved} travée(s) déplacée(s) vers la salle '{$newSalle->nom}'.",
            'errors' => $errors
        ]);
    }

    private function bulkOptimizeTravees($traveeIds)
    {
        // Placeholder for optimization logic
        // This could involve re-indexing, cleaning up unused data, etc.
        return response()->json([
            'success' => true,
            'message' => 'Optimisation des travées effectuée avec succès.'
        ]);
    }
}