@extends('layouts.admin')

@section('title', 'Créer une Nouvelle Boîte')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-plus me-2"></i>
            Créer une Nouvelle Boîte
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.boites.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informations de la Boîte
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.boites.store') }}" method="POST">
                    @csrf
                    
                    <!-- Numéro et référence -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="numero" class="form-label">Numéro <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('numero') is-invalid @enderror" 
                                   id="numero" name="numero" value="{{ old('numero') }}" required>
                            @error('numero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Numéro unique d'identification</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="reference" class="form-label">Référence</label>
                            <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                                   id="reference" name="reference" value="{{ old('reference') }}">
                            @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Référence complémentaire</small>
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
                                        {{ old('position_id', request('position_id')) == $position->id ? 'selected' : '' }}
                                        data-tablette="{{ $position->tablette->nom }}"
                                        data-travee="{{ $position->tablette->travee->nom }}"
                                        data-salle="{{ $position->tablette->travee->salle->nom }}"
                                        data-organisme="{{ $position->tablette->travee->salle->organisme->nom_org }}">
                                    {{ $position->full_path }}
                                    @if(!$position->vide)
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
                    <div class="alert alert-info" id="positionInfo" style="display: none;">
                        <h6><i class="fas fa-info-circle me-2"></i>Informations sur la position</h6>
                        <p class="mb-0">
                            <strong>Tablette :</strong> <span id="tabletteInfo"></span><br>
                            <strong>Travée :</strong> <span id="traveeInfo"></span><br>
                            <strong>Salle :</strong> <span id="salleInfo"></span><br>
                            <strong>Organisme :</strong> <span id="organismeInfo"></span>
                        </p>
                    </div>
                    
                    <!-- Dates -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_archivage" class="form-label">Date d'archivage <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_archivage') is-invalid @enderror" 
                                   id="date_archivage" name="date_archivage" value="{{ old('date_archivage', date('Y-m-d')) }}" required>
                            @error('date_archivage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_elimination" class="form-label">Date d'élimination prévue</label>
                            <input type="date" class="form-control @error('date_elimination') is-invalid @enderror" 
                                   id="date_elimination" name="date_elimination" value="{{ old('date_elimination') }}">
                            @error('date_elimination')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Créer la boîte
                        </button>
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
        <!-- Statistiques des positions -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Statistiques des Positions
                </h6>
            </div>
            <div class="card-body" id="positionStats">
                <div class="text-center py-5">
                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Sélectionnez une position pour voir ses statistiques</p>
                </div>
            </div>
        </div>
        
        <!-- Bonnes pratiques -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    Bonnes Pratiques
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-primary"><i class="fas fa-check me-2"></i>Numérotation</h6>
                    <ul class="list-unstyled mb-0">
                        <li><small>• Utilisez un système de numérotation cohérent</small></li>
                        <li><small>• Évitez les caractères spéciaux</small></li>
                        <li><small>• Gardez les numéros aussi courts que possible</small></li>
                    </ul>
                </div>

                <div class="mb-3">
                    <h6 class="text-success"><i class="fas fa-check me-2"></i>Archivage</h6>
                    <ul class="list-unstyled mb-0">
                        <li><small>• Vérifiez la position avant archivage</small></li>
                        <li><small>• Saisissez une date d'élimination si connue</small></li>
                        <li><small>• Ajoutez une description claire</small></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mise à jour des informations de la position sélectionnée
    document.getElementById('position_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const positionInfo = document.getElementById('positionInfo');
        const positionStats = document.getElementById('positionStats');
        
        if (selectedOption.value) {
            // Afficher les informations de base
            document.getElementById('tabletteInfo').textContent = selectedOption.dataset.tablette;
            document.getElementById('traveeInfo').textContent = selectedOption.dataset.travee;
            document.getElementById('salleInfo').textContent = selectedOption.dataset.salle;
            document.getElementById('organismeInfo').textContent = selectedOption.dataset.organisme;
            positionInfo.style.display = 'block';
            
            // Si la position est occupée, afficher un avertissement
            if (selectedOption.text.includes('Occupée')) {
                positionInfo.innerHTML += `
                    <div class="alert alert-warning mt-2">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Cette position est déjà occupée par une autre boîte
                    </div>
                `;
            }
            
            // Charger les statistiques via AJAX
            fetch(`/api/positions/${selectedOption.value}/stats`)
                .then(response => response.json())
                .then(data => {
                    positionStats.innerHTML = `
                        <div class="text-center mb-3">
                            <h2 class="text-primary">${data.boite ? data.boite.numero : 'Libre'}</h2>
                            <p class="text-muted">Statut actuel</p>
                        </div>
                        
                        <div class="mb-2">
                            <span class="text-muted">Dernière modification :</span>
                            <span class="fw-bold">${data.last_updated || '-'}</span>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-2">
                            <span class="text-muted">Tablette :</span>
                            <span class="fw-bold">${data.tablette || '-'}</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted">Travée :</span>
                            <span class="fw-bold">${data.travee || '-'}</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted">Salle :</span>
                            <span class="fw-bold">${data.salle || '-'}</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted">Organisme :</span>
                            <span class="fw-bold">${data.organisme || '-'}</span>
                        </div>
                    `;
                });
        } else {
            positionInfo.style.display = 'none';
            positionStats.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Sélectionnez une position pour voir ses statistiques</p>
                </div>
            `;
        }
    });
    
    // Si une position est présélectionnée (via query string)
    document.addEventListener('DOMContentLoaded', function() {
        const positionSelect = document.getElementById('position_id');
        if (positionSelect.value) {
            positionSelect.dispatchEvent(new Event('change'));
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
    
    .list-unstyled li {
        padding: 0.1rem 0;
    }
</style>
@endpush