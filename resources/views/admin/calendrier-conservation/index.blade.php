@extends('layouts.admin')

@section('title', 'Gestion du Calendrier de Conservation')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-calendar me-2"></i>
            Gestion du Calendrier de Conservation
        </h1>
        <a href="{{ route('admin.calendrier-conservation.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>
            Ajouter une nouvelle règle
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
                           placeholder="Rechercher..."
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search') || request('plan_classement') || request('sort_final'))
                        <a href="{{ route('admin.calendrier-conservation.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
            
            <!-- Filtre par plan classement -->
            <div class="col-md-3">
                <form method="GET">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('sort_final'))
                        <input type="hidden" name="sort_final" value="{{ request('sort_final') }}">
                    @endif
                    <select name="plan_classement" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les plans</option>
                        @foreach($planClassements as $plan)
                            <option value="{{ $plan->id }}" {{ request('plan_classement') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->formatted_code }} - {{ $plan->short_description }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Filtre par sort final -->
            <div class="col-md-3">
                <form method="GET">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('plan_classement'))
                        <input type="hidden" name="plan_classement" value="{{ request('plan_classement') }}">
                    @endif
                    <select name="sort_final" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les sorts</option>
                        <option value="C" {{ request('sort_final') == 'C' ? 'selected' : '' }}>Conservation</option>
                        <option value="E" {{ request('sort_final') == 'E' ? 'selected' : '' }}>Élimination</option>
                        <option value="T" {{ request('sort_final') == 'T' ? 'selected' : '' }}>Tri</option>
                    </select>
                </form>
            </div>
            
            <!-- Actions et pagination -->
            <div class="col-md-3">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <!-- Export -->
                    <form method="GET" action="{{ route('admin.calendrier-conservation.export') }}">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request('plan_classement'))
                            <input type="hidden" name="plan_classement" value="{{ request('plan_classement') }}">
                        @endif
                        @if(request('sort_final'))
                            <input type="hidden" name="sort_final" value="{{ request('sort_final') }}">
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
                        @if(request('plan_classement'))
                            <input type="hidden" name="plan_classement" value="{{ request('plan_classement') }}">
                        @endif
                        @if(request('sort_final'))
                            <input type="hidden" name="sort_final" value="{{ request('sort_final') }}">
                        @endif
                        <span class="text-nowrap me-1">Afficher</span>
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
                    <span id="selected-count">0</span> règle(s) sélectionnée(s)
                    <div class="btn-group ms-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="showBulkSortModal()">
                            <i class="fas fa-edit me-1"></i>Modifier sort final
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

        <!-- Regles Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        <th>
                            <i class="fas fa-hashtag me-1"></i>
                            N° Règle
                        </th>
                        <th>
                            <i class="fas fa-layer-group me-1"></i>
                            Plan
                        </th>
                        <th>
                            <i class="fas fa-file-alt me-1"></i>
                            Nature du Dossier
                        </th>
                        <th>
                            <i class="fas fa-clock me-1"></i>
                            Durées (AC/AI)
                        </th>
                        <th>
                            <i class="fas fa-balance-scale me-1"></i>
                            Délais Légaux
                        </th>
                        <th>
                            <i class="fas fa-flag me-1"></i>
                            Sort Final
                        </th>
                        <th width="150">
                            <i class="fas fa-cogs me-1"></i>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($regles as $regle)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input regle-checkbox" value="{{ $regle->id }}">
                            </td>
                            <td>
                                <span class="badge bg-secondary fs-6">{{ $regle->NO_regle }}</span>
                            </td>
                            <td>
                                <div>
                                    <span class="badge bg-primary">{{ $regle->planClassement->formatted_code }}</span>
                                    <br>
                                    <small class="text-muted">{{ $regle->planClassement->short_description }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $regle->short_nature }}</div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-info me-1">{{ $regle->archive_courant }}AC</span>
                                    <span class="badge bg-warning me-1">{{ $regle->archive_intermediaire }}AI</span>
                                    <small class="text-muted">({{ $regle->total_duration }} total)</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-dark">{{ $regle->delais_legaux }} ans</span>
                            </td>
                            <td>
                                <span class="badge {{ $regle->status_badge_class }}">
                                    {{ $regle->status }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.calendrier-conservation.show', $regle) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.calendrier-conservation.edit', $regle) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Supprimer"
                                            onclick="confirmDelete('{{ $regle->id }}', '{{ $regle->NO_regle }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-calendar fa-3x mb-3"></i>
                                    <p class="mb-0">Aucune règle de conservation trouvée</p>
                                    @if(request('search') || request('plan_classement') || request('sort_final'))
                                        <p class="mt-2">
                                            <a href="{{ route('admin.calendrier-conservation.index') }}" class="btn btn-sm btn-outline-primary">
                                                Voir toutes les règles
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
        @if($regles->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $regles->firstItem() }} à {{ $regles->lastItem() }} sur {{ $regles->total() }} résultats
                </div>
                <div>
                   {{ $regles->onEachSide(1)->links('pagination::simple-bootstrap-4') }}
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
                <p>Êtes-vous sûr de vouloir supprimer la règle <strong id="regleNumber"></strong> ?</p>
                <p class="text-danger"><small>Cette action est irréversible.</small></p>
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

