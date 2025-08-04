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
                    
                    <!-- Numéro et codes -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="numero" class="form-label">Numéro <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('numero') is-invalid @enderror" 
                                   id="numero" name="numero" value="{{ old('numero', $nextNumber ?? '') }}" required>
                            @error('numero')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Numéro unique d'identification</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="capacite" class="form-label">Capacité <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('capacite') is-invalid @enderror" 
                                   id="capacite" name="capacite" value="{{ old('capacite', 20) }}" min="1" required>
                            @error('capacite')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Nombre maximum de dossiers</small>
                        </div>
                    </div>

                    <!-- Codes thématique et topographique -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="code_thematique" class="form-label">Code Thématique</label>
                            <input type="text" class="form-control @error('code_thematique') is-invalid @enderror" 
                                   id="code_thematique" name="code_thematique" value="{{ old('code_thematique') }}">
                            @error('code_thematique')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Classification thématique</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="code_topo" class="form-label">Code Topographique</label>
                            <input type="text" class="form-control @error('code_topo') is-invalid @enderror" 
                                   id="code_topo" name="code_topo" value="{{ old('code_topo') }}">
                            @error('code_topo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Classification géographique</small>
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
                                        {{ old('position_id', request('position_id')) == $position->id ? 'selected' : '' }}
                                        data-tablette="{{ $position->tablette->nom }}"
                                        data-travee="{{ $position->tablette->travee->nom }}"
                                        data-salle="{{ $position->tablette->travee->salle->nom }}"
                                        data-organisme="{{ $position->tablette->travee->salle->organisme->nom_org }}">
                                    {{ $position->full_path }}
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
        <!-- Calcul automatique -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    Calcul de Capacité
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-light p-3 rounded">
                        <h4 class="text-primary mb-1" id="capaciteDisplay">20</h4>
                        <small class="text-muted">Dossiers max</small>
                    </div>
                </div>
                
                <div class="mb-2">
                    <span class="text-muted">Occupation initiale :</span>
                    <span class="fw-bold">0 dossiers</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Espace disponible :</span>
                    <span class="fw-bold text-success" id="espaceDisponible">20 dossiers</span>
                </div>
            </div>
        </div>

        <!-- Statistiques des positions -->
        <div class="card mt-3">
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
                    <h6 class="text-primary"><i class="fas fa-check me-2"></i>Capacité</h6>
                    <ul class="list-unstyled mb-0">
                        <li><small>• 15-25 dossiers par boîte standard</small></li>
                        <li><small>• Ajustez selon le type de documents</small></li>
                        <li><small>• Prévoyez de l'espace pour expansion</small></li>
                    </ul>
                </div>

                <div class="mb-3">
                    <h6 class="text-success"><i class="fas fa-check me-2"></i>Codification</h6>
                    <ul class="list-unstyled mb-0">
                        <li><small>• Utilisez des codes cohérents</small></li>
                        <li><small>• Code thématique pour le contenu</small></li>
                        <li><small>• Code topo pour la localisation</small></li>
                    </ul>
                </div>
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
        document.getElementById('capaciteDisplay').textContent = capacite;
        document.getElementById('espaceDisponible').textContent = capacite + ' dossiers';
    });

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
            
            // Simuler les statistiques (vous pouvez faire un appel AJAX pour récupérer les vraies données)
            positionStats.innerHTML = `
                <div class="text-center mb-3">
                    <h2 class="text-success">Libre</h2>
                    <p class="text-muted">Statut actuel</p>
                </div>
                
                <div class="mb-2">
                    <span class="text-muted">Tablette :</span>
                    <span class="fw-bold">${selectedOption.dataset.tablette}</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Travée :</span>
                    <span class="fw-bold">${selectedOption.dataset.travee}</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Salle :</span>
                    <span class="fw-bold">${selectedOption.dataset.salle}</span>
                </div>
                <div class="mb-2">
                    <span class="text-muted">Organisme :</span>
                    <span class="fw-bold">${selectedOption.dataset.organisme}</span>
                </div>
            `;
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
    
    // Si une position est présélectionnée
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
    .alert-info {
        background-color: rgba(13,202,240,0.1);
        border-left: 4px solid #0dcaf0;
    }
    
    .list-unstyled li {
        padding: 0.1rem 0;
    }

    .card-body h4 {
        font-size: 2rem;
        font-weight: 700;
    }
</style>
@endpush