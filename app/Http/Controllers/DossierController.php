<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\Boite;
use App\Models\CalendrierConservation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DossierController extends Controller
{
    /**
     * Display a listing of the dossiers.
     */
    public function index(Request $request)
    {
        $query = Dossier::with(['boite.position.tablette.travee.salle', 'calendrierConservation']);

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by status
        if ($request->filled('statut')) {
            $query->byStatus($request->statut);
        }

        // Filter by boite
        if ($request->filled('boite_id')) {
            $query->where('boite_id', $request->boite_id);
        }

        // Filter by conservation rule
        if ($request->filled('calendrier_conservation_id')) {
            $query->where('calendrier_conservation_id', $request->calendrier_conservation_id);
        }

        // Filter by elimination date
        if ($request->filled('elimination_filter')) {
            switch ($request->elimination_filter) {
                case 'due':
                    $query->dueForElimination();
                    break;
                case 'near':
                    $query->nearElimination(30);
                    break;
            }
        }

        $dossiers = $query->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 15))
                         ->withQueryString();

        $boites = Boite::active()->select('id', 'numero')->orderBy('numero')->get();
        $reglesConservation = CalendrierConservation::select('id', 'NO_regle', 'nature_dossier')
                                                   ->orderBy('NO_regle')
                                                   ->get();

        return view('admin.dossiers.index', compact('dossiers', 'boites', 'reglesConservation'));
    }

    /**
     * Show the form for creating a new dossier.
     */
    public function create()
    {
        $boites = Boite::active()->with('position.tablette.travee.salle')
                      ->where('nbr_dossiers', '<', \DB::raw('capacite'))
                      ->orderBy('numero')
                      ->get();
        
        $reglesConservation = CalendrierConservation::with('planClassement')
                                                   ->orderBy('NO_regle')
                                                   ->get();

        return view('admin.dossiers.create', compact('boites', 'reglesConservation'));
    }

    /**
     * Store a newly created dossier in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero' => 'required|string|max:255|unique:dossiers',
            'titre' => 'required|string|max:255',
            'date_creation' => 'required|date',
            'cote_classement' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'mots_cles' => 'nullable|string',
            'type_piece' => 'nullable|string|max:255',
            'boite_id' => 'required|exists:boites,id',
            'calendrier_conservation_id' => 'required|exists:calendrier_conservation,id',
        ], [
            'numero.required' => 'Le numéro du dossier est obligatoire.',
            'numero.unique' => 'Ce numéro de dossier existe déjà.',
            'titre.required' => 'Le titre est obligatoire.',
            'date_creation.required' => 'La date de création est obligatoire.',
            'boite_id.required' => 'La boîte est obligatoire.',
            'calendrier_conservation_id.required' => 'La règle de conservation est obligatoire.',
        ]);

        // Check if boite has space
        $boite = Boite::find($validated['boite_id']);
        if ($boite->isFull()) {
            return back()->withErrors([
                'boite_id' => 'Cette boîte est pleine.'
            ])->withInput();
        }

        // Calculate elimination date
        $calendrier = CalendrierConservation::find($validated['calendrier_conservation_id']);
        $dateCreation = \Carbon\Carbon::parse($validated['date_creation']);
        $totalYears = $calendrier->archive_courant + $calendrier->archive_intermediaire;
        $validated['date_elimination_prevue'] = $dateCreation->addYears($totalYears);

        $dossier = Dossier::create($validated);

        // Update boite dossier count
        $boite->updateNbrDossiers();

        return redirect()->route('admin.dossiers.index')
                        ->with('success', 'Dossier créé avec succès.');
    }

    /**
     * Display the specified dossier.
     */
    public function show(Dossier $dossier)
    {
        $dossier->load([
            'boite.position.tablette.travee.salle.organisme',
            'calendrierConservation.planClassement'
        ]);

        return view('admin.dossiers.show', compact('dossier'));
    }

    /**
     * Show the form for editing the specified dossier.
     */
    public function edit(Dossier $dossier)
    {
        $boites = Boite::active()->with('position.tablette.travee.salle')
                      ->where(function($query) use ($dossier) {
                          $query->where('nbr_dossiers', '<', \DB::raw('capacite'))
                                ->orWhere('id', $dossier->boite_id);
                      })
                      ->orderBy('numero')
                      ->get();
        
        $reglesConservation = CalendrierConservation::with('planClassement')
                                                   ->orderBy('NO_regle')
                                                   ->get();

        return view('admin.dossiers.edit', compact('dossier', 'boites', 'reglesConservation'));
    }

    /**
     * Update the specified dossier in storage.
     */
    public function update(Request $request, Dossier $dossier)
    {
        $validated = $request->validate([
            'numero' => ['required', 'string', 'max:255', Rule::unique('dossiers')->ignore($dossier->id)],
            'titre' => 'required|string|max:255',
            'date_creation' => 'required|date',
            'cote_classement' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'mots_cles' => 'nullable|string',
            'type_piece' => 'nullable|string|max:255',
            'statut' => 'required|in:actif,archive,elimine,en_cours',
            'disponible' => 'boolean',
            'boite_id' => 'required|exists:boites,id',
            'calendrier_conservation_id' => 'required|exists:calendrier_conservation,id',
        ], [
            'numero.required' => 'Le numéro du dossier est obligatoire.',
            'numero.unique' => 'Ce numéro de dossier existe déjà.',
            'titre.required' => 'Le titre est obligatoire.',
            'date_creation.required' => 'La date de création est obligatoire.',
            'statut.required' => 'Le statut est obligatoire.',
            'boite_id.required' => 'La boîte est obligatoire.',
            'calendrier_conservation_id.required' => 'La règle de conservation est obligatoire.',
        ]);

        $oldBoiteId = $dossier->boite_id;

        // Check if new boite has space (if boite changed)
        if ($validated['boite_id'] != $oldBoiteId) {
            $newBoite = Boite::find($validated['boite_id']);
            if ($newBoite->isFull()) {
                return back()->withErrors([
                    'boite_id' => 'Cette boîte est pleine.'
                ])->withInput();
            }
        }

        // Recalculate elimination date if conservation rule or creation date changed
        if ($validated['calendrier_conservation_id'] != $dossier->calendrier_conservation_id || 
            $validated['date_creation'] != $dossier->date_creation->format('Y-m-d')) {
            
            $calendrier = CalendrierConservation::find($validated['calendrier_conservation_id']);
            $dateCreation = \Carbon\Carbon::parse($validated['date_creation']);
            $totalYears = $calendrier->archive_courant + $calendrier->archive_intermediaire;
            $validated['date_elimination_prevue'] = $dateCreation->addYears($totalYears);
        }

        $dossier->update($validated);

        // Update boite counts if boite changed
        if ($validated['boite_id'] != $oldBoiteId) {
            Boite::find($oldBoiteId)->updateNbrDossiers();
            Boite::find($validated['boite_id'])->updateNbrDossiers();
        }

        return redirect()->route('admin.dossiers.index')
                        ->with('success', 'Dossier modifié avec succès.');
    }

    /**
     * Remove the specified dossier from storage.
     */
    public function destroy(Dossier $dossier)
    {
        $boiteId = $dossier->boite_id;
        
        $dossier->delete();

        // Update boite dossier count
        Boite::find($boiteId)->updateNbrDossiers();

        return redirect()->route('admin.dossiers.index')
                        ->with('success', 'Dossier supprimé avec succès.');
    }

    /**
     * Mark dossiers for elimination.
     */
    public function markForElimination(Request $request)
    {
        $validated = $request->validate([
            'dossier_ids' => 'required|array',
            'dossier_ids.*' => 'exists:dossiers,id',
        ]);

        $count = 0;
        foreach ($validated['dossier_ids'] as $dossierId) {
            $dossier = Dossier::find($dossierId);
            if ($dossier && $dossier->statut != 'elimine') {
                $dossier->markAsEliminated();
                $count++;
            }
        }

        return redirect()->route('admin.dossiers.index')
                        ->with('success', "{$count} dossier(s) marqué(s) pour élimination.");
    }

    /**
     * Archive dossiers.
     */
    public function archiveDossiers(Request $request)
    {
        $validated = $request->validate([
            'dossier_ids' => 'required|array',
            'dossier_ids.*' => 'exists:dossiers,id',
        ]);

        $count = 0;
        foreach ($validated['dossier_ids'] as $dossierId) {
            $dossier = Dossier::find($dossierId);
            if ($dossier) {
                $dossier->archive();
                $count++;
            }
        }

        return redirect()->route('admin.dossiers.index')
                        ->with('success', "{$count} dossier(s) archivé(s).");
    }

    /**
     * Get dossiers due for elimination.
     */
    public function dueForElimination(Request $request)
    {
        $dossiers = Dossier::with(['boite', 'calendrierConservation'])
                          ->dueForElimination()
                          ->orderBy('date_elimination_prevue')
                          ->paginate(15);

        return view('admin.dossiers.elimination', compact('dossiers'));
    }

    /**
     * Export dossiers to CSV.
     */
    public function export(Request $request)
    {
        $query = Dossier::with(['boite', 'calendrierConservation']);

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('statut')) {
            $query->byStatus($request->statut);
        }

        $dossiers = $query->orderBy('numero')->get();

        $filename = 'dossiers_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($dossiers) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, [
                'Numéro',
                'Titre',
                'Date Création',
                'Cote Classement',
                'Statut',
                'Type Pièce',
                'Boîte',
                'Règle Conservation',
                'Date Élimination Prévue',
                'Disponible',
                'Localisation'
            ], ';');
            
            foreach ($dossiers as $dossier) {
                fputcsv($file, [
                    $dossier->numero,
                    $dossier->titre,
                    $dossier->date_creation->format('d/m/Y'),
                    $dossier->cote_classement,
                    $dossier->status_display,
                    $dossier->type_piece,
                    $dossier->boite->numero,
                    $dossier->calendrierConservation->NO_regle,
                    $dossier->date_elimination_prevue?->format('d/m/Y'),
                    $dossier->disponible ? 'Oui' : 'Non',
                    $dossier->full_location
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
            'total_dossiers' => Dossier::count(),
            'dossiers_actifs' => Dossier::byStatus('actif')->count(),
            'dossiers_archives' => Dossier::byStatus('archive')->count(),
            'dossiers_elimines' => Dossier::byStatus('elimine')->count(),
            'dossiers_due_elimination' => Dossier::dueForElimination()->count(),
            'dossiers_near_elimination' => Dossier::nearElimination(30)->count(),
            'dossiers_par_statut' => Dossier::selectRaw('statut, count(*) as count')
                                           ->groupBy('statut')
                                           ->get(),
        ];

        return response()->json($stats);
    }
}