<!-- Bulk Sort Final Modal -->
<div class="modal fade" id="bulkSortModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier le sort final</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="bulkSortForm">
                    <div class="mb-3">
                        <label for="bulk_sort_final" class="form-label">Nouveau sort final</label>
                        <select id="bulk_sort_final" name="sort_final" class="form-select" required>
                            <option value="">Sélectionner un sort final</option>
                            <option value="C">Conservation</option>
                            <option value="E">Élimination</option>
                            <option value="T">Tri</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="executeBulkSort()">Modifier</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let selectedRegles = [];

    // Select all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.regle-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelection();
    });

    // Individual checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('regle-checkbox')) {
            updateSelection();
        }
    });

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.regle-checkbox:checked');
        selectedRegles = Array.from(checkboxes).map(cb => cb.value);
        
        const count = selectedRegles.length;
        document.getElementById('selected-count').textContent = count;
        document.getElementById('bulk-actions').style.display = count > 0 ? 'block' : 'none';
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.regle-checkbox');
        const selectAllCheckbox = document.getElementById('select-all');
        selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
        selectAllCheckbox.checked = count === allCheckboxes.length;
    }

    function clearSelection() {
        document.querySelectorAll('.regle-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }

    function bulkAction(action) {
        if (selectedRegles.length === 0) {
            alert('Veuillez sélectionner au moins une règle.');
            return;
        }

        if (action === 'delete') {
            if (confirm(`Êtes-vous sûr de vouloir supprimer ${selectedRegles.length} règle(s) ?`)) {
                executeBulkAction('delete');
            }
        } else if (action === 'export') {
            executeBulkAction('export');
        }
    }

    function showBulkSortModal() {
        if (selectedRegles.length === 0) {
            alert('Veuillez sélectionner au moins une règle.');
            return;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('bulkSortModal'));
        modal.show();
    }

    function executeBulkSort() {
        const sortFinal = document.getElementById('bulk_sort_final').value;
        if (!sortFinal) {
            alert('Veuillez sélectionner un sort final.');
            return;
        }

        const modal = bootstrap.Modal.getInstance(document.getElementById('bulkSortModal'));
        modal.hide();
        
        executeBulkAction('update_sort', { sort_final: sortFinal });
    }

    function executeBulkAction(action, data = {}) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.calendrier-conservation.bulk-action") }}';
        
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
        
        // Selected regles
        selectedRegles.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'regle_ids[]';
            input.value = id;
            form.appendChild(input);
        });
        
        // Additional data
        Object.keys(data).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = data[key];
            form.appendChild(input);
        });
        
        document.body.appendChild(form);
        form.submit();
    }

    function confirmDelete(regleId, regleNumber) {
        document.getElementById('regleNumber').textContent = regleNumber;
        document.getElementById('deleteForm').action = '{{ route("admin.calendrier-conservation.index") }}/' + regleId;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endpush