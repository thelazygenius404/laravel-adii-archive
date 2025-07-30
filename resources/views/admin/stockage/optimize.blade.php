{{-- resources/views/admin/stockage/optimize.blade.php --}}
@extends('layouts.admin')

@section('title', 'Optimisation du Stockage')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-magic me-2"></i>
            Optimisation du Stockage
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stockage.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour au tableau de bord
            </a>
            <button class="btn btn-primary" onclick="runOptimization()">
                <i class="fas fa-sync me-2"></i>
                Réanalyser
            </button>
            <button class="btn btn-success" onclick="exportReport()">
                <i class="fas fa-download me-2"></i>
                Exporter le rapport
            </button>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" id="filterForm">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label for="organisme_id" class="form-label">Filtrer par organisme</label>
                            <select name="organisme_id" id="organisme_id" class="form-select" onchange="submitFilter()">
                                <option value="">Tous les organismes</option>
                                @foreach($organismes as $org)
                                    <option value="{{ $org->id }}" {{ $organismeSelectionne && $organismeSelectionne->id == $org->id ? 'selected' : '' }}>
                                        {{ $org->nom_org }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            @if($organismeSelectionne)
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Analyse pour: <strong>{{ $organismeSelectionne->nom_org }}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Résumé de l'optimisation -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="fas fa-box text-info fa-3x mb-3"></i>
                <h3 class="text-info">{{ $stats['total_boites_analysees'] }}</h3>
                <p class="text-muted mb-0">Boîtes analysées</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-compress-arrows-alt text-warning fa-3x mb-3"></i>
                <h3 class="text-warning">{{ $stats['positions_potentiellement_liberables'] }}</h3>
                <p class="text-muted mb-0">Positions libérables</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-cube text-success fa-3x mb-3"></i>
                <h3 class="text-success">{{ $stats['espace_total_recuperable'] }}</h3>
                <p class="text-muted mb-0">Espace récupérable</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-percentage text-primary fa-3x mb-3"></i>
                <h3 class="text-primary">{{ $stats['taux_optimisation_possible'] }}%</h3>
                <p class="text-muted mb-0">Taux d'optimisation</p>
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Actions Rapides d'Optimisation
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="d-grid">
                            <button class="btn btn-outline-primary" onclick="consolidateBoxes()">
                                <i class="fas fa-compress-alt me-2"></i>
                                Consolider les boîtes
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-grid">
                            <button class="btn btn-outline-info" onclick="redistributeFiles()">
                                <i class="fas fa-random me-2"></i>
                                Redistribuer les dossiers
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-grid">
                            <button class="btn btn-outline-success" onclick="optimizeLocations()">
                                <i class="fas fa-map-marked-alt me-2"></i>
                                Optimiser les emplacements
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-grid">
                            <button class="btn btn-outline-warning" onclick="scheduleElimination()">
                                <i class="fas fa-trash me-2"></i>
                                Planifier éliminations
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Boîtes optimisables -->
@if(!empty($positionsOptimisables) && count($positionsOptimisables) > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    Boîtes Optimisables ({{ count($positionsOptimisables) }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Boîte</th>
                                <th>Localisation</th>
                                <th>Utilisation</th>
                                <th>Suggestions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($positionsOptimisables as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item['boite']['numero'] }}</strong>
                                        <br><small class="text-muted">{{ $item['boite']['code_thematique'] }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $item['localisation'] }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 100px; height: 8px;">
                                                <div class="progress-bar bg-{{ $item['taux_occupation'] < 30 ? 'danger' : ($item['taux_occupation'] < 50 ? 'warning' : 'success') }}" 
                                                     style="width: {{ $item['taux_occupation'] }}%"></div>
                                            </div>
                                            <small>{{ number_format($item['taux_occupation'], 1) }}%</small>
                                        </div>
                                        <small class="text-muted">{{ $item['occupation'] }}/{{ $item['capacite'] }} dossiers</small>
                                    </td>
                                    <td>
                                        @if(!empty($item['suggestions']))
                                            @foreach($item['suggestions'] as $suggestion)
                                                <span class="badge bg-light text-dark me-1 mb-1">{{ $suggestion }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">Aucune suggestion</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.boites.show', $item['boite']['id']) }}" class="btn btn-outline-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-outline-warning" onclick="optimizeBox({{ $item['boite']['id'] }})" title="Optimiser">
                                                <i class="fas fa-magic"></i>
                                            </button>
                                            @if($item['taux_occupation'] < 50 && $item['occupation'] > 0)
                                                <button class="btn btn-outline-success" onclick="consolidateBox({{ $item['boite']['id'] }})" title="Consolider">
                                                    <i class="fas fa-compress-alt"></i>
                                                </button>
                                            @endif
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
@else
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-success">
            <div class="card-body text-center py-5">
                <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                <h5 class="text-success">Excellent !</h5>
                <p class="text-muted">
                    @if(isset($organismeSelectionne) && $organismeSelectionne)
                        Le système de stockage de <strong>{{ $organismeSelectionne->nom_org }}</strong> est déjà bien optimisé.
                    @else
                        Votre système de stockage est déjà bien optimisé.
                    @endif
                    Aucune optimisation n'est nécessaire pour le moment.
                </p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Analyse détaillée par catégorie -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Analyse Détaillée par Catégorie d'Utilisation
                </h5>
            </div>
            <div class="card-body">
                @if($positionsOptimisables && $positionsOptimisables->count() > 0)
                    @php
                        $categories = [
                            'vides' => $positionsOptimisables->filter(function($item) { return $item['occupation'] == 0; }),
                            'tres_faible' => $positionsOptimisables->filter(function($item) { return $item['taux_occupation'] > 0 && $item['taux_occupation'] < 30; }),
                            'faible' => $positionsOptimisables->filter(function($item) { return $item['taux_occupation'] >= 30 && $item['taux_occupation'] < 50; }),
                        ];
                    @endphp

                    <div class="accordion" id="categoriesAccordion">
                        @foreach($categories as $categoryKey => $categoryItems)
                            @if($categoryItems->count() > 0)
                                @php
                                    $categoryInfo = [
                                        'vides' => ['title' => 'Boîtes Vides', 'icon' => 'inbox', 'color' => 'danger'],
                                        'tres_faible' => ['title' => 'Utilisation Très Faible (< 30%)', 'icon' => 'exclamation-triangle', 'color' => 'warning'],
                                        'faible' => ['title' => 'Utilisation Faible (30-50%)', 'icon' => 'info-circle', 'color' => 'info']
                                    ][$categoryKey];
                                @endphp
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $categoryKey }}">
                                        <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapse{{ $categoryKey }}">
                                            <i class="fas fa-{{ $categoryInfo['icon'] }} me-2"></i>
                                            {{ $categoryInfo['title'] }}
                                            <span class="badge bg-{{ $categoryInfo['color'] }} ms-2">{{ $categoryItems->count() }}</span>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $categoryKey }}" 
                                         class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                         data-bs-parent="#categoriesAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                @foreach($categoryItems as $item)
                                                    @php $boite = $item['boite']; @endphp
                                                    <div class="col-md-6 mb-3">
                                                    <div class="card border-{{ $categoryInfo['color'] }}">
                                                        <div class="card-body">
                                                            <h6 class="card-title">{{ $item['boite']['numero'] }}</h6> {{-- CORRECTION --}}
                                                            <p class="card-text small text-muted">{{ $item['localisation'] }}</p>
                                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                                <span class="badge bg-{{ $categoryInfo['color'] }}">
                                                                    {{ number_format($item['taux_occupation'], 1) }}%
                                                                </span>
                                                                <small class="text-muted">{{ $item['occupation'] }}/{{ $item['capacite'] }}</small>
                                                            </div>
                                                            @foreach($item['suggestions'] as $suggestion)
                                                                <small class="d-block text-muted">
                                                                    <i class="fas fa-arrow-right me-1"></i>{{ $suggestion }}
                                                                </small>
                                                            @endforeach
                                                            <div class="mt-2">
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="{{ route('admin.boites.show', $item['boite']['id']) }}" class="btn btn-outline-{{ $categoryInfo['color'] }}">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    <button class="btn btn-outline-{{ $categoryInfo['color'] }}" onclick="optimizeBox({{ $item['boite']['id'] }})">
                                                                        <i class="fas fa-magic"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                        <h5 class="text-success">Parfait !</h5>
                        <p class="text-muted">Toutes les boîtes sont utilisées de manière optimale.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistiques d'optimisation -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Répartition de l'efficacité
                </h6>
            </div>
            <div class="card-body">
                <canvas id="utilizationChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Recommandations générales -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    Recommandations Générales
                </h6>
            </div>
            <div class="card-body">
                @if($stats['total_boites_analysees'] > 0)
                    @if($stats['taux_optimisation_possible'] > 50)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Optimisation recommandée</strong><br>
                            Plus de 50% des boîtes peuvent être optimisées.
                        </div>
                    @elseif($stats['taux_optimisation_possible'] > 20)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Améliorations possibles</strong><br>
                            Quelques optimisations peuvent être effectuées.
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Bon niveau d'optimisation</strong><br>
                            Le système fonctionne efficacement.
                        </div>
                    @endif

                    <ul class="list-unstyled mb-0">
                        @if($stats['positions_potentiellement_liberables'] > 0)
                            <li><i class="fas fa-arrow-right text-primary me-2"></i>{{ $stats['positions_potentiellement_liberables'] }} positions peuvent être libérées</li>
                        @endif
                        @if($stats['espace_total_recuperable'] > 0)
                            <li><i class="fas fa-arrow-right text-success me-2"></i>{{ $stats['espace_total_recuperable'] }} emplacements récupérables</li>
                        @endif
                        <li><i class="fas fa-arrow-right text-info me-2"></i>Taux d'optimisation: {{ $stats['taux_optimisation_possible'] }}%</li>
                    </ul>
                @else
                    <div class="text-muted text-center">
                        <i class="fas fa-info-circle fa-2x mb-2"></i>
                        <p>Aucune donnée à analyser</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal pour actions d'optimisation -->
