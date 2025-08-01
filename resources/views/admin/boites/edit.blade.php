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
                    Modification de la boîte
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.boites.update', $boite) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Numéro et référence -->
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
                            <label for="reference" class="form-label">Référence</label>
                            <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                                   id="reference" name="reference" value="{{ old('reference', $boite->reference) }}">
                            @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Position -->
                    <div class="mb-3">
                        <label for="position_id" class="form-label">Position</label>
                        <select class="form-select @error('position_id') is-invalid @enderror" 
                                id="position_id" name="position_id">
                            <option value="">Sélectionner une position</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" 
                                        {{ old('position_id', $boite->position_id) == $position->id ? 'selected' : '' }}
                                        data-tablette="{{ $position->tablette->nom }}"
                                        data-travee="{{ $position->tablette->travee->nom }}"
                                        data-salle="{{ $position->tablette->travee->salle->nom }}"
                                        data-organisme="{{ $position->tablette->travee->salle->organisme->nom_org }}">
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
                    
                    <!-- Dates -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_archivage" class="form-label">Date d'archivage <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_archivage') is-invalid @enderror" 
                                   id="date_archivage" name="date_archivage" 
                                   value="{{ old('date_archivage', $boite->date_archivage->format('Y-m-d')) }}" required>
                            @error('date_archivage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_elimination" class="form-label">Date d'élimination prévue</label>
                            <input type="date" class="form-control @error('date_elimination') is-invalid @enderror" 
                                   id="date_elimination" name="date_elimination" 
                                   value="{{ old('date_elimination', $boite->date_elimination ? $boite->date_elimination->format('Y-m-d') : '') }}">
                            @error('date_elimination')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description', $boite->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Statut -->
                    @if($boite->elimine)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Cette boîte est actuellement marquée comme éliminée
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
                    <h2 class="text-primary">{{ $boite->dossiers->count() }}</h2>
                    <p class="text-muted">Dossiers dans cette boîte</p>
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
                
                @if($boite->position)
                    <hr>
                    
                    <div class="mb-2">
                        <span class="text-muted">Position actuelle :</span>
                        <span class="fw-bold">{{ $boite->position->full_path }}</span>
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
                    @if($boite->position)
                        <a href="{{ route('admin.positions.show', $boite->position) }}" class="btn btn-info">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Voir la position
                        </a>
                    @endif
                    
                    <a href="{{ route('admin.dossiers.create', ['boite_id' => $boite->id]) }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>
                        Ajouter un dossier
                    </a>
                    
                    @if($boite->elimine)
                        <a href="{{ route('admin.boites.restore-box', $boite) }}" class="btn btn-outline-success">
                            <i class="fas fa-trash-restore me-2"></i>
                            Restaurer la boîte
                        </a>
                    @else
                        <a href="{{ route('admin.boites.destroy-box', $boite) }}" class="btn btn-outline-danger"
                           onclick="return confirm('Êtes-vous sûr de vouloir marquer cette boîte comme éliminée ?')">
                            <i class="fas fa-trash me-2"></i>
                            Éliminer la boîte
                        </a>
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
                                <strong>{{ $dossier->reference }}</strong>
                                <br><small class="text-muted">{{ Str::limit($dossier->titre, 20) }}</small>
                            </div>
                            <div>
                                @if($dossier->elimine)
                                    <span class="badge bg-secondary">Éliminé</span>
                                @else
                                    <span class="badge bg-success">Actif</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mise à jour des informations de la position sélectionnée
    document.getElementById('position_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            document.getElementById('tabletteInfo').textContent = selectedOption.dataset.tablette;
            document.getElementById('traveeInfo').textContent = selectedOption.dataset.travee;
            document.getElementById('salleInfo').textContent = selectedOption.dataset.salle;
            document.getElementById('organismeInfo').textContent = selectedOption.dataset.organisme;
            
            // Si la position est occupée, afficher un avertissement
            if (selectedOption.text.includes('Occupée') && selectedOption.value != "{{ $boite->position_id }}") {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-warning mt-2';
                alertDiv.innerHTML = `
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Cette position est déjà occupée par une autre boîte
                `;
                
                // Vérifier si l'alerte existe déjà
                if (!document.querySelector('#positionInfo .alert-warning')) {
                    document.getElementById('positionInfo').appendChild(alertDiv);
                }
            } else {
                const existingAlert = document.querySelector('#positionInfo .alert-warning');
                if (existingAlert) {
                    existingAlert.remove();
                }
            }
        }
    });
</script>
@endpush

@push('styles')
<style>
    .alert-warning {
        background-color: rgba(255,193,7,0.1);
        border-left: 4px solid #ffc107;
    }
</style>
@endpush