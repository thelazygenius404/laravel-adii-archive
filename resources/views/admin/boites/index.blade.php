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
                               value="{{ request('search') }}" placeholder="Numéro, référence...">
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
                            <option value="eliminated" {{ request('status') == 'eliminated' ? 'selected' : '' }}>Éliminées</option>
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
                <h3 class="text-success">{{ $boites->where('elimine', false)->count() }}</h3>
                <p class="text-muted mb-0">Boîtes Actives</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                <h3 class="text-warning">{{ $boites->where('elimine', true)->count() }}</h3>
                <p class="text-muted mb-0">Boîtes Éliminées</p>
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
            </div>
            <div class="card-body">
                @if($boites->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Numéro</th>
                                    <th>Localisation</th>
                                    <th>Dossiers</th>
                                    <th>Dates</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($boites as $boite)
                                    <tr class="{{ $boite->elimine ? 'table-secondary' : '' }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="boite-icon bg-{{ $boite->elimine ? 'secondary' : 'primary' }}">
                                                        <i class="fas fa-archive"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $boite->numero }}</h6>
                                                    <small class="text-muted">{{ $boite->reference }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($boite->position)
                                                <a href="{{ route('admin.positions.show', $boite->position) }}">
                                                    {{ $boite->position->full_path }}
                                                </a>
                                            @else
                                                <span class="text-muted">Non localisée</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $boite->dossiers_count }}</span>
                                            @if($boite->dossiers_count > 0)
                                                <br><small class="text-muted">{{ $boite->occupation_percentage }}% occupé</small>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">Archivage:</small>
                                            <div>{{ $boite->date_archivage->format('d/m/Y') }}</div>
                                            <small class="text-muted">Élimination:</small>
                                            <div>{{ $boite->date_elimination ? $boite->date_elimination->format('d/m/Y') : '-' }}</div>
                                        </td>
                                        <td>
                                            @if($boite->elimine)
                                                <span class="badge bg-secondary">Éliminée</span>
                                            @else
                                                <span class="badge bg-success">Active</span>
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
                                                @if($boite->elimine)
                                                    <button class="btn btn-outline-success" 
                                                            onclick="restoreBoite({{ $boite->id }})" title="Restaurer">
                                                        <i class="fas fa-trash-restore"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-danger" 
                                                            onclick="confirmElimination({{ $boite->id }})" title="Éliminer">
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

<!-- Modal de confirmation d'élimination -->
<div class="modal fade" id="eliminationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer l'élimination</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir marquer cette boîte comme éliminée ?</p>
                <p class="text-danger"><small>Cette action est réversible mais doit être utilisée avec précaution.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="eliminationForm" method="POST" style="display: none;">
                    @csrf
                    @method('PUT')
                </form>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('eliminationForm').submit()">
                    Confirmer l'élimination
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de restauration -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la restauration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir restaurer cette boîte ?</p>
                <p class="text-success"><small>La boîte redeviendra active dans le système.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="restoreForm" method="POST" style="display: none;">
                    @csrf
                    @method('PUT')
                </form>
                <button type="button" class="btn btn-success" onclick="document.getElementById('restoreForm').submit()">
                    Confirmer la restauration
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Confirmer l'élimination d'une boîte
    function confirmElimination(id) {
        const modal = new bootstrap.Modal(document.getElementById('eliminationModal'));
        const form = document.getElementById('eliminationForm');
        form.action = `/admin/boites/${id}/eliminate`;
        modal.show();
    }

    // Restaurer une boîte
    function restoreBoite(id) {
        const modal = new bootstrap.Modal(document.getElementById('restoreModal'));
        const form = document.getElementById('restoreForm');
        form.action = `/admin/boites/${id}/restore`;
        modal.show();
    }

    // Exporter les données
    function exportData() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = `{{ route('admin.boites.index') }}/export?${params.toString()}`;
    }

    // Actions groupées
    function showBulkActions() {
        const selected = Array.from(document.querySelectorAll('.boite-checkbox:checked')).map(cb => cb.value);
        
        if (selected.length === 0) {
            alert('Veuillez sélectionner au moins une boîte.');
            return;
        }
        
        // Implémenter les actions groupées
        console.log('Actions groupées pour:', selected);
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
</style>
@endpush