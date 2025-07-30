{{-- resources/views/admin/salles/index.blade.php --}}
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
            <a href="{{ route('admin.salles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nouvelle Salle
            </a>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-cogs me-1"></i>
                    Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.salles.export') }}">
                        <i class="fas fa-download me-2"></i>Exporter la liste
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.salles.statistics') }}">
                        <i class="fas fa-chart-bar me-2"></i>Voir les statistiques
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="bulkAction()">
                        <i class="fas fa-tasks me-2"></i>Actions groupées
                    </a></li>
                </ul>
            </div>
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
                               value="{{ request('search') }}" placeholder="Nom de la salle...">
                    </div>
                    <div class="col-md-3">
                        <label for="organisme" class="form-label">Organisme</label>
                        <select class="form-select" id="organisme" name="organisme">
                            <option value="">Tous les organismes</option>
                            @foreach($organismes as $org)
                                <option value="{{ $org->id }}" {{ request('organisme') == $org->id ? 'selected' : '' }}>
                                    {{ $org->nom_org }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="full" {{ request('status') == 'full' ? 'selected' : '' }}>Pleine</option>
                            <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Peu utilisée</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="sort" class="form-label">Trier par</label>
                        <select class="form-select" id="sort" name="sort">
                            <option value="nom" {{ request('sort') == 'nom' ? 'selected' : '' }}>Nom</option>
                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date création</option>
                            <option value="utilisation" {{ request('sort') == 'utilisation' ? 'selected' : '' }}>Utilisation</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>
                            Filtrer
                        </button>
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

<!-- Liste des salles -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>
                    Liste des Salles
                    <span class="badge bg-primary ms-2">{{ $salles->total() }}</span>
                </h5>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary active" onclick="toggleView('table')">
                        <i class="fas fa-table"></i>
                    </button>
                    <button class="btn btn-outline-primary" onclick="toggleView('grid')">
                        <i class="fas fa-th"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Vue tableau -->
                <div id="tableView">
                    @if($salles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </th>
                                        <th>Salle</th>
                                        <th>Organisme</th>
                                        <th>Capacité</th>
                                        <th>Utilisation</th>
                                        <th>Structure</th>
                                        <th>Dernière activité</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salles as $salle)
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
                                                        <h6 class="mb-0">{{ $salle->nom }}</h6>
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
                                                    <strong>{{ $salle->capacite_totale }}</strong>
                                                    <br><small class="text-muted">positions</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 100px; height: 8px;">
                                                        <div class="progress-bar bg-{{ $salle->utilisation_percentage < 50 ? 'success' : ($salle->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                                                             style="width: {{ $salle->utilisation_percentage }}%"></div>
                                                    </div>
                                                    <small>{{ number_format($salle->utilisation_percentage, 1) }}%</small>
                                                </div>
                                                <small class="text-muted">{{ $salle->positions_occupees }}/{{ $salle->capacite_totale }}</small>
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
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.salles.show', $salle) }}" 
                                                       class="btn btn-outline-info" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.salles.edit', $salle) }}" 
                                                       class="btn btn-outline-primary" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.stockage.hierarchy') }}?salle={{ $salle->id }}" 
                                                       class="btn btn-outline-success" title="Structure">
                                                        <i class="fas fa-sitemap"></i>
                                                    </a>
                                                    <button class="btn btn-outline-danger" 
                                                            onclick="deleteSalle({{ $salle->id }})" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-home fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune salle trouvée</h5>
                            <p class="text-muted">Commencez par créer votre première salle de stockage.</p>
                            <a href="{{ route('admin.salles.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Créer une salle
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Vue grille -->
                <div id="gridView" style="display: none;">
                    @if($salles->count() > 0)
                        <div class="row">
                            @foreach($salles as $salle)
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                    <div class="card h-100 salle-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="avatar bg-{{ $salle->utilisation_percentage > 80 ? 'danger' : ($salle->utilisation_percentage > 50 ? 'warning' : 'success') }} text-white rounded me-3">
                                                    {{ strtoupper(substr($salle->nom, 0, 2)) }}
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="card-title mb-0">{{ $salle->nom }}</h6>
                                                    <small class="text-muted">{{ $salle->organisme->nom_org }}</small>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="{{ route('admin.salles.show', $salle) }}">
                                                            <i class="fas fa-eye me-2"></i>Voir
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="{{ route('admin.salles.edit', $salle) }}">
                                                            <i class="fas fa-edit me-2"></i>Modifier
                                                        </a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteSalle({{ $salle->id }})">
                                                            <i class="fas fa-trash me-2"></i>Supprimer
                                                        </a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            
                                            @if($salle->description)
                                                <p class="card-text text-muted mb-3">{{ Str::limit($salle->description, 60) }}</p>
                                            @endif
                                            
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-muted">Utilisation</small>
                                                    <small>{{ number_format($salle->utilisation_percentage, 1) }}%</small>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-{{ $salle->utilisation_percentage < 50 ? 'success' : ($salle->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                                                         style="width: {{ $salle->utilisation_percentage }}%"></div>
                                                </div>
                                            </div>

                                            <div class="row text-center">
                                                <div class="col-4">
                                                    <div class="border-end">
                                                        <h6 class="mb-0">{{ $salle->travees_count }}</h6>
                                                        <small class="text-muted">Travées</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <div class="border-end">
                                                        <h6 class="mb-0">{{ $salle->tablettes_count }}</h6>
                                                        <small class="text-muted">Tablettes</small>
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <h6 class="mb-0">{{ $salle->positions_occupees }}</h6>
                                                    <small class="text-muted">Occupées</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.salles.show', $salle) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                                    <i class="fas fa-eye me-1"></i>Voir
                                                </a>
                                                <a href="{{ route('admin.stockage.hierarchy') }}?salle={{ $salle->id }}" class="btn btn-sm btn-outline-success flex-fill">
                                                    <i class="fas fa-sitemap me-1"></i>Structure
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-home fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune salle trouvée</h5>
                            <p class="text-muted">Commencez par créer votre première salle de stockage.</p>
                            <a href="{{ route('admin.salles.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Créer une salle
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if($salles->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $salles->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal pour actions groupées -->
<div class="modal fade" id="bulkActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-tasks me-2"></i>
                    Actions Groupées
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="bulkActionForm">
                    <div class="mb-3">
                        <label for="bulkAction" class="form-label">Action à effectuer</label>
                        <select class="form-select" id="bulkAction" name="action" required>
                            <option value="">Choisir une action...</option>
                            <option value="export">Exporter les salles sélectionnées</option>
                            <option value="update_organisme">Changer d'organisme</option>
                            <option value="optimize">Optimiser l'organisation</option>
                            <option value="delete">Supprimer les salles sélectionnées</option>
                        </select>
                    </div>
                    <div id="additionalFields"></div>
                    <input type="hidden" id="selectedSalles" name="salles">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="submitBulkAction()">
                    <i class="fas fa-check me-2"></i>
                    Exécuter
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Basculer entre les vues
    function toggleView(view) {
        const tableView = document.getElementById('tableView');
        const gridView = document.getElementById('gridView');
        const buttons = document.querySelectorAll('[onclick*="toggleView"]');
        
        buttons.forEach(btn => btn.classList.remove('active'));
        
        if (view === 'grid') {
            tableView.style.display = 'none';
            gridView.style.display = 'block';
            document.querySelector('[onclick="toggleView(\'grid\')"]').classList.add('active');
        } else {
            tableView.style.display = 'block';
            gridView.style.display = 'none';
            document.querySelector('[onclick="toggleView(\'table\')"]').classList.add('active');
        }
    }

    // Sélection multiple
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.salle-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Actions groupées
    function bulkAction() {
        const selected = Array.from(document.querySelectorAll('.salle-checkbox:checked')).map(cb => cb.value);
        
        if (selected.length === 0) {
            alert('Veuillez sélectionner au moins une salle.');
            return;
        }
        
        document.getElementById('selectedSalles').value = JSON.stringify(selected);
        const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
        modal.show();
    }

    // Changer les champs additionnels selon l'action
    document.getElementById('bulkAction').addEventListener('change', function() {
        const additionalFields = document.getElementById('additionalFields');
        additionalFields.innerHTML = '';
        
        switch(this.value) {
            case 'update_organisme':
                additionalFields.innerHTML = `
                    <div class="mb-3">
                        <label for="newOrganisme" class="form-label">Nouvel organisme</label>
                        <select class="form-select" id="newOrganisme" name="organisme_id" required>
                            <option value="">Choisir un organisme...</option>
                            @foreach($organismes as $org)
                                <option value="{{ $org->id }}">{{ $org->nom_org }}</option>
                            @endforeach
                        </select>
                    </div>
                `;
                break;
            case 'delete':
                additionalFields.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention !</strong> Cette action est irréversible et supprimera également toutes les structures de stockage associées.
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmDelete" name="confirm" required>
                        <label class="form-check-label" for="confirmDelete">
                            Je comprends que cette action est irréversible
                        </label>
                    </div>
                `;
                break;
        }
    });

    // Soumettre l'action groupée
    function submitBulkAction() {
        const form = document.getElementById('bulkActionForm');
        const formData = new FormData(form);
        
        fetch('{{ route("admin.salles.index") }}/bulk-action', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de l\'exécution de l\'action: ' + (data.message || 'Erreur inconnue'));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'exécution de l\'action');
        });
    }

    // Supprimer une salle
    function deleteSalle(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette salle ? Cette action supprimera également toutes les structures de stockage associées.')) {
            fetch(`{{ route('admin.salles.index') }}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la suppression: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la suppression');
            });
        }
    }

    // Recherche en temps réel
    let searchTimeout;
    document.getElementById('search').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 2 || this.value.length === 0) {
                this.form.submit();
            }
        }, 500);
    });
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

    .salle-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .salle-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .progress {
        background-color: #e9ecef;
    }

    .card-body .border-end:last-child {
        border-right: none !important;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }

    .btn-group .btn.active {
        background-color: var(--bs-primary);
        color: white;
        border-color: var(--bs-primary);
    }

    .card-footer {
        padding: 0.75rem 1.25rem;
    }

    .alert-danger {
        border: none;
        border-radius: 8px;
    }
</style>
@endpush