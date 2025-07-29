{{-- resources/views/admin/stockage/search.blade.php --}}
@extends('layouts.admin')

@section('title', 'Recherche dans les Espaces de Stockage')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-search me-2"></i>
            Recherche dans les Espaces de Stockage
        </h1>
        <div class="btn-group">
            <a href="{{ route('stockage.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour au tableau de bord
            </a>
            <button class="btn btn-primary" onclick="clearSearch()">
                <i class="fas fa-times me-2"></i>
                Effacer
            </button>
        </div>
    </div>
</div>

<!-- Formulaire de recherche avancée -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-filter me-2"></i>
                    Critères de Recherche
                </h5>
            </div>
            <div class="card-body">
                <form id="searchForm" method="GET">
                    <div class="row">
                        <!-- Recherche générale -->
                        <div class="col-md-6 mb-3">
                            <label for="q" class="form-label">Recherche générale</label>
                            <input type="text" class="form-control" id="q" name="q" 
                                   value="{{ request('q') }}" 
                                   placeholder="Numéro de boîte, dossier, localisation...">
                        </div>

                        <!-- Type d'élément -->
                        <div class="col-md-3 mb-3">
                            <label for="type" class="form-label">Type d'élément</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">Tous les types</option>
                                <option value="dossier" {{ request('type') == 'dossier' ? 'selected' : '' }}>Dossiers</option>
                                <option value="boite" {{ request('type') == 'boite' ? 'selected' : '' }}>Boîtes</option>
                                <option value="position" {{ request('type') == 'position' ? 'selected' : '' }}>Positions</option>
                                <option value="salle" {{ request('type') == 'salle' ? 'selected' : '' }}>Salles</option>
                            </select>
                        </div>

                        <!-- Organisme -->
                        <div class="col-md-3 mb-3">
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

                        <!-- Salle -->
                        <div class="col-md-3 mb-3">
                            <label for="salle" class="form-label">Salle</label>
                            <select class="form-select" id="salle" name="salle">
                                <option value="">Toutes les salles</option>
                                @foreach($salles as $salle)
                                    <option value="{{ $salle->id }}" {{ request('salle') == $salle->id ? 'selected' : '' }}>
                                        {{ $salle->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Statut -->
                        <div class="col-md-3 mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Tous les statuts</option>
                                <option value="libre" {{ request('status') == 'libre' ? 'selected' : '' }}>Libre</option>
                                <option value="occupe" {{ request('status') == 'occupe' ? 'selected' : '' }}>Occupé</option>
                                <option value="plein" {{ request('status') == 'plein' ? 'selected' : '' }}>Plein</option>
                                <option value="actif" {{ request('status') == 'actif' ? 'selected' : '' }}>Actif</option>
                                <option value="archive" {{ request('status') == 'archive' ? 'selected' : '' }}>Archivé</option>
                            </select>
                        </div>

                        <!-- Date de création -->
                        <div class="col-md-3 mb-3">
                            <label for="date_from" class="form-label">Créé après le</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="{{ request('date_from') }}">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="date_to" class="form-label">Créé avant le</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" 
                                   value="{{ request('date_to') }}">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>
                            Rechercher
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="clearSearch()">
                            <i class="fas fa-times me-2"></i>
                            Effacer
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="exportResults()">
                            <i class="fas fa-download me-2"></i>
                            Exporter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Résultats de recherche -->
@if(request()->hasAny(['q', 'type', 'organisme', 'salle', 'status', 'date_from', 'date_to']))
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>
                        Résultats de Recherche
                        @if(isset($results))
                            <span class="badge bg-primary ms-2">{{ $results->total() }} résultat(s)</span>
                        @endif
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" onclick="toggleView('grid')">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="btn btn-outline-primary active" onclick="toggleView('list')">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($results) && $results->count() > 0)
                        <div id="listView">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Identifiant</th>
                                            <th>Titre/Description</th>
                                            <th>Localisation</th>
                                            <th>Organisme</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($results as $result)
                                            <tr>
                                                <td>
                                                    @switch($result->type)
                                                        @case('dossier')
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-file-alt me-1"></i>Dossier
                                                            </span>
                                                            @break
                                                        @case('boite')
                                                            <span class="badge bg-primary">
                                                                <i class="fas fa-box me-1"></i>Boîte
                                                            </span>
                                                            @break
                                                        @case('position')
                                                            <span class="badge bg-info">
                                                                <i class="fas fa-map-marker-alt me-1"></i>Position
                                                            </span>
                                                            @break
                                                        @case('salle')
                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-home me-1"></i>Salle
                                                            </span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <strong>{{ $result->identifiant }}</strong>
                                                </td>
                                                <td>
                                                    {{ $result->titre }}
                                                    @if($result->description)
                                                        <br><small class="text-muted">{{ Str::limit($result->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $result->localisation }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $result->organisme }}</span>
                                                </td>
                                                <td>
                                                    @if($result->statut == 'libre')
                                                        <span class="badge bg-warning">Libre</span>
                                                    @elseif($result->statut == 'occupe')
                                                        <span class="badge bg-success">Occupé</span>
                                                    @elseif($result->statut == 'plein')
                                                        <span class="badge bg-danger">Plein</span>
                                                    @elseif($result->statut == 'actif')
                                                        <span class="badge bg-primary">Actif</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $result->statut }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ $result->url }}" class="btn btn-outline-info" title="Voir">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($result->edit_url)
                                                            <a href="{{ $result->edit_url }}" class="btn btn-outline-primary" title="Modifier">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="gridView" style="display: none;">
                            <div class="row">
                                @foreach($results as $result)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center mb-2">
                                                    @switch($result->type)
                                                        @case('dossier')
                                                            <i class="fas fa-file-alt text-success fa-2x me-3"></i>
                                                            @break
                                                        @case('boite')
                                                            <i class="fas fa-box text-primary fa-2x me-3"></i>
                                                            @break
                                                        @case('position')
                                                            <i class="fas fa-map-marker-alt text-info fa-2x me-3"></i>
                                                            @break
                                                        @case('salle')
                                                            <i class="fas fa-home text-warning fa-2x me-3"></i>
                                                            @break
                                                    @endswitch
                                                    <div>
                                                        <h6 class="card-title mb-1">{{ $result->identifiant }}</h6>
                                                        <small class="text-muted">{{ ucfirst($result->type) }}</small>
                                                    </div>
                                                </div>
                                                <p class="card-text">{{ Str::limit($result->titre, 60) }}</p>
                                                <div class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-map-marker-alt me-1"></i>
                                                        {{ $result->localisation }}
                                                    </small>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-secondary">{{ $result->organisme }}</span>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ $result->url }}" class="btn btn-outline-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($result->edit_url)
                                                            <a href="{{ $result->edit_url }}" class="btn btn-outline-primary">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if($results->hasPages())
                            <div class="d-flex justify-content-center">
                                {{ $results->appends(request()->query())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun résultat trouvé</h5>
                            <p class="text-muted">Essayez de modifier vos critères de recherche.</p>
                            <button class="btn btn-primary" onclick="clearSearch()">
                                <i class="fas fa-times me-2"></i>
                                Effacer les filtres
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Guide de recherche -->
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-search fa-4x text-primary mb-4"></i>
                    <h4>Recherche Avancée</h4>
                    <p class="text-muted mb-4">
                        Utilisez les critères ci-dessus pour rechercher dans tous vos espaces de stockage.
                        Vous pouvez combiner plusieurs critères pour affiner vos résultats.
                    </p>
                    
                    <div class="row text-start">
                        <div class="col-md-6 mb-3">
                            <h6><i class="fas fa-lightbulb text-warning me-2"></i>Conseils de recherche</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Utilisez des mots-clés simples</li>
                                <li><i class="fas fa-check text-success me-2"></i>Combinez plusieurs critères</li>
                                <li><i class="fas fa-check text-success me-2"></i>Utilisez les filtres par date</li>
                            </ul>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6><i class="fas fa-examples text-info me-2"></i>Exemples de recherche</h6>
                            <ul class="list-unstyled">
                                <li><code>BOX-0001</code> - Recherche par numéro</li>
                                <li><code>Contrat</code> - Recherche par type</li>
                                <li><code>Salle A</code> - Recherche par localisation</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    // Effacer la recherche
    function clearSearch() {
        document.getElementById('searchForm').reset();
        window.location.href = '{{ route("stockage.search") }}';
    }

    // Basculer entre vue liste et grille
    function toggleView(view) {
        const listView = document.getElementById('listView');
        const gridView = document.getElementById('gridView');
        const buttons = document.querySelectorAll('[onclick*="toggleView"]');
        
        buttons.forEach(btn => btn.classList.remove('active'));
        
        if (view === 'grid') {
            listView.style.display = 'none';
            gridView.style.display = 'block';
            document.querySelector('[onclick="toggleView(\'grid\')"]').classList.add('active');
        } else {
            listView.style.display = 'block';
            gridView.style.display = 'none';
            document.querySelector('[onclick="toggleView(\'list\')"]').classList.add('active');
        }
    }

    // Exporter les résultats
    function exportResults() {
        const params = new URLSearchParams(window.location.search);
        params.set('export', '1');
        window.location.href = '{{ route("stockage.search") }}?' + params.toString();
    }

    // Filtrage dynamique des salles par organisme
    document.getElementById('organisme').addEventListener('change', function() {
        const organismeId = this.value;
        const salleSelect = document.getElementById('salle');
        
        // Réinitialiser les options de salle
        salleSelect.innerHTML = '<option value="">Toutes les salles</option>';
        
        if (organismeId) {
            fetch(`{{ route('salles.by-organisme', '') }}/${organismeId}`)
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

    // Recherche en temps réel
    let searchTimeout;
    document.getElementById('q').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 3 || this.value.length === 0) {
                // Auto-recherche après 500ms de pause dans la saisie
                // Vous pouvez implémenter une recherche AJAX ici
            }
        }, 500);
    });

    // Raccourcis clavier
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            document.getElementById('q').focus();
        }
        if (e.key === 'Escape') {
            clearSearch();
        }
    });
</script>
@endpush

@push('styles')
<style>
    .card-body .fas.fa-4x {
        opacity: 0.3;
    }
    
    .btn-group .btn.active {
        background-color: var(--bs-primary);
        color: white;
        border-color: var(--bs-primary);
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
    
    .card .card-title {
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .list-unstyled li {
        padding: 0.25rem 0;
    }
    
    code {
        background-color: #f8f9fa;
        padding: 0.125rem 0.25rem;
        border-radius: 0.25rem;
        font-size: 0.875em;
    }
</style>
@endpush