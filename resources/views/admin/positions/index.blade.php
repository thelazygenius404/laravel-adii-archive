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

    // Actions groupées
    function showBulkActions() {
        const selected = Array.from(document.querySelectorAll('.position-checkbox:checked')).map(cb => cb.value);
        
        if (selected.length === 0) {
            alert('Veuillez sélectionner au moins une position.');
            return;
        }
        
        // Implémenter les actions groupées
        console.log('Actions groupées pour:', selected);
    }
    
    // Afficher le modal de création en masse
    function showBulkCreateModal() {
        const modal = new bootstrap.Modal(document.getElementById('bulkCreateModal'));
        modal.show();
    }
    
    // Soumission du formulaire de création en masse
    document.getElementById('bulkCreateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
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
                alert(`${data.created} positions créées avec succès !`);
                window.location.reload();
            } else {
                alert('Une erreur est survenue lors de la création.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue.');
        });
    });
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