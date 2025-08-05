@extends('layouts.admin')

@section('title', 'Gestion des Tablettes')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-table me-2"></i>
            Gestion des Tablettes
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.travees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour 
            </a>
            <a href="{{ route('admin.tablettes.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>
                Nouvelle Tablette
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
                           placeholder="Nom de tablette..."
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request()->hasAny(['search', 'travee_id']))
                        <a href="{{ route('admin.tablettes.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
            
            <!-- Filtre par travée -->
            <div class="col-md-3">
                <form method="GET">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select name="travee_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Toutes les travées</option>
                        @foreach($travees as $travee)
                            <option value="{{ $travee->id }}" {{ request('travee_id') == $travee->id ? 'selected' : '' }}>
                                {{ $travee->nom }} ({{ $travee->salle->nom }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            
            <!-- Actions et pagination -->
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <!-- Pagination -->
                    <form method="GET" class="d-flex align-items-center">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request('travee_id'))
                            <input type="hidden" name="travee_id" value="{{ request('travee_id') }}">
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
                        <i class="fas fa-table text-primary fa-3x mb-3"></i>
                        <h3 class="text-primary">{{ $tablettes->total() }}</h3>
                        <p class="text-muted mb-0">Tablettes Totales</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-map-marker-alt text-success fa-3x mb-3"></i>
                        <h3 class="text-success">{{ $tablettes->sum('positions_count') }}</h3>
                        <p class="text-muted mb-0">Positions Totales</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-percentage text-warning fa-3x mb-3"></i>
                        <h3 class="text-warning">{{ number_format($tablettes->where('positions_count', '>', 0)->avg('utilisation_percentage') ?? 0, 1) }}%</h3>
                        <p class="text-muted mb-0">Utilisation Moyenne</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <i class="fas fa-archive text-info fa-3x mb-3"></i>
                        <h3 class="text-info">{{ $travees->count() }}</h3>
                        <p class="text-muted mb-0">Travées Connectées</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="row mb-3" id="bulk-actions" style="display: none;">
            <div class="col-12">
                <div class="alert alert-info">
                    <span id="selected-count">0</span> tablette(s) sélectionnée(s)
                    <div class="btn-group ms-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="bulkAction('export')">
                            <i class="fas fa-file-excel me-1"></i>Exporter
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="bulkAction('move')">
                            <i class="fas fa-arrows-alt me-1"></i>Déplacer
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="bulkAction('optimize')">
                            <i class="fas fa-magic me-1"></i>Optimiser
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

        <!-- Tablettes Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        <th>
                            <i class="fas fa-table me-1"></i>
                            Tablette
                        </th>
                        <th>
                            <i class="fas fa-building me-1"></i>
                            Localisation
                        </th>
                        <th>
                            <i class="fas fa-map-marker-alt me-1"></i>
                            Positions
                        </th>
                        <th>
                            <i class="fas fa-chart-pie me-1"></i>
                            Utilisation
                        </th>
                        <th>
                            <i class="fas fa-info-circle me-1"></i>
                            Statut
                        </th>
                        <th width="150">
                            <i class="fas fa-cogs me-1"></i>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tablettes as $tablette)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input tablette-checkbox" value="{{ $tablette->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar bg-{{ $tablette->utilisation_percentage > 80 ? 'danger' : ($tablette->utilisation_percentage > 50 ? 'warning' : 'success') }} text-white rounded">
                                            <i class="fas fa-table"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $tablette->nom }}</div>
                                        <small class="text-muted">ID: {{ $tablette->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $tablette->travee->salle->nom }}</span>
                                    <small class="text-muted">
                                        {{ $tablette->travee->salle->organisme->nom_org }}
                                    </small>
                                    <small class="text-muted">{{ $tablette->travee->nom }}</small>
                                </div>
                            </td>
                            <td class="text-center">
                                <div>
                                    <span class="badge bg-info">{{ $tablette->positions_count }}</span>
                                    @if($tablette->positions_count > 0)
                                        <br><small class="text-muted">{{ $tablette->positions_occupees ?? 0 }} occupées</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($tablette->positions_count > 0)
                                    <div class="d-flex align-items-center">
                                        <div class="progress me-2" style="width: 100px; height: 8px;">
                                            <div class="progress-bar bg-{{ $tablette->utilisation_percentage < 50 ? 'success' : ($tablette->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                                                 style="width: {{ $tablette->utilisation_percentage }}%"></div>
                                        </div>
                                        <span class="badge {{ $tablette->utilisation_percentage < 50 ? 'bg-success' : ($tablette->utilisation_percentage < 80 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ number_format($tablette->utilisation_percentage, 1) }}%
                                        </span>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($tablette->positions_count == 0)
                                    <span class="badge bg-secondary">Vide</span>
                                @elseif($tablette->utilisation_percentage >= 90)
                                    <span class="badge bg-danger">Pleine</span>
                                @elseif($tablette->utilisation_percentage >= 70)
                                    <span class="badge bg-warning">Occupée</span>
                                @else
                                    <span class="badge bg-success">Disponible</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.tablettes.show', $tablette) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.tablettes.edit', $tablette) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($tablette->positions_count == 0)
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Supprimer"
                                                onclick="confirmDelete('{{ $tablette->id }}', '{{ $tablette->nom }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-secondary" 
                                                disabled 
                                                title="Contient des positions">
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
                                    <i class="fas fa-table fa-3x mb-3"></i>
                                    <p class="mb-0">Aucune tablette trouvée</p>
                                    @if(request()->hasAny(['search', 'travee_id']))
                                        <p class="mt-2">
                                            <a href="{{ route('admin.tablettes.index') }}" class="btn btn-sm btn-outline-primary">
                                                Voir toutes les tablettes
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
        @if($tablettes->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $tablettes->firstItem() }} à {{ $tablettes->lastItem() }} sur {{ $tablettes->total() }} résultats
                </div>
                <div>
                   {{ $tablettes->onEachSide(1)->links('pagination::simple-bootstrap-4') }}
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
                <p>Êtes-vous sûr de vouloir supprimer la tablette <strong id="tabletteName"></strong> ?</p>
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
                <h5 class="modal-title">Déplacer les tablettes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Déplacer <span id="moveCount">0</span> tablette(s) vers :</p>
                <div class="mb-3">
                    <label for="newTraveeId" class="form-label">Nouvelle travée</label>
                    <select class="form-select" id="newTraveeId" name="new_travee_id" required>
                        <option value="">Sélectionner une travée</option>
                        @foreach($travees as $travee)
                            <option value="{{ $travee->id }}">
                                {{ $travee->nom }} ({{ $travee->salle->nom }})
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
    let selectedTablettes = [];

    // Select all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.tablette-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelection();
    });

    // Individual checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('tablette-checkbox')) {
            updateSelection();
        }
    });

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.tablette-checkbox:checked');
        selectedTablettes = Array.from(checkboxes).map(cb => cb.value);
        
        const count = selectedTablettes.length;
        document.getElementById('selected-count').textContent = count;
        document.getElementById('bulk-actions').style.display = count > 0 ? 'block' : 'none';
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.tablette-checkbox');
        const selectAllCheckbox = document.getElementById('select-all');
        selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
        selectAllCheckbox.checked = count === allCheckboxes.length;
    }

    function clearSelection() {
        document.querySelectorAll('.tablette-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }

    function bulkAction(action) {
        if (selectedTablettes.length === 0) {
            alert('Veuillez sélectionner au moins une tablette.');
            return;
        }

        if (action === 'move') {
            document.getElementById('moveCount').textContent = selectedTablettes.length;
            const modal = new bootstrap.Modal(document.getElementById('moveModal'));
            modal.show();
            return;
        }

        let confirmMessage = '';
        switch(action) {
            case 'delete':
                confirmMessage = `Êtes-vous sûr de vouloir supprimer définitivement ${selectedTablettes.length} tablette(s) ? Cette action est irréversible.`;
                break;
            case 'optimize':
                confirmMessage = `Optimiser ${selectedTablettes.length} tablette(s) ? Cette action mettra à jour les compteurs et corrigera les incohérences.`;
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
        const newTraveeId = document.getElementById('newTraveeId').value;
        if (!newTraveeId) {
            alert('Veuillez sélectionner une travée de destination.');
            return;
        }

        executeBulkAction('move', { new_travee_id: newTraveeId });
        bootstrap.Modal.getInstance(document.getElementById('moveModal')).hide();
    }

    function executeBulkAction(action, extraData = {}) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.tablettes.bulk-action") }}';
        
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
        
        // Add tablette IDs
        selectedTablettes.forEach(function(tabletteId, index) {
            const tabletteInput = document.createElement('input');
            tabletteInput.type = 'hidden';
            tabletteInput.name = 'tablette_ids[' + index + ']';
            tabletteInput.value = tabletteId;
            form.appendChild(tabletteInput);
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
        const exportUrl = `{{ route('admin.tablettes.export') }}?${params.toString()}`;
        window.open(exportUrl, '_blank');
    }

    function confirmDelete(tabletteId, tabletteName) {
        document.getElementById('tabletteName').textContent = tabletteName;
        document.getElementById('deleteForm').action = `/admin/tablettes/${tabletteId}`;
        
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

    .progress {
        background-color: #e9ecef;
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