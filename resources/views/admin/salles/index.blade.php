@extends('layouts.admin')

@section('title', 'Gestion des Salles')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-home me-2"></i>
            Gestion des Salles
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stockage.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour au tableau de bord
            </a>
            <a href="{{ route('admin.salles.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>
                Nouvelle Salle
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
                           placeholder="Rechercher..."
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search') || request('organisme_id') || request('status'))
                        <a href="{{ route('admin.salles.index') }}" class="btn btn-outline-secondary ms-2">
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
                        <option value="full" {{ request('status') == 'full' ? 'selected' : '' }}>Pleines</option>
                        <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Peu utilisées</option>
                    </select>
                </form>
            </div>
            
            <!-- Actions et pagination -->
            <div class="col-md-3">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <!-- Export -->
                    <form method="GET" action="{{ route('admin.salles.export') }}">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request('organisme_id'))
                            <input type="hidden" name="organisme_id" value="{{ request('organisme_id') }}">
                        @endif
                        @if(request('status'))
                            <input type="hidden" name="status" value="{{ request('status') }}">
                        @endif
                        
                    </form>
                    
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
                        <i class="fas fa-home text-primary fa-3x mb-3"></i>
                        <h3 class="text-primary">{{ $stats['total'] }}</h3>
                        <p class="text-muted mb-0">Salles Totales</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <h3 class="text-success">{{ $stats['actives'] }}</h3>
                        <p class="text-muted mb-0">Salles Actives</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-percentage text-warning fa-3x mb-3"></i>
                        <h3 class="text-warning">{{ number_format($stats['utilisation_moyenne'], 1) }}%</h3>
                        <p class="text-muted mb-0">Utilisation Moyenne</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <i class="fas fa-map-marker-alt text-info fa-3x mb-3"></i>
                        <h3 class="text-info">{{ $stats['positions_totales'] }}</h3>
                        <p class="text-muted mb-0">Positions Totales</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="row mb-3" id="bulk-actions" style="display: none;">
            <div class="col-12">
                <div class="alert alert-info">
                    <span id="selected-count">0</span> salle(s) sélectionnée(s)
                    <div class="btn-group ms-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="showBulkOrganismeModal()">
                            <i class="fas fa-building me-1"></i>Changer organisme
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="bulkAction('optimize')">
                            <i class="fas fa-cogs me-1"></i>Optimiser
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

        <!-- Salles Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" id="select-all" class="form-check-input">
                        </th>
                        <th>
                            <i class="fas fa-home me-1"></i>
                            Salle
                        </th>
                        <th>
                            <i class="fas fa-building me-1"></i>
                            Organisme
                        </th>
                        <th>
                            <i class="fas fa-chart-pie me-1"></i>
                            Capacité
                        </th>
                        <th>
                            <i class="fas fa-percentage me-1"></i>
                            Utilisation
                        </th>
                        <th>
                            <i class="fas fa-sitemap me-1"></i>
                            Structure
                        </th>
                        <th>
                            <i class="fas fa-clock me-1"></i>
                            Dernière activité
                        </th>
                        <th width="150">
                            <i class="fas fa-cogs me-1"></i>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salles as $salle)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input salle-checkbox" value="{{ $salle->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <div class="avatar bg-{{ $salle->utilisation_percentage > 80 ? 'danger' : ($salle->utilisation_percentage > 50 ? 'warning' : 'success') }} text-white rounded">
                                            {{ strtoupper(substr($salle->nom, 0, 2)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $salle->nom }}</div>
                                        @if($salle->description)
                                            <small class="text-muted">{{ Str::limit($salle->description, 30) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $salle->organisme->nom_org }}</span>
                            </td>
                            <td>
                                <div class="text-center">
                                    <strong>{{ $salle->capacite_actuelle }}/{{ $salle->capacite_max }}</strong>
                                    <br><small class="text-muted">positions</small>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress me-2" style="width: 100px; height: 8px;">
                                        <div class="progress-bar bg-{{ $salle->utilisation_percentage < 50 ? 'success' : ($salle->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                                            style="width: {{ $salle->utilisation_percentage }}%"></div>
                                    </div>
                                    <span class="badge {{ $salle->utilisation_percentage < 50 ? 'bg-success' : ($salle->utilisation_percentage < 80 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ number_format($salle->utilisation_percentage, 1) }}%
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <span class="badge bg-info">{{ $salle->travees_count }} travée(s)</span>
                                    <span class="badge bg-secondary">{{ $salle->tablettes_count }} tablette(s)</span>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $salle->derniere_activite ? $salle->derniere_activite->diffForHumans() : 'Aucune activité' }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.salles.show', $salle) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.salles.edit', $salle) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.stockage.hierarchy') }}?salle={{ $salle->id }}" 
                                       class="btn btn-sm btn-outline-success" 
                                       title="Structure">
                                        <i class="fas fa-sitemap"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            title="Supprimer"
                                            onclick="confirmDelete('{{ $salle->id }}', '{{ $salle->nom }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-home fa-3x mb-3"></i>
                                    <p class="mb-0">Aucune salle trouvée</p>
                                    @if(request('search') || request('organisme_id') || request('status'))
                                        <p class="mt-2">
                                            <a href="{{ route('admin.salles.index') }}" class="btn btn-sm btn-outline-primary">
                                                Voir toutes les salles
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
        @if($salles->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $salles->firstItem() }} à {{ $salles->lastItem() }} sur {{ $salles->total() }} résultats
                </div>
                <div>
                   {{ $salles->onEachSide(1)->links('pagination::simple-bootstrap-4') }}
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
                <p>Êtes-vous sûr de vouloir supprimer la salle <strong id="salleNom"></strong> ?</p>
                <p class="text-danger"><small>Cette action supprimera également toutes les structures de stockage associées et est irréversible.</small></p>
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

