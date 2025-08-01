@extends('layouts.admin')

@section('title', 'Modifier la Tablette')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-edit me-2"></i>
            Modifier la Tablette
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.tablettes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
            <a href="{{ route('admin.tablettes.show', $tablette) }}" class="btn btn-outline-info">
                <i class="fas fa-eye me-2"></i>
                Voir détails
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Modification de la tablette : {{ $tablette->nom }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tablettes.update', $tablette) }}" method="POST" id="tabletteForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Sélection de la travée -->
                    <div class="mb-3">
                        <label for="travee_id" class="form-label">Travée <span class="text-danger">*</span></label>
                        <select class="form-select @error('travee_id') is-invalid @enderror" 
                                id="travee_id" name="travee_id" required onchange="updateTraveeInfo()">
                            <option value="">Sélectionner une travée</option>
                            @foreach($travees as $travee)
                                <option value="{{ $travee->id }}" 
                                        data-salle="{{ $travee->salle->nom }}"
                                        data-organisme="{{ $travee->salle->organisme->nom_org }}"
                                        {{ old('travee_id', $tablette->travee_id) == $travee->id ? 'selected' : '' }}>
                                    {{ $travee->nom }} - {{ $travee->salle->nom }} ({{ $travee->salle->organisme->nom_org }})
                                </option>
                            @endforeach
                        </select>
                        @error('travee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Informations de la travée sélectionnée -->
                    <div class="alert alert-info" id="traveeInfo">
                        <h6><i class="fas fa-info-circle me-2"></i>Informations de la travée</h6>
                        <p class="mb-0">
                            <strong>Salle :</strong> <span id="salleInfo">{{ $tablette->travee->salle->nom }}</span><br>
                            <strong>Organisme :</strong> <span id="organismeInfo">{{ $tablette->travee->salle->organisme->nom_org }}</span>
                        </p>
                    </div>

                    <!-- Nom de la tablette -->
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de la tablette <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                               id="nom" name="nom" value="{{ old('nom', $tablette->nom) }}" 
                               placeholder="Ex: E01, Tablette-A, etc." required>
                        @error('nom')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Utilisez une nomenclature cohérente</small>
                    </div>

                    <!-- Informations sur les positions existantes -->
                    <div class="card border-light mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Informations sur la Tablette
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Date de création :</strong> {{ $tablette->created_at->format('d/m/Y à H:i') }}</p>
                                    <p><strong>Dernière modification :</strong> {{ $tablette->updated_at->format('d/m/Y à H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Nombre de positions :</strong> 
                                        <span class="badge bg-info">{{ $tablette->positions->count() }}</span>
                                    </p>
                                    <p><strong>Positions occupées :</strong> 
                                        <span class="badge bg-success">{{ $tablette->positions->where('vide', false)->count() }}</span>
                                    </p>
                                    <p><strong>Positions libres :</strong> 
                                        <span class="badge bg-warning">{{ $tablette->positions->where('vide', true)->count() }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Warning si changement de travée -->
                    @if($tablette->positions->count() > 0)
                        <div class="alert alert-warning" id="changeWarning" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Attention !</strong> Cette tablette contient {{ $tablette->positions->count() }} position(s). 
                            Le changement de travée peut affecter la logique d'organisation.
                        </div>
                    @endif

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Enregistrer les modifications
                        </button>
                        <a href="{{ route('admin.tablettes.show', $tablette) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye me-2"></i>
                            Voir détails
                        </a>
                        <a href="{{ route('admin.tablettes.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Statistiques -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Statistiques de la Tablette
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h2 class="text-primary">{{ $tablette->positions->count() }}</h2>
                    <small class="text-muted">Positions totales</small>
                </div>
                
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-success">{{ $tablette->positions->where('vide', false)->count() }}</h4>
                        <small class="text-muted">Occupées</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning">{{ $tablette->positions->where('vide', true)->count() }}</h4>
                        <small class="text-muted">Libres</small>
                    </div>
                </div>

                <hr>

                @if($tablette->positions->count() > 0)
                    <div class="mb-2">
                        <span class="text-muted">Taux d'occupation :</span>
                        <span class="fw-bold">{{ number_format($tablette->utilisation_percentage, 1) }}%</span>
                    </div>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-{{ $tablette->utilisation_percentage < 50 ? 'success' : ($tablette->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                             style="width: {{ $tablette->utilisation_percentage }}%"></div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Actions Rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.positions.create') }}?tablette_id={{ $tablette->id }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>
                        Ajouter une position
                    </a>
                    
                    @if($tablette->positions->count() > 0)
                        <a href="{{ route('admin.positions.index') }}?tablette_id={{ $tablette->id }}" class="btn btn-info">
                            <i class="fas fa-list me-2"></i>
                            Voir toutes les positions
                        </a>
                    @endif
                    
                    <button class="btn btn-outline-primary" onclick="viewPositionsList()">
                        <i class="fas fa-map-marked-alt me-2"></i>
                        Plan des positions
                    </button>
                    
                    @if($tablette->positions->count() == 0)
                        <button class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i>
                            Supprimer la tablette
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Positions récentes -->
        @if($tablette->positions->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Positions Récentes
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($tablette->positions->sortByDesc('created_at')->take(5) as $position)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $position->nom }}</strong>
                                <br><small class="text-muted">{{ $position->created_at->format('d/m/Y') }}</small>
                            </div>
                            <div>
                                @if($position->vide)
                                    <span class="badge bg-warning">Libre</span>
                                @else
                                    <span class="badge bg-success">Occupée</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer la tablette <strong>{{ $tablette->nom }}</strong> ?</p>
                <p class="text-danger"><small>Cette action est irréversible.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.tablettes.destroy', $tablette) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal du plan des positions -->
<div class="modal fade" id="positionsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-map-marked-alt me-2"></i>
                    Plan des Positions - {{ $tablette->nom }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @forelse($tablette->positions->sortBy('nom') as $position)
                        <div class="col-md-3 mb-3">
                            <div class="card border-{{ $position->vide ? 'warning' : 'success' }}">
                                <div class="card-body text-center p-2">
                                    <i class="fas fa-map-marker-alt {{ $position->vide ? 'text-warning' : 'text-success' }} fa-2x mb-2"></i>
                                    <h6 class="card-title mb-1">{{ $position->nom }}</h6>
                                    <span class="badge bg-{{ $position->vide ? 'warning' : 'success' }}">
                                        {{ $position->vide ? 'Libre' : 'Occupée' }}
                                    </span>
                                    @if(!$position->vide && $position->boite)
                                        <br><small class="text-muted mt-1">{{ $position->boite->numero }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center">
                            <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune position dans cette tablette</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const originalTraveeId = {{ $tablette->travee_id }};

    // Mise à jour des informations de la travée
    function updateTraveeInfo() {
        const select = document.getElementById('travee_id');
        const selectedOption = select.options[select.selectedIndex];
        const changeWarning = document.getElementById('changeWarning');
        
        if (selectedOption.value) {
            document.getElementById('salleInfo').textContent = selectedOption.dataset.salle;
            document.getElementById('organismeInfo').textContent = selectedOption.dataset.organisme;
            
            // Afficher l'avertissement si changement de travée
            if (selectedOption.value != originalTraveeId && {{ $tablette->positions->count() }} > 0) {
                changeWarning.style.display = 'block';
            } else {
                changeWarning.style.display = 'none';
            }
        }
    }

    // Confirmation de suppression
    function confirmDelete() {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    // Voir la liste des positions
    function viewPositionsList() {
        const modal = new bootstrap.Modal(document.getElementById('positionsModal'));
        modal.show();
    }

    // Validation du formulaire
    document.getElementById('tabletteForm').addEventListener('submit', function(e) {
        const traveeSelect = document.getElementById('travee_id');
        
        if (traveeSelect.value != originalTraveeId && {{ $tablette->positions->count() }} > 0) {
            if (!confirm('Attention ! Le changement de travée peut affecter l\'organisation. Êtes-vous sûr de vouloir continuer ?')) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        updateTraveeInfo();
    });
</script>
@endpush

@push('styles')
<style>
    .progress {
        height: 8px;
    }

    .card-body .card-title {
        font-size: 0.9rem;
    }

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
</style>
@endpush