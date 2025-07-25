<?php

namespace App\Http\Controllers;

use App\Models\Organisme;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OrganismeController extends Controller
{
    /**
     * Display a listing of the organismes.
     */
    public function index(Request $request)
    {
        $query = Organisme::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nom_org', 'LIKE', "%{$search}%");
        }

        // Get organismes with counts
        $organismes = $query->withCount(['entiteProductrices', 'users'])
                           ->orderBy('nom_org')
                           ->paginate($request->get('per_page', 10))
                           ->withQueryString();

        return view('admin.organismes.index', compact('organismes'));
    }

    /**
     * Show the form for creating a new organisme.
     */
    public function create()
    {
        return view('admin.organismes.create');
    }

    /**
     * Store a newly created organisme in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_org' => 'required|string|max:255|unique:organismes',
        ], [
            'nom_org.required' => 'Le nom de l\'organisme est obligatoire.',
            'nom_org.unique' => 'Ce nom d\'organisme existe déjà.',
        ]);

        Organisme::create($validated);

        return redirect()->route('admin.organismes.index')
                        ->with('success', 'Organisme créé avec succès.');
    }

    /**
     * Display the specified organisme.
     */
    public function show(Organisme $organisme)
    {
        $organisme->load(['entiteProductrices.parent', 'entiteProductrices.children']);
        
        // Get statistics
        $stats = [
            'total_entites' => $organisme->entiteProductrices()->count(),
            'root_entites' => $organisme->entiteProductrices()->roots()->count(),
            'total_users' => $organisme->users()->count(),
            'recent_entites' => $organisme->entiteProductrices()->latest()->limit(5)->get(),
        ];

        return view('admin.organismes.show', compact('organisme', 'stats'));
    }

    /**
     * Show the form for editing the specified organisme.
     */
    public function edit(Organisme $organisme)
    {
        return view('admin.organismes.edit', compact('organisme'));
    }

    /**
     * Update the specified organisme in storage.
     */
    public function update(Request $request, Organisme $organisme)
    {
        $validated = $request->validate([
            'nom_org' => ['required', 'string', 'max:255', Rule::unique('organismes')->ignore($organisme->id)],
        ], [
            'nom_org.required' => 'Le nom de l\'organisme est obligatoire.',
            'nom_org.unique' => 'Ce nom d\'organisme existe déjà.',
        ]);

        $organisme->update($validated);

        return redirect()->route('admin.organismes.index')
                        ->with('success', 'Organisme modifié avec succès.');
    }

    /**
     * Remove the specified organisme from storage.
     */
    public function destroy(Organisme $organisme)
    {
        // Check if organisme has entite productrices
        if ($organisme->entiteProductrices()->count() > 0) {
            return redirect()->route('admin.organismes.index')
                            ->with('error', 'Impossible de supprimer cet organisme car il contient des entités productrices.');
        }

        $organisme->delete();

        return redirect()->route('admin.organismes.index')
                        ->with('success', 'Organisme supprimé avec succès.');
    }

    /**
     * Get organismes for API/AJAX requests.
     */
    public function api(Request $request)
    {
        $query = Organisme::query();
        
        if ($request->filled('search')) {
            $query->where('nom_org', 'LIKE', "%{$request->search}%");
        }
        
        $organismes = $query->select('id', 'nom_org')
                           ->orderBy('nom_org')
                           ->limit(20)
                           ->get();
        
        return response()->json($organismes);
    }

    /**
     * Export organismes to Excel.
     */
    public function export(Request $request)
    {
        $query = Organisme::query();

        if ($request->filled('search')) {
            $query->where('nom_org', 'LIKE', "%{$request->search}%");
        }

        $organismes = $query->withCount(['entiteProductrices', 'users'])
                           ->orderBy('nom_org')
                           ->get();

        $filename = 'organismes_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($organismes) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, ['ID', 'Nom Organisme', 'Nombre Entités', 'Nombre Utilisateurs', 'Date de création'], ';');
            
            foreach ($organismes as $organisme) {
                fputcsv($file, [
                    $organisme->id,
                    $organisme->nom_org,
                    $organisme->entite_productrices_count ?? 0,
                    $organisme->users_count ?? 0,
                    $organisme->created_at->format('d/m/Y H:i:s')
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}