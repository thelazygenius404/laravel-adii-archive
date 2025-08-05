{{-- resources/views/admin/stockage/hierarchy.blade.php --}}
@extends('layouts.admin')

@section('title', 'Hiérarchie de Stockage')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-sitemap me-2"></i>
            Hiérarchie de Stockage
            @if($organisme)
                - {{ $organisme->nom_org }}
            @endif
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stockage.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour au tableau de bord
            </a>
            <button class="btn btn-outline-primary" onclick="toggleAllHierarchy(true)">
                <i class="fas fa-expand-alt me-2"></i>
                Tout développer
            </button>
            <button class="btn btn-outline-secondary" onclick="toggleAllHierarchy(false)">
                <i class="fas fa-compress-alt me-2"></i>
                Tout réduire
            </button>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-filter me-2"></i>
                    Filtres
                </h6>
            </div>
            <div class="card-body">
                <!-- Filtre par organisme -->
                <div class="mb-3">
                    <label for="organismeFilter" class="form-label">Organisme</label>
                    <select class="form-select" id="organismeFilter" onchange="filterByOrganisme()">
                        <option value="">Tous les organismes</option>
                        @foreach($organismes as $org)
                            <option value="{{ $org->id }}" {{ $organisme && $organisme->id == $org->id ? 'selected' : '' }}>
                                {{ $org->nom_org }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtre par statut -->
                <div class="mb-3">
                    <label for="statusFilter" class="form-label">Statut</label>
                    <select class="form-select" id="statusFilter" onchange="filterByStatus()">
                        <option value="">Tous</option>
                        <option value="available">Positions libres</option>
                        <option value="occupied">Positions occupées</option>
                    </select>
                </div>

                <!-- Recherche -->
                <div class="mb-3">
                    <label for="searchInput" class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Rechercher..." oninput="searchInHierarchy()">
                </div>

                <!-- Statistiques rapides -->
                <div class="border-top pt-3">
                    <h6 class="text-muted mb-2">Statistiques</h6>
                    <div id="quickStats">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Salles:</span>
                            <span class="badge bg-primary" id="statsRooms">{{ $salles->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Positions totales:</span>
                            <span class="badge bg-info" id="statsPositions">-</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Occupées:</span>
                            <span class="badge bg-success" id="statsOccupied">-</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Libres:</span>
                            <span class="badge bg-warning" id="statsFree">-</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-warehouse me-2"></i>
                    Structure de Stockage
                </h5>
                <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-success" onclick="showAvailablePositions()">
                        <i class="fas fa-circle me-1"></i>
                        Positions libres
                    </button>
                    <button class="btn btn-outline-info" onclick="exportHierarchy()">
                        <i class="fas fa-download me-1"></i>
                        Exporter
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="hierarchyContainer">
                    @foreach($salles as $salle)
                        <div class="hierarchy-item salle-item" data-type="salle" data-organisme="{{ $salle->organisme_id }}">
                            <div class="hierarchy-header" onclick="toggleHierarchyItem(this)">
                                <i class="fas fa-chevron-down toggle-icon"></i>
                                <i class="fas fa-home text-primary me-2"></i>
                                <strong>{{ $salle->nom }}</strong>
                                <span class="badge bg-primary ms-2">{{ $salle->organisme->nom_org }}</span>
                                <span class="badge bg-info ms-1">{{ $salle->travees_count ?? $salle->travees->count() }} travée(s)</span>
                                <div class="ms-auto d-flex align-items-center">
                                    <div class="progress me-2" style="width: 100px; height: 8px;">
                                        <div class="progress-bar bg-success" style="width: {{ $salle->utilisation_percentage }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $salle->utilisation_percentage }}%</small>
                                </div>
                            </div>
                            <div class="hierarchy-children">
                                @foreach($salle->travees as $travee)
                                    <div class="hierarchy-item travee-item" data-type="travee">
                                        <div class="hierarchy-header" onclick="toggleHierarchyItem(this)">
                                            <i class="fas fa-chevron-down toggle-icon"></i>
                                            <i class="fas fa-layer-group text-success me-2"></i>
                                            <strong>{{ $travee->nom }}</strong>
                                            <span class="badge bg-success ms-2">{{ $travee->tablettes_count ?? $travee->tablettes->count() }} tablette(s)</span>
                                            <div class="ms-auto">
                                                <small class="text-muted">{{ $travee->positions_count ?? $travee->positions->count() }} positions</small>
                                            </div>
                                        </div>
                                        <div class="hierarchy-children">
                                            @foreach($travee->tablettes as $tablette)
                                                <div class="hierarchy-item tablette-item" data-type="tablette">
                                                    <div class="hierarchy-header" onclick="toggleHierarchyItem(this)">
                                                        <i class="fas fa-chevron-down toggle-icon"></i>
                                                        <i class="fas fa-table text-info me-2"></i>
                                                        <strong>{{ $tablette->nom }}</strong>
                                                        <span class="badge bg-info ms-2">{{ $tablette->positions_count ?? $tablette->positions->count() }} position(s)</span>
                                                        <div class="ms-auto">
                                                            <span class="badge bg-warning">{{ $tablette->positions_occupees ?? $tablette->positions->where('vide', false)->count() }} occupée(s)</span>
                                                        </div>
                                                    </div>
                                                    <div class="hierarchy-children">
                                                        @foreach($tablette->positions as $position)
                                                            <div class="hierarchy-item position-item" 
                                                                 data-type="position" 
                                                                 data-status="{{ $position->vide ? 'free' : 'occupied' }}">
                                                                <div class="hierarchy-header">
                                                                    <i class="fas fa-map-marker-alt {{ $position->vide ? 'text-warning' : 'text-success' }} me-2"></i>
                                                                    <strong>{{ $position->nom }}</strong>
                                                                    @if($position->vide)
                                                                        <span class="badge bg-warning ms-2">Libre</span>
                                                                    @else
                                                                        <span class="badge bg-success ms-2">Occupée</span>
                                                                        @if($position->boite)
                                                                            <span class="text-muted ms-2">
                                                                                Boîte: {{ $position->boite->numero }}
                                                                                ({{ $position->boite->nbr_dossiers }}/{{ $position->boite->capacite }})
                                                                            </span>
                                                                        @endif
                                                                    @endif
                                                                    <div class="ms-auto">
                                                                        <div class="btn-group btn-group-sm">
                                                                            @if($position->vide)
                                                                                <button class="btn btn-outline-success btn-sm" 
                                                                                        onclick="assignBox({{ $position->id }})"
                                                                                        title="Assigner une boîte">
                                                                                    <i class="fas fa-plus"></i>
                                                                                </button>
                                                                            @else
                                                                                <a href="{{ route('admin.boites.show', $position->boite) }}" 
                                                                                   class="btn btn-outline-info btn-sm"
                                                                                   title="Voir la boîte">
                                                                                    <i class="fas fa-eye"></i>
                                                                                </a>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($salles->count() == 0)
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-warehouse fa-3x mb-3"></i>
                        <h5>Aucune salle trouvée</h5>
                        <p>
                            @if($organisme)
                                Aucune salle n'est configurée pour {{ $organisme->nom_org }}.
                            @else
                                Aucune salle n'est configurée dans le système.
                            @endif
                        </p>
                        <a href="{{ route('admin.salles.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Créer la première salle
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal pour assigner une boîte -->
<div class="modal fade" id="assignBoxModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-box me-2"></i>
                    Assigner une Boîte
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignBoxForm">
                    <input type="hidden" id="positionId" name="position_id">
                    <div class="mb-3">
                        <label for="boxNumber" class="form-label">Numéro de boîte</label>
                        <input type="text" class="form-control" id="boxNumber" name="numero" required>
                    </div>
                    <div class="mb-3">
                        <label for="boxCapacity" class="form-label">Capacité</label>
                        <input type="number" class="form-control" id="boxCapacity" name="capacite" value="20" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="boxTheme" class="form-label">Code thématique (optionnel)</label>
                        <input type="text" class="form-control" id="boxTheme" name="code_thematique">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="submitAssignBox()">
                    <i class="fas fa-check me-2"></i>
                    Assigner
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour positions libres -->
<div class="modal fade" id="availablePositionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-circle me-2"></i>
                    Positions Libres
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th>Localisation</th>
                                <th>Organisme</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="availablePositionsTable">
                            <!-- Sera rempli par AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .modal {
        z-index: 1060 !important;
    }
    
    /* Pour le modal de fond */
    .modal-backdrop {
        z-index: 1050 !important;
    }
    .hierarchy-item {
        margin-left: 20px;
        border-left: 2px solid #dee2e6;
        position: relative;
    }

    .hierarchy-item.salle-item {
        margin-left: 0;
        border-left: none;
    }

    .hierarchy-header {
        padding: 10px 15px;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        margin: 5px 0;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
    }

    .hierarchy-header:hover {
        background-color: #e9ecef;
        transform: translateX(5px);
    }

    .hierarchy-children {
        display: block;
        padding-left: 20px;
        transition: all 0.3s ease;
    }

    .hierarchy-item.collapsed .hierarchy-children {
        display: none;
    }

    .hierarchy-item.collapsed .toggle-icon {
        transform: rotate(-90deg);
    }

    .toggle-icon {
        transition: transform 0.3s ease;
        width: 12px;
        margin-right: 8px;
        cursor: pointer;
    }

    .position-item .hierarchy-header {
        background-color: #fff;
        border: 1px solid #e3e6f0;
    }

    .position-item[data-status="occupied"] .hierarchy-header {
        background-color: #d4edda;
        border-color: #c3e6cb;
    }

    .position-item[data-status="free"] .hierarchy-header {
        background-color: #fff3cd;
        border-color: #ffeaa7;
    }

    .hierarchy-item[data-type="position"] {
        border-left-color: #28a745;
    }

    .hierarchy-item[data-type="tablette"] {
        border-left-color: #17a2b8;
    }

    .hierarchy-item[data-type="travee"] {
        border-left-color: #28a745;
    }

    .hierarchy-item[data-type="salle"] {
        border-left-color: #007bff;
    }

    .progress {
        background-color: #e9ecef;
    }

    .search-highlight {
        background-color: #fff3cd !important;
        border-color: #ffeaa7 !important;
    }

    @media (max-width: 768px) {
        .hierarchy-item {
            margin-left: 10px;
        }

        .hierarchy-children {
            padding-left: 10px;
        }

        .hierarchy-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .hierarchy-header .ms-auto {
            margin-left: 0 !important;
            margin-top: 10px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let currentOrganisme = {{ $organisme ? $organisme->id : 'null' }};

    // Toggle hiérarchie
    function toggleHierarchyItem(element) {
        const item = element.closest('.hierarchy-item');
        item.classList.toggle('collapsed');
        updateStats();
    }

    // Toggle toute la hiérarchie
    function toggleAllHierarchy(expand) {
        const items = document.querySelectorAll('.hierarchy-item');
        items.forEach(item => {
            if (expand) {
                item.classList.remove('collapsed');
            } else {
                item.classList.add('collapsed');
            }
        });
        updateStats();
    }

    // Filtrer par organisme
    function filterByOrganisme() {
    const organismeId = document.getElementById('organismeFilter').value;
    const currentUrl = new URL(window.location);
    
    // Nettoyer les anciens paramètres d'organisme
    currentUrl.searchParams.delete('organisme_id');
    currentUrl.searchParams.delete('organisme');
    
    if (organismeId) {
        currentUrl.searchParams.set('organisme_id', organismeId);
    }
    
    window.location.href = currentUrl.toString();
}
    // Filtrer par statut
      function filterByStatus() {
        const status = document.getElementById('statusFilter').value;
        const positions = document.querySelectorAll('.position-item');
        
        let hasVisibleItems = false;

        positions.forEach(position => {
            const positionStatus = position.getAttribute('data-status');
            position.style.display = 'none';
            
            if (status === '') {
                position.style.display = 'block';
                hasVisibleItems = true;
            } else if (status === 'available' && positionStatus === 'free') {
                position.style.display = 'block';
                hasVisibleItems = true;
            } else if (status === 'occupied' && positionStatus === 'occupied') {
                position.style.display = 'block';
                hasVisibleItems = true;
            } else if (status === 'full' && positionStatus === 'occupied') {
                const boiteInfo = position.querySelector('.text-muted');
                if (boiteInfo && boiteInfo.textContent.includes('100%')) {
                    position.style.display = 'block';
                    hasVisibleItems = true;
                }
            } else if (status === 'low' && positionStatus === 'occupied') {
                const boiteInfo = position.querySelector('.text-muted');
                if (boiteInfo && boiteInfo.textContent.includes('/') && !boiteInfo.textContent.includes('100%')) {
                    position.style.display = 'block';
                    hasVisibleItems = true;
                }
            }
        });

        // Gérer l'affichage des parents
        const allItems = document.querySelectorAll('.hierarchy-item');
        allItems.forEach(item => {
            if (item.dataset.type !== 'position') {
                item.style.display = 'block';
                const children = item.querySelectorAll('.hierarchy-item');
                let hasVisibleChildren = false;
                
                children.forEach(child => {
                    if (child.style.display !== 'none') {
                        hasVisibleChildren = true;
                    }
                });
                
                if (!hasVisibleChildren && status !== '') {
                    item.style.display = 'none';
                }
            }
        });

        // Afficher un message si aucun résultat
        document.getElementById('noResultsMessage').style.display = hasVisibleItems ? 'none' : 'block';
        updateStats();
    }

    // Fonction améliorée pour la recherche
    function searchInHierarchy() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const items = document.querySelectorAll('.hierarchy-header');
        let hasResults = false;
        
        items.forEach(header => {
            const item = header.closest('.hierarchy-item');
            const text = header.textContent.toLowerCase();
            const match = text.includes(searchTerm);
            
            item.classList.toggle('search-highlight', match);
            item.style.display = match ? 'block' : 'none';
            
            if (match) {
                hasResults = true;
                // Développer les parents
                let parent = item.parentElement.closest('.hierarchy-item');
                while (parent) {
                    parent.classList.remove('collapsed');
                    parent.style.display = 'block';
                    parent = parent.parentElement.closest('.hierarchy-item');
                }
            }
        });

        document.getElementById('noResultsMessage').style.display = hasResults ? 'none' : 'block';
        updateStats();
    }

    // Assigner une boîte
     function assignBox(positionId) {
        // Fermer le modal des positions libres s'il est ouvert
        const availableModal = bootstrap.Modal.getInstance(document.getElementById('availablePositionsModal'));
        if (availableModal) {
            availableModal.hide();
        }

        // Ouvrir le modal d'assignation
        document.getElementById('positionId').value = positionId;
        const nextBoxNumber = generateNextBoxNumber();
        document.getElementById('boxNumber').value = nextBoxNumber;
        
        const modal = new bootstrap.Modal(document.getElementById('assignBoxModal'));
        modal.show();
    }

    // Générer le prochain numéro de boîte
    function generateNextBoxNumber() {
        const existingNumbers = Array.from(document.querySelectorAll('.hierarchy-header'))
            .map(header => {
                const match = header.textContent.match(/Boîte: (BOX-\d+)/);
                return match ? parseInt(match[1].split('-')[1]) : 0;
            })
            .filter(num => num > 0);
        
        const maxNumber = existingNumbers.length > 0 ? Math.max(...existingNumbers) : 0;
        return `BOX-${String(maxNumber + 1).padStart(4, '0')}`;
    }

    // Soumettre l'assignation de boîte
    function submitAssignBox() {
        const form = document.getElementById('assignBoxForm');
        const formData = new FormData(form);
        
        fetch('{{ route("admin.boites.store") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur lors de l\'assignation de la boîte');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'assignation de la boîte');
        });
    }

    // Afficher les positions libres
    function showAvailablePositions() {
        const modal = new bootstrap.Modal(document.getElementById('availablePositionsModal'));
        modal.show();
        
        const organismeParam = currentOrganisme ? `?organisme_id=${currentOrganisme}` : '';
        
        fetch(`{{ route('admin.stockage.positions.available') }}${organismeParam}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('availablePositionsTable');
                tbody.innerHTML = '';
                
                if (data.length > 0) {
                    data.forEach(position => {
                        const row = `
                            <tr>
                                <td><strong>${position.nom}</strong></td>
                                <td><small class="text-muted">${position.full_path}</small></td>
                                <td><span class="badge bg-primary">${position.organisme}</span></td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="assignBox(${position.id})">
                                        <i class="fas fa-plus me-1"></i>Assigner
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Aucune position libre trouvée</td></tr>';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.getElementById('availablePositionsTable').innerHTML = 
                    '<tr><td colspan="4" class="text-center text-danger">Erreur lors du chargement</td></tr>';
            });
    }

    // Exporter la hiérarchie
    function exportHierarchy() {
        const organismeParam = currentOrganisme ? `?organisme_id=${currentOrganisme}` : '';
        window.location.href = `{{ route('admin.stockage.export') }}${organismeParam}`;
    }

    // Mettre à jour les statistiques
    function updateStats() {
        const visiblePositions = document.querySelectorAll('.position-item:not([style*="display: none"])');
        const occupiedPositions = Array.from(visiblePositions).filter(pos => 
            pos.getAttribute('data-status') === 'occupied'
        );
        const freePositions = Array.from(visiblePositions).filter(pos => 
            pos.getAttribute('data-status') === 'free'
        );
        
        document.getElementById('statsPositions').textContent = visiblePositions.length;
        document.getElementById('statsOccupied').textContent = occupiedPositions.length;
        document.getElementById('statsFree').textContent = freePositions.length;
    }

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        updateStats();
        
        // Auto-collapse des niveaux profonds pour une meilleure lisibilité
        const tablettes = document.querySelectorAll('.tablette-item');
        tablettes.forEach(tablette => {
            tablette.classList.add('collapsed');
        });
    });

    // Raccourcis clavier
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey) {
            switch(e.key) {
                case 'f':
                    e.preventDefault();
                    document.getElementById('searchInput').focus();
                    break;
                case 'e':
                    e.preventDefault();
                    toggleAllHierarchy(true);
                    break;
                case 'r':
                    e.preventDefault();
                    toggleAllHierarchy(false);
                    break;
            }
        }
    });
</script>
@endpush