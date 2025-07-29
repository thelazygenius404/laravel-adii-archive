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
            <a href="{{ route('stockage.index') }}" class="btn btn-outline-secondary">
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

<!-- Résumé de l'optimisation -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="fas fa-lightbulb text-info fa-3x mb-3"></i>
                <h3 class="text-info">{{ $recommendations->count() }}</h3>
                <p class="text-muted mb-0">Recommandations</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-compress-arrows-alt text-warning fa-3x mb-3"></i>
                <h3 class="text-warning">{{ $spaceOptimization['potential_savings'] ?? 0 }}%</h3>
                <p class="text-muted mb-0">Économie d'espace potentielle</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-box text-success fa-3x mb-3"></i>
                <h3 class="text-success">{{ $inefficientBoxes->count() }}</h3>
                <p class="text-muted mb-0">Boîtes à optimiser</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-danger">
            <div class="card-body text-center">
                <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                <h3 class="text-danger">{{ $urgentActions->count() }}</h3>
                <p class="text-muted mb-0">Actions urgentes</p>
            </div>
        </div>
    </div>
</div>

<!-- Actions urgentes -->
@if($urgentActions->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Actions Urgentes Requises
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($urgentActions as $action)
                        <div class="col-md-6 mb-3">
                            <div class="alert alert-danger mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $action['icon'] }} fa-2x me-3"></i>
                                    <div class="flex-grow-1">
                                        <h6 class="alert-heading mb-1">{{ $action['title'] }}</h6>
                                        <p class="mb-2">{{ $action['description'] }}</p>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ $action['action_url'] }}" class="btn btn-outline-danger">
                                                {{ $action['action_text'] }}
                                            </a>
                                            <button class="btn btn-outline-secondary" onclick="dismissAction({{ $action['id'] }})">
                                                Ignorer
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
</div>
@endif

