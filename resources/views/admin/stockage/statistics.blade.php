{{-- resources/views/admin/stockage/statistics.blade.php --}}
@extends('layouts.admin')

@section('title', 'Statistiques de Stockage - ' . $organisme->nom_org)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-chart-line me-2"></i>
            Statistiques de Stockage
            <span class="text-muted">- {{ $organisme->nom_org }}</span>
        </h1>
        <div class="btn-group">
            <a href="{{ route('stockage.hierarchy', $organisme) }}" class="btn btn-outline-secondary">
                <i class="fas fa-sitemap me-2"></i>
                Vue hiérarchique
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
                <h4 class="text-primary">{{ $stats['salles'] }}</h4>
                <small class="text-muted">Salles</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="fas fa-map-marker-alt text-info fa-2x mb-2"></i>
                <h4 class="text-info">{{ $stats['positions_totales'] }}</h4>
                <small class="text-muted">Positions</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-box text-success fa-2x mb-2"></i>
                <h4 class="text-success">{{ $stats['boites_actives'] }}</h4>
                <small class="text-muted">Boîtes actives</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-file-alt text-warning fa-2x mb-2"></i>
                <h4 class="text-warning">{{ $stats['dossiers_actifs'] }}</h4>
                <small class="text-muted">Dossiers actifs</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-danger">
            <div class="card-body text-center">
                <i class="fas fa-percentage text-danger fa-2x mb-2"></i>
                <h4 class="text-danger">{{ number_format($stats['taux_occupation'], 1) }}%</h4>
                <small class="text-muted">Taux d'occupation</small>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
        <div class="card border-secondary">
            <div class="card-body text-center">
                <i class="fas fa-chart-line text-secondary fa-2x mb-2"></i>
                <h4 class="text-secondary">{{ $stats['croissance'] > 0 ? '+' : '' }}{{ number_format($stats['croissance'], 1) }}%</h4>
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
                            @foreach($performanceSalles as $salle)
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
                            @endforeach
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
                        <span class="fw-bold">{{ number_format($kpis['taux_utilisation_optimal'], 1) }}%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ $kpis['taux_utilisation_optimal'] }}%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Efficacité du stockage</span>
                        <span class="fw-bold">{{ number_format($kpis['efficacite_stockage'], 1) }}%</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: {{ $kpis['efficacite_stockage'] }}%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Rotation des dossiers</span>
                        <span class="fw-bold">{{ number_format($kpis['rotation_dossiers'], 1) }}</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-warning" style="width: {{ min($kpis['rotation_dossiers'] * 10, 100) }}%"></div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Score d'organisation</span>
                        <span class="fw-bold">{{ number_format($kpis['score_organisation'], 1) }}/10</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" style="width: {{ $kpis['score_organisation'] * 10 }}%"></div>
                    </div>
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
                @else
                    <div class="text-center text-success">
                        <i class="fas fa-check-circle fa-3x mb-2"></i>
                        <p class="mb-0">Aucune alerte</p>
                        <small class="text-muted">Tout fonctionne parfaitement</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-forecast me-2"></i>
                    Prévisions
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-muted">Capacité maximale dans:</h6>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar text-warning fa-2x me-3"></i>
                        <div>
                            <h5 class="mb-0">{{ $previsions['temps_avant_saturation'] }}</h5>
                            <small class="text-muted">au rythme actuel</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted">Croissance prévue (3 mois):</h6>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-line text-info fa-2x me-3"></i>
                        <div>
                            <h5 class="mb-0">+{{ number_format($previsions['croissance_prevue'], 1) }}%</h5>
                            <small class="text-muted">{{ $previsions['nouveaux_dossiers_prevus'] }} nouveaux dossiers</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="text-muted">Optimisations recommandées:</h6>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-lightbulb text-success fa-2x me-3"></i>
                        <div>
                            <h5 class="mb-0">{{ count($previsions['optimisations']) }}</h5>
                            <small class="text-muted">actions possibles</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Comparatif temporel -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Évolution Comparative
                </h5>
            </div>
            <div class="card-body">
                <canvas id="comparatifChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Détails par type de dossier -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-alt me-2"></i>
                    Répartition par Type de Dossier
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type de Dossier</th>
                                        <th>Nombre</th>
                                        <th>Pourcentage</th>
                                        <th>Tendance</th>
                                        <th>Durée moy. stockage</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($repartitionDossiers as $type)
                                        <tr>
                                            <td>
                                                <strong>{{ $type['nom'] }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $type['nombre'] }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 100px; height: 8px;">
                                                        <div class="progress-bar bg-info" style="width: {{ $type['pourcentage'] }}%"></div>
                                                    </div>
                                                    <small>{{ number_format($type['pourcentage'], 1) }}%</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($type['tendance'] > 0)
                                                    <span class="text-success">
                                                        <i class="fas fa-arrow-up me-1"></i>
                                                        +{{ number_format($type['tendance'], 1) }}%
                                                    </span>
                                                @elseif($type['tendance'] < 0)
                                                    <span class="text-danger">
                                                        <i class="fas fa-arrow-down me-1"></i>
                                                        {{ number_format($type['tendance'], 1) }}%
                                                    </span>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="fas fa-minus me-1"></i>
                                                        Stable
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ $type['duree_moyenne'] }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <canvas id="typesChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    // Configuration des couleurs
    const colors = {
        primary: '#007bff',
        success: '#28a745',
        warning: '#ffc107',
        danger: '#dc3545',
        info: '#17a2b8',
        secondary: '#6c757d'
    };

    // Graphique d'évolution de l'occupation
    const occupationCtx = document.getElementById('occupationChart').getContext('2d');
    const occupationChart = new Chart(occupationCtx, {
        type: 'line',
        data: {
            labels: @json($chartData['occupation']['labels']),
            datasets: [{
                label: 'Taux d\'occupation (%)',
                data: @json($chartData['occupation']['data']),
                borderColor: colors.primary,
                backgroundColor: colors.primary + '20',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // Graphique de répartition par salle
    const sallesCtx = document.getElementById('sallesChart').getContext('2d');
    const sallesChart = new Chart(sallesCtx, {
        type: 'doughnut',
        data: {
            labels: @json($chartData['salles']['labels']),
            datasets: [{
                data: @json($chartData['salles']['data']),
                backgroundColor: [
                    colors.primary,
                    colors.success,
                    colors.warning,
                    colors.danger,
                    colors.info,
                    colors.secondary
                ]
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

    // Graphique comparatif
    const comparatifCtx = document.getElementById('comparatifChart').getContext('2d');
    const comparatifChart = new Chart(comparatifCtx, {
        type: 'line',
        data: {
            labels: @json($chartData['comparatif']['labels']),
            datasets: [{
                label: 'Dossiers créés',
                data: @json($chartData['comparatif']['dossiers']),
                borderColor: colors.success,
                backgroundColor: colors.success + '20',
                yAxisID: 'y'
            }, {
                label: 'Dossiers éliminés',
                data: @json($chartData['comparatif']['elimines']),
                borderColor: colors.danger,
                backgroundColor: colors.danger + '20',
                yAxisID: 'y'
            }, {
                label: 'Taux d\'occupation (%)',
                data: @json($chartData['comparatif']['occupation']),
                borderColor: colors.warning,
                backgroundColor: colors.warning + '20',
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            }
        }
    });

    // Graphique types de dossiers
    const typesCtx = document.getElementById('typesChart').getContext('2d');
    const typesChart = new Chart(typesCtx, {
        type: 'pie',
        data: {
            labels: @json($chartData['types']['labels']),
            datasets: [{
                data: @json($chartData['types']['data']),
                backgroundColor: [
                    colors.primary,
                    colors.success,
                    colors.warning,
                    colors.danger,
                    colors.info,
                    colors.secondary
                ]
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

    // Fonctions utilitaires
    function refreshStats() {
        window.location.reload();
    }

    function exportStats() {
        const params = new URLSearchParams(window.location.search);
        params.set('export', '1');
        window.location.href = window.location.pathname + '?' + params.toString();
    }

    // Gestion des filtres personnalisés
    document.getElementById('period').addEventListener('change', function() {
        const customDates = document.querySelectorAll('#customDateFrom, #customDateTo');
        if (this.value === 'custom') {
            customDates.forEach(el => el.style.display = 'block');
        } else {
            customDates.forEach(el => el.style.display = 'none');
        }
    });

    // Auto-refresh toutes les 5 minutes
    setInterval(function() {
        // Vous pouvez implémenter un refresh automatique ici
        console.log('Auto-refresh des statistiques');
    }, 300000);
</script>
@endpush

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

    .timeline-item:before {
        content: '';
        position: absolute;
        left: -15px;
        top: 25px;
        width: 2px;
        height: calc(100% + 10px);
        background-color: #dee2e6;
    }

    .timeline-item:last-child:before {
        display: none;
    }

    .timeline-marker {
        position: absolute;
        left: -25px;
        top: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
    }

    .timeline-content {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border-left: 3px solid var(--bs-primary);
    }

    .progress {
        background-color: #e9ecef;
    }

    .alert-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .card-body .fas.fa-3x {
        opacity: 0.7;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
    }
</style>
@endpush