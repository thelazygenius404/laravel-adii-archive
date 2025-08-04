@extends('layouts.admin')

@section('title', 'Gestion des Boîtes')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-archive me-2"></i>
            Gestion des Boîtes
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour
            </a>
            <a href="{{ route('admin.boites.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nouvelle Boîte
            </a>
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                <i class="fas fa-cogs me-1"></i>
                Actions
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="#" onclick="exportData()">
                    <i class="fas fa-download me-2"></i>Exporter la liste
                </a></li>
                <li><a class="dropdown-item" href="#" onclick="showBulkActions()">
                    <i class="fas fa-tasks me-2"></i>Actions groupées
                </a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="{{ route('admin.boites.low-occupancy') }}">
                    <i class="fas fa-chart-pie me-2"></i>Boîtes peu occupées
                </a></li>
            </ul>
        </div>
    </div>
</div>

<!-- Filtres et recherche -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Numéro, code...">
                    </div>
                    <div class="col-md-2">
                        <label for="position_id" class="form-label">Position</label>
                        <select class="form-select" id="position_id" name="position_id">
                            <option value="">Toutes positions</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                    {{ $position->full_path }}
                                </option>
                            @endforeach
                        </select>
                    </div> 
                    <div class="col-md-2">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actives</option>
                            <option value="destroyed" {{ request('status') == 'destroyed' ? 'selected' : '' }}>Détruites</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="per_page" class="form-label">Par page</label>
                        <select class="form-select" id="per_page" name="per_page">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>
                            Filtrer
                        </button>
                        <a href="{{ route('admin.boites.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques rapides -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-archive text-primary fa-3x mb-3"></i>
                <h3 class="text-primary">{{ $boites->total() }}</h3>
                <p class="text-muted mb-0">Boîtes Totales</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                <h3 class="text-success">{{ $boites->where('detruite', false)->count() }}</h3>
                <p class="text-muted mb-0">Boîtes Actives</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                <h3 class="text-warning">{{ $boites->where('detruite', true)->count() }}</h3>
                <p class="text-muted mb-0">Boîtes Détruites</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="fas fa-folder text-info fa-3x mb-3"></i>
                <h3 class="text-info">{{ $boites->sum('dossiers_count') }}</h3>
                <p class="text-muted mb-0">Dossiers Stockés</p>
            </div>
        </div>
    </div>
</div>

