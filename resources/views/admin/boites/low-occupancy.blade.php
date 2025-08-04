@extends('layouts.admin')

@section('title', 'Boîtes Peu Occupées')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title">
                <i class="fas fa-chart-pie me-2"></i>
                Boîtes Peu Occupées
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.boites.index') }}">Boîtes</a></li>
                    <li class="breadcrumb-item active">Boîtes peu occupées</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.boites.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
            <button type="button" class="btn btn-outline-primary" onclick="exportLowOccupancy()">
                <i class="fas fa-download me-2"></i>
                Exporter
            </button>
        </div>
    </div>
</div>

<!-- Filtres pour le seuil d'occupation -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-3">
                        <label for="threshold" class="form-label">Seuil d'occupation (%)</label>
                        <input type="number" 
                               class="form-control" 
                               id="threshold" 
                               name="threshold" 
                               value="{{ request('threshold', 50) }}" 
                               min="1" 
                               max="99"
                               placeholder="50">
                    </div>
                    <div class="col-md-3">
                        <label for="organisme_id" class="form-label">Organisme</label>
                        <select class="form-select" id="organisme_id" name="organisme_id">
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
                    </div>
                    <div class="col-md-2">
                        <label for="per_page" class="form-label">Par page</label>
                        <select class="form-select" id="per_page" name="per_page">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                            <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>
                            Analyser
                        </button>
                        <a href="{{ route('admin.boites.low-occupancy') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques d'analyse -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                <h3 class="text-warning">{{ $boites->total() }}</h3>
                <p class="text-muted mb-0">Boîtes sous le seuil</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="fas fa-percentage text-info fa-3x mb-3"></i>
                <h3 class="text-info">{{ request('threshold', 50) }}%</h3>
                <p class="text-muted mb-0">Seuil d'occupation</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-arrows-alt text-success fa-3x mb-3"></i>
                <h3 class="text-success">{{ $boites->sum(function($b) { return $b->capacite_restante; }) }}</h3>
                <p class="text-muted mb-0">Places disponibles</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-compress-arrows-alt text-primary fa-3x mb-3"></i>
                <h3 class="text-primary">{{ $boites->count() > 0 ? number_format($boites->avg('utilisation_percentage'), 1) : 0 }}%</h3>
                <p class="text-muted mb-0">Occupation moyenne</p>
            </div>
        </div>
    </div>
</div>

