@extends('layouts.admin')

@section('title', 'Gestion des Positions')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-map-marker-alt me-2"></i>
            Gestion des Positions
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.tablettes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour
            </a>
            <a href="{{ route('admin.positions.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>
                Nouvelle Position
            </a>
        </div>
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
                           placeholder="Nom de position..."
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request()->hasAny(['search', 'tablette_id', 'status']))
                        <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
            
            <!-- Filtre par tablette -->
            <div class="col-md-3">
                <form method="GET">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <select name="tablette_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Toutes les tablettes</option>
                        @foreach($tablettes as $tablette)
                            <option value="{{ $tablette->id }}" {{ request('tablette_id') == $tablette->id ? 'selected' : '' }}>
                                {{ $tablette->nom }} ({{ $tablette->travee->nom }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Filtre par statut -->
            <div class="col-md-3">
                <form method="GET">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('tablette_id'))
                        <input type="hidden" name="tablette_id" value="{{ request('tablette_id') }}">
                    @endif
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="libre" {{ request('status') == 'libre' ? 'selected' : '' }}>Libres</option>
                        <option value="occupee" {{ request('status') == 'occupee' ? 'selected' : '' }}>Occupées</option>
                    </select>
                </form>
            </div>
            
            <!-- Actions et pagination -->
            <div class="col-md-3">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <!-- Pagination -->
                    <form method="GET" class="d-flex align-items-center">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request('tablette_id'))
                            <input type="hidden" name="tablette_id" value="{{ request('tablette_id') }}">
                        @endif
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
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

        <!-- Statistiques rapides -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <i class="fas fa-map-marker-alt text-primary fa-3x mb-3"></i>
                        <h3 class="text-primary">{{ $positions->total() }}</h3>
                        <p class="text-muted mb-0">Positions Totales</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <h3 class="text-success">{{ $positions->where('vide', false)->count() }}</h3>
                        <p class="text-muted mb-0">Positions Occupées</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-circle-notch text-warning fa-3x mb-3"></i>
                        <h3 class="text-warning">{{ $positions->where('vide', true)->count() }}</h3>
                        <p class="text-muted mb-0">Positions Libres</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <i class="fas fa-tablet-alt text-info fa-3x mb-3"></i>
                        <h3 class="text-info">{{ $tablettes->count() }}</h3>
                        <p class="text-muted mb-0">Tablettes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="row mb-3" id="bulk-actions" style="display: none;">
            <div class="col-12">
                <div class="alert alert-info">
                    <span id="selected-count">0</span> position(s) sélectionnée(s)
                    <div class="btn-group ms-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="bulkAction('export')">
                            <i class="fas fa-file-excel me-1"></i>Exporter
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="bulkAction('move')">
                            <i class="fas fa-arrows-alt me-1"></i>Déplacer
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

        <!-- Positions Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        <th>
                            <i class="fas fa-map-marker-alt me-1"></i>
                            Position
                        </th>
                        <th>
                            <i class="fas fa-building me-1"></i>
                            Localisation
                        </th>
                        <th>
                            <i class="fas fa-info-circle me-1"></i>
                            Statut
                        </th>
                        <th>
                            <i class="fas fa-archive me-1"></i>
                            Contenu
                        </th>
                        <th>
                            <i class="fas fa-clock me-1"></i>
                            Modifiée
                        </th>
                        <th width="150">
                            <i class="fas fa-cogs me-1"></i>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($positions as $position)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input position-checkbox" value="{{ $position->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar bg-{{ $position->vide ? 'warning' : 'success' }} text-white rounded">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $position->nom }}</div>
                                        <small class="text-muted">{{ $position->tablette->nom }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $position->tablette->travee->salle->nom }}</span>
                                    <small class="text-muted">
                                        {{ $position->tablette->travee->salle->organisme->nom_org }}
                                    </small>
                                    <small class="text-muted">{{ $position->tablette->travee->nom }}</small>
                                </div>
                            </td>
                            <td>
                                @if($position->vide)
                                    <span class="badge bg-warning">Libre</span>
                                @else
                                    <span class="badge bg-success">Occupée</span>
                                @endif
                            </td>
                            <td>
                                @if(!$position->vide && $position->boite)
                                    <a href="{{ route('admin.boites.show', $position->boite) }}" class="text-decoration-none">
                                        <span class="badge bg-primary">{{ $position->boite->numero }}</span>
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-center">
                                    <div>{{ $position->updated_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $position->updated_at->format('H:i') }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.positions.show', $position) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.positions.edit', $position) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($position->vide)
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Supprimer"
                                                onclick="confirmDelete('{{ $position->id }}', '{{ $position->nom }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-secondary" 
                                                disabled 
                                                title="Position occupée">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                                    <p class="mb-0">Aucune position trouvée</p>
                                    @if(request()->hasAny(['search', 'tablette_id', 'status']))
                                        <p class="mt-2">
                                            <a href="{{ route('admin.positions.index') }}" class="btn btn-sm btn-outline-primary">
                                                Voir toutes les positions
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
        @if($positions->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $positions->firstItem() }} à {{ $positions->lastItem() }} sur {{ $positions->total() }} résultats
                </div>
                <div>
                   {{ $positions->onEachSide(1)->links('pagination::simple-bootstrap-4') }}
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
                <p>Êtes-vous sûr de vouloir supprimer la position <strong id="positionName"></strong> ?</p>
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

