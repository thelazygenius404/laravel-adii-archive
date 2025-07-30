{{-- resources/views/admin/stockage/index.blade.php --}}
@extends('layouts.admin')

@section('title', 'Tableau de Bord - Gestion des Espaces de Stockage')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-warehouse me-2"></i>
            Gestion des Espaces de Stockage
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stockage.hierarchy') }}" class="btn btn-primary">
                <i class="fas fa-sitemap me-2"></i>
                Vue Hiérarchique
            </a>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-cogs me-1"></i>
                    Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.stockage.optimize') }}">
                        <i class="fas fa-magic me-2"></i>Optimiser le stockage
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stockage.export') }}">
                        <i class="fas fa-download me-2"></i>Exporter rapport
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('admin.salles.create') }}">
                        <i class="fas fa-plus me-2"></i>Ajouter une salle
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques générales -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-home text-primary fa-3x mb-3"></i>
                <h3 class="text-primary">{{ $stats['total_salles'] }}</h3>
                <p class="text-muted mb-0">Salles Totales</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="fas fa-map-marker-alt text-info fa-3x mb-3"></i>
                <h3 class="text-info">{{ $stats['total_positions'] }}</h3>
                <p class="text-muted mb-0">Positions Totales</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                <h3 class="text-success">{{ $stats['positions_occupees'] }}</h3>
                <p class="text-muted mb-0">Positions Occupées</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-circle text-warning fa-3x mb-3"></i>
                <h3 class="text-warning">{{ $stats['positions_libres'] }}</h3>
                <p class="text-muted mb-0">Positions Libres</p>
            </div>
        </div>
    </div>
</div>

