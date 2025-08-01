<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\Boite;
use App\Models\CalendrierConservation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Foundation\Auth\Access\Authorizable;
use function Spatie\Activitylog\activity;
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

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('date_creation', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date_creation', '<=', $request->date_to);
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['numero', 'titre', 'date_creation', 'created_at', 'date_elimination_prevue'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $dossiers = $query->paginate($request->get('per_page', 15))
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
                      ->where('nbr_dossiers', '<', DB::raw('capacite'))
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
            'disponible' => 'boolean',
        ], [
            'numero.required' => 'Le numéro du dossier est obligatoire.',
            'numero.unique' => 'Ce numéro de dossier existe déjà.',
            'titre.required' => 'Le titre est obligatoire.',
            'date_creation.required' => 'La date de création est obligatoire.',
            'boite_id.required' => 'La boîte est obligatoire.',
            'calendrier_conservation_id.required' => 'La règle de conservation est obligatoire.',
        ]);

        try {
            DB::beginTransaction();

            // Check if boite has space
            $boite = Boite::find($validated['boite_id']);
            if ($boite->isFull()) {
                return back()->withErrors([
                    'boite_id' => 'Cette boîte est pleine.'
                ])->withInput();
            }

            // Calculate elimination date
            $calendrier = CalendrierConservation::find($validated['calendrier_conservation_id']);
            $dateCreation = Carbon::parse($validated['date_creation']);
            $totalYears = $calendrier->archive_courant + $calendrier->archive_intermediaire;
            $validated['date_elimination_prevue'] = $dateCreation->copy()->addYears($totalYears);
            
            // Set default values
            $validated['statut'] = 'actif';
            $validated['disponible'] = $validated['disponible'] ?? true;

            $dossier = Dossier::create($validated);

            // Update boite dossier count
            $boite->updateNbrDossiers();

            DB::commit();

            Log::info('Dossier created', ['dossier_id' => $dossier->id, 'numero' => $dossier->numero]);

            return redirect()->route('admin.dossiers.index')
                            ->with('success', 'Dossier créé avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating dossier', ['error' => $e->getMessage()]);
            
            return back()->withErrors([
                'general' => 'Une erreur est survenue lors de la création du dossier.'
            ])->withInput();
        }
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
                          $query->where('nbr_dossiers', '<', DB::raw('capacite'))
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

        try {
            DB::beginTransaction();

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
                $dateCreation = Carbon::parse($validated['date_creation']);
                $totalYears = $calendrier->archive_courant + $calendrier->archive_intermediaire;
                $validated['date_elimination_prevue'] = $dateCreation->copy()->addYears($totalYears);
            }

            $dossier->update($validated);

            // Update boite counts if boite changed
            if ($validated['boite_id'] != $oldBoiteId) {
                Boite::find($oldBoiteId)->updateNbrDossiers();
                Boite::find($validated['boite_id'])->updateNbrDossiers();
            }

            DB::commit();

            Log::info('Dossier updated', ['dossier_id' => $dossier->id, 'numero' => $dossier->numero]);

            return redirect()->route('admin.dossiers.index')
                            ->with('success', 'Dossier modifié avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating dossier', ['dossier_id' => $dossier->id, 'error' => $e->getMessage()]);
            
            return back()->withErrors([
                'general' => 'Une erreur est survenue lors de la modification du dossier.'
            ])->withInput();
        }
    }

    /**
     * Remove the specified dossier from storage.
     */
    public function destroy(Dossier $dossier)
    {
        try {
            DB::beginTransaction();

            $boiteId = $dossier->boite_id;
            $dossierNumero = $dossier->numero;
            
            $dossier->delete();

            // Update boite dossier count
            Boite::find($boiteId)->updateNbrDossiers();

            DB::commit();

            Log::info('Dossier deleted', ['numero' => $dossierNumero]);

            return redirect()->route('admin.dossiers.index')
                            ->with('success', 'Dossier supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting dossier', ['dossier_id' => $dossier->id, 'error' => $e->getMessage()]);
            
            return redirect()->route('admin.dossiers.index')
                            ->with('error', 'Une erreur est survenue lors de la suppression du dossier.');
        }
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

        try {
            DB::beginTransaction();

            $count = 0;
            foreach ($validated['dossier_ids'] as $dossierId) {
                $dossier = Dossier::find($dossierId);
                if ($dossier && $dossier->statut != 'elimine') {
                    $dossier->markAsEliminated();
                    $count++;
                }
            }

            DB::commit();

            Log::info('Dossiers marked for elimination', ['count' => $count]);

            return redirect()->route('admin.dossiers.index')
                            ->with('success', "{$count} dossier(s) marqué(s) pour élimination.");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error marking dossiers for elimination', ['error' => $e->getMessage()]);
            
            return redirect()->route('admin.dossiers.index')
                            ->with('error', 'Une erreur est survenue lors du marquage pour élimination.');
        }
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

        try {
            DB::beginTransaction();

            $count = 0;
            foreach ($validated['dossier_ids'] as $dossierId) {
                $dossier = Dossier::find($dossierId);
                if ($dossier) {
                    $dossier->archive();
                    $count++;
                }
            }

            DB::commit();

            Log::info('Dossiers archived', ['count' => $count]);

            return redirect()->route('admin.dossiers.index')
                            ->with('success', "{$count} dossier(s) archivé(s).");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error archiving dossiers', ['error' => $e->getMessage()]);
            
            return redirect()->route('admin.dossiers.index')
                            ->with('error', 'Une erreur est survenue lors de l\'archivage.');
        }
    }

    /**
     * Restore archived dossiers.
     */
    public function restoreDossiers(Request $request)
    {
        $validated = $request->validate([
            'dossier_ids' => 'required|array',
            'dossier_ids.*' => 'exists:dossiers,id',
        ]);

        try {
            DB::beginTransaction();

            $count = 0;
            foreach ($validated['dossier_ids'] as $dossierId) {
                $dossier = Dossier::find($dossierId);
                if ($dossier && $dossier->statut === 'archive') {
                    $dossier->update(['statut' => 'actif']);
                    $count++;
                }
            }

            DB::commit();

            Log::info('Dossiers restored', ['count' => $count]);

            return redirect()->route('admin.dossiers.index')
                            ->with('success', "{$count} dossier(s) restauré(s).");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error restoring dossiers', ['error' => $e->getMessage()]);
            
            return redirect()->route('admin.dossiers.index')
                            ->with('error', 'Une erreur est survenue lors de la restauration.');
        }
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
     * Get dossiers nearing elimination.
     */
    public function nearElimination(Request $request)
    {
        $days = $request->get('days', 30);
        
        $dossiers = Dossier::with(['boite', 'calendrierConservation'])
                          ->nearElimination($days)
                          ->orderBy('date_elimination_prevue')
                          ->paginate(15);

        return view('admin.dossiers.near-elimination', compact('dossiers', 'days'));
    }

    /**
     * Move dossiers to different boite.
     */
    public function moveDossiers(Request $request)
    {
        $validated = $request->validate([
            'dossier_ids' => 'required|array',
            'dossier_ids.*' => 'exists:dossiers,id',
            'target_boite_id' => 'required|exists:boites,id',
        ]);

        try {
            DB::beginTransaction();

            $targetBoite = Boite::find($validated['target_boite_id']);
            $dossierCount = count($validated['dossier_ids']);
            
            // Check if target boite has enough space
            if (($targetBoite->nbr_dossiers + $dossierCount) > $targetBoite->capacite) {
                return back()->withErrors([
                    'target_boite_id' => 'La boîte de destination n\'a pas assez d\'espace.'
                ]);
            }

            $oldBoites = [];
            $movedCount = 0;

            foreach ($validated['dossier_ids'] as $dossierId) {
                $dossier = Dossier::find($dossierId);
                if ($dossier) {
                    $oldBoites[] = $dossier->boite_id;
                    $dossier->update(['boite_id' => $validated['target_boite_id']]);
                    $movedCount++;
                }
            }

            // Update boite counts
            foreach (array_unique($oldBoites) as $boiteId) {
                Boite::find($boiteId)->updateNbrDossiers();
            }
            $targetBoite->updateNbrDossiers();

            DB::commit();

            Log::info('Dossiers moved', ['count' => $movedCount, 'target_boite' => $validated['target_boite_id']]);

            return redirect()->route('admin.dossiers.index')
                            ->with('success', "{$movedCount} dossier(s) déplacé(s) avec succès.");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error moving dossiers', ['error' => $e->getMessage()]);
            
            return redirect()->route('admin.dossiers.index')
                            ->with('error', 'Une erreur est survenue lors du déplacement.');
        }
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

        if ($request->filled('boite_id')) {
            $query->where('boite_id', $request->boite_id);
        }

        if ($request->filled('calendrier_conservation_id')) {
            $query->where('calendrier_conservation_id', $request->calendrier_conservation_id);
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
                'Localisation',
                'Description',
                'Mots Clés'
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
                    $dossier->full_location,
                    $dossier->description,
                    $dossier->mots_cles
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk import dossiers from CSV.
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('csv_file');
            $csvData = array_map('str_getcsv', file($file->getPathname()));
            $header = array_shift($csvData);
            
            $imported = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($csvData as $index => $row) {
                $row = array_combine($header, $row);
                
                try {
                    // Validate required fields
                    if (empty($row['numero']) || empty($row['titre'])) {
                        $errors[] = "Ligne " . ($index + 2) . ": Numéro et titre requis";
                        continue;
                    }

                    // Check if dossier already exists
                    if (Dossier::where('numero', $row['numero'])->exists()) {
                        $errors[] = "Ligne " . ($index + 2) . ": Numéro {$row['numero']} existe déjà";
                        continue;
                    }

                    $dossierData = [
                        'numero' => $row['numero'],
                        'titre' => $row['titre'],
                        'date_creation' => Carbon::parse($row['date_creation'] ?? now()),
                        'cote_classement' => $row['cote_classement'] ?? null,
                        'description' => $row['description'] ?? null,
                        'mots_cles' => $row['mots_cles'] ?? null,
                        'type_piece' => $row['type_piece'] ?? null,
                        'statut' => $row['statut'] ?? 'actif',
                        'disponible' => ($row['disponible'] ?? 'oui') === 'oui',
                    ];

                    // Find boite
                    if (!empty($row['boite_numero'])) {
                        $boite = Boite::where('numero', $row['boite_numero'])->first();
                        if ($boite) {
                            $dossierData['boite_id'] = $boite->id;
                        }
                    }

                    // Find conservation rule
                    if (!empty($row['regle_conservation'])) {
                        $regle = CalendrierConservation::where('NO_regle', $row['regle_conservation'])->first();
                        if ($regle) {
                            $dossierData['calendrier_conservation_id'] = $regle->id;
                            
                            // Calculate elimination date
                            $totalYears = $regle->archive_courant + $regle->archive_intermediaire;
                            $dossierData['date_elimination_prevue'] = $dossierData['date_creation']->copy()->addYears($totalYears);
                        }
                    }

                    Dossier::create($dossierData);
                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Ligne " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "{$imported} dossier(s) importé(s) avec succès.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " erreur(s) détectée(s).";
            }

            return redirect()->route('admin.dossiers.index')
                            ->with('success', $message)
                            ->with('import_errors', $errors);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error importing dossiers', ['error' => $e->getMessage()]);
            
            return back()->withErrors([
                'csv_file' => 'Erreur lors de l\'importation du fichier CSV.'
            ]);
        }
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
            'dossiers_en_cours' => Dossier::byStatus('en_cours')->count(),
            'dossiers_due_elimination' => Dossier::dueForElimination()->count(),
            'dossiers_near_elimination' => Dossier::nearElimination(30)->count(),
            'dossiers_par_statut' => Dossier::selectRaw('statut, count(*) as count')
                                           ->groupBy('statut')
                                           ->pluck('count', 'statut'),
            'dossiers_par_mois' => Dossier::selectRaw('YEAR(date_creation) as year, MONTH(date_creation) as month, count(*) as count')
                                         ->whereYear('date_creation', '>=', now()->subYear())
                                         ->groupBy('year', 'month')
                                         ->orderBy('year')
                                         ->orderBy('month')
                                         ->get(),
            'boites_utilisation' => Boite::selectRaw('(nbr_dossiers / capacite * 100) as utilisation_pourcentage')
                                        ->whereRaw('capacite > 0')
                                        ->avg('utilisation_pourcentage'),
        ];

        return response()->json($stats);
    }

    /**
     * Generate reports.
     */
    public function reports(Request $request)
    {
        $reportType = $request->get('type', 'general');
        
        switch ($reportType) {
            case 'elimination':
                return $this->eliminationReport($request);
            case 'conservation':
                return $this->conservationReport($request);
            case 'boites':
                return $this->boitesReport($request);
            default:
                return $this->generalReport($request);
        }
    }

    /**
     * Generate elimination report.
     */
    private function eliminationReport(Request $request)
    {
        $dossiers = Dossier::with(['boite', 'calendrierConservation'])
                          ->where('date_elimination_prevue', '<=', now()->addMonths(12))
                          ->orderBy('date_elimination_prevue')
                          ->get();

        $data = [
            'title' => 'Rapport d\'élimination',
            'dossiers' => $dossiers,
            'total' => $dossiers->count(),
            'due_now' => $dossiers->where('date_elimination_prevue', '<=', now())->count(),
            'due_soon' => $dossiers->where('date_elimination_prevue', '>', now())
                                   ->where('date_elimination_prevue', '<=', now()->addMonths(3))->count(),
        ];

        return view('admin.dossiers.reports.elimination', $data);
    }

    /**
     * Generate conservation report.
     */
    private function conservationReport(Request $request)
    {
        $regles = CalendrierConservation::withCount('dossiers')
                                       ->orderBy('NO_regle')
                                       ->get();

        $data = [
            'title' => 'Rapport de conservation',
            'regles' => $regles,
            'total_regles' => $regles->count(),
            'total_dossiers' => $regles->sum('dossiers_count'),
        ];

        return view('admin.dossiers.reports.conservation', $data);
    }

    /**
     * Generate boites report.
     */
    private function boitesReport(Request $request)
    {
        $boites = Boite::with(['position.tablette.travee.salle', 'dossiers'])
                      ->withCount('dossiers')
                      ->orderBy('numero')
                      ->get();

        $data = [
            'title' => 'Rapport des boîtes',
            'boites' => $boites,
            'total_boites' => $boites->count(),
            'boites_pleines' => $boites->filter(function($boite) {
                return $boite->nbr_dossiers >= $boite->capacite;
            })->count(),
            'boites_vides' => $boites->filter(function($boite) {
                return $boite->nbr_dossiers == 0;
            })->count(),
            'taux_occupation' => $boites->sum('nbr_dossiers') / $boites->sum('capacite') * 100,
        ];

        return view('admin.dossiers.reports.boites', $data);
    }

    /**
     * Generate general report.
     */
    private function generalReport(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subYear()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $dossiers = Dossier::with(['boite', 'calendrierConservation'])
                          ->whereBetween('date_creation', [$dateFrom, $dateTo])
                          ->get();

        $data = [
            'title' => 'Rapport général',
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'total_dossiers' => $dossiers->count(),
            'dossiers_par_statut' => $dossiers->groupBy('statut')->map->count(),
            'dossiers_par_mois' => $dossiers->groupBy(function($dossier) {
                return $dossier->date_creation->format('Y-m');
            })->map->count(),
            'types_pieces' => $dossiers->whereNotNull('type_piece')
                                     ->groupBy('type_piece')
                                     ->map->count(),
            'moyenne_par_boite' => $dossiers->groupBy('boite_id')->map->count()->avg(),
        ];

        return view('admin.dossiers.reports.general', $data);
    }

    /**
     * Search dossiers (API endpoint).
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $limit = $request->get('limit', 20);

        if (empty($query)) {
            return response()->json([]);
        }

        $dossiers = Dossier::with(['boite'])
                          ->search($query)
                          ->limit($limit)
                          ->get()
                          ->map(function($dossier) {
                              return [
                                  'id' => $dossier->id,
                                  'numero' => $dossier->numero,
                                  'titre' => $dossier->titre,
                                  'statut' => $dossier->statut,
                                  'boite' => $dossier->boite->numero,
                                  'date_creation' => $dossier->date_creation->format('d/m/Y'),
                              ];
                          });

        return response()->json($dossiers);
    }

    /**
     * Get available boites (API endpoint).
     */
    public function availableBoites(Request $request)
    {
        $currentDossierId = $request->get('current_dossier_id');
        
        $boites = Boite::active()
                      ->with('position.tablette.travee.salle')
                      ->where(function($query) use ($currentDossierId) {
                          $query->where('nbr_dossiers', '<', DB::raw('capacite'));
                          
                          if ($currentDossierId) {
                              $currentDossier = Dossier::find($currentDossierId);
                              if ($currentDossier) {
                                  $query->orWhere('id', $currentDossier->boite_id);
                              }
                          }
                      })
                      ->orderBy('numero')
                      ->get()
                      ->map(function($boite) {
                          return [
                              'id' => $boite->id,
                              'numero' => $boite->numero,
                              'capacite' => $boite->capacite,
                              'nbr_dossiers' => $boite->nbr_dossiers,
                              'disponible' => $boite->capacite - $boite->nbr_dossiers,
                              'location' => $boite->full_location ?? 'Non localisée',
                          ];
                      });

        return response()->json($boites);
    }

    /**
     * Get dossier details (API endpoint).
     */
    public function getDossier(Dossier $dossier)
    {
        $dossier->load([
            'boite.position.tablette.travee.salle',
            'calendrierConservation'
        ]);

        return response()->json([
            'id' => $dossier->id,
            'numero' => $dossier->numero,
            'titre' => $dossier->titre,
            'date_creation' => $dossier->date_creation->format('Y-m-d'),
            'cote_classement' => $dossier->cote_classement,
            'description' => $dossier->description,
            'mots_cles' => $dossier->mots_cles,
            'type_piece' => $dossier->type_piece,
            'statut' => $dossier->statut,
            'disponible' => $dossier->disponible,
            'date_elimination_prevue' => $dossier->date_elimination_prevue?->format('Y-m-d'),
            'boite' => [
                'id' => $dossier->boite->id,
                'numero' => $dossier->boite->numero,
                'location' => $dossier->boite->full_location,
            ],
            'regle_conservation' => [
                'id' => $dossier->calendrierConservation->id,
                'numero' => $dossier->calendrierConservation->NO_regle,
                'nature_dossier' => $dossier->calendrierConservation->nature_dossier,
            ],
        ]);
    }

    /**
     * Validate dossier data before creation/update.
     */
    private function validateDossierData(Request $request, $dossier = null)
    {
        $rules = [
            'numero' => [
                'required',
                'string',
                'max:255',
                $dossier ? Rule::unique('dossiers')->ignore($dossier->id) : 'unique:dossiers'
            ],
            'titre' => 'required|string|max:255',
            'date_creation' => 'required|date|before_or_equal:today',
            'cote_classement' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'mots_cles' => 'nullable|string|max:500',
            'type_piece' => 'nullable|string|max:255',
            'boite_id' => 'required|exists:boites,id',
            'calendrier_conservation_id' => 'required|exists:calendrier_conservation,id',
            'disponible' => 'boolean',
        ];

        if ($dossier) {
            $rules['statut'] = 'required|in:actif,archive,elimine,en_cours';
        }

        $messages = [
            'numero.required' => 'Le numéro du dossier est obligatoire.',
            'numero.unique' => 'Ce numéro de dossier existe déjà.',
            'numero.max' => 'Le numéro ne peut pas dépasser 255 caractères.',
            'titre.required' => 'Le titre est obligatoire.',
            'titre.max' => 'Le titre ne peut pas dépasser 255 caractères.',
            'date_creation.required' => 'La date de création est obligatoire.',
            'date_creation.date' => 'La date de création doit être une date valide.',
            'date_creation.before_or_equal' => 'La date de création ne peut pas être dans le futur.',
            'cote_classement.max' => 'La cote de classement ne peut pas dépasser 255 caractères.',
            'description.max' => 'La description ne peut pas dépasser 2000 caractères.',
            'mots_cles.max' => 'Les mots clés ne peuvent pas dépasser 500 caractères.',
            'type_piece.max' => 'Le type de pièce ne peut pas dépasser 255 caractères.',
            'statut.required' => 'Le statut est obligatoire.',
            'statut.in' => 'Le statut sélectionné n\'est pas valide.',
            'boite_id.required' => 'La boîte est obligatoire.',
            'boite_id.exists' => 'La boîte sélectionnée n\'existe pas.',
            'calendrier_conservation_id.required' => 'La règle de conservation est obligatoire.',
            'calendrier_conservation_id.exists' => 'La règle de conservation sélectionnée n\'existe pas.',
        ];

        return $request->validate($rules, $messages);
    }

    /**
     * Check if user can perform action on dossier.
     */
    private function authorizeAction($action, $dossier = null)
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }
        
        // Admin can do everything
        if ($user->is_admin ?? false) {
            return true;
        }
        
        // Regular users can view and create
        return in_array($action, ['view', 'create']);
    }

    /**
     * Log dossier activity.
     */
    private function logActivity($action, $dossier, $details = [])
    {
        $user = Auth::user();
        if (!$user) {
            return; // No user logged in
        }   
        $activity = [
            'user_id' => $user->id,
            'dossier_id' => $dossier->id,
            'action' => $action,
            'details' => json_encode($details),
            'created_at' => now(),
        ];
        // Save activity log to database or external service
        // This is a placeholder for logging logic
        Log::info('Dossier activity logged', [
            'user_id' => $user->id,
            'dossier_id' => $dossier->id,
            'action' => $action,
            'details' => $details
        ]);
    }

    /**
     * Send notification for dossier events.
     */
    private function sendNotification($event, $dossier, $recipients = [])
    {
        // Implementation depends on your notification system
        // This is a placeholder for notification logic
        
        try {
            switch ($event) {
                case 'elimination_due':
                    // Notify about dossiers due for elimination
                    break;
                case 'boite_full':
                    // Notify when a boite becomes full
                    break;
                case 'dossier_created':
                    // Notify about new dossier creation
                    break;
                default:
                    break;
            }
        } catch (\Exception $e) {
            Log::warning('Failed to send notification', [
                'event' => $event,
                'dossier_id' => $dossier->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate QR code for dossier.
     */
    public function generateQrCode(Dossier $dossier)
    {
        try {
            // You'll need to install a QR code library like simplesoftwareio/simple-qrcode
            // composer require simplesoftwareio/simple-qrcode
            
            $qrData = json_encode([
                'type' => 'dossier',
                'id' => $dossier->id,
                'numero' => $dossier->numero,
                'url' => route('admin.dossiers.show', $dossier)
            ]);

            // Generate QR code
            // $qrCode = QrCode::size(200)->generate($qrData);
            
            return response()->json([
                'success' => true,
                'qr_data' => $qrData,
                // 'qr_code' => $qrCode
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating QR code', ['dossier_id' => $dossier->id, 'error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du QR code.'
            ], 500);
        }
    }

    /**
     * Duplicate a dossier.
     */
    public function duplicate(Dossier $dossier)
    {
        try {
            DB::beginTransaction();

            $newDossier = $dossier->replicate();
            $newDossier->numero = $dossier->numero . '_COPY_' . time();
            $newDossier->titre = $dossier->titre . ' (Copie)';
            $newDossier->statut = 'actif';
            $newDossier->created_at = now();
            $newDossier->updated_at = now();
            
            // Recalculate elimination date
            $calendrier = CalendrierConservation::find($newDossier->calendrier_conservation_id);
            $totalYears = $calendrier->archive_courant + $calendrier->archive_intermediaire;
            $newDossier->date_elimination_prevue = $newDossier->date_creation->copy()->addYears($totalYears);

            $newDossier->save();

            // Update boite count
            $boite = Boite::find($newDossier->boite_id);
            $boite->updateNbrDossiers();

            DB::commit();

            $this->logActivity('duplicated', $newDossier, ['original_id' => $dossier->id]);

            return redirect()->route('admin.dossiers.edit', $newDossier)
                            ->with('success', 'Dossier dupliqué avec succès. Vous pouvez maintenant le modifier.');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error duplicating dossier', ['dossier_id' => $dossier->id, 'error' => $e->getMessage()]);
            
            return back()->with('error', 'Erreur lors de la duplication du dossier.');
        }
    }

    /**
     * Get elimination schedule.
     */
    public function eliminationSchedule(Request $request)
    {
        $months = $request->get('months', 12);
        
        $schedule = collect();
        
        for ($i = 0; $i < $months; $i++) {
            $startDate = now()->addMonths($i)->startOfMonth();
            $endDate = now()->addMonths($i)->endOfMonth();
            
            $count = Dossier::whereBetween('date_elimination_prevue', [$startDate, $endDate])
                           ->count();
            
            if ($count > 0) {
                $schedule->push([
                    'month' => $startDate->format('Y-m'),
                    'month_name' => $startDate->format('F Y'),
                    'count' => $count,
                    'dossiers' => Dossier::with(['boite', 'calendrierConservation'])
                                        ->whereBetween('date_elimination_prevue', [$startDate, $endDate])
                                        ->orderBy('date_elimination_prevue')
                                        ->get()
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json($schedule);
        }

        return view('admin.dossiers.elimination-schedule', compact('schedule', 'months'));
    }
}