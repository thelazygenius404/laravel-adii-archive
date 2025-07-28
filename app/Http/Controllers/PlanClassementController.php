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

        $plans = $query->withCount('calendrierConservation')
                      ->orderBy('code_classement')
                      ->paginate($request->get('per_page', 15))
                      ->withQueryString();

        return view('admin.plan-classement.index', compact('plans'));
    }

    /**
     * Show the form for creating a new plan classement.
     */
    public function create()
    {
        // Get next available code
        $nextCode = PlanClassement::max('code_classement') + 1;
        
        return view('admin.plan-classement.create', compact('nextCode'));
    }

    /**
     * Store a newly created plan classement in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code_classement' => 'required|integer|min:1|unique:plan_classement',
            'objet_classement' => 'required|string|max:500',
        ], [
            'code_classement.required' => 'Le code de classement est obligatoire.',
            'code_classement.integer' => 'Le code de classement doit être un nombre entier.',
            'code_classement.unique' => 'Ce code de classement existe déjà.',
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
        $planClassement->load(['calendrierConservation' => function($query) {
            $query->orderBy('NO_regle');
        }]);
        
        // Get statistics
        $stats = [
            'total_regles' => $planClassement->calendrierConservation()->count(),
            'regles_conservation' => $planClassement->calendrierConservation()->where('sort_final', 'C')->count(),
            'regles_elimination' => $planClassement->calendrierConservation()->where('sort_final', 'E')->count(),
            'regles_tri' => $planClassement->calendrierConservation()->where('sort_final', 'T')->count(),
            'duree_moyenne' => $planClassement->calendrierConservation()->avg('delais_legaux'),
        ];

        return view('admin.plan-classement.show', compact('planClassement', 'stats'));
    }

    /**
     * Show the form for editing the specified plan classement.
     */
    public function edit(PlanClassement $planClassement)
    {
        return view('admin.plan-classement.edit', compact('planClassement'));
    }

    /**
     * Update the specified plan classement in storage.
     */
    public function update(Request $request, PlanClassement $planClassement)
    {
        $validated = $request->validate([
            'code_classement' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('plan_classement')->ignore($planClassement->id)
            ],
            'objet_classement' => 'required|string|max:500',
        ], [
            'code_classement.required' => 'Le code de classement est obligatoire.',
            'code_classement.integer' => 'Le code de classement doit être un nombre entier.',
            'code_classement.unique' => 'Ce code de classement existe déjà.',
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
        if ($planClassement->calendrierConservation()->count() > 0) {
            return redirect()->route('admin.plan-classement.index')
                            ->with('error', 'Impossible de supprimer ce plan car il contient des règles de conservation.');
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
                'Nombre de Règles',
                'Date de Création'
            ], ';');
            
            foreach ($plans as $plan) {
                fputcsv($file, [
                    $plan->formatted_code,
                    $plan->objet_classement,
                    $plan->calendrier_conservation_count ?? 0,
                    $plan->created_at->format('d/m/Y H:i:s')
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
        
        $plans = $query->select('id', 'code_classement', 'objet_classement')
                      ->orderBy('code_classement')
                      ->limit(20)
                      ->get();
        
        return response()->json($plans);
    }

    /**
     * Bulk operations.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,export',
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
            if ($plan->calendrierConservation()->count() > 0) {
                $errors[] = "Le plan '{$plan->formatted_code}' contient des règles de conservation.";
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
                'Nombre de Règles',
                'Date de Création'
            ], ';');
            
            foreach ($plans as $plan) {
                fputcsv($file, [
                    $plan->formatted_code,
                    $plan->objet_classement,
                    $plan->calendrier_conservation_count ?? 0,
                    $plan->created_at->format('d/m/Y H:i:s')
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
            'total_plans' => PlanClassement::count(),
            'total_regles' => \App\Models\CalendrierConservation::count(),
            'plans_avec_regles' => PlanClassement::has('calendrierConservation')->count(),
            'plans_sans_regles' => PlanClassement::doesntHave('calendrierConservation')->count(),
            'recent_plans' => PlanClassement::latest()->limit(5)->get(),
        ];

        return response()->json($stats);
    }
}