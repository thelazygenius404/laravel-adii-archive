{{-- resources/views/admin/stockage/statistics.blade.php --}}
@extends('layouts.admin')

@section('title', 'Statistiques de Stockage - ' .  $organismeModel->nom_org)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-chart-line me-2"></i>
            Statistiques de Stockage
            <span class="text-muted">- {{ $organismeModel->nom_org }}</span>
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stockage.hierarchy', $organismeModel->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-sitemap me-2"></i>
                Vue hiérarchique
            </a>
            <a href="{{ route('admin.stockage.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour au tableau de bord
            </a>
            <button class="btn btn-primary" onclick="refreshStats()">
                <i class="fas fa-sync me-2"></i>
                Actualiser
            </button>
            <button class="btn btn-success" onclick="exportStats()">
                <i class="fas fa-download me-2"></i>
                Exporter
            </button>
        </div>
    </div>
</div>

<!-- Filtres de période -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row align-items-end">
                    <div class="col-md-3">
                        <label for="period" class="form-label">Période</label>
                        <select class="form-select" id="period" name="period" onchange="this.form.submit()">
                            <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                            <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Ce mois</option>
                            <option value="quarter" {{ request('period') == 'quarter' ? 'selected' : '' }}>Ce trimestre</option>
                            <option value="year" {{ request('period') == 'year' ? 'selected' : '' }}>Cette année</option>
                            <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Personnalisée</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="customDateFrom" style="{{ request('period') == 'custom' ? '' : 'display: none;' }}">
                        <label for="date_from" class="form-label">Du</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3" id="customDateTo" style="{{ request('period') == 'custom' ? '' : 'display: none;' }}">
                        <label for="date_to" class="form-label">Au</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>
                            Appliquer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Métriques principales -->