<!-- Bulk Organisme Modal -->
<div class="modal fade" id="bulkOrganismeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Changer l'organisme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="bulkOrganismeForm">
                    <div class="mb-3">
                        <label for="bulk_organisme_id" class="form-label">Nouvel organisme</label>
                        <select id="bulk_organisme_id" name="organisme_id" class="form-select" required>
                            <option value="">Sélectionner un organisme</option>
                            @foreach($organismes as $org)
                                <option value="{{ $org->id }}">{{ $org->nom_org }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="executeBulkOrganisme()">Modifier</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let selectedSalles = [];

    // Select all checkbox
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.salle-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelection();
    });

    // Individual checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('salle-checkbox')) {
            updateSelection();
        }
    });

    function updateSelection() {
        const checkboxes = document.querySelectorAll('.salle-checkbox:checked');
        selectedSalles = Array.from(checkboxes).map(cb => cb.value);
        
        const count = selectedSalles.length;
        document.getElementById('selected-count').textContent = count;
        document.getElementById('bulk-actions').style.display = count > 0 ? 'block' : 'none';
        
        // Update select all checkbox
        const allCheckboxes = document.querySelectorAll('.salle-checkbox');
        const selectAllCheckbox = document.getElementById('select-all');
        selectAllCheckbox.indeterminate = count > 0 && count < allCheckboxes.length;
        selectAllCheckbox.checked = count === allCheckboxes.length;
    }

    function clearSelection() {
        document.querySelectorAll('.salle-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        updateSelection();
    }

    function bulkAction(action) {
        if (selectedSalles.length === 0) {
            alert('Veuillez sélectionner au moins une salle.');
            return;
        }

        if (action === 'delete') {
            if (confirm(`Êtes-vous sûr de vouloir supprimer ${selectedSalles.length} salle(s) ? Cette action supprimera également toutes les structures de stockage associées.`)) {
                executeBulkAction('delete', { confirm: '1' });
            }
        } else if (action === 'export') {
            executeBulkAction('export');
        } else if (action === 'optimize') {
            if (confirm(`Optimiser ${selectedSalles.length} salle(s) ? Cette action recalculera les capacités actuelles.`)) {
                executeBulkAction('optimize');
            }
        }
    }

    function showBulkOrganismeModal() {
        if (selectedSalles.length === 0) {
            alert('Veuillez sélectionner au moins une salle.');
            return;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('bulkOrganismeModal'));
        modal.show();
    }

    function executeBulkOrganisme() {
        const organismeId = document.getElementById('bulk_organisme_id').value;
        if (!organismeId) {
            alert('Veuillez sélectionner un organisme.');
            return;
        }

        const modal = bootstrap.Modal.getInstance(document.getElementById('bulkOrganismeModal'));
        modal.hide();
        
        executeBulkAction('update_organisme', { organisme_id: organismeId });
    }

    function executeBulkAction(action, data = {}) {
        const requestData = {
            action: action,
            salle_ids: selectedSalles,
            ...data
        };

        fetch('{{ route("admin.salles.bulk-action") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(requestData)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || `Erreur HTTP: ${response.status}`);
                });
            }
            
            // Handle export downloads
            if (action === 'export') {
                return response.blob().then(blob => {
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = url;
                    a.download = 'salles_export.csv';
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    return { success: true, message: 'Export terminé avec succès.' };
                });
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (data.message) {
                    alert(data.message);
                }
                
                // Reload page except for export
                if (action !== 'export') {
                    location.reload();
                }
            } else {
                alert('Erreur: ' + (data.message || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'exécution de l\'action: ' + error.message);
        });
    }

    function confirmDelete(salleId, salleNom) {
        document.getElementById('salleNom').textContent = salleNom;
        document.getElementById('deleteForm').action = '{{ route("admin.salles.index") }}/' + salleId;
        
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
</style>
@endpush