<!-- Analyse détaillée -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Analyse Détaillée des Recommandations
                </h5>
            </div>
            <div class="card-body">
                @if($recommendations->count() > 0)
                    <div class="accordion" id="recommendationsAccordion">
                        @foreach($recommendations->groupBy('category') as $category => $categoryRecommendations)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ Str::slug($category) }}">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#collapse{{ Str::slug($category) }}">
                                        <i class="fas fa-{{ $categoryRecommendations->first()['icon'] ?? 'lightbulb' }} me-2"></i>
                                        {{ $category }} 
                                        <span class="badge bg-primary ms-2">{{ $categoryRecommendations->count() }}</span>
                                    </button>
                                </h2>
                                <div id="collapse{{ Str::slug($category) }}" 
                                     class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" 
                                     data-bs-parent="#recommendationsAccordion">
                                    <div class="accordion-body">
                                        @foreach($categoryRecommendations as $recommendation)
                                            <div class="recommendation-item mb-3 p-3 border rounded">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $recommendation['title'] }}</h6>
                                                        <p class="text-muted mb-2">{{ $recommendation['description'] }}</p>
                                                        
                                                        @if(isset($recommendation['impact']))
                                                            <div class="mb-2">
                                                                <span class="badge bg-info">
                                                                    Impact: {{ $recommendation['impact'] }}
                                                                </span>
                                                                @if(isset($recommendation['priority']))
                                                                    <span class="badge bg-{{ $recommendation['priority'] == 'high' ? 'danger' : ($recommendation['priority'] == 'medium' ? 'warning' : 'success') }}">
                                                                        Priorité: {{ ucfirst($recommendation['priority']) }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        @if(isset($recommendation['details']) && is_array($recommendation['details']))
                                                            <ul class="list-unstyled mb-2">
                                                                @foreach($recommendation['details'] as $detail)
                                                                    <li><i class="fas fa-arrow-right text-muted me-2"></i>{{ $detail }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @endif
                                                    </div>
                                                    <div class="ms-3">
                                                        @if(isset($recommendation['action_url']))
                                                            <a href="{{ $recommendation['action_url'] }}" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-play me-1"></i>
                                                                Appliquer
                                                            </a>
                                                        @endif
                                                        <button class="btn btn-sm btn-outline-secondary" onclick="dismissRecommendation({{ $recommendation['id'] }})">
                                                            <i class="fas fa-times me-1"></i>
                                                            Ignorer
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle text-success fa-4x mb-3"></i>
                        <h5 class="text-success">Excellent !</h5>
                        <p class="text-muted">Votre système de stockage est déjà bien optimisé. Aucune recommandation d'amélioration pour le moment.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Statistiques d'optimisation -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Répartition de l'utilisation
                </h6>
            </div>
            <div class="card-body">
                <canvas id="utilizationChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Actions Rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="consolidateBoxes()">
                        <i class="fas fa-compress-alt me-2"></i>
                        Consolider les boîtes
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="redistributeFiles()">
                        <i class="fas fa-random me-2"></i>
                        Redistribuer les dossiers
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="optimizeLocations()">
                        <i class="fas fa-map-marked-alt me-2"></i>
                        Optimiser les emplacements
                    </button>
                    <button class="btn btn-outline-warning btn-sm" onclick="scheduleElimination()">
                        <i class="fas fa-trash me-2"></i>
                        Planifier éliminations
                    </button>
                </div>
            </div>
        </div>

        <!-- Tendances -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-trending-up me-2"></i>
                    Tendances d'utilisation
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Cette semaine</small>
                        <small class="text-success">+{{ $trends['weekly_growth'] ?? 0 }}%</small>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ $trends['weekly_progress'] ?? 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">Ce mois</small>
                        <small class="text-info">+{{ $trends['monthly_growth'] ?? 0 }}%</small>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-info" style="width: {{ $trends['monthly_progress'] ?? 0 }}%"></div>
                    </div>
                </div>
                <div class="text-center">
                    <small class="text-muted">
                        Projection: {{ $trends['projected_full'] ?? 'N/A' }}
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Boîtes inefficaces -->
@if($inefficientBoxes->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-box me-2"></i>
                    Boîtes à Faible Efficacité
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
                                <th>Recommandations</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inefficientBoxes as $box)
                                <tr>
                                    <td>
                                        <strong>{{ $box->numero }}</strong>
                                        <br><small class="text-muted">{{ $box->code_thematique }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $box->full_location }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress me-2" style="width: 100px; height: 8px;">
                                                <div class="progress-bar bg-{{ $box->utilisation_percentage < 30 ? 'danger' : ($box->utilisation_percentage < 50 ? 'warning' : 'success') }}" 
                                                     style="width: {{ $box->utilisation_percentage }}%"></div>
                                            </div>
                                            <small>{{ $box->utilisation_percentage }}%</small>
                                        </div>
                                        <small class="text-muted">{{ $box->nbr_dossiers }}/{{ $box->capacite }} dossiers</small>
                                    </td>
                                    <td>
                                        @foreach($box->recommendations as $rec)
                                            <span class="badge bg-light text-dark me-1">{{ $rec }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.boites.show', $box) }}" class="btn btn-outline-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-outline-warning" onclick="optimizeBox({{ $box->id }})" title="Optimiser">
                                                <i class="fas fa-magic"></i>
                                            </button>
                                            <button class="btn btn-outline-success" onclick="consolidateBox({{ $box->id }})" title="Consolider">
                                                <i class="fas fa-compress-alt"></i>
                                            </button>
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
@endif

<!-- Plan d'action -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tasks me-2"></i>
                    Plan d'Action Recommandé
                </h5>
            </div>
            <div class="card-body">
                @if(isset($actionPlan) && count($actionPlan) > 0)
                    <div class="timeline">
                        @foreach($actionPlan as $index => $action)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-{{ $action['priority_color'] ?? 'primary' }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="timeline-content">
                                    <h6>{{ $action['title'] }}</h6>
                                    <p class="text-muted mb-2">{{ $action['description'] }}</p>
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-{{ $action['priority_color'] ?? 'primary' }} me-2">
                                            {{ ucfirst($action['priority'] ?? 'normal') }}
                                        </span>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ $action['estimated_time'] ?? 'N/A' }}
                                        </small>
                                    </div>
                                    @if(isset($action['steps']) && is_array($action['steps']))
                                        <ol class="mb-2">
                                            @foreach($action['steps'] as $step)
                                                <li>{{ $step }}</li>
                                            @endforeach
                                        </ol>
                                    @endif
                                    <div class="btn-group btn-group-sm">
                                        @if(isset($action['action_url']))
                                            <a href="{{ $action['action_url'] }}" class="btn btn-primary">
                                                <i class="fas fa-play me-1"></i>
                                                Commencer
                                            </a>
                                        @endif
                                        <button class="btn btn-outline-secondary" onclick="markAsCompleted({{ $action['id'] ?? $index }})">
                                            <i class="fas fa-check me-1"></i>
                                            Marquer comme terminé
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                        <h6>Aucun plan d'action nécessaire</h6>
                        <p class="text-muted">Votre système de stockage fonctionne de manière optimale.</p>
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
    // Graphique d'utilisation
    const ctx = document.getElementById('utilizationChart').getContext('2d');
    const utilizationChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Occupé', 'Libre', 'Inefficace'],
            datasets: [{
                data: [
                    {{ $chartData['occupied'] ?? 0 }},
                    {{ $chartData['free'] ?? 0 }},
                    {{ $chartData['inefficient'] ?? 0 }}
                ],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
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
    function runOptimization() {
        const btn = event.target;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Analyse en cours...';
        btn.disabled = true;

        fetch('{{ route("stockage.optimize") }}?reanalyze=1')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
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

    function dismissRecommendation(id) {
        if (confirm('Voulez-vous ignorer cette recommandation ?')) {
            fetch(`{{ route('stockage.optimize') }}/dismiss/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }

    function dismissAction(id) {
        if (confirm('Voulez-vous ignorer cette action urgente ?')) {
            fetch(`{{ route('stockage.optimize') }}/dismiss-action/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
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
        
        // Charger le contenu spécifique de l'action
        fetch(`{{ route('stockage.optimize') }}/action/${action}${id ? '/' + id : ''}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('optimizationModalContent').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('optimizationModalContent').innerHTML = 
                    '<div class="alert alert-danger">Erreur lors du chargement de l\'action.</div>';
            });
    }

    function markAsCompleted(actionId) {
        if (confirm('Marquer cette action comme terminée ?')) {
            fetch(`{{ route('stockage.optimize') }}/complete/${actionId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }
    }

    function exportReport() {
        window.location.href = '{{ route("stockage.optimize") }}?export=1';
    }

    // Confirmation de l'action d'optimisation
    document.getElementById('confirmOptimizationAction').addEventListener('click', function() {
        const form = document.querySelector('#optimizationModalContent form');
        if (form) {
            form.submit();
        }
    });
</script>
@endpush

@push('styles')
<style>
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
</style>
@endpush