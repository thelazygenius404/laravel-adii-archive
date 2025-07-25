@extends('layouts.admin')

@section('title', 'Gestion des Entités Productrices')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-building me-2"></i>
            Gestion des Entités Productrices
        </h1>
        <a href="{{ route('admin.entites.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>
            Ajouter une nouvelle entité
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
                    @if(request('search') || request('organisme') || request('parent'))
                        <a href="{{ route('admin.entites.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
            
            <!-- Filtre par organisme -->
            <div class="col-md-3">
                <form method="GET">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('parent'))
                        <input type="hidden" name="parent" value="{{ request('parent') }}">
                    @endif
                    <select name="organisme" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les organismes</option>
                        @foreach($organismes as $organisme)
                            <option value="{{ $organisme->id }}" {{ request('organisme') == $organisme->id ? 'selected' : '' }}>
                                {{ $organisme->nom_org }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Filtre par parent -->
            <div class="col-md-3">
                <form method="GET">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('organisme'))
                        <input type="hidden" name="organisme" value="{{ request('organisme') }}">
                    @endif
                    <select name="parent" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les niveaux</option>
                        <option value="root" {{ request('parent') == 'root' ? 'selected' : '' }}>Entités racines</option>
                        @foreach($parentEntites as $parent)
                            <option value="{{ $parent->id }}" {{ request('parent') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->nom_entite }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            
            <!-- Actions et pagination -->
            <div class="col-md-3">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <!-- Export -->
                    <form method="GET" action="{{ route('admin.entites.export') }}">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request('organisme'))
                            <input type="hidden" name="organisme" value="{{ request('organisme') }}">
                        @endif
                        @if(request('parent'))
                            <input type="hidden" name="parent" value="{{ request('parent') }}">
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
                        @if(request('organisme'))
                            <input type="hidden" name="organisme" value="{{ request('organisme') }}">
                        @endif
                        @if(request('parent'))
                            <input type="hidden" name="parent" value="{{ request('parent') }}">
                        @endif
                        <span class="text-nowrap me-1">Afficher</span>
                        <select name="per_page" class="form-select form-select-sm" style="width: 80px;" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
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
                    <span id="selected-count">0</span> entité(s) sélectionnée(s)
                    <div class="btn-group ms-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="bulkAction('move')">
                            <i class="fas fa-arrows-alt me-1"></i>Déplacer
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

        <!-- Entités Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        <th>
                            <i class="fas fa-building me-1"></i>
                            Nom de l'Entité
                        </th>
                        <th>
                            <i class="fas fa-code me-1"></i>
                            Code
                        </th>
                        <th>
                            <i class="fas fa-sitemap me-1"></i>
                            Organisme
                        </th>
                        <th>
                            <i class="fas fa-layer-group me-1"></i>
                            Entité Parent
                        </th>
                        <th>
                            <i class="fas fa-users me-1"></i>
                            Utilisateurs
                        </th>
                        <th>
                            <i class="fas fa-project-diagram me-1"></i>
                            Sous-entités
                        </th>
                        <th width="150">
                            <i class="fas fa-cogs me-1"></i>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entites as $entite)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input entite-checkbox" value="{{ $entite->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @php
                                        $level = 0;
                                        $parent = $entite->parent;
                                        while ($parent) {
                                            $level++;
                                            $parent = $parent->parent;
                                        }
                                    @endphp
                                    
                                    @for($i = 0; $i < $level; $i++)
                                        <span class="me-2 text-muted">└─</span>
                                    @endfor
                                    
                                    <div class="avatar me-3">
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                            <i class="fas fa-building"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <strong>{{ $entite->nom_entite }}</strong>
                                        @if($level > 0)
                                            <br><small class="text-muted">Niveau {{ $level + 1 }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $entite->code_entite }}</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $entite->organisme->nom_org ?? 'N/A' }}</span>
                            </td>
                            <td>
                                @if($entite->parent)
                                    <span class="text-primary">{{ $entite->parent->nom_entite }}</span>
                                @else
                                    <span class="badge bg-warning">Racine</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $entite->users_count ?? 0 }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    {{ $entite->children_count ?? 0 }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.entites.show', $entite) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.entites.edit', $entite) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Supprimer"
                                            onclick="confirmDelete('{{ $entite->id }}', '{{ $entite->nom_entite }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-building fa-3x mb-3"></i>
                                    <p class="mb-0">Aucune entité productrice trouvée</p>
                                    @if(request('search') || request('organisme') || request('parent'))
                                        <p class="mt-2">
                                            <a href="{{ route('admin.entites.index') }}" class="btn btn-sm btn-outline-primary">
                                                Voir toutes les entités
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
        @if($entites->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-1">
                <div class="text-muted">
                    Affichage de {{ $entites->firstItem() }} à {{ $entites->lastItem() }} sur {{ $entites->total() }} résultats
                </div>
                <div>
                    {{ $entites->links() }}
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
                <p>Êtes-vous sûr de vouloir supprimer l'entité <strong id="entiteName"></strong> ?</p>
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

<!-- Bulk Move Modal -->
<div class="modal fade" id="bulkMoveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Déplacer les entités sélectionnées</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="bulkMoveForm">
                    <div class="mb-3">
                        <label for="new_parent_id" class="form-label">Nouvelle entité parent</label>
                        <select id="new_parent_id" name="new_parent_id" class="form-select">
                            <option value="">Aucun parent (racine)</option>
                            @foreach($parentEntites as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->nom_entite }} ({{ $parent->organisme->nom_org }})</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="executeBulkMove()">Déplacer</button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    let selectedEntites = [];

    // Select all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.entite-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelection();
    });

    // Individual checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('entite-checkbox')) {
            updateSelection();
        }
    });

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.entite-checkbox:checked');
        selectedEntites = Array.from(checkboxes).map(cb => cb.value);
        
        const count = selectedEntites.length;
        document.getElementById('selected-count').textContent = count;
        document.getElementById('bulk-actions').style.display = count > 0 ? 'block' : 'none';
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.entite-checkbox');
        const selectAllCheckbox = document.getElementById('select-all');
        selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
        selectAllCheckbox.checked = count === allCheckboxes.length;
    }

    function clearSelection() {
        document.querySelectorAll('.entite-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }

    function bulkAction(action) {
        if (selectedEntites.length === 0) {
            alert('Veuillez sélectionner au moins une entité.');
            return;
        }

        switch(action) {
            case 'delete':
                if (confirm(`Êtes-vous sûr de vouloir supprimer ${selectedEntites.length} entité(s) ?`)) {
                    executeBulkAction('delete');
                }
                break;
            case 'move':
                const modal = new bootstrap.Modal(document.getElementById('bulkMoveModal'));
                modal.show();
                break;
            case 'export':
                executeBulkAction('export');
                break;
        }
    }

    function executeBulkAction(action, data = {}) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.entites.bulk-action") }}';
        
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
        
        // Selected entites
        selectedEntites.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'entite_ids[]';
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

    function executeBulkMove() {
        const newParentId = document.getElementById('new_parent_id').value;
        const modal = bootstrap.Modal.getInstance(document.getElementById('bulkMoveModal'));
        modal.hide();
        
        executeBulkAction('move', { new_parent_id: newParentId });
    }

    function confirmDelete(entiteId, entiteName) {
        document.getElementById('entiteName').textContent = entiteName;
        document.getElementById('deleteForm').action = '{{ route("admin.entites.index") }}/' + entiteId;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endpush