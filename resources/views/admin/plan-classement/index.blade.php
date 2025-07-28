@extends('layouts.admin')

@section('title', 'Gestion du Plan de Classement')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-layer-group me-2"></i>
            Gestion du Plan de Classement
        </h1>
        <a href="{{ route('admin.plan-classement.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>
            Ajouter un nouveau plan
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filters and Search -->
        <div class="row mb-4">
            <!-- Recherche -->
            <div class="col-md-6">
                <form method="GET" class="d-flex">
                    <input type="text" 
                           name="search" 
                           class="form-control me-2" 
                           placeholder="Rechercher par code ou objet..."
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.plan-classement.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
            
            <!-- Actions et pagination -->
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <!-- Export -->
                    <form method="GET" action="{{ route('admin.plan-classement.export') }}">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <button type="submit" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-excel me-1"></i>
                            Exporter
                        </button>
                    </form>
                    
                    <!-- Pagination -->
                    <form method="GET" class="d-flex align-items-center">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <span class="text-nowrap me-2">Afficher</span>
                        <select name="per_page" class="form-select form-select-sm" style="width: 80px;" onchange="this.form.submit()">
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="row mb-3" id="bulk-actions" style="display: none;">
            <div class="col-12">
                <div class="alert alert-info">
                    <span id="selected-count">0</span> plan(s) sélectionné(s)
                    <div class="btn-group ms-3">
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="bulkAction('export')">
                            <i class="fas fa-file-excel me-1"></i>Exporter
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="bulkAction('delete')">
                            <i class="fas fa-trash me-1"></i>Supprimer
                        </button>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary ms-2" onclick="clearSelection()">
                        Annuler sélection
                    </button>
                </div>
            </div>
        </div>

        <!-- Plans Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        <th>
                            <i class="fas fa-code me-1"></i>
                            Code
                        </th>
                        <th>
                            <i class="fas fa-file-alt me-1"></i>
                            Objet de Classement
                        </th>
                        <th>
                            <i class="fas fa-calendar me-1"></i>
                            Règles de Conservation
                        </th>
                        <th>
                            <i class="fas fa-clock me-1"></i>
                            Date de Création
                        </th>
                        <th width="150">
                            <i class="fas fa-cogs me-1"></i>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input plan-checkbox" value="{{ $plan->id }}">
                            </td>
                            <td>
                                <span class="badge bg-primary fs-6">{{ $plan->formatted_code }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $plan->short_description }}</div>
                                @if(strlen($plan->objet_classement) > 100)
                                    <small class="text-muted" title="{{ $plan->objet_classement }}">
                                        Cliquer pour voir le texte complet
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $plan->calendrier_conservation_count ?? 0 }} règle(s)
                                </span>
                                @if($plan->calendrier_conservation_count > 0)
                                    <a href="{{ route('admin.calendrier-conservation.index', ['plan_classement' => $plan->id]) }}" 
                                       class="btn btn-sm btn-outline-info ms-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $plan->created_at->format('d/m/Y à H:i') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.plan-classement.show', $plan) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.plan-classement.edit', $plan) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Supprimer"
                                            onclick="confirmDelete('{{ $plan->id }}', '{{ $plan->formatted_code }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-layer-group fa-3x mb-3"></i>
                                    <p class="mb-0">Aucun plan de classement trouvé</p>
                                    @if(request('search'))
                                        <p class="mt-2">
                                            <a href="{{ route('admin.plan-classement.index') }}" class="btn btn-sm btn-outline-primary">
                                                Voir tous les plans
                                            </a>
                                        </p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($plans->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $plans->firstItem() }} à {{ $plans->lastItem() }} sur {{ $plans->total() }} résultats
                </div>
                <div>
                    {{ $plans->onEachSide(1)->links('pagination::simple-bootstrap-4') }}
                </div>

            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le plan <strong id="planCode"></strong> ?</p>
                <p class="text-danger"><small>Cette action supprimera aussi toutes les règles de conservation associées.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let selectedPlans = [];

    // Select all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.plan-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelection();
    });

    // Individual checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('plan-checkbox')) {
            updateSelection();
        }
    });

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.plan-checkbox:checked');
        selectedPlans = Array.from(checkboxes).map(cb => cb.value);
        
        const count = selectedPlans.length;
        document.getElementById('selected-count').textContent = count;
        document.getElementById('bulk-actions').style.display = count > 0 ? 'block' : 'none';
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.plan-checkbox');
        const selectAllCheckbox = document.getElementById('select-all');
        selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
        selectAllCheckbox.checked = count === allCheckboxes.length;
    }

    function clearSelection() {
        document.querySelectorAll('.plan-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }

    function bulkAction(action) {
        if (selectedPlans.length === 0) {
            alert('Veuillez sélectionner au moins un plan.');
            return;
        }

        if (action === 'delete') {
            if (confirm(`Êtes-vous sûr de vouloir supprimer ${selectedPlans.length} plan(s) ?`)) {
                executeBulkAction('delete');
            }
        } else if (action === 'export') {
            executeBulkAction('export');
        }
    }

    function executeBulkAction(action) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.plan-classement.bulk-action") }}';
        
        // CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Action
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);
        
        // Selected plans
        selectedPlans.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'plan_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }

    function confirmDelete(planId, planCode) {
        document.getElementById('planCode').textContent = planCode;
        document.getElementById('deleteForm').action = '{{ route("admin.plan-classement.index") }}/' + planId;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endpush