<!-- Liste des boîtes -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>
                    Liste des Boîtes
                    <span class="badge bg-primary ms-2">{{ $boites->total() }}</span>
                </h5>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="selectAll">
                    <label class="form-check-label" for="selectAll">Tout sélectionner</label>
                </div>
            </div>
            <div class="card-body">
                @if($boites->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" class="form-check-input" id="selectAllHeader">
                                    </th>
                                    <th>Numéro</th>
                                    <th>Codes</th>
                                    <th>Localisation</th>
                                    <th>Occupation</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($boites as $boite)
                                    <tr class="{{ $boite->detruite ? 'table-secondary' : '' }}">
                                        <td>
                                            <input type="checkbox" class="form-check-input boite-checkbox" value="{{ $boite->id }}">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="boite-icon bg-{{ $boite->detruite ? 'secondary' : ($boite->utilisation_percentage > 90 ? 'danger' : ($boite->utilisation_percentage > 70 ? 'warning' : 'primary')) }}">
                                                        <i class="fas fa-archive"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $boite->numero }}</h6>
                                                    <small class="text-muted">ID: {{ $boite->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($boite->code_thematique)
                                                <span class="badge bg-info">{{ $boite->code_thematique }}</span>
                                            @endif
                                            @if($boite->code_topo)
                                                <br><span class="badge bg-secondary">{{ $boite->code_topo }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($boite->position)
                                                <a href="{{ route('admin.positions.show', $boite->position) }}" class="text-decoration-none">
                                                    {{ $boite->full_location }}
                                                </a>
                                            @else
                                                <span class="text-muted">Non localisée</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <span class="fw-bold">{{ $boite->nbr_dossiers }}/{{ $boite->capacite }}</span>
                                                </div>
                                                <div class="progress me-2" style="width: 60px; height: 8px;">
                                                    <div class="progress-bar bg-{{ $boite->utilisation_percentage < 50 ? 'success' : ($boite->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                                                         style="width: {{ $boite->utilisation_percentage }}%"></div>
                                                </div>
                                                <small>{{ number_format($boite->utilisation_percentage, 1) }}%</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($boite->detruite)
                                                <span class="badge bg-secondary">Détruite</span>
                                            @elseif($boite->utilisation_percentage >= 100)
                                                <span class="badge bg-danger">Pleine</span>
                                            @elseif($boite->utilisation_percentage >= 90)
                                                <span class="badge bg-warning">Presque pleine</span>
                                            @elseif($boite->utilisation_percentage > 0)
                                                <span class="badge bg-success">En cours</span>
                                            @else
                                                <span class="badge bg-info">Vide</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.boites.show', $boite) }}" 
                                                   class="btn btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.boites.edit', $boite) }}" 
                                                   class="btn btn-outline-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($boite->detruite)
                                                    <button class="btn btn-outline-success" 
                                                            onclick="restoreBoite({{ $boite->id }})" title="Restaurer">
                                                        <i class="fas fa-trash-restore"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-danger" 
                                                            onclick="confirmDestruction({{ $boite->id }})" title="Détruire">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($boites->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $boites->onEachSide(1)->links('pagination::simple-bootstrap-4') }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-archive fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune boîte trouvée</h5>
                        <p class="text-muted">Commencez par créer votre première boîte.</p>
                        <a href="{{ route('admin.boites.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Créer une boîte
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Actions groupées -->
<div class="row mt-4" id="bulkActionsRow" style="display: none;">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <strong id="selectedCount">0</strong> boîte(s) sélectionnée(s)
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-outline-success" onclick="bulkRestore()">
                            <i class="fas fa-trash-restore me-2"></i>Restaurer
                        </button>
                        <button class="btn btn-outline-danger" onclick="bulkDestroy()">
                            <i class="fas fa-trash me-2"></i>Détruire
                        </button>
                        <button class="btn btn-outline-info" onclick="bulkExport()">
                            <i class="fas fa-download me-2"></i>Exporter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let selectedBoites = [];

    // Gestion de la sélection multiple
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllButtons = ['selectAll', 'selectAllHeader'];
        selectAllButtons.forEach(id => {
            document.getElementById(id).addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.boite-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedBoites();
                // Synchroniser les autres boutons "Tout sélectionner"
                selectAllButtons.forEach(otherId => {
                    if (otherId !== id) {
                        document.getElementById(otherId).checked = this.checked;
                    }
                });
            });
        });

        // Écouter les changements sur les checkboxes individuelles
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('boite-checkbox')) {
                updateSelectedBoites();
            }
        });
    });

    function updateSelectedBoites() {
        selectedBoites = Array.from(document.querySelectorAll('.boite-checkbox:checked')).map(cb => cb.value);
        const count = selectedBoites.length;
        
        document.getElementById('selectedCount').textContent = count;
        document.getElementById('bulkActionsRow').style.display = count > 0 ? 'block' : 'none';
        
        // Mettre à jour l'état des boutons "Tout sélectionner"
        const allCheckboxes = document.querySelectorAll('.boite-checkbox');
        const selectAllButtons = ['selectAll', 'selectAllHeader'];
        selectAllButtons.forEach(id => {
            document.getElementById(id).checked = count === allCheckboxes.length;
        });
    }

    // Confirmer la destruction d'une boîte
    function confirmDestruction(id) {
        if (confirm('Êtes-vous sûr de vouloir marquer cette boîte comme détruite ?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/boites/${id}/destroy-box`;
            form.innerHTML = `
                @csrf
                @method('PUT')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Restaurer une boîte
    function restoreBoite(id) {
        if (confirm('Êtes-vous sûr de vouloir restaurer cette boîte ?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/boites/${id}/restore-box`;
            form.innerHTML = `
                @csrf
                @method('PUT')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Actions groupées
    function bulkDestroy() {
        if (selectedBoites.length === 0) {
            alert('Veuillez sélectionner au moins une boîte.');
            return;
        }
        
        if (confirm(`Êtes-vous sûr de vouloir détruire ${selectedBoites.length} boîte(s) ?`)) {
            performBulkAction('destroy');
        }
    }

    function bulkRestore() {
        if (selectedBoites.length === 0) {
            alert('Veuillez sélectionner au moins une boîte.');
            return;
        }
        
        if (confirm(`Êtes-vous sûr de vouloir restaurer ${selectedBoites.length} boîte(s) ?`)) {
            performBulkAction('restore');
        }
    }

    function bulkExport() {
        if (selectedBoites.length === 0) {
            alert('Veuillez sélectionner au moins une boîte.');
            return;
        }
        
        performBulkAction('export');
    }

    function performBulkAction(action) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.boites.bulk-action") }}';
        form.innerHTML = `
            @csrf
            <input type="hidden" name="action" value="${action}">
            <input type="hidden" name="boite_ids" value="${JSON.stringify(selectedBoites)}">
        `;
        document.body.appendChild(form);
        form.submit();
    }

    // Exporter les données
    function exportData() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = `{{ route('admin.boites.export') }}?${params.toString()}`;
    }

    // Actions groupées générales
    function showBulkActions() {
        if (selectedBoites.length === 0) {
            alert('Veuillez d\'abord sélectionner des boîtes en cochant les cases correspondantes.');
            return;
        }
        
        // Afficher le panneau d'actions groupées
        document.getElementById('bulkActionsRow').style.display = 'block';
    }
</script>
@endpush

@push('styles')
<style>
    .boite-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        color: white;
        font-size: 1.2rem;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.75em;
    }

    .progress {
        background-color: #e9ecef;
    }

    .table-secondary {
        opacity: 0.7;
    }

    .card-body .text-center h3 {
        font-size: 2rem;
        font-weight: 700;
    }
</style>
@endpush