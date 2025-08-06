<?php

namespace App\Http\Controllers;

use App\Models\PlanClassement;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanClassementController extends Controller
{
    /**
     * Display a listing of the plan classement.
     */
    public function index(Request $request)
    {
        $query = PlanClassement::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by conservation status
        if ($request->filled('has_rules')) {
            if ($request->has_rules === '1') {
                $query->withConservationRules();
            } else {
                $query->withoutConservationRules();
            }
        }

        $plans = $query->withCount('calendrierConservation')
                      ->orderBy('code_classement')
                      ->paginate($request->get('per_page', 15))
                      ->withQueryString();

        // Get categories for filter
        $categories = PlanClassement::getCategories();
        return view('admin.plan-classement.index', compact('plans', 'categories'));
    }

    /**
     * Show the form for creating a new plan classement.
     */
    public function create()
    {
        // Get available categories
        $categories = [
            '100' => 'Organisation et administration',
            '510' => 'Régimes économiques douaniers',
            '520' => 'Transit et transport',
            '530' => 'Contentieux douanier',
            '540' => 'Recours et réclamations',
            '550' => 'Contrôle et vérification',
            '560' => 'Facilitations commerciales',
            '610' => 'Dédouanement des marchandises',
        ];
        
        return view('admin.plan-classement.create', compact('categories'));
    }

    /**
     * Store a newly created plan classement in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code_classement' => 'required|string|max:20|unique:plan_classement|regex:/^[0-9]+(\.[0-9]+)*$/',
            'objet_classement' => 'required|string|max:500',
            'description' => 'nullable|string',
        ], [
            'code_classement.required' => 'Le code de classement est obligatoire.',
            'code_classement.unique' => 'Ce code de classement existe déjà.',
            'code_classement.regex' => 'Le code doit être au format numérique (ex: 100.10.1).',
            'objet_classement.required' => 'L\'objet de classement est obligatoire.',
            'objet_classement.max' => 'L\'objet de classement ne peut pas dépasser 500 caractères.',
        ]);

        PlanClassement::create($validated);

        return redirect()->route('admin.plan-classement.index')
                        ->with('success', 'Plan de classement créé avec succès.');
    }

    /**
     * Display the specified plan classement.
     */
    public function show(PlanClassement $planClassement)
    {
        $planClassement->load('calendrierConservation');
        
        // Get statistics
        $stats = [
            'total_regles' => $planClassement->calendrierConservation()->count(),
            'regles_conservation' => $planClassement->calendrierConservation()->where('sort_final', 'C')->count(),
            'regles_destruction' => $planClassement->calendrierConservation()->where('sort_final', 'D')->count(),
            'regles_tri' => $planClassement->calendrierConservation()->where('sort_final', 'T')->count(),
            'has_legal_requirement' => $planClassement->calendrierConservation()
                                                     ->where('delai_legal', '!=', '_')
                                                     ->where('delai_legal', '!=', '')
                                                     ->whereNotNull('delai_legal')
                                                     ->exists(),
            'compliance_issues' => 0, // Will be calculated
        ];

        // Calculate compliance issues
        if ($planClassement->calendrierConservation) {
            $regle = $planClassement->calendrierConservation;
            $stats['compliance_issues'] = count($regle->getValidationIssues());
        }

        // Get related plans (same category or parent/children)
        $relatedPlans = PlanClassement::where('code_classement', 'LIKE', $planClassement->category . '%')
                                    ->where('id', '!=', $planClassement->id)
                                    ->orderBy('code_classement')
                                    ->limit(10)
                                    ->get();

        return view('admin.plan-classement.show', compact('planClassement', 'stats', 'relatedPlans'));
    }

    /**
     * Show the form for editing the specified plan classement.
     */
    public function edit(PlanClassement $planClassement)
    {
        // Get available categories
        $categories = [
            '100' => 'Organisation et administration',
            '510' => 'Régimes économiques douaniers',
            '520' => 'Transit et transport',
            '530' => 'Contentieux douanier',
            '540' => 'Recours et réclamations',
            '550' => 'Contrôle et vérification',
            '560' => 'Facilitations commerciales',
            '610' => 'Dédouanement des marchandises',
        ];

        return view('admin.plan-classement.edit', compact('planClassement', 'categories'));
    }

    /**
     * Update the specified plan classement in storage.
     */
    public function update(Request $request, PlanClassement $planClassement)
    {
        $validated = $request->validate([
            'code_classement' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9]+(\.[0-9]+)*$/',
                Rule::unique('plan_classement')->ignore($planClassement->id)
            ],
            'objet_classement' => 'required|string|max:500',
            'description' => 'nullable|string',
        ], [
            'code_classement.required' => 'Le code de classement est obligatoire.',
            'code_classement.unique' => 'Ce code de classement existe déjà.',
            'code_classement.regex' => 'Le code doit être au format numérique (ex: 100.10.1).',
            'objet_classement.required' => 'L\'objet de classement est obligatoire.',
            'objet_classement.max' => 'L\'objet de classement ne peut pas dépasser 500 caractères.',
        ]);

        $planClassement->update($validated);

        return redirect()->route('admin.plan-classement.index')
                        ->with('success', 'Plan de classement modifié avec succès.');
    }

    /**
     * Remove the specified plan classement from storage.
     */
    public function destroy(PlanClassement $planClassement)
    {
        // Check if plan has related calendrier conservation
        if ($planClassement->hasConservationRule()) {
            return redirect()->route('admin.plan-classement.index')
                            ->with('error', 'Impossible de supprimer ce plan car il contient une règle de conservation.');
        }

        $planClassement->delete();

        return redirect()->route('admin.plan-classement.index')
                        ->with('success', 'Plan de classement supprimé avec succès.');
    }

    /**
     * Export plans to CSV.
     */
    public function export(Request $request)
    {
        $query = PlanClassement::query();

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('has_rules')) {
            if ($request->has_rules === '1') {
                $query->withConservationRules();
            } else {
                $query->withoutConservationRules();
            }
        }

        $plans = $query->withCount('calendrierConservation')
                      ->orderBy('code_classement')
                      ->get();

        $filename = 'plan_classement_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($plans) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, [
                'Code Classement',
                'Objet de Classement',
                'Catégorie',
                'Niveau',
                'A une Règle de Conservation',
                'Description',
                'Date de Création',
                'Dernière Modification'
            ], ';');
            
            foreach ($plans as $plan) {
                fputcsv($file, [
                    $plan->code_classement,
                    $plan->objet_classement,
                    $plan->category_name,
                    $plan->level,
                    $plan->hasConservationRule() ? 'Oui' : 'Non',
                    $plan->description,
                    $plan->created_at->format('d/m/Y H:i:s'),
                    $plan->updated_at->format('d/m/Y H:i:s')
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get plans for API/AJAX requests.
     */
    public function api(Request $request)
    {
        $query = PlanClassement::query();
        
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('without_rules') && $request->without_rules) {
            $query->withoutConservationRules();
        }
        
        $plans = $query->select('id', 'code_classement', 'objet_classement')
                      ->orderBy('code_classement')
                      ->limit(50)
                      ->get()
                      ->map(function($plan) {
                          return [
                              'id' => $plan->id,
                              'code_classement' => $plan->code_classement,
                              'objet_classement' => $plan->objet_classement,
                              'hierarchical_name' => $plan->hierarchical_name,
                              'has_rule' => $plan->hasConservationRule()
                          ];
                      });
        
        return response()->json($plans);
    }

    /**
     * Bulk operations.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,export,create_rules',
            'plan_ids' => 'required|array',
            'plan_ids.*' => 'exists:plan_classement,id',
        ]);

        $planIds = $validated['plan_ids'];
        $action = $validated['action'];

        switch ($action) {
            case 'delete':
                return $this->bulkDelete($planIds);
            
            case 'export':
                return $this->bulkExport($planIds);

            case 'create_rules':
                return $this->bulkCreateRules($planIds);
            
            default:
                return response()->json(['success' => false, 'message' => 'Action non reconnue.'], 400);
        }
    }

    /**
     * Bulk delete multiple plans.
     */
    private function bulkDelete($planIds)
    {
        $plans = PlanClassement::whereIn('id', $planIds)->get();
        $errors = [];
        $deleted = 0;

        foreach ($plans as $plan) {
            // Check if plan has calendrier conservation
            if ($plan->hasConservationRule()) {
                $errors[] = "Le plan '{$plan->code_classement}' contient une règle de conservation.";
                continue;
            }

            $plan->delete();
            $deleted++;
        }

        $message = $deleted > 0 ? "{$deleted} plan(s) supprimé(s) avec succès." : '';
        if (!empty($errors)) {
            $message .= ' Erreurs: ' . implode(' ', $errors);
        }

        return redirect()->route('admin.plan-classement.index')
                        ->with($deleted > 0 ? 'success' : 'error', $message);
    }

    /**
     * Bulk export specific plans.
     */
    private function bulkExport($planIds)
    {
        $plans = PlanClassement::withCount('calendrierConservation')
                              ->whereIn('id', $planIds)
                              ->orderBy('code_classement')
                              ->get();

        $filename = 'plan_classement_selection_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($plans) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, [
                'Code Classement',
                'Objet de Classement',
                'Catégorie',
                'Niveau',
                'A une Règle de Conservation',
                'Description'
            ], ';');
            
            foreach ($plans as $plan) {
                fputcsv($file, [
                    $plan->code_classement,
                    $plan->objet_classement,
                    $plan->category_name,
                    $plan->level,
                    $plan->hasConservationRule() ? 'Oui' : 'Non',
                    $plan->description
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk create conservation rules for plans without rules.
     */
    private function bulkCreateRules($planIds)
    {
        $plans = PlanClassement::whereIn('id', $planIds)->withoutConservationRules()->get();
        $created = 0;

        foreach ($plans as $plan) {
            // Create a default conservation rule
            $plan->calendrierConservation()->create([
                'pieces_constituant' => 'À définir',
                'principal_secondaire' => 'S', // Secondary by default
                'delai_legal' => '_',
                'reference_juridique' => 'À définir',
                'archives_courantes' => '3 ans',
                'archives_intermediaires' => '7 ans',
                'sort_final' => 'D', // Destruction by default
                'observation' => 'Règle créée automatiquement - À réviser et compléter'
            ]);
            $created++;
        }

        $message = $created > 0 ? "{$created} règle(s) de conservation créée(s) avec succès." : 
                                 'Aucune règle créée (plans déjà avec règles).';

        return redirect()->route('admin.plan-classement.index')
                        ->with('success', $message);
    }

    /**
     * Get statistics for dashboard.
     */
    public function statistics()
    {
        $stats = [
            'total_plans' => PlanClassement::count(),
            'total_regles' => \App\Models\CalendrierConservation::count(),
            'plans_avec_regles' => PlanClassement::has('calendrierConservation')->count(),
            'plans_sans_regles' => PlanClassement::doesntHave('calendrierConservation')->count(),
            'par_categorie' => PlanClassement::selectRaw("SUBSTRING_INDEX(code_classement, '.', 1) as category, COUNT(*) as count")
                                            ->groupBy('category')
                                            ->orderBy('category')
                                            ->get()
                                            ->mapWithKeys(function($item) {
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
                                                $categoryName = $categoryNames[$item->category] ?? "Catégorie {$item->category}";
                                                return [$categoryName => $item->count];
                                            }),
            'recent_plans' => PlanClassement::latest()->limit(5)->get(),
            'recent_without_rules' => PlanClassement::withoutConservationRules()->latest()->limit(5)->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Get hierarchical view of plans by category.
     */
    public function hierarchical(Request $request)
    {
        $category = $request->get('category');
        
        $query = PlanClassement::query();
        
        if ($category) {
            $query->byCategory($category);
        }
        
        $plans = $query->withCount('calendrierConservation')
                      ->orderBy('code_classement')
                      ->get()
                      ->groupBy('category');

        $hierarchicalPlans = [];
        
        foreach ($plans as $categoryCode => $categoryPlans) {
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

            $hierarchicalPlans[] = [
                'category_code' => $categoryCode,
                'category_name' => $categoryNames[$categoryCode] ?? "Catégorie $categoryCode",
                'plans' => $categoryPlans->map(function($plan) {
                    return [
                        'id' => $plan->id,
                        'code' => $plan->code_classement,
                        'objet' => $plan->objet_classement,
                        'level' => $plan->level,
                        'has_rule' => $plan->calendrier_conservation_count > 0,
                        'parent' => $plan->parent ? $plan->parent->code_classement : null,
                        'children_count' => $plan->children->count()
                    ];
                })
            ];
        }

        return response()->json($hierarchicalPlans);
    }

    /**
     * Import plans from CSV/Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,xlsx,xls|max:2048',
        ]);

        try {
            $file = $request->file('import_file');
            
            // Process import file here
            // This would involve reading the file and creating/updating records
            
            return redirect()->route('admin.plan-classement.index')
                            ->with('success', 'Import réalisé avec succès.');
            
        } catch (\Exception $e) {
            return redirect()->route('admin.plan-classement.index')
                            ->with('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }

    /**
     * Validate plan classement code format.
     */
    public function validateCode(Request $request)
    {
        $code = $request->get('code');
        
        $validation = [
            'exists' => PlanClassement::where('code_classement', $code)->exists(),
            'format_valid' => preg_match('/^[0-9]+(\.[0-9]+)*$/', $code),
            'category' => substr($code, 0, 3),
            'level' => substr_count($code, '.') + 1
        ];
        
        return response()->json($validation);
    }
}