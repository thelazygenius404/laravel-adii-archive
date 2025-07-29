<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\Tablette;
use Illuminate\Http\Request;

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
            'tablette_id' => 'required|exists:tablettes,id',
            'nombre_positions' => 'required|integer|min:1|max:100',
            'prefix' => 'required|string|max:50',
        ], [
            'tablette_id.required' => 'La tablette est obligatoire.',
            'nombre_positions.required' => 'Le nombre de positions est obligatoire.',
            'nombre_positions.max' => 'Vous ne pouvez pas créer plus de 100 positions à la fois.',
            'prefix.required' => 'Le préfixe est obligatoire.',
        ]);

        $tablette = Tablette::find($validated['tablette_id']);
        $created = 0;

        for ($i = 1; $i <= $validated['nombre_positions']; $i++) {
            $nom = $validated['prefix'] . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            
            // Check if position name already exists for this tablette
            if (!Position::where('tablette_id', $tablette->id)->where('nom', $nom)->exists()) {
                Position::create([
                    'nom' => $nom,
                    'vide' => true,
                    'tablette_id' => $tablette->id,
                ]);
                $created++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$created} position(s) créée(s) avec succès.",
            'created' => $created
        ]);
    }
}