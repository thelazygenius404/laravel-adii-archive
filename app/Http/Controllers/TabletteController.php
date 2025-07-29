<?php

namespace App\Http\Controllers;

use App\Models\Tablette;
use App\Models\Travee;
use Illuminate\Http\Request;

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
}