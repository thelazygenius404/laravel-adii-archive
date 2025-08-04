@extends('layouts.admin')

@section('title', 'Modifier la Boîte')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-edit me-2"></i>
            Modifier la Boîte : {{ $boite->numero }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.boites.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
            <a href="{{ route('admin.boites.show', $boite) }}" class="btn btn-outline-info">
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
                    <i class="fas fa-edit me-2"></i>
                    Modification de la boîte
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.boites.update', $boite) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Numéro et capacité -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="numero" class="form-label">Numéro <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('numero') is-invalid @enderror" 
                                   id="numero" name="numero" value="{{ old('numero', $boite->numero) }}" required>
                            @error('numero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="capacite" class="form-label">Capacité <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('capacite') is-invalid @enderror" 
                                   id="capacite" name="capacite" value="{{ old('capacite', $boite->capacite) }}" min="1" required>
                            @error('capacite')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Actuel: {{ $boite->nbr_dossiers }} dossiers. 
                                Ne peut pas être inférieur à ce nombre.
                            </small>
                        </div>
                    </div>

                    <!-- Codes thématique et topographique -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="code_thematique" class="form-label">Code Thématique</label>
                            <input type="text" class="form-control @error('code_thematique') is-invalid @enderror" 
                                   id="code_thematique" name="code_thematique" value="{{ old('code_thematique', $boite->code_thematique) }}">
                            @error('code_thematique')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="code_topo" class="form-label">Code Topographique</label>
                            <input type="text" class="form-control @error('code_topo') is-invalid @enderror" 
                                   id="code_topo" name="code_topo" value="{{ old('code_topo', $boite->code_topo) }}">
                            @error('code_topo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Position -->
                    <div class="mb-3">
                        <label for="position_id" class="form-label">Position <span class="text-danger">*</span></label>
                        <select class="form-select @error('position_id') is-invalid @enderror" 
                                id="position_id" name="position_id" required>
                            <option value="">Sélectionner une position</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" 
                                        {{ old('position_id', $boite->position_id) == $position->id ? 'selected' : '' }}
                                        data-tablette="{{ $position->tablette->nom }}"
                                        data-travee="{{ $position->tablette->travee->nom }}"
                                        data-salle="{{ $position->tablette->travee->salle->nom }}"
                                        data-organisme="{{ $position->tablette->travee->salle->organisme->nom_org }}"
                                        @if(!$position->vide && $position->id != $boite->position_id)
                                            data-occupied="true"
                                        @endif>
                                    {{ $position->full_path }}
                                    @if(!$position->vide && $position->id != $boite->position_id)
                                        (Occupée)
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('position_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Informations sur la position sélectionnée -->
                    <div class="alert alert-info" id="positionInfo">
                        <h6><i class="fas fa-info-circle me-2"></i>Informations sur la position</h6>
                        <p class="mb-0">
                            <strong>Tablette :</strong> <span id="tabletteInfo">
                                @if($boite->position)
                                    {{ $boite->position->tablette->nom }}
                                @else
                                    -
                                @endif
                            </span><br>
                            <strong>Travée :</strong> <span id="traveeInfo">
                                @if($boite->position)
                                    {{ $boite->position->tablette->travee->nom }}
                                @else
                                    -
                                @endif
                            </span><br>
                            <strong>Salle :</strong> <span id="salleInfo">
                                @if($boite->position)
                                    {{ $boite->position->tablette->travee->salle->nom }}
                                @else
                                    -
                                @endif
                            </span><br>
                            <strong>Organisme :</strong> <span id="organismeInfo">
                                @if($boite->position)
                                    {{ $boite->position->tablette->travee->salle->organisme->nom_org }}
                                @else
                                    -
                                @endif
                            </span>
                        </p>
                    </div>
                    
                    <!-- Statut -->
                    @if($boite->detruite)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Cette boîte est actuellement marquée comme détruite
                        </div>
                    @endif
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Enregistrer
                        </button>
                        <a href="{{ route('admin.boites.show', $boite) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye me-2"></i>
                            Voir détails
                        </a>
                        <a href="{{ route('admin.boites.index') }}" class="btn btn-outline-secondary">
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
                    Statistiques
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-light p-3 rounded">
                        <h4 class="text-primary mb-1" id="capaciteDisplay">{{ $boite->capacite }}</h4>
                        <small class="text-muted">Capacité</small>
                    </div>
                </div>
                
                <div class="mb-2">
                    <span class="text-muted">Dossiers actuels :</span>
                    <span class="fw-bold">{{ $boite->nbr_dossiers }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Taux d'occupation :</span>
                    <span class="fw-bold">{{ $boite->utilisation_percentage }}%</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Espace restant :</span>
                    <span class="fw-bold text-success" id="espaceDisponible">{{ $boite->capacite_restante }} dossiers</span>
                </div>
                
                <hr>
                
                <div class="mb-2">
                    <span class="text-muted">Date de création :</span>
                    <span class="fw-bold">{{ $boite->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Dernière modification :</span>
                    <span class="fw-bold">{{ $boite->updated_at->format('d/m/Y H:i') }}</span>
                </div>
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
                    @if($boite->position)
                        <a href="{{ route('admin.positions.show', $boite->position) }}" class="btn btn-info">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Voir la position
                        </a>
                    @endif
                    
                    @if(!$boite->detruite && $boite->hasSpace())
                        <a href="{{ route('admin.dossiers.create', ['boite_id' => $boite->id]) }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>
                            Ajouter un dossier
                        </a>
                    @endif
                    
                    @if($boite->detruite)
                        <button class="btn btn-outline-success" onclick="confirmRestore()">
                            <i class="fas fa-trash-restore me-2"></i>
                            Restaurer la boîte
                        </button>
                    @else
                        <button class="btn btn-outline-danger" onclick="confirmDestruction()">
                            <i class="fas fa-trash me-2"></i>
                            Détruire la boîte
                        </button>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Dossiers récents -->
        @if($boite->dossiers->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Dossiers Récents
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($boite->dossiers->sortByDesc('updated_at')->take(3) as $dossier)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong>{{ $dossier->numero }}</strong>
                                <br><small class="text-muted">{{ Str::limit($dossier->titre, 20) }}</small>
                            </div>
                            <div>
                                <span class="badge bg-{{ $dossier->statut === 'elimine' ? 'secondary' : 'success' }}">
                                    {{ $dossier->status_display }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Calcul de capacité -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    Calcul de Capacité
                </h6>
            </div>
            <div class="card-body">
                <div class="progress mb-2">
                    <div class="progress-bar bg-{{ $boite->utilisation_percentage < 50 ? 'success' : ($boite->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                         style="width: {{ $boite->utilisation_percentage }}%"></div>
                </div>
                <small class="text-muted">{{ $boite->utilisation_percentage }}% utilisé</small>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de destruction -->
<div class="modal fade" id="destructionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la destruction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir marquer cette boîte comme détruite ?</p>
                <p class="text-danger"><small>Cette action marquera également tous les dossiers contenus comme éliminés.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="destructionForm" method="POST" action="{{ route('admin.boites.destroy-box', $boite) }}" style="display: none;">
                    @csrf
                    @method('PUT')
                </form>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('destructionForm').submit()">
                    Confirmer la destruction
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de restauration -->
<div class="modal fade" id="restoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la restauration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir restaurer cette boîte ?</p>
                <p class="text-success"><small>Cette action restaurera également tous les dossiers contenus.</small></p>
                @if($boite->position && !$boite->position->vide)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        La position précédente est maintenant occupée. Vous devrez assigner une nouvelle position.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="restoreForm" method="POST" action="{{ route('admin.boites.restore-box', $boite) }}" style="display: none;">
                    @csrf
                    @method('PUT')
                </form>
                <button type="button" class="btn btn-success" onclick="document.getElementById('restoreForm').submit()">
                    Confirmer la restauration
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mise à jour du calcul de capacité
    document.getElementById('capacite').addEventListener('input', function() {
        const capacite = parseInt(this.value) || 0;
        const dossiersActuels = {{ $boite->nbr_dossiers }};
        document.getElementById('capaciteDisplay').textContent = capacite;
        document.getElementById('espaceDisponible').textContent = Math.max(0, capacite - dossiersActuels) + ' dossiers';
        
        // Validation
        if (capacite < dossiersActuels) {
            this.setCustomValidity('La capacité ne peut pas être inférieure au nombre de dossiers actuels (' + dossiersActuels + ')');
        } else {
            this.setCustomValidity('');
        }
    });

    // Mise à jour des informations de la position sélectionnée
    document.getElementById('position_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            document.getElementById('tabletteInfo').textContent = selectedOption.dataset.tablette;
            document.getElementById('traveeInfo').textContent = selectedOption.dataset.travee;
            document.getElementById('salleInfo').textContent = selectedOption.dataset.salle;
            document.getElementById('organismeInfo').textContent = selectedOption.dataset.organisme;
            
            // Supprimer l'ancienne alerte si elle existe
            const existingAlert = document.querySelector('#positionInfo .alert-warning');
            if (existingAlert) {
                existingAlert.remove();
            }
            
            // Si la position est occupée, afficher un avertissement
            if (selectedOption.dataset.occupied && selectedOption.value != "{{ $boite->position_id }}") {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-warning mt-2';
                alertDiv.innerHTML = `
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cette position est déjà occupée par une autre boîte
                `;
                document.getElementById('positionInfo').appendChild(alertDiv);
            }
        }
    });

    // Confirmer la destruction
        function confirmDestruction() {
        const modal = new bootstrap.Modal(document.getElementById('destructionModal'));
        modal.show();
    }

    // Confirmer la restauration
    function confirmRestore() {
        const modal = new bootstrap.Modal(document.getElementById('restoreModal'));
        modal.show();
    }
</script>
@endpush

@push('styles')
<style>
    .alert-warning {
        background-color: rgba(255,193,7,0.1);
        border-left: 4px solid #ffc107;
    }
    
    .progress {
        height: 10px;
        background-color: #e9ecef;
    }
    
    .card-body h4 {
        font-size: 2rem;
        font-weight: 700;
    }
</style>
@endpush