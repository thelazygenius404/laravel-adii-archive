<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use App\Models\Organisme;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalleController extends Controller
{
    /**
     * Display a listing of the salles.
     */
    public function index(Request $request)
    {
        $query = Salle::with('organisme');

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by organisme
        if ($request->filled('organisme_id')) {
            $query->where('organisme_id', $request->organisme_id);
        }

        $salles = $query->withCount('travees')
                       ->orderBy('nom')
                       ->paginate($request->get('per_page', 15))
                       ->withQueryString();

        $organismes = Organisme::orderBy('nom_org')->get();

        return view('admin.salles.index', compact('salles', 'organismes'));
    }

    /**
     * Show the form for creating a new salle.
     */
    public function create()
    {
        $organismes = Organisme::orderBy('nom_org')->get();
        
        return view('admin.salles.create', compact('organismes'));
    }

    /**
     * Store a newly created salle in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'capacite_max' => 'required|integer|min:1',
            'organisme_id' => 'required|exists:organismes,id',
        ], [
            'nom.required' => 'Le nom de la salle est obligatoire.',
            'capacite_max.required' => 'La capacité maximale est obligatoire.',
            'capacite_max.integer' => 'La capacité maximale doit être un nombre entier.',
            'capacite_max.min' => 'La capacité maximale doit être d\'au moins 1.',
            'organisme_id.required' => 'L\'organisme est obligatoire.',
            'organisme_id.exists' => 'L\'organisme sélectionné n\'existe pas.',
        ]);

        $validated['capacite_actuelle'] = 0;

        Salle::create($validated);

        return redirect()->route('admin.salles.index')
                        ->with('success', 'Salle créée avec succès.');
    }

    /**
     * Display the specified salle.
     */
    public function show(Salle $salle)
    {
        $salle->load(['organisme', 'travees.tablettes.positions']);
        
        // Get statistics
        $stats = [
            'total_travees' => $salle->travees()->count(),
            'total_tablettes' => $salle->tablettes()->count(),
            'total_positions' => $salle->positions()->count(),
            'positions_occupees' => $salle->positions()->occupied()->count(),
            'positions_libres' => $salle->positions()->available()->count(),
            'utilisation_percentage' => $salle->utilisation_percentage,
            'capacite_restante' => $salle->capacite_restante,
        ];

        return view('admin.salles.show', compact('salle', 'stats'));
    }

    /**
     * Show the form for editing the specified salle.
     */
    public function edit(Salle $salle)
    {
        $organismes = Organisme::orderBy('nom_org')->get();
        
        return view('admin.salles.edit', compact('salle', 'organismes'));
    }

    /**
     * Update the specified salle in storage.
     */
    public function update(Request $request, Salle $salle)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'capacite_max' => 'required|integer|min:1',
            'organisme_id' => 'required|exists:organismes,id',
        ], [
            'nom.required' => 'Le nom de la salle est obligatoire.',
            'capacite_max.required' => 'La capacité maximale est obligatoire.',
            'capacite_max.integer' => 'La capacité maximale doit être un nombre entier.',
            'capacite_max.min' => 'La capacité maximale doit être d\'au moins 1.',
            'organisme_id.required' => 'L\'organisme est obligatoire.',
            'organisme_id.exists' => 'L\'organisme sélectionné n\'existe pas.',
        ]);

        // Check if new capacity is not less than current occupancy
        if ($validated['capacite_max'] < $salle->capacite_actuelle) {
            return back()->withErrors([
                'capacite_max' => 'La capacité maximale ne peut pas être inférieure à la capacité actuelle (' . $salle->capacite_actuelle . ').'
            ])->withInput();
        }

        $salle->update($validated);

        return redirect()->route('admin.salles.index')
                        ->with('success', 'Salle modifiée avec succès.');
    }

    /**
     * Remove the specified salle from storage.
     */
    public function destroy(Salle $salle)
    {
        // Check if salle has travées
        if ($salle->travees()->count() > 0) {
            return redirect()->route('admin.salles.index')
                            ->with('error', 'Impossible de supprimer cette salle car elle contient des travées.');
        }

        $salle->delete();

        return redirect()->route('admin.salles.index')
                        ->with('success', 'Salle supprimée avec succès.');
    }

    /**
     * Update capacity of the salle.
     */
    public function updateCapacity(Salle $salle)
    {
        $salle->updateCapaciteActuelle();
        
        return response()->json([
            'success' => true,
            'capacite_actuelle' => $salle->fresh()->capacite_actuelle,
            'utilisation_percentage' => $salle->fresh()->utilisation_percentage,
        ]);
    }

    /**
     * Get salles by organisme for API/AJAX requests.
     */
    public function byOrganisme(Request $request, $organismeId)
    {
        $salles = Salle::where('organisme_id', $organismeId)
                      ->select('id', 'nom', 'capacite_max', 'capacite_actuelle')
                      ->orderBy('nom')
                      ->get();
        
        return response()->json($salles);
    }

    /**
     * Export salles to CSV.
     */
    public function export(Request $request)
    {
        $query = Salle::with('organisme');

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('organisme_id')) {
            $query->where('organisme_id', $request->organisme_id);
        }

        $salles = $query->withCount('travees')->orderBy('nom')->get();

        $filename = 'salles_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($salles) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, [
                'ID',
                'Nom',
                'Organisme',
                'Capacité Maximale',
                'Capacité Actuelle',
                'Utilisation (%)',
                'Nombre de Travées',
                'Date de Création'
            ], ';');
            
            foreach ($salles as $salle) {
                fputcsv($file, [
                    $salle->id,
                    $salle->nom,
                    $salle->organisme->nom_org,
                    $salle->capacite_max,
                    $salle->capacite_actuelle,
                    $salle->utilisation_percentage,
                    $salle->travees_count,
                    $salle->created_at->format('d/m/Y H:i:s')
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total_salles' => Salle::count(),
            'capacite_totale' => Salle::sum('capacite_max'),
            'capacite_utilisee' => Salle::sum('capacite_actuelle'),
            'utilisation_moyenne' => Salle::avg('capacite_actuelle'),
            'salles_pleines' => Salle::whereRaw('capacite_actuelle >= capacite_max')->count(),
            'salles_par_organisme' => Salle::with('organisme')
                                          ->selectRaw('organisme_id, count(*) as count')
                                          ->groupBy('organisme_id')
                                          ->get(),
        ];

        return response()->json($stats);
    }
}