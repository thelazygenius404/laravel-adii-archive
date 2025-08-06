@extends('layouts.admin')

@section('title', 'Détails de la Règle de Conservation')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-eye me-2"></i>
            Détails de la Règle de Conservation
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.calendrier-conservation.edit', $calendrierConservation) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                Modifier
            </a>
            <a href="{{ route('admin.calendrier-conservation.index') }}" class="btn btn-outline-secondary">
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
                    <i class="fas fa-calendar me-2"></i>
                    Règle {{ $calendrierConservation->plan_classement_code }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Plan de classement :</strong></td>
                                <td>
                                    <span class="badge bg-primary fs-6">{{ $calendrierConservation->plan_classement_code }}</span>
                                    @if($calendrierConservation->planClassement)
                                        <a href="{{ route('admin.plan-classement.show', $calendrierConservation->planClassement) }}" 
                                           class="btn btn-sm btn-outline-primary ms-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Sort final :</strong></td>
                                <td>
                                    <span class="badge {{ $calendrierConservation->sort_final == 'C' ? 'bg-success' : ($calendrierConservation->sort_final == 'D' ? 'bg-danger' : 'bg-warning') }}">
                                        {{ $calendrierConservation->sort_final == 'C' ? 'Conservation' : ($calendrierConservation->sort_final == 'D' ? 'Destruction' : 'Tri') }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Type :</strong></td>
                                <td>
                                    @if($calendrierConservation->principal_secondaire)
                                        <span class="badge {{ $calendrierConservation->principal_secondaire == 'P' ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ $calendrierConservation->principal_secondaire == 'P' ? 'Principal' : 'Secondaire' }}
                                        </span>
                                    @else
                                        <span class="text-muted">Non défini</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Date de création :</strong></td>
                                <td>{{ $calendrierConservation->created_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Délai légal :</strong></td>
                                <td>
                                    <span class="badge bg-dark">{{ $calendrierConservation->delai_legal != '_' ? $calendrierConservation->delai_legal : 'Non défini' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Archive courante :</strong></td>
                                <td><span class="badge bg-info">{{ $calendrierConservation->archives_courantes }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Archive intermédiaire :</strong></td>
                                <td><span class="badge bg-warning">{{ $calendrierConservation->archives_intermediaires }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Dernière modification :</strong></td>
                                <td>{{ $calendrierConservation->updated_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pièces Constituant et Références -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-alt me-2"></i>
                    Pièces Constituant et Références
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Pièces constituant le dossier :</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $calendrierConservation->pieces_constituant ?: 'Non défini' }}</p>
                    </div>
                </div>

                @if($calendrierConservation->reference_juridique)
                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Référence juridique :</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0">{{ $calendrierConservation->reference_juridique }}</p>
                        </div>
                    </div>
                @endif

                @if($calendrierConservation->observation)
                    <div>
                        <h6 class="text-muted mb-2">Observations :</h6>
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0">{{ $calendrierConservation->observation }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Plan de Classement Associé -->
        @if($calendrierConservation->planClassement)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        Plan de Classement Associé
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-1">
                                <span class="badge bg-primary me-2">{{ $calendrierConservation->planClassement->code_classement }}</span>
                                Plan de Classement
                            </h6>
                            <p class="text-muted mb-0">{{ $calendrierConservation->planClassement->objet_classement }}</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('admin.plan-classement.show', $calendrierConservation->planClassement) }}" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>
                                Voir le plan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Analyse des Durées -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Analyse des Durées de Conservation
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center mb-4">
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h4 class="text-dark mb-1">{{ $calendrierConservation->delai_legal != '_' ? $calendrierConservation->delai_legal : 'N/A' }}</h4>
                            <small class="text-muted">Délai légal</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h4 class="text-info mb-1">{{ $calendrierConservation->archives_courantes }}</h4>
                            <small class="text-muted">Archive courante</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border rounded p-3">
                            <h4 class="text-warning mb-1">{{ $calendrierConservation->archives_intermediaires }}</h4>
                            <small class="text-muted">Archive intermédiaire</small>
                        </div>
                    </div>
                </div>

                <!-- Timeline visuelle -->
                <div class="mb-3">
                    <h6 class="text-muted mb-3">Cycle de vie du document :</h6>
                    <div class="timeline-conservation">
                        <div class="timeline-step">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Archive Courante</h6>
                                <small class="text-muted">{{ $calendrierConservation->archives_courantes }} de conservation active</small>
                            </div>
                        </div>
                        <div class="timeline-step">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Archive Intermédiaire</h6>
                                <small class="text-muted">{{ $calendrierConservation->archives_intermediaires }} de conservation semi-active</small>
                            </div>
                        </div>
                        <div class="timeline-step">
                            <div class="timeline-marker {{ $calendrierConservation->sort_final == 'C' ? 'bg-success' : ($calendrierConservation->sort_final == 'D' ? 'bg-danger' : 'bg-warning') }}"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Sort Final</h6>
                                <small class="text-muted">{{ $calendrierConservation->sort_final == 'C' ? 'Conservation' : ($calendrierConservation->sort_final == 'D' ? 'Destruction' : 'Tri') }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Information sur les exigences légales -->
                @if($calendrierConservation->delai_legal && $calendrierConservation->delai_legal != '_')
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Délai légal :</strong> Cette règle est soumise à un délai légal de {{ $calendrierConservation->delai_legal }}.
                        @if($calendrierConservation->reference_juridique)
                            <br><strong>Base légale :</strong> {{ $calendrierConservation->reference_juridique }}
                        @endif
                    </div>
                @endif
            </div>
        </div>
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
                <div class="row text-center">
                    <div class="col-12 mb-3">
                        <div class="border rounded p-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <span class="badge {{ $calendrierConservation->sort_final == 'C' ? 'bg-success' : ($calendrierConservation->sort_final == 'D' ? 'bg-danger' : 'bg-warning') }} fs-5 me-2">
                                    {{ $calendrierConservation->sort_final }}
                                </span>
                                <span class="fw-bold">{{ $calendrierConservation->sort_final == 'C' ? 'Conservation' : ($calendrierConservation->sort_final == 'D' ? 'Destruction' : 'Tri') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Identifiant :</span>
                        <strong>{{ $calendrierConservation->id }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Plan :</span>
                        <strong>{{ $calendrierConservation->plan_classement_code }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Type :</span>
                        <strong>{{ $calendrierConservation->principal_secondaire == 'P' ? 'Principal' : ($calendrierConservation->principal_secondaire == 'S' ? 'Secondaire' : 'Non défini') }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Créée le :</span>
                        <strong>{{ $calendrierConservation->created_at->format('d/m/Y') }}</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Modifiée le :</span>
                        <strong>{{ $calendrierConservation->updated_at->format('d/m/Y') }}</strong>
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
                    <a href="{{ route('admin.calendrier-conservation.edit', $calendrierConservation) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>
                        Modifier cette règle
                    </a>
                    
                    @if($calendrierConservation->planClassement)
                        <a href="{{ route('admin.plan-classement.show', $calendrierConservation->planClassement) }}" 
                           class="btn btn-info">
                            <i class="fas fa-layer-group me-2"></i>
                            Voir le plan de classement
                        </a>
                    @endif
                    
                    <a href="{{ route('admin.calendrier-conservation.index', ['plan_classement' => $calendrierConservation->plan_classement_code]) }}" 
                       class="btn btn-outline-info">
                        <i class="fas fa-list me-2"></i>
                        Autres règles du plan
                    </a>
                    
                    <button class="btn btn-outline-success" onclick="exportRegleData()">
                        <i class="fas fa-download me-2"></i>
                        Exporter cette règle
                    </button>
                    
                    <button class="btn btn-outline-secondary" onclick="printRegle()">
                        <i class="fas fa-print me-2"></i>
                        Imprimer
                    </button>
                    
                    <button class="btn btn-outline-danger" onclick="confirmDelete()">
                        <i class="fas fa-trash me-2"></i>
                        Supprimer cette règle
                    </button>
                </div>
            </div>
        </div>

        <!-- Règles Similaires -->
        @php
            $reglesSimilaires = \App\Models\CalendrierConservation::with('planClassement')
                ->where('plan_classement_code', 'LIKE', substr($calendrierConservation->plan_classement_code, 0, 3) . '%')
                ->where('id', '!=', $calendrierConservation->id)
                ->orderBy('plan_classement_code')
                ->limit(5)
                ->get();
        @endphp

        @if($reglesSimilaires->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-link me-2"></i>
                        Autres Règles de la Catégorie {{ substr($calendrierConservation->plan_classement_code, 0, 3) }}
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($reglesSimilaires as $regle)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <a href="{{ route('admin.calendrier-conservation.show', $regle) }}" 
                                   class="text-decoration-none">
                                    <span class="badge bg-secondary me-1">{{ $regle->plan_classement_code }}</span>
                                    {{ Str::limit($regle->pieces_constituant ?: 'Non défini', 30) }}
                                </a>
                                <div>
                                    <small class="text-muted">
                                        <span class="badge {{ $regle->sort_final == 'C' ? 'bg-success' : ($regle->sort_final == 'D' ? 'bg-danger' : 'bg-warning') }} badge-sm">
                                            {{ $regle->sort_final == 'C' ? 'Conservation' : ($regle->sort_final == 'D' ? 'Destruction' : 'Tri') }}
                                        </span>
                                        {{ $regle->archives_courantes }} + {{ $regle->archives_intermediaires }}
                                    </small>
                                </div>
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('admin.calendrier-conservation.show', $regle) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach

                    @if($reglesSimilaires->count() >= 5)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.calendrier-conservation.index', ['plan_classement' => substr($calendrierConservation->plan_classement_code, 0, 3)]) }}" 
                               class="btn btn-sm btn-outline-primary">
                                Voir toutes les règles
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Métriques de Comparaison -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Comparaison
                </h5>
            </div>
            <div class="card-body">
                @php
                    $category = substr($calendrierConservation->plan_classement_code, 0, 3);
                    $moyenneCategorie = \App\Models\CalendrierConservation::where('plan_classement_code', 'LIKE', $category . '%')
                        ->where('delai_legal', '!=', '_')
                        ->whereNotNull('delai_legal')
                        ->avg('delai_legal');
                    $moyenneGlobale = \App\Models\CalendrierConservation::where('delai_legal', '!=', '_')
                        ->whereNotNull('delai_legal')
                        ->avg('delai_legal');
                @endphp

                @if($calendrierConservation->delai_legal && $calendrierConservation->delai_legal != '_' && is_numeric($calendrierConservation->delai_legal))
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Délai légal par rapport à :</h6>
                        @if($moyenneCategorie)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Moyenne catégorie :</span>
                                <div>
                                    <span class="badge bg-info">{{ number_format($moyenneCategorie, 1) }} ans</span>
                                    @if($calendrierConservation->delai_legal > $moyenneCategorie)
                                        <i class="fas fa-arrow-up text-success ms-1"></i>
                                    @elseif($calendrierConservation->delai_legal < $moyenneCategorie)
                                        <i class="fas fa-arrow-down text-warning ms-1"></i>
                                    @else
                                        <i class="fas fa-equals text-muted ms-1"></i>
                                    @endif
                                </div>
                            </div>
                        @endif
                        @if($moyenneGlobale)
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Moyenne globale :</span>
                                <div>
                                    <span class="badge bg-secondary">{{ number_format($moyenneGlobale, 1) }} ans</span>
                                    @if($calendrierConservation->delai_legal > $moyenneGlobale)
                                        <i class="fas fa-arrow-up text-success ms-1"></i>
                                    @elseif($calendrierConservation->delai_legal < $moyenneGlobale)
                                        <i class="fas fa-arrow-down text-warning ms-1"></i>
                                    @else
                                        <i class="fas fa-equals text-muted ms-1"></i>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                @php
                    $distribution = \App\Models\CalendrierConservation::selectRaw('sort_final, COUNT(*) as count')
                        ->where('plan_classement_code', 'LIKE', $category . '%')
                        ->groupBy('sort_final')
                        ->pluck('count', 'sort_final');
                @endphp

                <div class="mt-3">
                    <h6 class="text-muted mb-2">Distribution des sorts finaux (catégorie {{ $category }}) :</h6>
                    @foreach(['C' => 'Conservation', 'D' => 'Destruction', 'T' => 'Tri'] as $code => $label)
                        @php
                            $count = $distribution->get($code, 0);
                            $badgeClass = match($code) {
                                'C' => 'bg-success',
                                'D' => 'bg-danger',
                                'T' => 'bg-warning',
                            };
                        @endphp
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted">{{ $label }} :</span>
                            <span class="badge {{ $badgeClass }}">{{ $count }}</span>
                        </div>
                    @endforeach
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
                <p>Êtes-vous sûr de vouloir supprimer la règle <strong>{{ $calendrierConservation->plan_classement_code }}</strong> ?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Attention :</strong> Cette action est irréversible et supprimera définitivement :
                    <ul class="mb-0 mt-2">
                        <li>La règle de conservation</li>
                        <li>Toutes les métadonnées associées</li>
                        <li>L'historique de cette règle</li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.calendrier-conservation.destroy', $calendrierConservation) }}" method="POST" class="d-inline">
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
@endsection

@push('styles')
<style>
    .timeline-conservation {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline-step {
        position: relative;
        margin-bottom: 20px;
    }
    
    .timeline-marker {
        position: absolute;
        left: -35px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid white;
    }
    
    .timeline-step:not(:last-child)::before {
        content: '';
        position: absolute;
        left: -30px;
        top: 17px;
        width: 2px;
        height: calc(100% + 5px);
        background-color: #dee2e6;
    }
    
    .timeline-content h6 {
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
        color: #495057;
    }
    
    .badge-sm {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
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
    
    function exportRegleData() {
        // Create a form to export this specific rule
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.calendrier-conservation.bulk-action") }}';
        
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
        
        // Rule ID
        const regleInput = document.createElement('input');
        regleInput.type = 'hidden';
        regleInput.name = 'regle_ids[]';
        regleInput.value = '{{ $calendrierConservation->id }}';
        form.appendChild(regleInput);
        
        document.body.appendChild(form);
        form.submit();
    }
    
    function printRegle() {
        window.print();
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
                    window.location.href = '{{ route("admin.calendrier-conservation.edit", $calendrierConservation) }}';
                    break;
                case 'p':
                    e.preventDefault();
                    printRegle();
                    break;
                case 'Backspace':
                    e.preventDefault();
                    window.location.href = '{{ route("admin.calendrier-conservation.index") }}';
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