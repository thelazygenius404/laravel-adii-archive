<?php

namespace App\Http\Controllers;

use App\Models\CalendrierConservation;
use App\Models\PlanClassement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CalendrierConservationController extends Controller
{
    /**
     * Display a listing of the calendrier conservation.
     */
    public function index(Request $request)
    {
        $query = CalendrierConservation::with('planClassement');

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by plan classement
        if ($request->filled('plan_classement')) {
            $query->where('plan_classement_id', $request->plan_classement);
        }

        // Filter by sort final
        if ($request->filled('sort_final')) {
            $query->where('sort_final', $request->sort_final);
        }

        $regles = $query->orderBy('NO_regle')
                       ->paginate($request->get('per_page', 15))
                       ->withQueryString();

        $planClassements = PlanClassement::orderBy('code_classement')->get();

        return view('admin.calendrier-conservation.index', compact('regles', 'planClassements'));
    }

    /**
     * Show the form for creating a new calendrier conservation.
     */
    public function create()
    {
        $planClassements = PlanClassement::orderBy('code_classement')->get();
        
        // Generate next rule number
        $nextRule = $this->generateNextRuleNumber();
        
        return view('admin.calendrier-conservation.create', compact('planClassements', 'nextRule'));
    }

    /**
     * Store a newly created calendrier conservation in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'NO_regle' => 'required|string|max:10|unique:calendrier_conservation',
            'delais_legaux' => 'required|integer|min:0',
            'nature_dossier' => 'required|string|max:50',
            'reference' => 'required|string',
            'plan_classement_id' => 'required|exists:plan_classement,id',
            'sort_final' => 'required|in:C,E,T',
            'archive_courant' => 'required|integer|min:0',
            'archive_intermediaire' => 'required|integer|min:0',
            'observation' => 'nullable|string',
        ], [
            'NO_regle.required' => 'Le numéro de règle est obligatoire.',
            'NO_regle.unique' => 'Ce numéro de règle existe déjà.',
            'delais_legaux.required' => 'Les délais légaux sont obligatoires.',
            'delais_legaux.integer' => 'Les délais légaux doivent être un nombre entier.',
            'nature_dossier.required' => 'La nature du dossier est obligatoire.',
            'reference.required' => 'La référence est obligatoire.',
            'plan_classement_id.required' => 'Le plan de classement est obligatoire.',
            'sort_final.required' => 'Le sort final est obligatoire.',
            'sort_final.in' => 'Le sort final doit être C (Conservation), E (Élimination) ou T (Tri).',
            'archive_courant.required' => 'La durée d\'archive courante est obligatoire.',
            'archive_intermediaire.required' => 'La durée d\'archive intermédiaire est obligatoire.',
        ]);

        CalendrierConservation::create($validated);

        return redirect()->route('admin.calendrier-conservation.index')
                        ->with('success', 'Règle de conservation créée avec succès.');
    }

    /**
     * Display the specified calendrier conservation.
     */
    public function show(CalendrierConservation $calendrierConservation)
    {
        $calendrierConservation->load('planClassement');
        
        return view('admin.calendrier-conservation.show', compact('calendrierConservation'));
    }

    /**
     * Show the form for editing the specified calendrier conservation.
     */
    public function edit(CalendrierConservation $calendrierConservation)
    {
        $planClassements = PlanClassement::orderBy('code_classement')->get();
        
        return view('admin.calendrier-conservation.edit', compact('calendrierConservation', 'planClassements'));
    }

    /**
     * Update the specified calendrier conservation in storage.
     */
    public function update(Request $request, CalendrierConservation $calendrierConservation)
    {
        $validated = $request->validate([
            'NO_regle' => [
                'required',
                'string',
                'max:10',
                Rule::unique('calendrier_conservation')->ignore($calendrierConservation->id)
            ],
            'delais_legaux' => 'required|integer|min:0',
            'nature_dossier' => 'required|string|max:50',
            'reference' => 'required|string',
            'plan_classement_id' => 'required|exists:plan_classement,id',
            'sort_final' => 'required|in:C,E,T',
            'archive_courant' => 'required|integer|min:0',
            'archive_intermediaire' => 'required|integer|min:0',
            'observation' => 'nullable|string',
        ], [
            'NO_regle.required' => 'Le numéro de règle est obligatoire.',
            'NO_regle.unique' => 'Ce numéro de règle existe déjà.',
            'delais_legaux.required' => 'Les délais légaux sont obligatoires.',
            'nature_dossier.required' => 'La nature du dossier est obligatoire.',
            'reference.required' => 'La référence est obligatoire.',
            'plan_classement_id.required' => 'Le plan de classement est obligatoire.',
            'sort_final.required' => 'Le sort final est obligatoire.',
            'archive_courant.required' => 'La durée d\'archive courante est obligatoire.',
            'archive_intermediaire.required' => 'La durée d\'archive intermédiaire est obligatoire.',
        ]);

        $calendrierConservation->update($validated);

        return redirect()->route('admin.calendrier-conservation.index')
                        ->with('success', 'Règle de conservation modifiée avec succès.');
    }

    /**
     * Remove the specified calendrier conservation from storage.
     */
    public function destroy(CalendrierConservation $calendrierConservation)
    {
        $calendrierConservation->delete();

        return redirect()->route('admin.calendrier-conservation.index')
                        ->with('success', 'Règle de conservation supprimée avec succès.');
    }

    /**
     * Export regles to CSV.
     */
    public function export(Request $request)
    {
        $query = CalendrierConservation::with('planClassement');

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('plan_classement')) {
            $query->where('plan_classement_id', $request->plan_classement);
        }

        if ($request->filled('sort_final')) {
            $query->where('sort_final', $request->sort_final);
        }

        $regles = $query->orderBy('NO_regle')->get();

        $filename = 'calendrier_conservation_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($regles) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, [
                'N° Règle',
                'Plan de Classement',
                'Nature du Dossier',
                'Délais Légaux',
                'Archive Courante',
                'Archive Intermédiaire',
                'Durée Totale',
                'Sort Final',
                'Référence',
                'Observation',
                'Date de Création'
            ], ';');
            
            foreach ($regles as $regle) {
                fputcsv($file, [
                    $regle->NO_regle,
                    $regle->planClassement->formatted_code . ' - ' . $regle->planClassement->objet_classement,
                    $regle->nature_dossier,
                    $regle->delais_legaux,
                    $regle->archive_courant,
                    $regle->archive_intermediaire,
                    $regle->total_duration,
                    $regle->status,
                    $regle->reference,
                    $regle->observation,
                    $regle->created_at->format('d/m/Y H:i:s')
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get regles by plan classement for API/AJAX requests.
     */
    public function byPlanClassement(Request $request, $planClassementId)
    {
        $regles = CalendrierConservation::where('plan_classement_id', $planClassementId)
                                       ->select('id', 'NO_regle', 'nature_dossier', 'sort_final')
                                       ->orderBy('NO_regle')
                                       ->get();
        
        return response()->json($regles);
    }

    /**
     * Bulk operations.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,export,update_sort',
            'regle_ids' => 'required|array',
            'regle_ids.*' => 'exists:calendrier_conservation,id',
            'sort_final' => 'nullable|in:C,E,T',
        ]);

        $regleIds = $validated['regle_ids'];
        $action = $validated['action'];

        switch ($action) {
            case 'delete':
                return $this->bulkDelete($regleIds);
            
            case 'export':
                return $this->bulkExport($regleIds);
            
            case 'update_sort':
                return $this->bulkUpdateSort($regleIds, $validated['sort_final']);
            
            default:
                return response()->json(['success' => false, 'message' => 'Action non reconnue.'], 400);
        }
    }

    /**
     * Bulk delete multiple regles.
     */
    private function bulkDelete($regleIds)
    {
        $deleted = CalendrierConservation::whereIn('id', $regleIds)->delete();

        $message = "{$deleted} règle(s) supprimée(s) avec succès.";

        return redirect()->route('admin.calendrier-conservation.index')
                        ->with('success', $message);
    }

    /**
     * Bulk export specific regles.
     */
    private function bulkExport($regleIds)
    {
        $regles = CalendrierConservation::with('planClassement')
                                       ->whereIn('id', $regleIds)
                                       ->orderBy('NO_regle')
                                       ->get();

        $filename = 'calendrier_conservation_selection_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($regles) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, [
                'N° Règle',
                'Plan de Classement',
                'Nature du Dossier',
                'Délais Légaux',
                'Archive Courante',
                'Archive Intermédiaire',
                'Durée Totale',
                'Sort Final',
                'Référence',
                'Observation'
            ], ';');
            
            foreach ($regles as $regle) {
                fputcsv($file, [
                    $regle->NO_regle,
                    $regle->planClassement->formatted_code . ' - ' . $regle->planClassement->objet_classement,
                    $regle->nature_dossier,
                    $regle->delais_legaux,
                    $regle->archive_courant,
                    $regle->archive_intermediaire,
                    $regle->total_duration,
                    $regle->status,
                    $regle->reference,
                    $regle->observation
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk update sort final.
     */
    private function bulkUpdateSort($regleIds, $sortFinal)
    {
        if (!$sortFinal) {
            return redirect()->route('admin.calendrier-conservation.index')
                            ->with('error', 'Le sort final est requis pour cette opération.');
        }

        $updated = CalendrierConservation::whereIn('id', $regleIds)
                                        ->update(['sort_final' => $sortFinal]);

        $statusLabel = match($sortFinal) {
            'C' => 'Conservation',
            'E' => 'Élimination',
            'T' => 'Tri',
        };

        $message = "{$updated} règle(s) mise(s) à jour vers '{$statusLabel}' avec succès.";

        return redirect()->route('admin.calendrier-conservation.index')
                        ->with('success', $message);
    }

    /**
     * Generate next rule number.
     */
    private function generateNextRuleNumber()
    {
        $lastRule = CalendrierConservation::orderBy('NO_regle', 'desc')->first();
        
        if (!$lastRule) {
            return 'R001';
        }
        
        // Extract number from last rule (assuming format like R001, R002, etc.)
        $lastNumber = (int) substr($lastRule->NO_regle, 1);
        $nextNumber = $lastNumber + 1;
        
        return 'R' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total_regles' => CalendrierConservation::count(),
            'regles_conservation' => CalendrierConservation::where('sort_final', 'C')->count(),
            'regles_elimination' => CalendrierConservation::where('sort_final', 'E')->count(),
            'regles_tri' => CalendrierConservation::where('sort_final', 'T')->count(),
            'duree_moyenne_legale' => CalendrierConservation::avg('delais_legaux'),
            'duree_moyenne_totale' => CalendrierConservation::selectRaw('AVG(archive_courant + archive_intermediaire) as avg_total')->first()->avg_total,
            'recent_regles' => CalendrierConservation::with('planClassement')->latest()->limit(5)->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Get regles for specific plan classement.
     */
    public function getReglesByPlan(Request $request, PlanClassement $planClassement)
    {
        $regles = $planClassement->calendrierConservation()
                                ->orderBy('NO_regle')
                                ->get();

        return view('admin.calendrier-conservation.by-plan', compact('planClassement', 'regles'));
    }
}