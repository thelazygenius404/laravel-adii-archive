{{-- resources/views/admin/boites/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Gestion des Boîtes')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-box me-2"></i>
            Gestion des Boîtes
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.boites.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nouvelle Boîte
            </a>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-cogs me-1"></i>
                    Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.boites.export') }}">
                        <i class="fas fa-download me-2"></i>Exporter la liste
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.boites.low-occupancy') }}">
                        <i class="fas fa-exclamation-triangle me-2"></i>Faible occupation
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.boites.available-space') }}">
                        <i class="fas fa-search me-2"></i>Espaces disponibles
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
                               value="{{ request('search') }}" placeholder="Numéro de boîte...">
                    </div>
                    <div class="col-md-2">
                        <label for="organisme" class="form-label">Organisme</label>
                        <select class="form-select" id="organisme" name="organisme">
                            <option value="">Tous</option>
                            @foreach($organismes as $org)
                                <option value="{{ $org->id }}" {{ request('organisme') == $org->id ? 'selected' : '' }}>
                                    {{ $org->nom_org }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="salle" class="form-label">Salle</label>
                        <select class="form-select" id="salle" name="salle">
                            <option value="">Toutes</option>
                            @foreach($salles as $salle)
                                <option value="{{ $salle->id }}" {{ request('salle') == $salle->id ? 'selected' : '' }}>
                                    {{ $salle->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Tous</option>
                            <option value="empty" {{ request('status') == 'empty' ? 'selected' : '' }}>Vide</option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partielle</option>
                            <option value="full" {{ request('status') == 'full' ? 'selected' : '' }}>Pleine</option>
                            <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>Faible occupation</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label for="per_page" class="form-label">Par page</label>
                        <select class="form-select" id="per_page" name="per_page">
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
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
                <i class="fas fa-box text-primary fa-3x mb-3"></i>
                <h3 class="text-primary">{{ $stats['total'] }}</h3>
                <p class="text-muted mb-0">Boîtes Totales</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                <h3 class="text-success">{{ $stats['actives'] }}</h3>
                <p class="text-muted mb-0">Boîtes Actives</p>
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
        <div class="card border-danger">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                <h3 class="text-danger">{{ $stats['faible_occupation'] }}</h3>
                <p class="text-muted mb-0">Faible Occupation</p>
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
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary active" onclick="toggleView('table')">
                        <i class="fas fa-table"></i>
                    </button>
                    <button class="btn btn-outline-primary" onclick="toggleView('cards')">
                        <i class="fas fa-th"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Vue tableau -->
                <div id="tableView">
                    @if($boites->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </th>
                                        <th>Boîte</th>
                                        <th>Localisation</th>
                                        <th>Capacité</th>
                                        <th>Utilisation</th>
                                        <th>Dossiers</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($boites as $boite)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input boite-checkbox" value="{{ $boite->id }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <div class="box-icon bg-{{ $boite->utilisation_percentage > 90 ? 'danger' : ($boite->utilisation_percentage > 70 ? 'warning' : 'success') }} text-white">
                                                            <i class="fas fa-box"></i>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $boite->numero }}</h6>
                                                        @if($boite->code_thematique)
                                                            <small class="text-muted">{{ $boite->code_thematique }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $boite->position->tablette->travee->salle->nom }}</strong>
                                                    <br><small class="text-muted">{{ $boite->full_location }}</small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <strong>{{ $boite->capacite }}</strong>
                                                <br><small class="text-muted">dossiers max</small>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 80px; height: 8px;">
                                                        <div class="progress-bar bg-{{ $boite->utilisation_percentage < 50 ? 'success' : ($boite->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                                                             style="width: {{ $boite->utilisation_percentage }}%"></div>
                                                    </div>
                                                    <small>{{ number_format($boite->utilisation_percentage, 1) }}%</small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ $boite->nbr_dossiers }}/{{ $boite->capacite }}</span>
                                                @if($boite->dossiers_actifs < $boite->nbr_dossiers)
                                                    <br><small class="text-muted">{{ $boite->dossiers_actifs }} actifs</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($boite->nbr_dossiers == 0)
                                                    <span class="badge bg-secondary">Vide</span>
                                                @elseif($boite->utilisation_percentage >= 100)
                                                    <span class="badge bg-danger">Pleine</span>
                                                @elseif($boite->utilisation_percentage < 30)
                                                    <span class="badge bg-warning">Faible</span>
                                                @else
                                                    <span class="badge bg-success">Active</span>
                                                @endif
                                                
                                                @if($boite->date_destruction)
                                                    <br><span class="badge bg-dark">Détruite</span>
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
                                                    @if(!$boite->date_destruction)
                                                        <button class="btn btn-outline-warning" 
                                                                onclick="destroyBox({{ $boite->id }})" title="Détruire">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <button class="btn btn-outline-success" 
                                                                onclick="restoreBox({{ $boite->id }})" title="Restaurer">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune boîte trouvée</h5>
                            <p class="text-muted">Commencez par créer votre première boîte de stockage.</p>
                            <a href="{{ route('admin.boites.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Créer une boîte
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Vue cartes -->
                <div id="cardsView" style="display: none;">
                    @if($boites->count() > 0)
                        <div class="row">
                            @foreach($boites as $boite)
                                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                    <div class="card h-100 boite-card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="box-icon bg-{{ $boite->utilisation_percentage > 90 ? 'danger' : ($boite->utilisation_percentage > 70 ? 'warning' : 'success') }} text-white me-3">
                                                    <i class="fas fa-box"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="card-title mb-0">{{ $boite->numero }}</h6>
                                                    @if($boite->code_thematique)
                                                        <small class="text-muted">{{ $boite->code_thematique }}</small>
                                                    @endif
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="{{ route('admin.boites.show', $boite) }}">
                                                            <i class="fas fa-eye me-2"></i>Voir
                                                        </a></li>
                                                        <li><a class="dropdown-item" href="{{ route('admin.boites.edit', $boite) }}">
                                                            <i class="fas fa-edit me-2"></i>Modifier
                                                        </a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="destroyBox({{ $boite->id }})">
                                                            <i class="fas fa-trash me-2"></i>Détruire
                                                        </a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                            
                                            @if($boite->code_thematique)
                                                <p class="card-text text-muted mb-3">{{ Str::limit($boite->code_thematique, 60) }}</p>
                                            @endif
                                            
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-muted">Utilisation</small>
                                                    <small>{{ number_format($boite->utilisation_percentage, 1) }}%</small>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-{{ $boite->utilisation_percentage < 50 ? 'success' : ($boite->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                                                         style="width: {{ $boite->utilisation_percentage }}%"></div>
                                                </div>
                                            </div>

                                            <div class="row text-center">
                                                <div class="col-6">
                                                    <div class="border-end">
                                                        <h6 class="mb-0">{{ $boite->capacite }}</h6>
                                                        <small class="text-muted">Capacité</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <h6 class="mb-0">{{ $boite->nbr_dossiers }}</h6>
                                                    <small class="text-muted">Dossiers</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.boites.show', $boite) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                                    <i class="fas fa-eye me-1"></i>Voir
                                                </a>
                                                <a href="{{ route('admin.boites.edit', $boite) }}" class="btn btn-sm btn-outline-success flex-fill">
                                                    <i class="fas fa-edit me-1"></i>Modifier
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune boîte trouvée</h5>
                            <p class="text-muted">Commencez par créer votre première boîte de stockage.</p>
                            <a href="{{ route('admin.boites.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Créer une boîte
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Pagination -->
                @if($boites->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $boites->appends(request()->query())->links() }}
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
                            <option value="export">Exporter les boîtes sélectionnées</option>
                            <option value="destroy">Marquer comme détruites</option>
                            <option value="move">Déplacer vers une autre position</option>
                            <option value="delete">Supprimer définitivement</option>
                        </select>
                    </div>
                    <div id="additionalFields"></div>
                    <input type="hidden" id="selectedBoites" name="boite_ids">
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
        const cardsView = document.getElementById('cardsView');
        const buttons = document.querySelectorAll('[onclick*="toggleView"]');
        
        buttons.forEach(btn => btn.classList.remove('active'));
        
        if (view === 'cards') {
            tableView.style.display = 'none';
            cardsView.style.display = 'block';
            document.querySelector('[onclick="toggleView(\'cards\')"]').classList.add('active');
        } else {
            tableView.style.display = 'block';
            cardsView.style.display = 'none';
            document.querySelector('[onclick="toggleView(\'table\')"]').classList.add('active');
        }
    }

    // Sélection multiple
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.boite-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Actions groupées
    function bulkAction() {
        const selected = Array.from(document.querySelectorAll('.boite-checkbox:checked')).map(cb => cb.value);
        
        if (selected.length === 0) {
            alert('Veuillez sélectionner au moins une boîte.');
            return;
        }
        
        document.getElementById('selectedBoites').value = JSON.stringify(selected);
        const modal = new bootstrap.Modal(document.getElementById('bulkActionModal'));
        modal.show();
    }

    // Changer les champs additionnels selon l'action
    document.getElementById('bulkAction').addEventListener('change', function() {
        const additionalFields = document.getElementById('additionalFields');
        additionalFields.innerHTML = '';
        
        switch(this.value) {
            case 'move':
                additionalFields.innerHTML = `
                    <div class="mb-3">
                        <label for="newPosition" class="form-label">Nouvelle position</label>
                        <select class="form-select" id="newPosition" name="new_position_id" required>
                            <option value="">Choisir une position...</option>
                            <!-- Options chargées via AJAX -->
                        </select>
                    </div>
                `;
                loadAvailablePositions();
                break;
            case 'delete':
                additionalFields.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention !</strong> Cette action supprimera définitivement les boîtes et tous leurs dossiers.
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmDelete" name="confirm" required>
                        <label class="form-check-label" for="confirmDelete">
                            Je comprends que cette action est irréversible
                        </label>
                    </div>
                `;
                break;
            case 'destroy':
                additionalFields.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Information :</strong> Les boîtes seront marquées comme détruites mais pourront être restaurées.
                    </div>
                `;
                break;
        }
    });

    // Charger les positions disponibles
    function loadAvailablePositions() {
        fetch('{{ route("stockage.positions.available") }}')
            .then(response => response.json())
            .then(positions => {
                const select = document.getElementById('newPosition');
                positions.forEach(position => {
                    const option = document.createElement('option');
                    option.value = position.id;
                    option.textContent = `${position.nom} - ${position.full_path}`;
                    select.appendChild(option);
                });
            })
            .catch(error => console.error('Erreur:', error));
    }

    // Soumettre l'action groupée
    function submitBulkAction() {
        const form = document.getElementById('bulkActionForm');
        const formData = new FormData(form);
        
        fetch('{{ route("admin.boites.bulk-action") }}', {
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

    // Détruire une boîte (logique)
    function destroyBox(id) {
        if (confirm('Êtes-vous sûr de vouloir marquer cette boîte comme détruite ?')) {
            fetch(`{{ route('admin.boites.index') }}/${id}/destroy-box`, {
                method: 'PUT',
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
                    alert('Erreur lors de la destruction: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la destruction');
            });
        }
    }

    // Restaurer une boîte
    function restoreBox(id) {
        if (confirm('Êtes-vous sûr de vouloir restaurer cette boîte ?')) {
            fetch(`{{ route('admin.boites.index') }}/${id}/restore-box`, {
                method: 'PUT',
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
                    alert('Erreur lors de la restauration: ' + (data.message || 'Erreur inconnue'));
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la restauration');
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

    // Filtrage dynamique des salles par organisme
    document.getElementById('organisme').addEventListener('change', function() {
        const organismeId = this.value;
        const salleSelect = document.getElementById('salle');
        
        // Réinitialiser les options de salle
        salleSelect.innerHTML = '<option value="">Toutes</option>';
        
        if (organismeId) {
            fetch(`{{ route('admin.salles.by-organisme', '') }}/${organismeId}`)
                .then(response => response.json())
                .then(salles => {
                    salles.forEach(salle => {
                        const option = document.createElement('option');
                        option.value = salle.id;
                        option.textContent = salle.nom;
                        salleSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Erreur:', error));
        }
    });
</script>
@endpush

@push('styles')
<style>
    .box-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 1.2rem;
    }

    .boite-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .boite-card:hover {
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

    .badge {
        font-size: 0.75em;
    }
</style>
@endpush