<!-- Barres de statistiques boîtes et dossiers -->
<div class="row mb-4">
    <div class="col-lg-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-box me-2"></i>
                    Statistiques des Boîtes
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h4 class="text-info">{{ $stats['total_boites'] }}</h4>
                        <small class="text-muted">Boîtes Actives</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-success">{{ $stats['total_dossiers'] }}</h4>
                        <small class="text-muted">Dossiers Stockés</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-warning">{{ $stats['dossiers_actifs'] }}</h4>
                        <small class="text-muted">Dossiers Actifs</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Alertes
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($stats['dossiers_due_elimination'] > 0)
                        <a href="{{ route('admin.dossiers.elimination') }}" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-trash me-2"></i>
                            {{ $stats['dossiers_due_elimination'] }} dossier(s) à éliminer
                        </a>
                    @endif
                    <button class="btn btn-outline-info btn-sm" onclick="showOptimizationSuggestions()">
                        <i class="fas fa-lightbulb me-2"></i>
                        Suggestions d'optimisation
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Utilisation par organisme -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Utilisation par Organisme
                </h5>
                <div class="btn-group btn-group-sm">
                    <input type="text" class="form-control form-control-sm" placeholder="Rechercher un organisme..." id="searchOrganisme">
                    <button class="btn btn-outline-primary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Organisme</th>
                                <th>Capacité Max</th>
                                <th>Capacité Actuelle</th>
                                <th>Utilisation</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($utilisationParOrganisme as $utilisation)
                                <tr>
                                    <td>
                                        <strong>{{ $utilisation['nom'] }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $utilisation['capacite_max'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $utilisation['capacite_actuelle'] }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="width: 200px;">
                                            <div class="progress-bar 
                                                @if($utilisation['utilisation_percentage'] < 50) bg-success
                                                @elseif($utilisation['utilisation_percentage'] < 80) bg-warning
                                                @else bg-danger @endif"
                                                style="width: {{ $utilisation['utilisation_percentage'] }}%">
                                                {{ number_format($utilisation['utilisation_percentage'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.stockage.hierarchy') }}?organisme={{ $utilisation['nom'] }}" 
                                               class="btn btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.stockage.statistics', ['organisme' => $utilisation['nom']]) }}" 
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-chart-line"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activités récentes -->
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Nouveaux Dossiers
                </h5>
            </div>
            <div class="card-body">
                @if($activitesRecentes['nouveaux_dossiers']->count() > 0)
                    @foreach($activitesRecentes['nouveaux_dossiers'] as $dossier)
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-3">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="fas fa-file-alt" style="font-size: 0.8rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $dossier->numero }}</div>
                                <small class="text-muted">{{ $dossier->titre }}</small>
                            </div>
                            <small class="text-muted">{{ $dossier->created_at->diffForHumans() }}</small>
                        </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.dossiers.index') }}" class="btn btn-sm btn-outline-primary">
                            Voir tous les dossiers
                        </a>
                    </div>
                @else
                    <p class="text-muted text-center">Aucun nouveau dossier</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-box me-2"></i>
                    Boîtes Pleines
                </h5>
            </div>
            <div class="card-body">
                @if($activitesRecentes['boites_pleines']->count() > 0)
                    @foreach($activitesRecentes['boites_pleines'] as $boite)
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-3">
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="fas fa-box" style="font-size: 0.8rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $boite->numero }}</div>
                                <small class="text-muted">{{ $boite->full_location }}</small>
                            </div>
                            <span class="badge bg-warning">{{ $boite->utilisation_percentage }}%</span>
                        </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.boites.index') }}?status=full" class="btn btn-sm btn-outline-warning">
                            Voir toutes les boîtes
                        </a>
                    </div>
                @else
                    <p class="text-muted text-center">Aucune boîte pleine</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-trash me-2"></i>
                    Dossiers à Éliminer
                </h5>
            </div>
            <div class="card-body">
                @if($activitesRecentes['dossiers_elimination']->count() > 0)
                    @foreach($activitesRecentes['dossiers_elimination'] as $dossier)
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-3">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="fas fa-exclamation-triangle" style="font-size: 0.8rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $dossier->numero }}</div>
                                <small class="text-muted">
                                    Échéance: {{ $dossier->date_elimination_prevue?->format('d/m/Y') }}
                                </small>
                            </div>
                            <span class="badge bg-danger">{{ $dossier->days_until_elimination }} j</span>
                        </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.dossiers.elimination') }}" class="btn btn-sm btn-outline-danger">
                            Gérer les éliminations
                        </a>
                    </div>
                @else
                    <p class="text-muted text-center">Aucun dossier en attente</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les suggestions d'optimisation -->
<div class="modal fade" id="optimizationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-lightbulb me-2"></i>
                    Suggestions d'Optimisation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="optimizationContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-2">Analyse en cours...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a href="{{ route('admin.stockage.optimize') }}" class="btn btn-primary">
                    <i class="fas fa-magic me-2"></i>
                    Voir le rapport complet
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Recherche dans le tableau des organismes
    document.getElementById('searchOrganisme').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('tbody tr');
        
        tableRows.forEach(row => {
            const organismeCell = row.querySelector('td:first-child strong');
            if (organismeCell) {
                const organismeName = organismeCell.textContent.toLowerCase();
                row.style.display = organismeName.includes(searchTerm) ? '' : 'none';
            }
        });
    });

    // Fonction pour afficher les suggestions d'optimisation
    function showOptimizationSuggestions() {
        const modal = new bootstrap.Modal(document.getElementById('optimizationModal'));
        modal.show();
        
        // Charger les suggestions via AJAX
        fetch('{{ route("admin.stockage.optimize") }}?ajax=1')
            .then(response => response.json())
            .then(data => {
                const content = document.getElementById('optimizationContent');
                
                if (data.optimisations && data.optimisations.length > 0) {
                    let html = '<div class="list-group">';
                    
                    data.optimisations.slice(0, 5).forEach(optimisation => {
                        html += `
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">${optimisation.boite}</h6>
                                    <small class="text-muted">${optimisation.taux_occupation}%</small>
                                </div>
                                <p class="mb-1"><small class="text-muted">${optimisation.localisation}</small></p>
                                <div class="d-flex gap-1">
                                    ${optimisation.suggestions.map(suggestion => 
                                        `<span class="badge bg-light text-dark">${suggestion}</span>`
                                    ).join('')}
                                </div>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                    
                    if (data.optimisations.length > 5) {
                        html += `<p class="text-center mt-3 text-muted">Et ${data.optimisations.length - 5} autre(s) suggestion(s)...</p>`;
                    }
                    
                    content.innerHTML = html;
                } else {
                    content.innerHTML = '<div class="text-center text-success"><i class="fas fa-check-circle fa-3x mb-3"></i><p>Aucune optimisation nécessaire. Votre stockage est bien organisé !</p></div>';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                document.getElementById('optimizationContent').innerHTML = '<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><p>Erreur lors du chargement des suggestions.</p></div>';
            });
    }

    // Auto-refresh des statistiques toutes les 5 minutes
    setInterval(function() {
        // Vous pouvez implémenter un refresh automatique des stats ici
        console.log('Auto-refresh statistiques');
    }, 300000); // 5 minutes
</script>
@endpush

@push('styles')
<style>
    .progress {
        height: 8px;
    }
    
    .avatar {
        flex-shrink: 0;
    }
    
    .card-body .list-group-item {
        border: none;
        padding: 0.5rem 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    .card-body .list-group-item:last-child {
        border-bottom: none;
    }
    
    .spinner-border {
        width: 3rem;
        height: 3rem;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-search me-2"></i>
            Recherche dans les Espaces de Stockage
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stockage.index') }}" class="btn btn-outline-secondary">
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
        window.location.href = '{{ route("admin.stockage.search") }}';
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
        window.location.href = '{{ route("admin.stockage.search") }}?' + params.toString();
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