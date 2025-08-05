@extends('layouts.admin')

@section('title', 'Gestion des Travées')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-layer-group me-2"></i>
            Gestion des Travées
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stockage.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour au stockage
            </a>
            <a href="{{ route('admin.travees.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>
                Nouvelle Travée
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
                           placeholder="Nom de la travée..."
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request()->hasAny(['search', 'salle_id']))
                        <a href="{{ route('admin.travees.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
            
            <!-- Filtre par salle -->
            <div class="col-md-3">
                <form method="GET">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select name="salle_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Toutes les salles</option>
                        @foreach($salles as $salle)
                            <option value="{{ $salle->id }}" {{ request('salle_id') == $salle->id ? 'selected' : '' }}>
                                {{ $salle->nom }} ({{ $salle->organisme->nom_org }})
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Spacer -->
            <div class="col-md-3"></div>
            
            <!-- Actions et pagination -->
            <div class="col-md-3">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <!-- Pagination -->
                    <form method="GET" class="d-flex align-items-center">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request('salle_id'))
                            <input type="hidden" name="salle_id" value="{{ request('salle_id') }}">
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
                        <i class="fas fa-layer-group text-primary fa-3x mb-3"></i>
                        <h3 class="text-primary">{{ $travees->total() }}</h3>
                        <p class="text-muted mb-0">Travées Totales</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-table text-success fa-3x mb-3"></i>
                        <h3 class="text-success">{{ $travees->sum('tablettes_count') }}</h3>
                        <p class="text-muted mb-0">Tablettes Totales</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-dot-circle text-warning fa-3x mb-3"></i>
                        <h3 class="text-warning">{{ $travees->sum('positions_count') ?? 0 }}</h3>
                        <p class="text-muted mb-0">Positions Totales</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <i class="fas fa-percentage text-info fa-3x mb-3"></i>
                        <h3 class="text-info">{{ number_format($travees->avg('utilisation_percentage') ?? 0, 1) }}%</h3>
                        <p class="text-muted mb-0">Utilisation Moyenne</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="row mb-3" id="bulk-actions" style="display: none;">
            <div class="col-12">
                <div class="alert alert-info">
                    <span id="selected-count">0</span> travée(s) sélectionnée(s)
                    <div class="btn-group ms-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="bulkAction('export')">
                            <i class="fas fa-file-excel me-1"></i>Exporter
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="bulkAction('move')">
                            <i class="fas fa-arrows-alt me-1"></i>Déplacer
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="bulkAction('delete')">
                            <i class="fas fa-times me-1"></i>Supprimer
                        </button>
                    </div>
                    <button type="button" class="btn btn-sm btn-secondary ms-2" onclick="clearSelection()">
                        Annuler sélection
                    </button>
                </div>
            </div>
        </div>

        <!-- Travees Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        <th>
                            <i class="fas fa-layer-group me-1"></i>
                            Travée
                        </th>
                        <th>
                            <i class="fas fa-building me-1"></i>
                            Salle
                        </th>
                        <th>
                            <i class="fas fa-university me-1"></i>
                            Organisme
                        </th>
                        <th>
                            <i class="fas fa-table me-1"></i>
                            Tablettes
                        </th>
                        <th>
                            <i class="fas fa-dot-circle me-1"></i>
                            Positions
                        </th>
                        <th>
                            <i class="fas fa-chart-pie me-1"></i>
                            Utilisation
                        </th>
                        <th width="150">
                            <i class="fas fa-cogs me-1"></i>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($travees as $travee)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input travee-checkbox" value="{{ $travee->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar bg-{{ $travee->utilisation_percentage > 90 ? 'danger' : ($travee->utilisation_percentage > 70 ? 'warning' : 'success') }} text-white rounded">
                                            <i class="fas fa-layer-group"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $travee->nom }}</div>
                                        <small class="text-muted">ID: {{ $travee->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $travee->salle->nom }}</span>
                                    <small class="text-muted">Salle ID: {{ $travee->salle->id }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $travee->salle->organisme->nom_org }}</span>
                            </td>
                            <td>
                                <div class="text-center">
                                    <strong>{{ $travee->tablettes_count }}</strong>
                                    <br><small class="text-muted">tablettes</small>
                                </div>
                            </td>
                            <td>
                                <div class="text-center">
                                    <strong>{{ $travee->positions_occupees ?? 0 }}/{{ $travee->total_positions ?? 0 }}</strong>
                                    <br><small class="text-muted">occupées</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 100px; height: 8px;">
                                        <div class="progress-bar bg-{{ $travee->utilisation_percentage < 50 ? 'success' : ($travee->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                                            style="width: {{ $travee->utilisation_percentage ?? 0 }}%"></div>
                                    </div>
                                    <span class="badge {{ $travee->utilisation_percentage < 50 ? 'bg-success' : ($travee->utilisation_percentage < 80 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ number_format($travee->utilisation_percentage ?? 0, 1) }}%
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.travees.show', $travee) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.travees.edit', $travee) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.salles.show', $travee->salle) }}" 
                                       class="btn btn-sm btn-outline-success" 
                                       title="Voir salle">
                                        <i class="fas fa-building"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Supprimer"
                                            onclick="confirmDelete('{{ $travee->id }}', '{{ $travee->nom }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-layer-group fa-3x mb-3"></i>
                                    <p class="mb-0">Aucune travée trouvée</p>
                                    @if(request()->hasAny(['search', 'salle_id']))
                                        <p class="mt-2">
                                            <a href="{{ route('admin.travees.index') }}" class="btn btn-sm btn-outline-primary">
                                                Voir toutes les travées
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
        @if($travees->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $travees->firstItem() }} à {{ $travees->lastItem() }} sur {{ $travees->total() }} résultats
                </div>
                <div>
                   {{ $travees->onEachSide(1)->links('pagination::simple-bootstrap-4') }}
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
                <p>Êtes-vous sûr de vouloir supprimer la travée <strong id="traveeNom"></strong> ?</p>
                <p class="text-danger"><small>Cette action supprimera également toutes les tablettes et positions associées.</small></p>
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
                <h5 class="modal-title">Déplacer les travées</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Déplacer <span id="moveCount">0</span> travée(s) vers une nouvelle salle :</p>
                <div class="mb-3">
                    <label for="newSalleId" class="form-label">Nouvelle salle</label>
                    <select class="form-select" id="newSalleId" required>
                        <option value="">Sélectionner une salle</option>
                        @foreach($salles as $salle)
                            <option value="{{ $salle->id }}">{{ $salle->nom }} ({{ $salle->organisme->nom_org }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="executeMoveAction()">Déplacer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let selectedTravees = [];

    // Select all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.travee-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelection();
    });

    // Individual checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('travee-checkbox')) {
            updateSelection();
        }
    });

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.travee-checkbox:checked');
        selectedTravees = Array.from(checkboxes).map(cb => cb.value);
        
        const count = selectedTravees.length;
        document.getElementById('selected-count').textContent = count;
        document.getElementById('bulk-actions').style.display = count > 0 ? 'block' : 'none';
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.travee-checkbox');
        const selectAllCheckbox = document.getElementById('select-all');
        selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
        selectAllCheckbox.checked = count === allCheckboxes.length;
    }

    function clearSelection() {
        document.querySelectorAll('.travee-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }

    function bulkAction(action) {
        if (selectedTravees.length === 0) {
            alert('Veuillez sélectionner au moins une travée.');
            return;
        }

        switch(action) {
            case 'export':
                executeBulkAction('export');
                break;
            case 'move':
                showMoveModal();
                break;
            case 'delete':
                if (confirm(`Êtes-vous sûr de vouloir supprimer ${selectedTravees.length} travée(s) ? Cette action supprimera également toutes les tablettes et positions associées.`)) {
                    executeBulkAction('delete');
                }
                break;
        }
    }

    function showMoveModal() {
        document.getElementById('moveCount').textContent = selectedTravees.length;
        document.getElementById('newSalleId').value = '';
        
        const modal = new bootstrap.Modal(document.getElementById('moveModal'));
        modal.show();
    }

    function executeMoveAction() {
        const newSalleId = document.getElementById('newSalleId').value;
        if (!newSalleId) {
            alert('Veuillez sélectionner une salle de destination.');
            return;
        }
        
        executeBulkAction('move', { new_salle_id: newSalleId });
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('moveModal'));
        if (modal) {
            modal.hide();
        }
    }

    function executeBulkAction(action, additionalData = {}) {
        const formData = new FormData();
        formData.append('action', action);
        
        // Add travee IDs as array
        selectedTravees.forEach(function(traveeId, index) {
            formData.append('travee_ids[' + index + ']', traveeId);
        });
        
        // Add additional data
        for (const [key, value] of Object.entries(additionalData)) {
            formData.append(key, value);
        }

        if (action === 'export') {
            // For export, use a form submission to open in new tab
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.travees.bulk-action") }}';
            form.target = '_blank';
            
            // Add CSRF token
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfInput);
            
            // Add form data
            for (const [key, value] of formData.entries()) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        } else {
            // For other actions, use fetch
            fetch('{{ route("admin.travees.bulk-action") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Erreur: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de l\'exécution de l\'action.');
            });
        }
    }

    function confirmDelete(traveeId, traveeNom) {
        document.getElementById('traveeNom').textContent = traveeNom;
        document.getElementById('deleteForm').action = '{{ route("admin.travees.index") }}' + '/' + traveeId;
        
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

    .badge {
        font-size: 0.75em;
    }
</style>
@endpush