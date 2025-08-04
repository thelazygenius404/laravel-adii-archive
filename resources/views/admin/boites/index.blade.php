@extends('layouts.admin')

@section('title', 'Gestion des Boîtes')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-archive me-2"></i>
            Gestion des Boîtes
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stockage.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour au stockage
            </a>
            <a href="{{ route('admin.boites.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>
                Nouvelle Boîte
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
                           placeholder="Numéro, code..."
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request()->hasAny(['search', 'organisme_id', 'status']))
                        <a href="{{ route('admin.boites.index') }}" class="btn btn-outline-secondary ms-2">
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
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <select name="organisme_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les organismes</option>
                        @php
                            $organismes = \App\Models\Organisme::orderBy('nom_org')->get();
                        @endphp
                        @foreach($organismes as $org)
                            <option value="{{ $org->id }}" {{ request('organisme_id') == $org->id ? 'selected' : '' }}>
                                {{ $org->nom_org }}
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
                    @if(request('organisme_id'))
                        <input type="hidden" name="organisme_id" value="{{ request('organisme_id') }}">
                    @endif
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actives</option>
                        <option value="destroyed" {{ request('status') == 'destroyed' ? 'selected' : '' }}>Détruites</option>
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
                        @if(request('organisme_id'))
                            <input type="hidden" name="organisme_id" value="{{ request('organisme_id') }}">
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

        <!-- Bulk Actions -->
        <div class="row mb-3" id="bulk-actions" style="display: none;">
            <div class="col-12">
                <div class="alert alert-info">
                    <span id="selected-count">0</span> boîte(s) sélectionnée(s)
                    <div class="btn-group ms-3">
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="bulkAction('restore')">
                            <i class="fas fa-trash-restore me-1"></i>Restaurer
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="bulkAction('destroy')">
                            <i class="fas fa-trash me-1"></i>Détruire
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="bulkAction('export')">
                            <i class="fas fa-file-excel me-1"></i>Exporter
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

        <!-- Boites Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        <th>
                            <i class="fas fa-archive me-1"></i>
                            Boîte
                        </th>
                        <th>
                            <i class="fas fa-map-marker-alt me-1"></i>
                            Localisation
                        </th>
                        <th>
                            <i class="fas fa-chart-pie me-1"></i>
                            Occupation
                        </th>
                        <th>
                            <i class="fas fa-percentage me-1"></i>
                            Utilisation
                        </th>
                        <th>
                            <i class="fas fa-tags me-1"></i>
                            Codes
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
                    @forelse($boites as $boite)
                        <tr class="{{ $boite->detruite ? 'table-secondary' : '' }}">
                            <td>
                                <input type="checkbox" class="form-check-input boite-checkbox" value="{{ $boite->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar bg-{{ $boite->detruite ? 'secondary' : ($boite->utilisation_percentage > 90 ? 'danger' : ($boite->utilisation_percentage > 70 ? 'warning' : 'primary')) }} text-white rounded">
                                            <i class="fas fa-archive"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $boite->numero }}</div>
                                        <small class="text-muted">ID: {{ $boite->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($boite->position)
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold">{{ $boite->position->nom }}</span>
                                        <small class="text-muted">
                                            {{ $boite->position->tablette->travee->salle->nom }} - 
                                            {{ $boite->position->tablette->travee->salle->organisme->nom_org }}
                                        </small>
                                    </div>
                                @else
                                    <span class="text-muted">Non localisée</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-center">
                                    <strong>{{ $boite->nbr_dossiers }}/{{ $boite->capacite }}</strong>
                                    <br><small class="text-muted">dossiers</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 100px; height: 8px;">
                                        <div class="progress-bar bg-{{ $boite->utilisation_percentage < 50 ? 'success' : ($boite->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                                            style="width: {{ $boite->utilisation_percentage }}%"></div>
                                    </div>
                                    <span class="badge {{ $boite->utilisation_percentage < 50 ? 'bg-success' : ($boite->utilisation_percentage < 80 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ number_format($boite->utilisation_percentage, 1) }}%
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    @if($boite->code_thematique)
                                        <span class="badge bg-info">{{ $boite->code_thematique }}</span>
                                    @endif
                                    @if($boite->code_topo)
                                        <span class="badge bg-secondary">{{ $boite->code_topo }}</span>
                                    @endif
                                    @if(!$boite->code_thematique && !$boite->code_topo)
                                        <span class="text-muted">-</span>
                                    @endif
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
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-info">Vide</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.boites.show', $boite) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.boites.edit', $boite) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($boite->position)
                                        <a href="{{ route('admin.positions.show', $boite->position) }}" 
                                           class="btn btn-sm btn-outline-success" 
                                           title="Voir position">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </a>
                                    @endif
                                    @if($boite->detruite)
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-success" 
                                                title="Restaurer"
                                                onclick="confirmRestore('{{ $boite->id }}', '{{ $boite->numero }}')">
                                            <i class="fas fa-trash-restore"></i>
                                        </button>
                                    @else
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Détruire"
                                                onclick="confirmDestroy('{{ $boite->id }}', '{{ $boite->numero }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-archive fa-3x mb-3"></i>
                                    <p class="mb-0">Aucune boîte trouvée</p>
                                    @if(request()->hasAny(['search', 'organisme_id', 'status']))
                                        <p class="mt-2">
                                            <a href="{{ route('admin.boites.index') }}" class="btn btn-sm btn-outline-primary">
                                                Voir toutes les boîtes
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
        @if($boites->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $boites->firstItem() }} à {{ $boites->lastItem() }} sur {{ $boites->total() }} résultats
                </div>
                <div>
                   {{ $boites->onEachSide(1)->links('pagination::simple-bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="destroyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la destruction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir détruire la boîte <strong id="boiteNumero"></strong> ?</p>
                <p class="text-danger"><small>Cette action marquera également tous les dossiers contenus comme éliminés et libérera la position.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="destroyForm" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-danger">Détruire</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la restauration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir restaurer la boîte <strong id="restoreBoiteNumero"></strong> ?</p>
                <p class="text-success"><small>Cette action restaurera également tous les dossiers contenus et marquera la position comme occupée.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="restoreForm" method="POST" class="d-inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success">Restaurer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let selectedBoites = [];

    // Select all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.boite-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelection();
    });

    // Individual checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('boite-checkbox')) {
            updateSelection();
        }
    });

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.boite-checkbox:checked');
        selectedBoites = Array.from(checkboxes).map(cb => cb.value);
        
        const count = selectedBoites.length;
        document.getElementById('selected-count').textContent = count;
        document.getElementById('bulk-actions').style.display = count > 0 ? 'block' : 'none';
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.boite-checkbox');
        const selectAllCheckbox = document.getElementById('select-all');
        selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
        selectAllCheckbox.checked = count === allCheckboxes.length;
    }

    function clearSelection() {
        document.querySelectorAll('.boite-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }

    function bulkAction(action) {
        if (selectedBoites.length === 0) {
            alert('Veuillez sélectionner au moins une boîte.');
            return;
        }

        let confirmMessage = '';
        switch(action) {
            case 'delete':
                confirmMessage = `Êtes-vous sûr de vouloir supprimer définitivement ${selectedBoites.length} boîte(s) ? Cette action est irréversible.`;
                break;
            case 'destroy':
                confirmMessage = `Êtes-vous sûr de vouloir détruire ${selectedBoites.length} boîte(s) ? Cette action marquera les boîtes et leurs dossiers comme éliminés.`;
                break;
            case 'restore':
                confirmMessage = `Êtes-vous sûr de vouloir restaurer ${selectedBoites.length} boîte(s) ?`;
                break;
            case 'export':
                executeBulkAction('export');
                return;
        }

        if (confirm(confirmMessage)) {
            executeBulkAction(action);
        }
    }

    function executeBulkAction(action) {
        if (action === 'export') {
            // Pour l'exportation, on utilise une méthode spéciale avec target="_blank"
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.boites.bulk-action") }}';
            form.target = '_blank'; // Ouvrir dans un nouvel onglet pour le téléchargement
            
            // Ajouter le token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            // Ajouter l'action
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'export';
            form.appendChild(actionInput);
            
            // Ajouter les IDs des boîtes sélectionnées
            selectedBoites.forEach(function(boiteId, index) {
                const boiteInput = document.createElement('input');
                boiteInput.type = 'hidden';
                boiteInput.name = 'boite_ids[' + index + ']';
                boiteInput.value = boiteId;
                form.appendChild(boiteInput);
            });
            
            // Ajouter le formulaire au DOM et le soumettre
            document.body.appendChild(form);
            form.submit();
            
            // Nettoyer après soumission
            setTimeout(() => {
                document.body.removeChild(form);
            }, 1000);
        } else {
            // Pour les autres actions (delete, destroy, restore)
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.boites.bulk-action") }}';
            
            // Ajouter le token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            // Ajouter l'action
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);
            
            // Ajouter les IDs des boîtes sélectionnées
            selectedBoites.forEach(function(boiteId, index) {
                const boiteInput = document.createElement('input');
                boiteInput.type = 'hidden';
                boiteInput.name = 'boite_ids[' + index + ']';
                boiteInput.value = boiteId;
                form.appendChild(boiteInput);
            });
            
            // Ajouter le formulaire au DOM et le soumettre
            document.body.appendChild(form);
            form.submit();
        }
    }

    function bulkAction(action) {
        if (selectedBoites.length === 0) {
            alert('Veuillez sélectionner au moins une boîte.');
            return;
        }

        let confirmMessage = '';
        switch(action) {
            case 'delete':
                confirmMessage = `Êtes-vous sûr de vouloir supprimer définitivement ${selectedBoites.length} boîte(s) ? Cette action est irréversible.`;
                break;
            case 'destroy':
                confirmMessage = `Êtes-vous sûr de vouloir détruire ${selectedBoites.length} boîte(s) ? Cette action marquera les boîtes et leurs dossiers comme éliminés.`;
                break;
            case 'restore':
                confirmMessage = `Êtes-vous sûr de vouloir restaurer ${selectedBoites.length} boîte(s) ?`;
                break;
            case 'export':
                // Pour l'exportation, pas besoin de confirmation
                executeBulkAction('export');
                return;
        }

        if (confirm(confirmMessage)) {
            executeBulkAction(action);
        }
    }

    // Fonction pour l'exportation générale (bouton export principal)
    function exportData() {
        try {
            const params = new URLSearchParams(window.location.search);
            const exportUrl = `{{ route('admin.boites.export') }}?${params.toString()}`;
            
            // Debug - afficher l'URL dans la console
            console.log('Export URL:', exportUrl);
            
            // Ouvrir dans une nouvelle fenêtre
            const newWindow = window.open(exportUrl, '_blank');
            
            // Vérifier si la fenêtre s'est ouverte
            if (!newWindow || newWindow.closed || typeof newWindow.closed == 'undefined') {
                // Si le popup est bloqué, utiliser window.location
                alert('Popup bloqué. Redirection vers le téléchargement...');
                window.location.href = exportUrl;
            }
        } catch (error) {
            console.error('Erreur lors de l\'exportation:', error);
            alert('Erreur lors de l\'exportation. Vérifiez la console pour plus de détails.');
        }
    }

    function confirmDestroy(boiteId, boiteNumero) {
        document.getElementById('boiteNumero').textContent = boiteNumero;
        document.getElementById('destroyForm').action = '/admin/boites/' + boiteId + '/destroy-box';
        
        const modal = new bootstrap.Modal(document.getElementById('destroyModal'));
        modal.show();
    }

    function confirmRestore(boiteId, boiteNumero) {
        document.getElementById('restoreBoiteNumero').textContent = boiteNumero;
        document.getElementById('restoreForm').action = '/admin/boites/' + boiteId + '/restore-box';
        
        const modal = new bootstrap.Modal(document.getElementById('restoreModal'));
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

    .table-secondary {
        opacity: 0.7;
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