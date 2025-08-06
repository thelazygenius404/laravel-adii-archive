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
            <div class="col-md-3">
                <form method="GET" class="d-flex">
                    <input type="text" 
                           name="search" 
                           class="form-control me-2" 
                           placeholder="Rechercher par code ou objet..."
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search') || request('category') || request('has_rules'))
                        <a href="{{ route('admin.plan-classement.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
            
            <!-- Filtre par catégorie -->
            <div class="col-md-3">
                <form method="GET">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('has_rules'))
                        <input type="hidden" name="has_rules" value="{{ request('has_rules') }}">
                    @endif
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">Toutes les catégories</option>
                        @if(isset($categories))
                            @foreach($categories as $code => $name)
                                <option value="{{ $code }}" {{ request('category') == $code ? 'selected' : '' }}>
                                    {{ $code }} - {{ $name }}
                                </option>
                            @endforeach
                        @else
                            <!-- Catégories statiques en cas d'absence de la variable -->
                            @php
                                $staticCategories = [
                                    '100' => 'Organisation et administration',
                                    '510' => 'Régimes économiques douaniers',
                                    '520' => 'Transit et transport',
                                    '530' => 'Contentieux douanier',
                                    '540' => 'Recours et réclamations',
                                    '550' => 'Contrôle et vérification',
                                    '560' => 'Facilitations commerciales',
                                    '610' => 'Dédouanement des marchandises',
                                ];
                            @endphp
                            @foreach($staticCategories as $code => $name)
                                <option value="{{ $code }}" {{ request('category') == $code ? 'selected' : '' }}>
                                    {{ $code }} - {{ $name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </form>
            </div>

            <!-- Filtre par règles de conservation -->
            <div class="col-md-3">
                <form method="GET">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <select name="has_rules" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les plans</option>
                        <option value="1" {{ request('has_rules') === '1' ? 'selected' : '' }}>Avec règles</option>
                        <option value="0" {{ request('has_rules') === '0' ? 'selected' : '' }}>Sans règles</option>
                    </select>
                </form>
            </div>
            
            <!-- Actions et pagination -->
            <div class="col-md-3">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <!-- Export -->
                    <form method="GET" action="{{ route('admin.plan-classement.export') }}">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if(request('has_rules'))
                            <input type="hidden" name="has_rules" value="{{ request('has_rules') }}">
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
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if(request('has_rules'))
                            <input type="hidden" name="has_rules" value="{{ request('has_rules') }}">
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
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="bulkAction('create_rules')">
                            <i class="fas fa-plus me-1"></i>Créer règles
                        </button>
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
                            <i class="fas fa-tag me-1"></i>
                            Catégorie
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
                                <span class="badge bg-primary fs-6">{{ $plan->code_classement }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">{{ Str::limit($plan->objet_classement, 60) }}</div>
                                @if(strlen($plan->objet_classement) > 60)
                                    <small class="text-muted" title="{{ $plan->objet_classement }}">
                                        <i class="fas fa-info-circle"></i> Texte tronqué
                                    </small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $plan->category }}</span>
                                <br>
                                <small class="text-muted">{{ $plan->category_name }}</small>
                            </td>
                            <td>
                                @if($plan->hasConservationRule())
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i> Oui
                                    </span>
                                    <a href="{{ route('admin.calendrier-conservation.by-plan', $plan->code_classement) }}" 
                                       class="btn btn-sm btn-outline-info ms-1" title="Voir la règle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-times"></i> Non
                                    </span>
                                    <a href="{{ route('admin.calendrier-conservation.create') }}?plan={{ $plan->code_classement }}" 
                                       class="btn btn-sm btn-outline-success ms-1" title="Créer une règle">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $plan->created_at->format('d/m/Y') }}<br>
                                    {{ $plan->created_at->format('H:i') }}
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
                                            onclick="confirmDelete('{{ $plan->id }}', '{{ $plan->code_classement }}', {{ $plan->hasConservationRule() ? 'true' : 'false' }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-layer-group fa-3x mb-3"></i>
                                    <p class="mb-0">Aucun plan de classement trouvé</p>
                                    @if(request('search') || request('category') || request('has_rules'))
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
                <p class="text-danger" id="warningText"><small>Cette action est irréversible.</small></p>
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

        let confirmMessage = '';
        switch(action) {
            case 'delete':
                confirmMessage = `Êtes-vous sûr de vouloir supprimer ${selectedPlans.length} plan(s) ? Cette action supprimera aussi les règles de conservation associées.`;
                break;
            case 'export':
                confirmMessage = `Exporter ${selectedPlans.length} plan(s) sélectionné(s) ?`;
                break;
            case 'create_rules':
                confirmMessage = `Créer des règles de conservation par défaut pour ${selectedPlans.length} plan(s) sans règles ?`;
                break;
        }

        if (confirm(confirmMessage)) {
            executeBulkAction(action);
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

    function confirmDelete(planId, planCode, hasRule) {
        document.getElementById('planCode').textContent = planCode;
        document.getElementById('deleteForm').action = '{{ route("admin.plan-classement.index") }}/' + planId;
        
        const warningText = document.getElementById('warningText');
        if (hasRule) {
            warningText.innerHTML = '<small class="text-danger">Ce plan a une règle de conservation associée qui sera également supprimée. Cette action est irréversible.</small>';
        } else {
            warningText.innerHTML = '<small>Cette action est irréversible.</small>';
        }
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endpush