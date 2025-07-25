<?php

namespace App\Http\Controllers;

use App\Models\EntiteProductrice;
use App\Models\Organisme;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EntiteProductriceController extends Controller
{
    /**
     * Display a listing of the entite productrices.
     */
    public function index(Request $request)
    {
        $query = EntiteProductrice::with(['organisme', 'parent']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom_entite', 'LIKE', "%{$search}%")
                  ->orWhere('code_entite', 'LIKE', "%{$search}%");
            });
        }

        // Filter by organisme
        if ($request->filled('organisme')) {
            $query->where('id_organisme', $request->organisme);
        }

        // Filter by parent entite
        if ($request->filled('parent')) {
            if ($request->parent === 'root') {
                $query->whereNull('entite_parent');
            } else {
                $query->where('entite_parent', $request->parent);
            }
        }

        $entites = $query->withCount(['children', 'users'])
                        ->orderBy('nom_entite')
                        ->paginate($request->get('per_page', 10))
                        ->withQueryString();

        $organismes = Organisme::orderBy('nom_org')->get();
        $parentEntites = EntiteProductrice::orderBy('nom_entite')->get();

        return view('admin.entites.index', compact('entites', 'organismes', 'parentEntites'));
    }

    /**
     * Show the form for creating a new entite productrice.
     */
    public function create()
    {
        $organismes = Organisme::orderBy('nom_org')->get();
        $parentEntites = EntiteProductrice::orderBy('nom_entite')->get();

        return view('admin.entites.create', compact('organismes', 'parentEntites'));
    }

    /**
     * Store a newly created entite productrice in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom_entite' => 'required|string|max:255',
            'code_entite' => 'required|string|max:50|unique:entite_productrices',
            'id_organisme' => 'required|exists:organismes,id',
            'entite_parent' => 'nullable|exists:entite_productrices,id',
        ], [
            'nom_entite.required' => 'Le nom de l\'entité est obligatoire.',
            'code_entite.required' => 'Le code de l\'entité est obligatoire.',
            'code_entite.unique' => 'Ce code d\'entité existe déjà.',
            'id_organisme.required' => 'L\'organisme est obligatoire.',
            'id_organisme.exists' => 'L\'organisme sélectionné n\'existe pas.',
            'entite_parent.exists' => 'L\'entité parent sélectionnée n\'existe pas.',
        ]);

        // Check for circular reference
        if ($validated['entite_parent']) {
            $parent = EntiteProductrice::find($validated['entite_parent']);
            if ($parent && $parent->id_organisme != $validated['id_organisme']) {
                return back()->withErrors(['entite_parent' => 'L\'entité parent doit appartenir au même organisme.'])
                           ->withInput();
            }
        }

        EntiteProductrice::create($validated);

        return redirect()->route('admin.entites.index')
                        ->with('success', 'Entité productrice créée avec succès.');
    }

    /**
     * Display the specified entite productrice.
     */
    public function show(EntiteProductrice $entite)
    {
        $entite->load(['organisme', 'parent', 'children.children', 'users']);
        
        // Get statistics
        $stats = [
            'total_children' => $entite->descendants()->count(),
            'direct_children' => $entite->children()->count(),
            'total_users' => $entite->users()->count(),
            'depth_level' => $this->getDepthLevel($entite),
        ];

        return view('admin.entites.show', compact('entite', 'stats'));
    }

    /**
     * Show the form for editing the specified entite productrice.
     */
    public function edit(EntiteProductrice $entite)
    {
        $organismes = Organisme::orderBy('nom_org')->get();
        
        // Get possible parent entites (exclude self and descendants to prevent circular references)
        $parentEntites = EntiteProductrice::where('id', '!=', $entite->id)
                                         ->where('id_organisme', $entite->id_organisme)
                                         ->whereNotIn('id', $this->getDescendantIds($entite))
                                         ->orderBy('nom_entite')
                                         ->get();

        return view('admin.entites.edit', compact('entite', 'organismes', 'parentEntites'));
    }

    /**
     * Update the specified entite productrice in storage.
     */
    public function update(Request $request, EntiteProductrice $entite)
    {
        $validated = $request->validate([
            'nom_entite' => 'required|string|max:255',
            'code_entite' => ['required', 'string', 'max:50', Rule::unique('entite_productrices')->ignore($entite->id)],
            'id_organisme' => 'required|exists:organismes,id',
            'entite_parent' => 'nullable|exists:entite_productrices,id',
        ], [
            'nom_entite.required' => 'Le nom de l\'entité est obligatoire.',
            'code_entite.required' => 'Le code de l\'entité est obligatoire.',
            'code_entite.unique' => 'Ce code d\'entité existe déjà.',
            'id_organisme.required' => 'L\'organisme est obligatoire.',
            'id_organisme.exists' => 'L\'organisme sélectionné n\'existe pas.',
            'entite_parent.exists' => 'L\'entité parent sélectionnée n\'existe pas.',
        ]);

        // Check for circular reference
        if ($validated['entite_parent']) {
            if ($validated['entite_parent'] == $entite->id) {
                return back()->withErrors(['entite_parent' => 'Une entité ne peut pas être son propre parent.'])
                           ->withInput();
            }

            $parent = EntiteProductrice::find($validated['entite_parent']);
            if ($parent && $parent->id_organisme != $validated['id_organisme']) {
                return back()->withErrors(['entite_parent' => 'L\'entité parent doit appartenir au même organisme.'])
                           ->withInput();
            }

            // Check if the selected parent is a descendant
            if (in_array($validated['entite_parent'], $this->getDescendantIds($entite))) {
                return back()->withErrors(['entite_parent' => 'Impossible de sélectionner un descendant comme parent.'])
                           ->withInput();
            }
        }

        $entite->update($validated);

        return redirect()->route('admin.entites.index')
                        ->with('success', 'Entité productrice modifiée avec succès.');
    }

    /**
     * Remove the specified entite productrice from storage.
     */
    public function destroy(EntiteProductrice $entite)
    {
        // Check if entite has children
        if ($entite->children()->count() > 0) {
            return redirect()->route('admin.entites.index')
                            ->with('error', 'Impossible de supprimer cette entité car elle a des sous-entités.');
        }

        // Check if entite has users
        if ($entite->users()->count() > 0) {
            return redirect()->route('admin.entites.index')
                            ->with('error', 'Impossible de supprimer cette entité car elle a des utilisateurs assignés.');
        }

        $entite->delete();

        return redirect()->route('admin.entites.index')
                        ->with('success', 'Entité productrice supprimée avec succès.');
    }

    /**
     * Get entites by organisme for API/AJAX requests.
     */
    public function byOrganisme(Request $request, $organismeId)
    {
        $entites = EntiteProductrice::where('id_organisme', $organismeId)
                                   ->select('id', 'nom_entite', 'code_entite', 'entite_parent')
                                   ->orderBy('nom_entite')
                                   ->get();
        
        return response()->json($entites);
    }

    /**
     * Get entite hierarchy for API/AJAX requests.
     */
    public function hierarchy(Request $request, $organismeId = null)
    {
        $query = EntiteProductrice::with(['children' => function($query) {
            $query->orderBy('nom_entite');
        }]);

        if ($organismeId) {
            $query->where('id_organisme', $organismeId);
        }

        $rootEntites = $query->roots()->orderBy('nom_entite')->get();
        
        return response()->json($this->buildHierarchyTree($rootEntites));
    }

    /**
     * Export entites to Excel.
     */
    public function export(Request $request)
    {
        $query = EntiteProductrice::with(['organisme', 'parent']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom_entite', 'LIKE', "%{$search}%")
                  ->orWhere('code_entite', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('organisme')) {
            $query->where('id_organisme', $request->organisme);
        }

        $entites = $query->withCount(['children', 'users'])
                        ->orderBy('nom_entite')
                        ->get();

        $filename = 'entites_productrices_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($entites) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, ['ID', 'Nom Entité', 'Code Entité', 'Organisme', 'Entité Parent', 'Nb Sous-entités', 'Nb Utilisateurs', 'Date de création'], ';');
            
            foreach ($entites as $entite) {
                fputcsv($file, [
                    $entite->id,
                    $entite->nom_entite,
                    $entite->code_entite,
                    $entite->organisme->nom_org ?? '',
                    $entite->parent->nom_entite ?? 'Racine',
                    $entite->children_count ?? 0,
                    $entite->users_count ?? 0,
                    $entite->created_at->format('d/m/Y H:i:s')
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Move an entite to a different parent.
     */
    public function move(Request $request, EntiteProductrice $entite)
    {
        $validated = $request->validate([
            'new_parent_id' => 'nullable|exists:entite_productrices,id',
        ]);

        $newParentId = $validated['new_parent_id'];

        // Check if the new parent exists and belongs to the same organisme
        if ($newParentId) {
            $newParent = EntiteProductrice::find($newParentId);
            
            if (!$newParent || $newParent->id_organisme != $entite->id_organisme) {
                return response()->json([
                    'success' => false,
                    'message' => 'L\'entité parent doit appartenir au même organisme.'
                ], 400);
            }

            // Check for circular reference
            if ($newParentId == $entite->id || in_array($newParentId, $this->getDescendantIds($entite))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de créer une référence circulaire.'
                ], 400);
            }
        }

        $entite->update(['entite_parent' => $newParentId]);

        return response()->json([
            'success' => true,
            'message' => 'Entité déplacée avec succès.'
        ]);
    }

    /**
     * Get children of a specific entite for tree view.
     */
    public function children(EntiteProductrice $entite)
    {
        $children = $entite->children()
                          ->with(['children', 'organisme'])
                          ->withCount(['children', 'users'])
                          ->orderBy('nom_entite')
                          ->get();

        return response()->json($children);
    }

    /**
     * Get full path/breadcrumb for an entite.
     */
    public function breadcrumb(EntiteProductrice $entite)
    {
        $breadcrumb = [];
        $current = $entite;

        while ($current) {
            array_unshift($breadcrumb, [
                'id' => $current->id,
                'nom_entite' => $current->nom_entite,
                'code_entite' => $current->code_entite
            ]);
            $current = $current->parent;
        }

        return response()->json($breadcrumb);
    }

    /**
     * Bulk operations on multiple entites.
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,move,export',
            'entite_ids' => 'required|array',
            'entite_ids.*' => 'exists:entite_productrices,id',
            'new_parent_id' => 'nullable|exists:entite_productrices,id'
        ]);

        $entiteIds = $validated['entite_ids'];
        $action = $validated['action'];

        switch ($action) {
            case 'delete':
                return $this->bulkDelete($entiteIds);
            
            case 'move':
                return $this->bulkMove($entiteIds, $validated['new_parent_id'] ?? null);
            
            case 'export':
                return $this->bulkExport($entiteIds);
            
            default:
                return response()->json(['success' => false, 'message' => 'Action non reconnue.'], 400);
        }
    }

    /**
     * Get descendant IDs of an entite (for preventing circular references).
     */
    private function getDescendantIds(EntiteProductrice $entite)
    {
        $descendants = [];
        $this->collectDescendants($entite, $descendants);
        return $descendants;
    }

    /**
     * Recursively collect descendant IDs.
     */
    private function collectDescendants(EntiteProductrice $entite, &$descendants)
    {
        foreach ($entite->children as $child) {
            $descendants[] = $child->id;
            $this->collectDescendants($child, $descendants);
        }
    }

    /**
     * Get the depth level of an entite in the hierarchy.
     */
    private function getDepthLevel(EntiteProductrice $entite)
    {
        $level = 0;
        $parent = $entite->parent;
        
        while ($parent) {
            $level++;
            $parent = $parent->parent;
        }
        
        return $level;
    }

    /**
     * Build hierarchy tree for JSON response.
     */
    private function buildHierarchyTree($entites)
    {
        return $entites->map(function ($entite) {
            return [
                'id' => $entite->id,
                'nom_entite' => $entite->nom_entite,
                'code_entite' => $entite->code_entite,
                'full_name' => $entite->full_name,
                'level' => $this->getDepthLevel($entite),
                'children' => $this->buildHierarchyTree($entite->children)
            ];
        });
    }

    /**
     * Bulk delete multiple entites.
     */
    private function bulkDelete($entiteIds)
    {
        $entites = EntiteProductrice::whereIn('id', $entiteIds)->get();
        $errors = [];
        $deleted = 0;

        foreach ($entites as $entite) {
            // Check if entite has children or users
            if ($entite->children()->count() > 0) {
                $errors[] = "L'entité '{$entite->nom_entite}' a des sous-entités.";
                continue;
            }

            if ($entite->users()->count() > 0) {
                $errors[] = "L'entité '{$entite->nom_entite}' a des utilisateurs assignés.";
                continue;
            }

            $entite->delete();
            $deleted++;
        }

        $message = $deleted > 0 ? "{$deleted} entité(s) supprimée(s) avec succès." : '';
        if (!empty($errors)) {
            $message .= ' Erreurs: ' . implode(' ', $errors);
        }

         return redirect()->route('admin.entites.index')
                        ->with($message ? 'success' : 'error', $message);
    }

    /**
     * Bulk move multiple entites.
     */
    private function bulkMove($entiteIds, $newParentId)
    {
        $entites = EntiteProductrice::whereIn('id', $entiteIds)->get();
        $errors = [];
        $moved = 0;

        // Validate new parent if provided
        $newParent = null;
        if ($newParentId) {
            $newParent = EntiteProductrice::find($newParentId);
            if (!$newParent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Entité parent non trouvée.'
                ], 400);
            }
        }

        foreach ($entites as $entite) {
            // Check organisme compatibility
            if ($newParent && $newParent->id_organisme != $entite->id_organisme) {
                $errors[] = "L'entité '{$entite->nom_entite}' ne peut pas être déplacée vers un organisme différent.";
                continue;
            }

            // Check circular reference
            if ($newParentId && ($newParentId == $entite->id || in_array($newParentId, $this->getDescendantIds($entite)))) {
                $errors[] = "Référence circulaire détectée pour '{$entite->nom_entite}'.";
                continue;
            }

            $entite->update(['entite_parent' => $newParentId]);
            $moved++;
        }

        $message = $moved > 0 ? "{$moved} entité(s) déplacée(s) avec succès." : '';
        if (!empty($errors)) {
            $message .= ' Erreurs: ' . implode(' ', $errors);
        }

        return response()->json([
            'success' => $moved > 0,
            'message' => $message,
            'moved' => $moved,
            'errors' => $errors
        ]);
    }

    /**
     * Bulk export specific entites.
     */
    private function bulkExport($entiteIds)
    {
        $entites = EntiteProductrice::with(['organisme', 'parent'])
                                   ->withCount(['children', 'users'])
                                   ->whereIn('id', $entiteIds)
                                   ->orderBy('nom_entite')
                                   ->get();

        $filename = 'entites_selection_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($entites) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers
            fputcsv($file, ['ID', 'Nom Entité', 'Code Entité', 'Organisme', 'Entité Parent', 'Hiérarchie Complète', 'Nb Sous-entités', 'Nb Utilisateurs', 'Date de création'], ';');
            
            foreach ($entites as $entite) {
                fputcsv($file, [
                    $entite->id,
                    $entite->nom_entite,
                    $entite->code_entite,
                    $entite->organisme->nom_org ?? '',
                    $entite->parent->nom_entite ?? 'Racine',
                    $entite->full_name,
                    $entite->children_count ?? 0,
                    $entite->users_count ?? 0,
                    $entite->created_at->format('d/m/Y H:i:s')
                ], ';');
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get statistics for an entite.
     */
    public function statistics(EntiteProductrice $entite)
    {
        $stats = [
            'id' => $entite->id,
            'nom_entite' => $entite->nom_entite,
            'code_entite' => $entite->code_entite,
            'organisme' => $entite->organisme->nom_org,
            'depth_level' => $this->getDepthLevel($entite),
            'direct_children' => $entite->children()->count(),
            'total_descendants' => count($this->getDescendantIds($entite)),
            'direct_users' => $entite->users()->count(),
            'total_users_in_hierarchy' => $this->getTotalUsersInHierarchy($entite),
            'parent_chain' => $this->getParentChain($entite),
            'created_at' => $entite->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $entite->updated_at->format('d/m/Y H:i:s'),
        ];

        return response()->json($stats);
    }

    /**
     * Get total users in the entire hierarchy (including descendants).
     */
    private function getTotalUsersInHierarchy(EntiteProductrice $entite)
    {
        $total = $entite->users()->count();
        
        foreach ($entite->children as $child) {
            $total += $this->getTotalUsersInHierarchy($child);
        }
        
        return $total;
    }

    /**
     * Get the parent chain for an entite.
     */
    private function getParentChain(EntiteProductrice $entite)
    {
        $chain = [];
        $current = $entite->parent;
        
        while ($current) {
            array_unshift($chain, [
                'id' => $current->id,
                'nom_entite' => $current->nom_entite,
                'code_entite' => $current->code_entite
            ]);
            $current = $current->parent;
        }
        
        return $chain;
    }
}