<div class="modal fade" id="optimizationActionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-magic me-2"></i>
                    Action d'Optimisation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="optimizationModalContent">
                    <!-- Contenu chargé dynamiquement -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmOptimizationAction">
                    <i class="fas fa-check me-2"></i>
                    Confirmer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    // Données pour le graphique
    @php
        $vides = $positionsOptimisables ? $positionsOptimisables->filter(function($item) { return $item['occupation'] == 0; })->count() : 0;
        $faibles = $positionsOptimisables ? $positionsOptimisables->filter(function($item) { return $item['taux_occupation'] > 0 && $item['taux_occupation'] < 50; })->count() : 0;
        $bonnes = $stats['total_boites_analysees'] - $vides - $faibles;
    @endphp

    // Graphique d'utilisation
    const ctx = document.getElementById('utilizationChart').getContext('2d');
    const utilizationChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Bien utilisées', 'Peu utilisées', 'Vides'],
            datasets: [{
                data: [{{ $bonnes }}, {{ $faibles }}, {{ $vides }}],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Fonctions d'optimisation
    function submitFilter() {
        document.getElementById('filterForm').submit();
    }

    function runOptimization() {
        const btn = event.target;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Analyse en cours...';
        btn.disabled = true;

        const organismeId = document.getElementById('organisme_id').value;
        const url = '{{ route("admin.stockage.optimize") }}' + (organismeId ? '?organisme_id=' + organismeId : '');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.stats) {
                location.reload();
            } else {
                alert('Erreur lors de l\'analyse');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de l\'analyse');
        })
        .finally(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
        });
    }

    function optimizeBox(boxId) {
        showOptimizationModal('optimize-box', boxId, 'Optimiser la boîte');
    }

    function consolidateBox(boxId) {
        showOptimizationModal('consolidate-box', boxId, 'Consolider la boîte');
    }

    function consolidateBoxes() {
        showOptimizationModal('consolidate-boxes', null, 'Consolider les boîtes');
    }

    function redistributeFiles() {
        showOptimizationModal('redistribute-files', null, 'Redistribuer les dossiers');
    }

    function optimizeLocations() {
        showOptimizationModal('optimize-locations', null, 'Optimiser les emplacements');
    }

    function scheduleElimination() {
        showOptimizationModal('schedule-elimination', null, 'Planifier les éliminations');
    }

    function showOptimizationModal(action, id, title) {
        document.querySelector('#optimizationActionModal .modal-title').innerHTML = 
            '<i class="fas fa-magic me-2"></i>' + title;
        
        const modal = new bootstrap.Modal(document.getElementById('optimizationActionModal'));
        modal.show();
        
        document.getElementById('optimizationModalContent').innerHTML = 
            '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Chargement...</p></div>';
        
        // Simulation du contenu (à adapter selon vos besoins)
        setTimeout(() => {
            document.getElementById('optimizationModalContent').innerHTML = 
                '<div class="alert alert-info">Action: ' + title + (id ? ' (ID: ' + id + ')' : '') + '</div>' +
                '<p>Cette fonctionnalité sera implémentée selon vos besoins spécifiques.</p>';
        }, 1000);
    }

    function exportReport() {
    const organismeId = document.getElementById('organisme_id').value;
    
    // Utiliser directement la méthode exportReport du controller
    let url = '{{ route("admin.stockage.optimize") }}';
    
    // Si vous avez une route séparée pour l'export, décommentez la ligne suivante:
    // let url = '/admin/stockage/export-report';
    
    if (organismeId) {
        url += (url.includes('?') ? '&' : '?') + 'organisme_id=' + organismeId;
    }
    
    url += (url.includes('?') ? '&' : '?') + 'export=1&type=optimization';
    
    window.location.href = url;
  }


    // Confirmation de l'action d'optimisation
    document.getElementById('confirmOptimizationAction').addEventListener('click', function() {
        const form = document.querySelector('#optimizationModalContent form');
        if (form) {
            form.submit();
        } else {
            // Action personnalisée
            bootstrap.Modal.getInstance(document.getElementById('optimizationActionModal')).hide();
            alert('Action confirmée');
        }
    });
</script>
@endpush

@push('styles')
<style>
    .recommendation-item {
        transition: all 0.3s ease;
    }

    .recommendation-item:hover {
        transform: translateX(5px);
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .progress {
        background-color: #e9ecef;
    }

    .alert {
        border: none;
        border-radius: 10px;
    }

    .card-body .fas.fa-4x {
        opacity: 0.7;
    }

    .accordion-button:not(.collapsed) {
        background-color: #e7f3ff;
        color: #0c63e4;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0,0,0,.125);
    }

    .timeline {
        position: relative;
        padding-left: 40px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 30px;
    }

    .timeline-item:before {
        content: '';
        position: absolute;
        left: -25px;
        top: 30px;
        width: 2px;
        height: calc(100% + 20px);
        background-color: #dee2e6;
    }

    .timeline-item:last-child:before {
        display: none;
    }

    .timeline-marker {
        position: absolute;
        left: -35px;
        top: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.8rem;
    }

    .timeline-content {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border-left: 4px solid var(--bs-primary);
    }

    .btn-group .btn {
        margin-right: 5px;
    }

    .card-title {
        font-weight: 600;
    }

    .badge {
        font-size: 0.75em;
    }

    .table-responsive {
        border-radius: 0.5rem;
    }
</style>
@endpush