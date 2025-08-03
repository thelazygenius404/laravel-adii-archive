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
        $validated = $request->validate([
            'action' => 'required|in:delete,export,move',
            'tablette_ids' => 'required|array',
            'tablette_ids.*' => 'exists:tablettes,id',
            'new_travee_id' => 'nullable|exists:travees,id'
        ]);

        $tabletteIds = $validated['tablette_ids'];
        $action = $validated['action'];

        try {
            switch ($action) {
                case 'delete':
                    return $this->bulkDeleteTablettes($tabletteIds);
                case 'export':
                    return $this->bulkExportTablettes($tabletteIds);
                case 'move':
                    return $this->bulkMoveTablettes($tabletteIds, $validated['new_travee_id'] ?? null);
                default:
                    return response()->json(['success' => false, 'message' => 'Action non reconnue.'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Tablette bulk action error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'exécution: ' . $e->getMessage()
            ], 500);
        }
    }

    private function bulkDeleteTablettes($tabletteIds)
    {
        $tablettes = Tablette::whereIn('id', $tabletteIds)->get();
        $errors = [];
        $deleted = 0;

        foreach ($tablettes as $tablette) {
            if ($tablette->positions()->count() > 0) {
                $errors[] = "La tablette '{$tablette->nom}' contient des positions.";
                continue;
            }
            $tablette->delete();
            $deleted++;
        }

        return response()->json([
            'success' => $deleted > 0,
            'message' => "{$deleted} tablette(s) supprimée(s).",
            'errors' => $errors
        ]);
    }

    private function bulkExportTablettes($tabletteIds)
    {
        $tablettes = Tablette::with(['travee.salle.organisme'])
                            ->withCount('positions')
                            ->whereIn('id', $tabletteIds)
                            ->get();

        $filename = 'tablettes_selection_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function() use ($tablettes) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['Nom', 'Travée', 'Salle', 'Organisme', 'Positions', 'Date'], ';');
            
            foreach ($tablettes as $tablette) {
                fputcsv($file, [
                    $tablette->nom,
                    $tablette->travee->nom,
                    $tablette->travee->salle->nom,
                    $tablette->travee->salle->organisme->nom_org,
                    $tablette->positions_count,
                    $tablette->created_at->format('d/m/Y')
                ], ';');
            }
            fclose($file);
        }, $filename);
    }

    private function bulkMoveTablettes($tabletteIds, $newTraveeId)
    {
        if (!$newTraveeId) {
            return response()->json([
                'success' => false,
                'message' => 'Travée de destination requise.'
            ], 400);
        }

        $updated = Tablette::whereIn('id', $tabletteIds)
                        ->update(['travee_id' => $newTraveeId]);

        return response()->json([
            'success' => true,
            'message' => "{$updated} tablette(s) déplacée(s)."
        ]);
    }
}