<!-- Move Modal -->
<div class="modal fade" id="moveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Déplacer les positions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Déplacer <span id="moveCount">0</span> position(s) vers :</p>
                <div class="mb-3">
                    <label for="newTabletteId" class="form-label">Nouvelle tablette</label>
                    <select class="form-select" id="newTabletteId" name="new_tablette_id" required>
                        <option value="">Sélectionner une tablette</option>
                        @foreach($tablettes as $tablette)
                            <option value="{{ $tablette->id }}">
                                {{ $tablette->nom }} ({{ $tablette->travee->nom }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="executeMove()">Déplacer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let selectedPositions = [];

    // Select all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.position-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelection();
    });

    // Individual checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('position-checkbox')) {
            updateSelection();
        }
    });

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.position-checkbox:checked');
        selectedPositions = Array.from(checkboxes).map(cb => cb.value);
        
        const count = selectedPositions.length;
        document.getElementById('selected-count').textContent = count;
        document.getElementById('bulk-actions').style.display = count > 0 ? 'block' : 'none';
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.position-checkbox');
        const selectAllCheckbox = document.getElementById('select-all');
        selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
        selectAllCheckbox.checked = count === allCheckboxes.length;
    }

    function clearSelection() {
        document.querySelectorAll('.position-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }

    function bulkAction(action) {
        if (selectedPositions.length === 0) {
            alert('Veuillez sélectionner au moins une position.');
            return;
        }

        if (action === 'move') {
            document.getElementById('moveCount').textContent = selectedPositions.length;
            const modal = new bootstrap.Modal(document.getElementById('moveModal'));
            modal.show();
            return;
        }

        let confirmMessage = '';
        switch(action) {
            case 'delete':
                confirmMessage = `Êtes-vous sûr de vouloir supprimer définitivement ${selectedPositions.length} position(s) ? Cette action est irréversible.`;
                break;
            case 'export':
                executeBulkAction('export');
                return;
        }

        if (confirm(confirmMessage)) {
            executeBulkAction(action);
        }
    }

    function executeMove() {
        const newTabletteId = document.getElementById('newTabletteId').value;
        if (!newTabletteId) {
            alert('Veuillez sélectionner une tablette de destination.');
            return;
        }

        executeBulkAction('move', { new_tablette_id: newTabletteId });
        bootstrap.Modal.getInstance(document.getElementById('moveModal')).hide();
    }

    function executeBulkAction(action, extraData = {}) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.positions.bulk-action") }}';
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        // Add action
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);
        
        // Add position IDs
        selectedPositions.forEach(function(positionId, index) {
            const positionInput = document.createElement('input');
            positionInput.type = 'hidden';
            positionInput.name = 'position_ids[' + index + ']';
            positionInput.value = positionId;
            form.appendChild(positionInput);
        });
        
        // Add extra data
        Object.keys(extraData).forEach(key => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = extraData[key];
            form.appendChild(input);
        });
        
        if (action === 'export') {
            form.target = '_blank';
        }
        
        document.body.appendChild(form);
        form.submit();
        
        if (action !== 'export') {
            setTimeout(() => document.body.removeChild(form), 1000);
        }
    }

    function exportData() {
        const params = new URLSearchParams(window.location.search);
        const exportUrl = `{{ route('admin.positions.export') }}?${params.toString()}`;
        window.open(exportUrl, '_blank');
    }

    function confirmDelete(positionId, positionName) {
        document.getElementById('positionName').textContent = positionName;
        document.getElementById('deleteForm').action = `/admin/positions/${positionId}`;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endpush

@push('styles')
<style>
    .avatar {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.875rem;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }

    .alert-info {
        border: none;
        border-radius: 8px;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .btn-group .btn {
        border-radius: 0.375rem;
        margin-right: 2px;
    }

    .btn-group .btn:last-child {
        margin-right: 0;
    }

    .card-body .text-center h3 {
        font-size: 2rem;
        font-weight: 700;
    }
</style>
@endpush