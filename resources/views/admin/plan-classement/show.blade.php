@extends('layouts.admin')

@section('title', 'Détails du Plan de Classement')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-eye me-2"></i>
            Détails du Plan de Classement
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.plan-classement.edit', $planClassement) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                Modifier
            </a>
            <a href="{{ route('admin.plan-classement.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Main Information -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-layer-group me-2"></i>
                    Plan {{ $planClassement->formatted_code }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Code de classement :</strong></td>
                                <td><span class="badge bg-primary fs-6">{{ $planClassement->formatted_code }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Date de création :</strong></td>
                                <td>{{ $planClassement->created_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Dernière modification :</strong></td>
                                <td>{{ $planClassement->updated_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nombre de règles :</strong></td>
                                <td><span class="badge bg-info">{{ $stats['total_regles'] }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Durée moyenne :</strong></td>
                                <td>
                                    @if($stats['duree_moyenne'])
                                        <span class="badge bg-dark">{{ number_format($stats['duree_moyenne'], 1) }} ans</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h6>Objet de classement :</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $planClassement->objet_classement }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Règles de Conservation -->
        @if($planClassement->calendrierConservation->count() > 0)
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar me-2"></i>
                        Règles de Conservation ({{ $planClassement->calendrierConservation->count() }})
                    </h5>
                    <div class="btn-group">
                        <a href="{{ route('admin.calendrier-conservation.create', ['plan_classement' => $planClassement->id]) }}" 
                           class="btn btn-sm btn-success">
                            <i class="fas fa-plus me-1"></i>
                            Ajouter une règle
                        </a>
                        <a href="{{ route('admin.calendrier-conservation.index', ['plan_classement' => $planClassement->id]) }}" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-list me-1"></i>
                            Voir toutes
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>N° Règle</th>
                                    <th>Nature du Dossier</th>
                                    <th>Délais</th>
                                    <th>AC/AI</th>
                                    <th>Sort Final</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($planClassement->calendrierConservation as $regle)
                                    <tr>
                                        <td><span class="badge bg-secondary">{{ $regle->NO_regle }}</span></td>
                                        <td>{{ $regle->short_nature }}</td>
                                        <td><span class="badge bg-dark">{{ $regle->delais_legaux }}ans</span></td>
                                        <td>
                                            <span class="badge bg-info me-1">{{ $regle->archive_courant }}AC</span>
                                            <span class="badge bg-warning">{{ $regle->archive_intermediaire }}AI</span>
                                        </td>
                                        <td><span class="badge {{ $regle->status_badge_class }}">{{ $regle->status }}</span></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.calendrier-conservation.show', $regle) }}" 
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.calendrier-conservation.edit', $regle) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
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
        @else
            <div class="card mt-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune règle de conservation</h5>
                    <p class="text-muted">Ce plan de classement n'a pas encore de règles de conservation associées.</p>
                    <a href="{{ route('admin.calendrier-conservation.create', ['plan_classement' => $planClassement->id]) }}" 
                       class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>
                        Créer la première règle
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Statistics Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistiques
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h3 class="text-info mb-1">{{ $stats['total_regles'] }}</h3>
                            <small class="text-muted">Règles totales</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h3 class="text-success mb-1">{{ $stats['regles_conservation'] }}</h3>
                            <small class="text-muted">Conservation</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h3 class="text-danger mb-1">{{ $stats['regles_elimination'] }}</h3>
                            <small class="text-muted">Élimination</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h3 class="text-warning mb-1">{{ $stats['regles_tri'] }}</h3>
                            <small class="text-muted">Tri</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Actions Rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.plan-classement.edit', $planClassement) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>
                        Modifier ce plan
                    </a>
                    
                    <a href="{{ route('admin.calendrier-conservation.create', ['plan_classement' => $planClassement->id]) }}" 
                       class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>
                        Ajouter une règle
                    </a>
                    
                    @if($planClassement->calendrierConservation->count() > 0)
                        <a href="{{ route('admin.calendrier-conservation.index', ['plan_classement' => $planClassement->id]) }}" 
                           class="btn btn-info">
                            <i class="fas fa-list me-2"></i>
                            Voir toutes les règles
                        </a>
                        
                        <button class="btn btn-outline-success" onclick="exportPlanData()">
                            <i class="fas fa-download me-2"></i>
                            Exporter avec règles
                        </button>
                    @endif
                    
                    <button class="btn btn-outline-secondary" onclick="exportPlanOnly()">
                        <i class="fas fa-file-export me-2"></i>
                        Exporter ce plan
                    </button>
                    
                    @if($planClassement->calendrierConservation->count() == 0)
                        <button class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i>
                            Supprimer ce plan
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Distribution Chart -->
        @if($stats['total_regles'] > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Répartition des Sorts Finaux
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $total = $stats['total_regles'];
                        $conservation_pct = $total > 0 ? ($stats['regles_conservation'] / $total) * 100 : 0;
                        $elimination_pct = $total > 0 ? ($stats['regles_elimination'] / $total) * 100 : 0;
                        $tri_pct = $total > 0 ? ($stats['regles_tri'] / $total) * 100 : 0;
                    @endphp
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-success">Conservation</span>
                            <span class="badge bg-success">{{ number_format($conservation_pct, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ $conservation_pct }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-danger">Élimination</span>
                            <span class="badge bg-danger">{{ number_format($elimination_pct, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-danger" style="width: {{ $elimination_pct }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-warning">Tri</span>
                            <span class="badge bg-warning">{{ number_format($tri_pct, 1) }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-warning" style="width: {{ $tri_pct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Activity Timeline -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>
                    Activité Récente
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Plan créé</h6>
                            <small class="text-muted">{{ $planClassement->created_at->format('d/m/Y à H:i') }}</small>
                        </div>
                    </div>
                    
                    @if($planClassement->updated_at != $planClassement->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Dernière modification</h6>
                                <small class="text-muted">{{ $planClassement->updated_at->format('d/m/Y à H:i') }}</small>
                            </div>
                        </div>
                    @endif
                    
                    @if($stats['total_regles'] > 0)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">{{ $stats['total_regles'] }} règle(s) associée(s)</h6>
                                <small class="text-muted">Dernière règle ajoutée</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer le plan <strong>{{ $planClassement->formatted_code }}</strong> ?</p>
                <p class="text-danger"><small>Cette action est irréversible.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.plan-classement.destroy', $planClassement) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 20px;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 15px;
    }
    
    .timeline-marker {
        position: absolute;
        left: -25px;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    
    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: -21px;
        top: 15px;
        width: 2px;
        height: calc(100% + 5px);
        background-color: #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmDelete() {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
    
    function exportPlanOnly() {
        // Export this specific plan
        window.location.href = '{{ route("admin.plan-classement.export") }}?plan_ids[]={{ $planClassement->id }}';
    }
    
    function exportPlanData() {
        // Export plan with all associated rules
        window.location.href = '{{ route("admin.calendrier-conservation.export") }}?plan_classement={{ $planClassement->id }}';
    }
</script>
@endpush