<!-- Liste des boîtes peu occupées -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>
                    Boîtes avec moins de {{ request('threshold', 50) }}% d'occupation
                    <span class="badge bg-warning ms-2">{{ $boites->total() }}</span>
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
                                    <th>Occupation</th>
                                    <th>Places disponibles</th>
                                    <th>Potentiel d'optimisation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($boites as $boite)
                                    <tr class="{{ $boite->detruite ? 'table-secondary' : '' }}">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <div class="boite-icon bg-warning text-white rounded">
                                                        <i class="fas fa-archive"></i>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $boite->numero }}</h6>
                                                    <small class="text-muted">
                                                        @if($boite->code_thematique)
                                                            <span class="badge bg-info me-1">{{ $boite->code_thematique }}</span>
                                                        @endif
                                                        @if($boite->code_topo)
                                                            <span class="badge bg-secondary">{{ $boite->code_topo }}</span>
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($boite->position)
                                                <div class="d-flex flex-column">
                                                    <span class="fw-bold">{{ $boite->position->nom }}</span>
                                                    <small class="text-muted">
                                                        {{ $boite->position->tablette->travee->nom }} - 
                                                        {{ $boite->position->tablette->travee->salle->nom }}
                                                    </small>
                                                    <small class="text-muted">
                                                        {{ $boite->position->tablette->travee->salle->organisme->nom_org }}
                                                    </small>
                                                </div>
                                            @else
                                                <span class="text-muted">Non localisée</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <span class="fw-bold">{{ $boite->nbr_dossiers }}/{{ $boite->capacite }}</span>
                                                </div>
                                                <div class="progress me-2" style="width: 80px; height: 10px;">
                                                    <div class="progress-bar bg-warning" 
                                                         style="width: {{ $boite->utilisation_percentage }}%"></div>
                                                </div>
                                                <small class="text-warning fw-bold">{{ number_format($boite->utilisation_percentage, 1) }}%</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-success fs-6">
                                                {{ $boite->capacite_restante }} places
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $potentiel = 100 - $boite->utilisation_percentage;
                                            @endphp
                                            <div class="d-flex align-items-center">
                                                @if($potentiel > 70)
                                                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                                    <span class="text-danger fw-bold">Très élevé ({{ number_format($potentiel, 1) }}%)</span>
                                                @elseif($potentiel > 50)
                                                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                                    <span class="text-warning fw-bold">Élevé ({{ number_format($potentiel, 1) }}%)</span>
                                                @else
                                                    <i class="fas fa-info-circle text-info me-2"></i>
                                                    <span class="text-info">Modéré ({{ number_format($potentiel, 1) }}%)</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.boites.show', $boite) }}" 
                                                   class="btn btn-outline-info" title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.boites.edit', $boite) }}" 
                                                   class="btn btn-outline-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-outline-warning" 
                                                        onclick="showOptimizationSuggestions({{ $boite->id }}, '{{ $boite->numero }}', {{ $boite->utilisation_percentage }}, {{ $boite->capacite_restante }})" 
                                                        title="Suggestions d'optimisation">
                                                    <i class="fas fa-lightbulb"></i>
                                                </button>
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
                            {{ $boites->appends(request()->query())->links() }}
                        </div>
                    @endif

                    <!-- Suggestions d'optimisation globales -->
                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-lightbulb me-2"></i>Suggestions d'optimisation globales :</h6>
                        <ul class="mb-0">
                            <li>Considérer le regroupement des dossiers dans les boîtes les plus pleines</li>
                            <li>Évaluer la possibilité de fusionner certaines boîtes peu occupées</li>
                            <li>Vérifier si certaines boîtes peuvent être supprimées ou réaffectées</li>
                            <li>Analyser les tendances d'utilisation pour optimiser l'espace de stockage</li>
                            <li>{{ $boites->sum(function($b) { return $b->capacite_restante; }) }} places pourraient être libérées par consolidation</li>
                        </ul>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-success">Excellente optimisation !</h5>
                        <p class="text-muted">
                            Aucune boîte n'a une occupation inférieure à {{ request('threshold', 50) }}%.
                            <br>Votre espace de stockage est bien optimisé.
                        </p>
                        <a href="{{ route('admin.boites.index') }}" class="btn btn-primary">
                            <i class="fas fa-archive me-2"></i>
                            Voir toutes les boîtes
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal des suggestions d'optimisation -->
<div class="modal fade" id="optimizationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-lightbulb me-2"></i>
                    Suggestions d'optimisation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="optimizationContent">
                <!-- Contenu dynamique -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="applyOptimization()">
                    <i class="fas fa-magic me-2"></i>
                    Appliquer les suggestions
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function exportLowOccupancy() {
        const params = new URLSearchParams(window.location.search);
        params.set('export', '1');
        window.location.href = `{{ route('admin.boites.low-occupancy') }}?${params.toString()}`;
    }

    function showOptimizationSuggestions(boiteId, boiteNumero, utilisation, placesLibres) {
        const modal = new bootstrap.Modal(document.getElementById('optimizationModal'));
        
        // Contenu des suggestions adapté aux données de la boîte
        const content = `
            <div class="alert alert-warning">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Analyse de la boîte ${boiteNumero}</h6>
                <p>Cette boîte présente un taux d'occupation de ${utilisation}% avec ${placesLibres} places disponibles.</p>
            </div>
            
            <h6>Actions recommandées :</h6>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex align-items-center">
                    <i class="fas fa-arrows-alt text-primary me-3"></i>
                    <div>
                        <strong>Regroupement</strong><br>
                        <small class="text-muted">Déplacer les dossiers vers une boîte plus pleine de la même zone</small>
                    </div>
                </li>
                <li class="list-group-item d-flex align-items-center">
                    <i class="fas fa-compress text-warning me-3"></i>
                    <div>
                        <strong>Compactage</strong><br>
                        <small class="text-muted">Réorganiser les dossiers pour libérer de l'espace continu</small>
                    </div>
                </li>
                <li class="list-group-item d-flex align-items-center">
                    <i class="fas fa-recycle text-success me-3"></i>
                    <div>
                        <strong>Réaffectation</strong><br>
                        <small class="text-muted">Utiliser cette boîte pour de nouveaux dossiers prioritaires</small>
                    </div>
                </li>
                ${utilisation < 20 ? `
                <li class="list-group-item d-flex align-items-center">
                    <i class="fas fa-trash-alt text-danger me-3"></i>
                    <div>
                        <strong>Consolidation</strong><br>
                        <small class="text-muted">Envisager la fusion avec une autre boîte peu occupée</small>
                    </div>
                </li>
                ` : ''}
            </ul>
            
            <div class="mt-3">
                <h6>Impact estimé :</h6>
                <p class="text-muted">
                    En optimisant cette boîte, vous pourriez libérer jusqu'à ${placesLibres} emplacements 
                    et améliorer le taux d'occupation global de votre système de stockage.
                </p>
            </div>
        `;
        
        document.getElementById('optimizationContent').innerHTML = content;
        modal.show();
    }

    function applyOptimization() {
        alert('Fonctionnalité d\'optimisation automatique à implémenter selon vos besoins spécifiques. Cette action pourrait inclure la suggestion de positions alternatives ou la création de tâches d\'optimisation.');
        // Ici vous pouvez implémenter la logique d'optimisation automatique
        // Par exemple: redirection vers une page de gestion des déplacements
        // window.location.href = '/admin/boites/optimize-suggestions';
    }

    // Validation du seuil
    document.addEventListener('DOMContentLoaded', function() {
        const thresholdInput = document.getElementById('threshold');
        if (thresholdInput) {
            thresholdInput.addEventListener('input', function() {
                const value = parseInt(this.value);
                if (value < 1 || value > 99) {
                    this.setCustomValidity('Le seuil doit être entre 1 et 99%');
                } else {
                    this.setCustomValidity('');
                }
            });
        }
    });
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

    .progress {
        background-color: #e9ecef;
    }

    .table-secondary {
        opacity: 0.7;
    }

    .card-body .text-center h3 {
        font-size: 2rem;
        font-weight: 700;
    }

    .list-group-item {
        border: none;
        padding: 1rem 0;
    }

    .list-group-item:not(:last-child) {
        border-bottom: 1px solid #dee2e6 !important;
    }

    .badge.fs-6 {
        font-size: 0.875rem !important;
    }
</style>
@endpush