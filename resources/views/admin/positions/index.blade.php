@extends('layouts.admin')

@section('title', 'Gestion des Positions')

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
            <a href="{{ route('admin.positions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nouvelle Position
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
                <li><a class="dropdown-item" href="#" onclick="showBulkCreateModal()">
                    <i class="fas fa-layer-group me-2"></i>Création en masse
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
                               value="{{ request('search') }}" placeholder="Nom de position...">
                    </div>
                    <div class="col-md-3">
                        <label for="tablette_id" class="form-label">Tablette</label>
                        <select class="form-select" id="tablette_id" name="tablette_id">
                            <option value="">Toutes les tablettes</option>
                            @foreach($tablettes as $tablette)
                                <option value="{{ $tablette->id }}" {{ request('tablette_id') == $tablette->id ? 'selected' : '' }}>
                                    {{ $tablette->nom }} ({{ $tablette->travee->nom }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous</option>
                            <option value="libre" {{ request('status') == 'libre' ? 'selected' : '' }}>Libres</option>
                            <option value="occupee" {{ request('status') == 'occupee' ? 'selected' : '' }}>Occupées</option>
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
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>
                            Filtrer
                        </button>
                        <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Reset
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

<!-- Liste des positions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>
                    Liste des Positions
                    <span class="badge bg-primary ms-2">{{ $positions->total() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($positions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </th>
                                        <th>Position</th>
                                        <th>Localisation</th>
                                        <th>Statut</th>
                                        <th>Contenu</th>
                                        <th>Dernière modification</th>
                                        <th>Actions</th>
                                    </tr>
                             </thead>
                            <tbody>
                                @foreach($positions as $position)
                                    <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input position-checkbox" value="{{ $position->id }}">
                                            </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="position-icon me-2 bg-{{ $position->vide ? 'warning' : 'success' }}">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $position->nom }}</h6>
                                                    <small class="text-muted">{{ $position->tablette->nom }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <small class="text-muted">{{ $position->tablette->travee->salle->organisme->nom_org }}</small>
                                                <br>{{ $position->tablette->travee->salle->nom }}
                                                <br>{{ $position->tablette->travee->nom }}
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
                                                <a href="{{ route('admin.boites.show', $position->boite) }}">
                                                    {{ $position->boite->numero }}
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $position->updated_at->format('d/m/Y H:i') }}
                                            <br><small>{{ $position->updated_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.positions.show', $position) }}" 
                                                   class="btn btn-outline-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.positions.edit', $position) }}" 
                                                   class="btn btn-outline-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($position->vide)
                                                    <button class="btn btn-outline-danger" 
                                                            onclick="deletePosition({{ $position->id }})" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-secondary" disabled title="Occupée - impossible à supprimer">
                                                        <i class="fas fa-lock"></i>
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
                    @if($positions->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $positions->onEachSide(1)->links('pagination::simple-bootstrap-4') }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune position trouvée</h5>
                        <p class="text-muted">Commencez par créer votre première position.</p>
                        <a href="{{ route('admin.positions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Créer une position
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de création en masse -->
<div class="modal fade" id="bulkCreateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-layer-group me-2"></i>
                    Création de Positions en Masse
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkCreateForm" action="{{ route('admin.positions.bulk-create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="bulk_tablette_id" class="form-label">Tablette <span class="text-danger">*</span></label>
                        <select class="form-select" id="bulk_tablette_id" name="tablette_id" required>
                            <option value="">Sélectionner une tablette</option>
                            @foreach($tablettes as $tablette)
                                <option value="{{ $tablette->id }}">
                                    {{ $tablette->nom }} ({{ $tablette->travee->nom }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nombre_positions" class="form-label">Nombre de positions <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="nombre_positions" 
                               name="nombre_positions" min="1" max="100" value="10" required>
                        <small class="text-muted">Maximum 100 positions à la fois</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="prefix" class="form-label">Préfixe <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="prefix" 
                               name="prefix" value="P" maxlength="3" required>
                        <small class="text-muted">Exemple: P → P001, P002...</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        Créer les positions
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Variables globales
    let selectedPositions = [];

    // Sélection multiple
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.position-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedPositions();
    });

    // Mise à jour des positions sélectionnées
    function updateSelectedPositions() {
        selectedPositions = Array.from(document.querySelectorAll('.position-checkbox:checked')).map(cb => cb.value);
        console.log('Positions sélectionnées:', selectedPositions);
    }

    // Ajouter des écouteurs sur les checkboxes individuelles
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('position-checkbox')) {
            updateSelectedPositions();
        }
    });

    // Actions groupées - Version corrigée
    function showBulkActions() {
        updateSelectedPositions(); // S'assurer que la sélection est à jour
        
        if (selectedPositions.length === 0) {
            alert('Veuillez sélectionner au moins une position.');
            return;
        }
        
        // Supprimer l'ancien modal s'il existe
        const existingModal = document.getElementById('bulkActionsModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Créer le modal d'actions groupées
        const modalHtml = `
            <div class="modal fade" id="bulkActionsModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Actions Groupées</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>${selectedPositions.length}</strong> position(s) sélectionnée(s)</p>
                            <div class="mb-3">
                                <label class="form-label">Action à effectuer :</label>
                                <select class="form-select" id="bulkActionSelect">
                                    <option value="export">Exporter la sélection</option>
                                    <option value="delete">Supprimer (positions libres uniquement)</option>
                                    <option value="move">Déplacer vers une autre tablette</option>
                                </select>
                            </div>
                            <div id="moveOptions" style="display: none;">
                                <label class="form-label">Nouvelle tablette :</label>
                                <select class="form-select" id="newTabletteId">
                                    <option value="">Sélectionner une tablette</option>
                                    @foreach($tablettes as $tablette)
                                        <option value="{{ $tablette->id }}">{{ $tablette->nom }} ({{ $tablette->travee->nom }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-primary" id="executeBulkBtn">Exécuter</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Attacher les événements APRÈS la création du modal
        const modal = new bootstrap.Modal(document.getElementById('bulkActionsModal'));
        
        // Gérer l'affichage des options de déplacement
        document.getElementById('bulkActionSelect').addEventListener('change', function() {
            const moveOptions = document.getElementById('moveOptions');
            moveOptions.style.display = this.value === 'move' ? 'block' : 'none';
        });
        
        // Attacher l'événement au bouton Exécuter
        document.getElementById('executeBulkBtn').addEventListener('click', function() {
            executeBulkAction();
        });
        
        modal.show();
    }

    // Exécuter l'action groupée - Version corrigée
    function executeBulkAction() {
        const action = document.getElementById('bulkActionSelect').value;
        const newTabletteId = document.getElementById('newTabletteId') ? document.getElementById('newTabletteId').value : null;
        
        if (action === 'move' && !newTabletteId) {
            alert('Veuillez sélectionner une tablette de destination.');
            return;
        }
        
        // Désactiver le bouton pendant le traitement
        const executeBtn = document.getElementById('executeBulkBtn');
        executeBtn.disabled = true;
        executeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement...';
        
        const formData = new FormData();
        formData.append('action', action);
        formData.append('position_ids', JSON.stringify(selectedPositions));
        if (newTabletteId) {
            formData.append('new_tablette_id', newTabletteId);
        }
        
        fetch('{{ route("admin.positions.bulk-action") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                alert(data.message);
                if (action === 'export') {
                    // Pour l'export, le fichier sera téléchargé automatiquement
                    console.log('Export effectué');
                } else {
                    window.location.reload();
                }
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue: ' + error.message);
        })
        .finally(() => {
            // Fermer le modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('bulkActionsModal'));
            if (modal) {
                modal.hide();
            }
        });
    }

    // Supprimer une position
    function deletePosition(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette position ?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('admin.positions.index') }}/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Exporter les données
    function exportData() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = `{{ route('admin.positions.index') }}/export?${params.toString()}`;
    }

    // Afficher le modal de création en masse
    function showBulkCreateModal() {
        const modal = new bootstrap.Modal(document.getElementById('bulkCreateModal'));
        modal.show();
    }
    
    // Soumission du formulaire de création en masse
    document.addEventListener('DOMContentLoaded', function() {
        const bulkCreateForm = document.getElementById('bulkCreateForm');
        if (bulkCreateForm) {
            bulkCreateForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const submitBtn = document.querySelector('#bulkCreateModal .btn-primary');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Création...';
                
                fetch('{{ route("admin.positions.bulk-create") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`${data.created || 0} position(s) créées avec succès !`);
                        bootstrap.Modal.getInstance(document.getElementById('bulkCreateModal')).hide();
                        window.location.reload();
                    } else {
                        alert('Erreur: ' + (data.message || 'Erreur inconnue'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue.');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        }
    });

    // Debug: Vérifier si les routes existent
    console.log('Route bulk-action:', '{{ route("admin.positions.bulk-action") }}');
    console.log('Route bulk-create:', '{{ route("admin.positions.bulk-create") }}');
</script>
@endpush

@push('styles')
<style>
    .position-icon {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: white;
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
</style>
@endpush