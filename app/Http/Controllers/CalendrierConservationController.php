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
            $query->where('plan_classement_code', $request->plan_classement);
        }

        // Filter by sort final
        if ($request->filled('sort_final')) {
            $query->where('sort_final', $request->sort_final);
        }

        $regles = $query->orderBy('plan_classement_code')
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
        
        // Get available plan classements that don't have conservation rules yet
        $availablePlans = PlanClassement::whereNotIn('code_classement', 
            CalendrierConservation::pluck('plan_classement_code')
        )->orderBy('code_classement')->get();
        
        return view('admin.calendrier-conservation.create', compact('planClassements', 'availablePlans'));
    }

    /**
     * Store a newly created calendrier conservation in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_classement_code' => 'required|string|max:20|exists:plan_classement,code_classement|unique:calendrier_conservation',
            'pieces_constituant' => 'nullable|string',
            'principal_secondaire' => 'nullable|in:P,S',
            'delai_legal' => 'nullable|string|max:50',
            'reference_juridique' => 'nullable|string',
            'archives_courantes' => 'required|string|max:100',
            'archives_intermediaires' => 'required|string|max:50',
            'sort_final' => 'required|in:C,D,T',
            'observation' => 'nullable|string',
        ], [
            'plan_classement_code.required' => 'Le plan de classement est obligatoire.',
            'plan_classement_code.unique' => 'Ce plan de classement a déjà une règle de conservation.',
            'plan_classement_code.exists' => 'Le plan de classement sélectionné n\'existe pas.',
            'archives_courantes.required' => 'La durée d\'archive courante est obligatoire.',
            'archives_intermediaires.required' => 'La durée d\'archive intermédiaire est obligatoire.',
            'sort_final.required' => 'Le sort final est obligatoire.',
            'sort_final.in' => 'Le sort final doit être C (Conservation), D (Destruction) ou T (Tri).',
            'principal_secondaire.in' => 'Le type doit être P (Principal) ou S (Secondaire).',
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
            'plan_classement_code' => [
                'required',
                'string',
                'max:20',
                'exists:plan_classement,code_classement',
                Rule::unique('calendrier_conservation')->ignore($calendrierConservation->id)
            ],
            'pieces_constituant' => 'nullable|string',
            'principal_secondaire' => 'nullable|in:P,S',
            'delai_legal' => 'nullable|string|max:50',
            'reference_juridique' => 'nullable|string',
            'archives_courantes' => 'required|string|max:100',
            'archives_intermediaires' => 'required|string|max:50',
            'sort_final' => 'required|in:C,D,T',
            'observation' => 'nullable|string',
        ], [
            'plan_classement_code.required' => 'Le plan de classement est obligatoire.',
            'plan_classement_code.unique' => 'Ce plan de classement a déjà une règle de conservation.',
            'plan_classement_code.exists' => 'Le plan de classement sélectionné n\'existe pas.',
            'archives_courantes.required' => 'La durée d\'archive courante est obligatoire.',
            'archives_intermediaires.required' => 'La durée d\'archive intermédiaire est obligatoire.',
            'sort_final.required' => 'Le sort final est obligatoire.',
            'principal_secondaire.in' => 'Le type doit être P (Principal) ou S (Secondaire).',
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
            $query->where('plan_classement_code', $request->plan_classement);
        }

        if ($request->filled('sort_final')) {
            $query->where('sort_final', $request->sort_final);
        }

        $regles = $query->orderBy('plan_classement_code')->get();

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
                'Type de Dossiers',
                'Pièces Constituant le Dossier',
                'Principal/Secondaire',
                'Délai Légal',
                'Référence Juridique',
                'Archives Courantes',
                'Archives Intermédiaires',
                'Durée Totale',
                'Sort Final',
                'Observation',
                'Date de Création'
            ], ';');
            
            foreach ($regles as $regle) {
                fputcsv($file, [
                    $regle->plan_classement_code,
                    $regle->planClassement ? $regle->planClassement->objet_classement : '',
                    $regle->pieces_constituant,
                    $regle->principal_secondaire,
                    $regle->delai_legal,
                    $regle->reference_juridique,
                    $regle->archives_courantes,
                    $regle->archives_intermediaires,
                    $regle->total_duration,
                    $regle->status,
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
    public function byPlanClassement(Request $request, $planClassementCode)
    {
        $regle = CalendrierConservation::where('plan_classement_code', $planClassementCode)
                                      ->with('planClassement')
                                      ->first();
        
        return response()->json($regle);
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
            'sort_final' => 'nullable|in:C,D,T',
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
                                       ->orderBy('plan_classement_code')
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
                'Type de Dossiers',
                'Pièces Constituant le Dossier',
                'Principal/Secondaire',
                'Délai Légal',
                'Référence Juridique',
                'Archives Courantes',
                'Archives Intermédiaires',
                'Durée Totale',
                'Sort Final',
                'Observation'
            ], ';');
            
            foreach ($regles as $regle) {
                fputcsv($file, [
                    $regle->plan_classement_code,
                    $regle->planClassement ? $regle->planClassement->objet_classement : '',
                    $regle->pieces_constituant,
                    $regle->principal_secondaire,
                    $regle->delai_legal,
                    $regle->reference_juridique,
                    $regle->archives_courantes,
                    $regle->archives_intermediaires,
                    $regle->total_duration,
                    $regle->status,
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
            'D' => 'Destruction',
            'T' => 'Tri',
        };

        $message = "{$updated} règle(s) mise(s) à jour vers '{$statusLabel}' avec succès.";

        return redirect()->route('admin.calendrier-conservation.index')
                        ->with('success', $message);
    }

    /**
     * Get statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total_regles' => CalendrierConservation::count(),
            'regles_conservation' => CalendrierConservation::where('sort_final', 'C')->count(),
            'regles_destruction' => CalendrierConservation::where('sort_final', 'D')->count(),
            'regles_tri' => CalendrierConservation::where('sort_final', 'T')->count(),
            'plans_avec_regles' => CalendrierConservation::distinct('plan_classement_code')->count(),
            'plans_sans_regles' => PlanClassement::whereNotIn('code_classement', 
                CalendrierConservation::pluck('plan_classement_code')
            )->count(),
            'recent_regles' => CalendrierConservation::with('planClassement')
                                                    ->latest()
                                                    ->limit(5)
                                                    ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Get regles for specific plan classement.
     */
    public function getReglesByPlan(Request $request, PlanClassement $planClassement)
    {
        $regle = CalendrierConservation::where('plan_classement_code', $planClassement->code_classement)
                                      ->first();

        return view('admin.calendrier-conservation.by-plan', compact('planClassement', 'regle'));
    }

    /**
     * Import regles from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:2048',
        ]);

        try {
            $file = $request->file('excel_file');
            
            // Process Excel file here
            // This would involve reading the Excel file and creating/updating records
            
            return redirect()->route('admin.calendrier-conservation.index')
                            ->with('success', 'Import réalisé avec succès.');
            
        } catch (\Exception $e) {
            return redirect()->route('admin.calendrier-conservation.index')
                            ->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    /**
     * Get conservation rules summary by category.
     */
    public function summaryByCategory()
    {
        $categories = CalendrierConservation::with('planClassement')
            ->get()
            ->groupBy(function($item) {
                // Group by first part of the code (e.g., 100, 510, 520, etc.)
                $parts = explode('.', $item->plan_classement_code);
                return $parts[0];
            })
            ->map(function($categoryItems, $categoryCode) {
                return [
                    'category_code' => $categoryCode,
                    'category_name' => $this->getCategoryName($categoryCode),
                    'total_regles' => $categoryItems->count(),
                    'conservation' => $categoryItems->where('sort_final', 'C')->count(),
                    'destruction' => $categoryItems->where('sort_final', 'D')->count(),
                    'tri' => $categoryItems->where('sort_final', 'T')->count(),
                    'regles' => $categoryItems->values()
                ];
            });

        return response()->json($categories);
    }

    /**
     * Get category name from code.
     */
    private function getCategoryName($categoryCode)
    {
        $categoryNames = [
            '100' => 'Organisation et administration',
            '510' => 'Régimes économiques douaniers',
            '520' => 'Transit et transport',
            '530' => 'Contentieux douanier',
            '540' => 'Recours et réclamations',
            '550' => 'Contrôle et vérification',
            '560' => 'Facilitations commerciales',
            '610' => 'Dédouanement des marchandises',
        ];

        return $categoryNames[$categoryCode] ?? 'Catégorie ' . $categoryCode;
    }

    /**
     * Validate conservation periods.
     */
    public function validatePeriods()
    {
        $invalidRules = [];
        $regles = CalendrierConservation::with('planClassement')->get();

        foreach ($regles as $regle) {
            $issues = [];
            
            // Check if periods are coherent
            $courante = $regle->extractNumericValue($regle->archives_courantes);
            $intermediaire = $regle->extractNumericValue($regle->archives_intermediaires);
            $delai = is_numeric($regle->delai_legal) ? (int)$regle->delai_legal : 0;
            
            if ($delai > 0 && ($courante + $intermediaire) < $delai) {
                $issues[] = 'Durée totale inférieure au délai légal';
            }
            
            if ($courante === 0 && $intermediaire === 0) {
                $issues[] = 'Aucune durée de conservation définie';
            }
            
            if (!empty($issues)) {
                $invalidRules[] = [
                    'regle' => $regle,
                    'issues' => $issues
                ];
            }
        }

        return response()->json([
            'total_checked' => $regles->count(),
            'invalid_count' => count($invalidRules),
            'invalid_rules' => $invalidRules
        ]);
    }
}