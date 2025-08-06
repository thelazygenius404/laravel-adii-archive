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
                    Plan {{ $planClassement->code_classement }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Code de classement :</strong></td>
                                <td><span class="badge bg-primary fs-6">{{ $planClassement->code_classement }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Catégorie :</strong></td>
                                <td>
                                    <span class="badge bg-secondary">{{ $planClassement->category }}</span>
                                    <br>
                                    <small class="text-muted">{{ $planClassement->category_name }}</small>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Niveau hiérarchique :</strong></td>
                                <td><span class="badge bg-info">{{ $planClassement->level }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Date de création :</strong></td>
                                <td>{{ $planClassement->created_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>A une règle de conservation :</strong></td>
                                <td>
                                    <span class="badge {{ $planClassement->hasConservationRule() ? 'bg-success' : 'bg-warning' }}">
                                        {{ $planClassement->hasConservationRule() ? 'Oui' : 'Non' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Dernière modification :</strong></td>
                                <td>{{ $planClassement->updated_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                            @if($stats['compliance_issues'] > 0)
                                <tr>
                                    <td><strong>Problèmes de conformité :</strong></td>
                                    <td><span class="badge bg-danger">{{ $stats['compliance_issues'] }}</span></td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
                
                <div class="mt-3">
                    <h6>Objet de classement :</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $planClassement->objet_classement }}</p>
                    </div>
                </div>

                @if($planClassement->description)
                    <div class="mt-3">
                        <h6>Description :</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0">{{ $planClassement->description }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Règle de Conservation -->
        @if($planClassement->hasConservationRule())
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar me-2"></i>
                        Règle de Conservation Associée
                    </h5>
                    <div class="btn-group">
                        <a href="{{ route('admin.calendrier-conservation.edit', $planClassement->calendrierConservation) }}" 
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-edit me-1"></i>
                            Modifier
                        </a>
                        <a href="{{ route('admin.calendrier-conservation.show', $planClassement->calendrierConservation) }}" 
                           class="btn btn-sm btn-outline-info">
                            <i class="fas fa-eye me-1"></i>
                            Voir détails
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @php $regle = $planClassement->calendrierConservation; @endphp
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-secondary me-2 fs-6">{{ $regle->plan_classement_code }}</span>
                                <span class="badge {{ $regle->sort_final == 'C' ? 'bg-success' : ($regle->sort_final == 'D' ? 'bg-danger' : 'bg-warning') }}">
                                    {{ $regle->sort_final == 'C' ? 'Conservation' : ($regle->sort_final == 'D' ? 'Destruction' : 'Tri') }}
                                </span>
                            </div>
                            <p><strong>Pièces constituant :</strong> {{ $regle->pieces_constituant ?: 'Non défini' }}</p>
                            <p><strong>Type :</strong> 
                                <span class="badge {{ $regle->principal_secondaire == 'P' ? 'bg-primary' : 'bg-secondary' }}">
                                    {{ $regle->principal_secondaire == 'P' ? 'Principal' : 'Secondaire' }}
                                </span>
                            </p>
                            <p><strong>Référence juridique :</strong> {{ $regle->reference_juridique ?: 'Non défini' }}</p>
                            @if($regle->observation)
                                <p><strong>Observation :</strong> {{ $regle->observation }}</p>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="mb-3">
                                    <h6 class="text-muted">Délai Légal</h6>
                                    <span class="badge bg-dark fs-6">{{ $regle->delai_legal != '_' ? $regle->delai_legal : 'Non défini' }}</span>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-muted">Archives Courantes</h6>
                                    <span class="badge bg-info fs-6">{{ $regle->archives_courantes }}</span>
                                </div>
                                <div class="mb-3">
                                    <h6 class="text-muted">Archives Intermédiaires</h6>
                                    <span class="badge bg-warning fs-6">{{ $regle->archives_intermediaires }}</span>
                                </div>
                                <div>
                                    <h6 class="text-muted">Sort Final</h6>
                                    <span class="badge {{ $regle->sort_final == 'C' ? 'bg-success' : ($regle->sort_final == 'D' ? 'bg-danger' : 'bg-warning') }} fs-6">
                                        {{ $regle->sort_final }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card mt-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune règle de conservation</h5>
                    <p class="text-muted">Ce plan de classement n'a pas encore de règle de conservation associée.</p>
                    <a href="{{ route('admin.calendrier-conservation.create') }}?plan_classement_code={{ $planClassement->code_classement }}" 
                       class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>
                        Créer la règle de conservation
                    </a>
                </div>
            </div>
        @endif

        <!-- Plans Connexes -->
        @if($relatedPlans->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-link me-2"></i>
                        Plans Connexes ({{ $relatedPlans->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Objet</th>
                                    <th>Règle</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($relatedPlans as $related)
                                    <tr>
                                        <td><span class="badge bg-primary">{{ $related->code_classement }}</span></td>
                                        <td>{{ Str::limit($related->objet_classement, 50) }}</td>
                                        <td>
                                            @if($related->hasConservationRule())
                                                <span class="badge bg-success">Oui</span>
                                            @else
                                                <span class="badge bg-warning">Non</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.plan-classement.show', $related) }}" 
                                                   class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.plan-classement.edit', $related) }}" 
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
        @endif
    </div>

    <!-- Statistics Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informations Détaillées
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Identifiant :</span>
                        <strong>{{ $planClassement->id }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Code :</span>
                        <strong>{{ $planClassement->code_classement }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Catégorie :</span>
                        <strong>{{ $planClassement->category }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Niveau :</span>
                        <strong>{{ $planClassement->level }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Créé le :</span>
                        <strong>{{ $planClassement->created_at->format('d/m/Y') }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Modifié le :</span>
                        <strong>{{ $planClassement->updated_at->format('d/m/Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistiques
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12 mb-3">
                        <div class="border rounded p-3">
                            <h3 class="text-{{ $planClassement->hasConservationRule() ? 'success' : 'warning' }} mb-1">
                                {{ $planClassement->hasConservationRule() ? '1' : '0' }}
                            </h3>
                            <small class="text-muted">Règle de conservation</small>
                        </div>
                    </div>
                    @if($stats['compliance_issues'])
                        <div class="col-12 mb-3">
                            <div class="border rounded p-3">
                                <h3 class="text-danger mb-1">{{ $stats['compliance_issues'] }}</h3>
                                <small class="text-muted">Problèmes de conformité</small>
                            </div>
                        </div>
                    @endif
                </div>

                @if($stats['has_legal_requirement'])
                    <div class="alert alert-info">
                        <i class="fas fa-balance-scale me-2"></i>
                        <strong>Exigences légales :</strong> Ce plan est soumis à des délais légaux spécifiques.
                    </div>
                @endif
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
                    
                    @if($planClassement->hasConservationRule())
                        <a href="{{ route('admin.calendrier-conservation.show', $planClassement->calendrierConservation) }}" 
                           class="btn btn-info">
                            <i class="fas fa-calendar me-2"></i>
                            Voir la règle de conservation
                        </a>
                        
                        <a href="{{ route('admin.calendrier-conservation.edit', $planClassement->calendrierConservation) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i>
                            Modifier la règle
                        </a>
                    @else
                        <a href="{{ route('admin.calendrier-conservation.create') }}?plan_classement_code={{ $planClassement->code_classement }}" 
                           class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>
                            Créer une règle de conservation
                        </a>
                    @endif
                    
                    <button class="btn btn-outline-success" onclick="exportPlanData()">
                        <i class="fas fa-download me-2"></i>
                        Exporter ce plan
                    </button>
                    
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>
                        Imprimer
                    </button>
                    
                    @if(!$planClassement->hasConservationRule())
                        <button class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i>
                            Supprimer ce plan
                        </button>
                    @endif
                </div>
            </div>
        </div>

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
                    
                    @if($planClassement->hasConservationRule())
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Règle de conservation associée</h6>
                                <small class="text-muted">{{ $planClassement->calendrierConservation->created_at->format('d/m/Y à H:i') }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if(!$planClassement->hasConservationRule())
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer le plan <strong>{{ $planClassement->code_classement }}</strong> ?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Attention :</strong> Cette action est irréversible et supprimera définitivement :
                        <ul class="mb-0 mt-2">
                            <li>Le plan de classement</li>
                            <li>Toutes les métadonnées associées</li>
                            <li>L'historique de ce plan</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form action="{{ route('admin.plan-classement.destroy', $planClassement) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>
                            Supprimer définitivement
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
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

    @media print {
        .btn, .card-header, .sidebar, .page-header .btn-group {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .page-title {
            font-size: 1.5rem !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmDelete() {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
    
    function exportPlanData() {
        // Create a form to export this specific plan
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.plan-classement.bulk-action") }}';
        
        // CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Action
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'export';
        form.appendChild(actionInput);
        
        // Plan ID
        const planInput = document.createElement('input');
        planInput.type = 'hidden';
        planInput.name = 'plan_ids[]';
        planInput.value = '{{ $planClassement->id }}';
        form.appendChild(planInput);
        
        document.body.appendChild(form);
        form.submit();
    }

    // Add copy to clipboard functionality for plan code
    document.addEventListener('DOMContentLoaded', function() {
        const planCode = document.querySelector('.badge.bg-primary.fs-6');
        if (planCode) {
            planCode.style.cursor = 'pointer';
            planCode.title = 'Cliquer pour copier';
            
            planCode.addEventListener('click', function() {
                navigator.clipboard.writeText(this.textContent).then(function() {
                    // Show temporary feedback
                    const originalText = planCode.textContent;
                    planCode.textContent = 'Copié!';
                    planCode.classList.add('bg-success');
                    planCode.classList.remove('bg-primary');
                    
                    setTimeout(function() {
                        planCode.textContent = originalText;
                        planCode.classList.remove('bg-success');
                        planCode.classList.add('bg-primary');
                    }, 1500);
                });
            });
        }
    });

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey || e.metaKey) {
            switch(e.key) {
                case 'e':
                    e.preventDefault();
                    window.location.href = '{{ route("admin.plan-classement.edit", $planClassement) }}';
                    break;
                case 'p':
                    e.preventDefault();
                    window.print();
                    break;
                case 'Backspace':
                    e.preventDefault();
                    window.location.href = '{{ route("admin.plan-classement.index") }}';
                    break;
            }
        }
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>
@endpush