<div class="row mb-4">
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-home text-primary fa-2x mb-2"></i>
                <h4 class="text-primary">{{ $stats['salles'] ?? 0 }}</h4>
                <small class="text-muted">Salles</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="fas fa-map-marker-alt text-info fa-2x mb-2"></i>
                <h4 class="text-info">{{ $stats['positions_totales'] ?? 0 }}</h4>
                <small class="text-muted">Positions</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-box text-success fa-2x mb-2"></i>
                <h4 class="text-success">{{ $stats['boites_actives'] ?? 0 }}</h4>
                <small class="text-muted">Boîtes actives</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-file-alt text-warning fa-2x mb-2"></i>
                <h4 class="text-warning">{{ $stats['dossiers_actifs'] ?? 0 }}</h4>
                <small class="text-muted">Dossiers actifs</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-danger">
            <div class="card-body text-center">
                <i class="fas fa-percentage text-danger fa-2x mb-2"></i>
                <h4 class="text-danger">{{ number_format($stats['taux_occupation'] ?? 0, 1) }}%</h4>
                <small class="text-muted">Taux d'occupation</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-secondary">
            <div class="card-body text-center">
                <i class="fas fa-chart-line text-secondary fa-2x mb-2"></i>
                <h4 class="text-secondary">{{ ($stats['croissance'] ?? 0) > 0 ? '+' : '' }}{{ number_format($stats['croissance'] ?? 0, 1) }}%</h4>
                <small class="text-muted">Croissance</small>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques principaux -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-area me-2"></i>
                    Évolution de l'Occupation
                </h5>
            </div>
            <div class="card-body">
                <canvas id="occupationChart" height="100"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Répartition par Salle
                </h5>
            </div>
            <div class="card-body">
                <canvas id="sallesChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Analyse détaillée -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Performance par Salle
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Salle</th>
                                <th>Capacité</th>
                                <th>Occupation</th>
                                <th>Efficacité</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($performanceSalles as $salle)
                                <tr>
                                    <td>
                                        <strong>{{ $salle['nom'] }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $salle['capacite'] }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 80px; height: 8px;">
                                                <div class="progress-bar bg-{{ $salle['occupation_percentage'] < 50 ? 'success' : ($salle['occupation_percentage'] < 80 ? 'warning' : 'danger') }}" 
                                                     style="width: {{ $salle['occupation_percentage'] }}%"></div>
                                            </div>
                                            <small>{{ number_format($salle['occupation_percentage'], 1) }}%</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $salle['efficacite'] >= 80 ? 'success' : ($salle['efficacite'] >= 60 ? 'warning' : 'danger') }}">
                                            {{ number_format($salle['efficacite'], 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-chart-bar fa-2x mb-2 opacity-50"></i>
                                        <p class="mb-0">Aucune donnée de performance disponible</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Activité Récente
                </h5>
            </div>
            <div class="card-body">
                @if(count($activiteRecente) > 0)
                    <div class="timeline">
                        @foreach($activiteRecente as $activite)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $activite['type_color'] }}">
                                    <i class="fas fa-{{ $activite['icon'] }}"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6>{{ $activite['title'] }}</h6>
                                    <p class="text-muted mb-1">{{ $activite['description'] }}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $activite['created_at']->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-clock fa-3x mb-3 opacity-50"></i>
                        <h6 class="text-muted">Aucune activité récente</h6>
                        <p class="mb-0">Les activités apparaîtront ici une fois configurées.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Analyses avancées -->
<div class="row mb-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Indicateurs de Performance
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Taux d'utilisation optimal</span>
                        <span class="fw-bold">{{ number_format($kpis['taux_utilisation_optimal'] ?? 0, 1) }}%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ $kpis['taux_utilisation_optimal'] ?? 0 }}%"></div>
                    </div>
                    <small class="text-muted">Optimal: 70-85%</small>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Efficacité du stockage</span>
                        <span class="fw-bold">{{ number_format($kpis['efficacite_stockage'] ?? 0, 1) }}%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: {{ $kpis['efficacite_stockage'] ?? 0 }}%"></div>
                    </div>
                    <small class="text-muted">Basé sur l'organisation spatiale</small>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Rotation des dossiers</span>
                        <span class="fw-bold">{{ number_format($kpis['rotation_dossiers'] ?? 0, 1) }}</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: {{ min(($kpis['rotation_dossiers'] ?? 0) * 10, 100) }}%"></div>
                    </div>
                    <small class="text-muted">Mouvements par trimestre</small>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Score d'organisation</span>
                        <span class="fw-bold">{{ number_format($kpis['score_organisation'] ?? 0, 1) }}/10</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: {{ ($kpis['score_organisation'] ?? 0) * 10 }}%"></div>
                    </div>
                    <small class="text-muted">Évaluation globale</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Alertes et Recommandations
                </h5>
            </div>
            <div class="card-body">
                @if(count($alertes) > 0)
                    @foreach($alertes as $alerte)
                        <div class="alert alert-{{ $alerte['type'] }} alert-sm mb-2">
                            <i class="fas fa-{{ $alerte['icon'] }} me-2"></i>
                            <strong>{{ $alerte['title'] }}</strong><br>
                            <small>{{ $alerte['message'] }}</small>
                        </div>
                    @endforeach
                    
                    <div class="mt-3">
                        <h6 class="text-muted mb-2">Actions recommandées:</h6>
                        <div class="d-grid gap-2">
                            @if(($stats['taux_occupation'] ?? 0) > 90)
                                <button class="btn btn-sm btn-outline-warning" onclick="suggestExpansion()">
                                    <i class="fas fa-expand me-1"></i>Planifier une extension
                                </button>
                            @endif
                            @if(($stats['positions_libres'] ?? 0) < 10)
                                <button class="btn btn-sm btn-outline-info" onclick="optimizeStorage()">
                                    <i class="fas fa-magic me-1"></i>Optimiser l'espace
                                </button>
                            @endif
                            <button class="btn btn-sm btn-outline-secondary" onclick="generateReport()">
                                <i class="fas fa-file-alt me-1"></i>Générer rapport
                            </button>
                        </div>
                    </div>
                @else
                    <div class="text-center text-success py-4">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h6 class="text-success">Aucune alerte</h6>
                        <p class="mb-0 text-muted">Tout fonctionne parfaitement</p>
                        <small class="text-muted">Dernière vérification: {{ now()->format('d/m/Y à H:i') }}</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-crystal-ball me-2"></i>
                    Prévisions
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted">Capacité maximale dans:</h6>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar text-warning fa-2x me-3"></i>
                        <div>
                            <h5 class="mb-0">{{ $previsions['temps_avant_saturation'] ?? 'N/A' }}</h5>
                            <small class="text-muted">au rythme actuel</small>
                        </div>
                    </div>
                    @if(($previsions['temps_avant_saturation'] ?? '') !== 'Saturé')
                        <div class="progress mt-2" style="height: 4px;">
                            @php
                                $saturationProgress = 100 - (($stats['taux_occupation'] ?? 0));
                            @endphp
                            <div class="progress-bar bg-warning" style="width: {{ 100 - $saturationProgress }}%"></div>
                        </div>
                    @endif
                </div>

                <div class="mb-3">
                    <h6 class="text-muted">Croissance prévue (3 mois):</h6>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-line text-info fa-2x me-3"></i>
                        <div>
                            <h5 class="mb-0">+{{ number_format($previsions['croissance_prevue'] ?? 0, 1) }}%</h5>
                            <small class="text-muted">{{ $previsions['nouveaux_dossiers_prevus'] ?? 0 }} nouveaux dossiers</small>
                        </div>
                    </div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-info" style="width: {{ min(($previsions['croissance_prevue'] ?? 0) * 2, 100) }}%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted">Optimisations recommandées:</h6>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lightbulb text-success fa-2x me-3"></i>
                        <div>
                            <h5 class="mb-0">{{ count($previsions['optimisations'] ?? []) }}</h5>
                            <small class="text-muted">actions possibles</small>
                        </div>
                    </div>
                    @if(count($previsions['optimisations'] ?? []) > 0)
                        <div class="mt-2">
                            <small class="text-success">
                                <i class="fas fa-check me-1"></i>
                                Optimisations disponibles
                            </small>
                        </div>
                    @endif
                </div>

                <div class="d-grid">
                    <button class="btn btn-outline-primary btn-sm" onclick="showPredictionDetails()">
                        <i class="fas fa-chart-line me-1"></i>
                        Voir détails des prévisions
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques d'exportation -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card border-secondary">
            <div class="card-header bg-secondary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-download me-2"></i>
                    Options d'Exportation
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <p class="text-muted">
                            Exportez vos statistiques de stockage dans différents formats pour analyse externe 
                            ou présentation. Les données incluent toutes les métriques et graphiques visibles.
                        </p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Formats disponibles:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-file-pdf text-danger me-2"></i>PDF (Rapport complet)</li>
                                    <li><i class="fas fa-file-excel text-success me-2"></i>Excel (Données brutes)</li>
                                    <li><i class="fas fa-file-csv text-info me-2"></i>CSV (Compatible tableur)</li>
                                    <li><i class="fas fa-file-image text-warning me-2"></i>PNG (Graphiques uniquement)</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Données incluses:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-check text-success me-2"></i>Métriques principales</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Performance par salle</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Évolution temporelle</li>
                                    <li><i class="fas fa-check text-success me-2"></i>Prévisions et KPIs</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="d-grid gap-2">
                            <button class="btn btn-danger" onclick="exportToPDF()">
                                <i class="fas fa-file-pdf me-2"></i>
                                Exporter en PDF
                            </button>
                            <button class="btn btn-success" onclick="exportToExcel()">
                                <i class="fas fa-file-excel me-2"></i>
                                Exporter en Excel
                            </button>
                            <button class="btn btn-info" onclick="exportToCSV()">
                                <i class="fas fa-file-csv me-2"></i>
                                Exporter en CSV
                            </button>
                            <button class="btn btn-secondary" onclick="exportCharts()">
                                <i class="fas fa-file-image me-2"></i>
                                Exporter graphiques
                            </button>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Les exports incluent automatiquement la période sélectionnée
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }

    .timeline-item:not(:last-child):before {
        content: '';
        position: absolute;
        left: -19px;
        top: 20px;
        width: 2px;
        height: calc(100% + 10px);
        background-color: #e9ecef;
    }

    .timeline-marker {
        position: absolute;
        left: -25px;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8px;
    }

    .timeline-content {
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 6px;
        border-left: 3px solid #007bff;
    }

    .timeline-content h6 {
        margin-bottom: 5px;
        font-size: 14px;
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }

    .progress {
        background-color: #e9ecef;
    }

    .badge {
        font-size: 0.75em;
    }

    .opacity-50 {
        opacity: 0.5;
    }

    .alert-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .btn-group-sm > .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    @media (max-width: 768px) {
        .col-lg-2 {
            margin-bottom: 1rem;
        }
        
        .btn-group {
            flex-direction: column;
        }
        
        .btn-group .btn {
            margin-bottom: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration des graphiques
    initializeCharts();
    
    // Gestion des filtres de période
    setupPeriodFilters();
    
    // Initialisation des tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Initialisation des graphiques
function initializeCharts() {
    // Graphique d'évolution de l'occupation
    const occupationCtx = document.getElementById('occupationChart').getContext('2d');
    new Chart(occupationCtx, {
        type: 'line',
        data: {
            labels: @json($chartData['occupation']['labels'] ?? []),
            datasets: [{
                label: 'Taux d\'occupation (%)',
                data: @json($chartData['occupation']['data'] ?? []),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    }
     
);

    // Graphique de répartition par salle
    const sallesCtx = document.getElementById('sallesChart').getContext('2d');
    new Chart(sallesCtx, {
        type: 'doughnut',
        data: {
            labels: @json($chartData['salles']['labels'] ?? []),
            datasets: [{
                data: @json($chartData['salles']['data'] ?? []),
                backgroundColor: ['#007bff', '#28a745', '#17a2b8', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
    // Graphique des types de dossiers
    const dossierTypesCtx = document.getElementById('dossierTypesChart').getContext('2d');
    new Chart(dossierTypesCtx, {
        type: 'pie',
        data: {
            labels: @json($chartData['types']['labels'] ?? []),
            datasets: [{
                data: @json($chartData['types']['data'] ?? []),
                backgroundColor: ['#007bff', '#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6f42c1']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

// Gestion des filtres de période
function setupPeriodFilters() {
    const periodSelect = document.getElementById('period');
    const customDateFrom = document.getElementById('customDateFrom');
    const customDateTo = document.getElementById('customDateTo');

    periodSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDateFrom.style.display = 'block';
            customDateTo.style.display = 'block';
        } else {
            customDateFrom.style.display = 'none';
            customDateTo.style.display = 'none';
        }
    });
}

// Fonctions d'action
function refreshStats() {
    location.reload();
}

function exportStats() {
    exportToPDF();
}

function exportToPDF() {
    const url = '{{ route("admin.stockage.exportReport") }}';
    const params = new URLSearchParams(window.location.search);
    params.append('type', 'complete');
    params.append('organisme_id', '{{ $organismeModel->id }}');
    window.open(url + '?' + params.toString(), '_blank');
}

function exportToExcel() {
    const url = '{{ route("admin.stockage.exportReport") }}';
    const params = new URLSearchParams(window.location.search);
    params.append('type', 'utilisation');
    params.append('organisme_id', '{{ $organismeModel->id }}');
    window.open(url + '?' + params.toString(), '_blank');
}

function exportToCSV() {
    const url = '{{ route("admin.stockage.exportReport") }}';
    const params = new URLSearchParams(window.location.search);
    params.append('type', 'inventory');
    params.append('organisme_id', '{{ $organismeModel->id }}');
    window.open(url + '?' + params.toString(), '_blank');
}

function exportCharts() {
    const url = '{{ route("admin.stockage.exportReport") }}';
    const params = new URLSearchParams(window.location.search);
    params.append('type', 'complete');
    params.append('organisme_id', '{{ $organismeModel->id }}');
    window.open(url + '?' + params.toString(), '_blank');
}

function suggestExpansion() {
    alert('Fonctionnalité de suggestion d\'extension à implémenter');
}

function optimizeStorage() {
    alert('Fonctionnalité d\'optimisation du stockage à implémenter');
}

function generateReport() {
    exportToPDF();
}

function showPredictionDetails() {
    alert('Détails des prévisions à implémenter dans une modal');
}

function changeChartPeriod(period) {
    // Mise à jour de l'apparence des boutons
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Rechargement des données (à implémenter)
    console.log('Changement de période:', period);
}

function filterDossierType(type) {
    const rows = document.querySelectorAll('#dossierTypesTable tbody tr[data-type]');
    
    rows.forEach(row => {
        if (type === 'all' || row.dataset.type === type) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endpush