{{-- resources/views/admin/travees/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Créer une Nouvelle Travée')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-plus me-2"></i>
            Créer une Nouvelle Travée
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.travees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
            <button type="button" class="btn btn-info" onclick="showHelp()">
                <i class="fas fa-question-circle me-2"></i>
                Aide
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informations de la Travée
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.travees.store') }}" method="POST" id="traveeForm">
                    @csrf
                    
                    <!-- Informations de base -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom de la travée <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" name="nom" value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Exemple: Travée A, T01, etc.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="salle_id" class="form-label">Salle <span class="text-danger">*</span></label>
                            <select class="form-select @error('salle_id') is-invalid @enderror" 
                                    id="salle_id" name="salle_id" required>
                                <option value="">Sélectionner une salle</option>
                                @foreach($salles as $salle)
                                    <option value="{{ $salle->id }}" {{ old('salle_id') == $salle->id ? 'selected' : '' }}>
                                        {{ $salle->nom }} ({{ $salle->organisme->nom_org }})
                                    </option>
                                @endforeach
                            </select>
                            @error('salle_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Description détaillée de la travée</small>
                    </div>

                    <!-- Configuration de la structure -->
                    <hr class="my-4">
                    <h6 class="mb-3">
                        <i class="fas fa-table me-2"></i>
                        Configuration des Tablettes
                    </h6>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nb_tablettes" class="form-label">Nombre de tablettes <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('nb_tablettes') is-invalid @enderror" 
                                   id="nb_tablettes" name="nb_tablettes" value="{{ old('nb_tablettes', 5) }}" min="1" max="20" required>
                            @error('nb_tablettes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="nb_positions_par_tablette" class="form-label">Positions par tablette <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('nb_positions_par_tablette') is-invalid @enderror" 
                                   id="nb_positions_par_tablette" name="nb_positions_par_tablette" value="{{ old('nb_positions_par_tablette', 10) }}" min="1" max="50" required>
                            @error('nb_positions_par_tablette')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="prefix_tablette" class="form-label">Préfixe des tablettes</label>
                            <input type="text" class="form-control @error('prefix_tablette') is-invalid @enderror" 
                                   id="prefix_tablette" name="prefix_tablette" value="{{ old('prefix_tablette', 'E') }}" maxlength="3">
                            @error('prefix_tablette')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Ex: E → E01, E02...</small>
                        </div>
                    </div>

                    <!-- Options avancées -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="auto_generate_structure" name="auto_generate_structure" 
                               {{ old('auto_generate_structure', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="auto_generate_structure">
                            Générer automatiquement toutes les tablettes et positions
                        </label>
                        <small class="form-text text-muted d-block">
                            Crée automatiquement toutes les tablettes et positions selon la configuration
                        </small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Créer la travée
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="previewStructure()">
                            <i class="fas fa-eye me-2"></i>
                            Prévisualiser
                        </button>
                        <a href="{{ route('admin.travees.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Aperçu de la configuration -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-calculator me-2"></i>
                    Aperçu de la Configuration
                </h6>
            </div>
            <div class="card-body">
                <div id="configPreview">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <div class="bg-light p-3 rounded">
                                <h4 class="text-primary mb-1" id="totalPositions">0</h4>
                                <small class="text-muted">Positions totales</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h6 class="text-success mb-1" id="totalTablettes">0</h6>
                            <small class="text-muted">Tablettes</small>
                        </div>
                        <div class="col-6">
                            <h6 class="text-warning mb-1" id="capaciteEstimee">0</h6>
                            <small class="text-muted">Boîtes</small>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <h6>Exemple de nomenclature :</h6>
                    <div class="bg-light p-2 rounded">
                        <small class="text-muted">
                            <span id="exempleNomenclature">Travée → E01 → P001</span>
                        </small>
                    </div>
                </div>

                <div class="alert alert-info alert-sm">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>
                        <strong>Conseil :</strong> Une travée type contient 5-10 tablettes.
                    </small>
                </div>
            </div>
        </div>

        <!-- Informations sur la salle sélectionnée -->
        <div class="card mt-3" id="salleInfo" style="display: none;">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-home me-2"></i>
                    Salle Sélectionnée
                </h6>
            </div>
            <div class="card-body">
                <div id="salleDetails">
                    <!-- Contenu généré dynamiquement -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de prévisualisation -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>
                    Prévisualisation de la Structure
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="structurePreview">
                    <!-- Contenu généré dynamiquement -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="$('#previewModal').modal('hide');">
                    <i class="fas fa-check me-2"></i>
                    Valider la configuration
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Calcul en temps réel de la configuration
    function updatePreview() {
        const nbTablettes = parseInt(document.getElementById('nb_tablettes').value) || 0;
        const nbPositions = parseInt(document.getElementById('nb_positions_par_tablette').value) || 0;
        
        const totalTablettes = nbTablettes;
        const totalPositions = totalTablettes * nbPositions;
        const capaciteEstimee = Math.floor(totalPositions * 0.8); // 80% d'utilisation estimée

        document.getElementById('totalTablettes').textContent = totalTablettes;
        document.getElementById('totalPositions').textContent = totalPositions;
        document.getElementById('capaciteEstimee').textContent = capaciteEstimee;

        // Mise à jour de l'exemple de nomenclature
        const prefixTablette = document.getElementById('prefix_tablette').value || 'E';
        const nomTravee = document.getElementById('nom').value || 'Travée';

        const exemple = `${nomTravee} → ${prefixTablette}01 → P001`;
        document.getElementById('exempleNomenclature').textContent = exemple;
    }

    // Écouteurs d'événements pour la mise à jour en temps réel
    ['nb_tablettes', 'nb_positions_par_tablette', 'prefix_tablette', 'nom'].forEach(id => {
        document.getElementById(id).addEventListener('input', updatePreview);
    });

    // Gestion du changement de salle
    document.getElementById('salle_id').addEventListener('change', function() {
        if (this.value) {
            // Ici vous pouvez faire un appel AJAX pour récupérer les informations de la salle
            showSalleInfo(this.value);
        } else {
            document.getElementById('salleInfo').style.display = 'none';
        }
    });

    function showSalleInfo(salleId) {
        // Simulation des informations de salle (à remplacer par un appel AJAX réel)
        const salleInfo = document.getElementById('salleInfo');
        const salleDetails = document.getElementById('salleDetails');
        
        salleDetails.innerHTML = `
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Capacité maximale:</span>
                <span class="fw-bold">1000 positions</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Capacité actuelle:</span>
                <span class="fw-bold">650 positions</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Utilisation:</span>
                <span class="fw-bold">65%</span>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-warning" style="width: 65%"></div>
            </div>
        `;
        
        salleInfo.style.display = 'block';
    }

    // Prévisualisation de la structure
    function previewStructure() {
        const nbTablettes = parseInt(document.getElementById('nb_tablettes').value) || 0;
        const nbPositions = parseInt(document.getElementById('nb_positions_par_tablette').value) || 0;
        
        if (nbTablettes === 0 || nbPositions === 0) {
            alert('Veuillez remplir tous les champs de configuration.');
            return;
        }

        generateStructurePreview(nbTablettes, nbPositions);
        
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    }

    // Générer l'aperçu de la structure
    function generateStructurePreview(nbTablettes, nbPositions) {
        const prefixTablette = document.getElementById('prefix_tablette').value || 'E';
        
        let html = '<div class="structure-preview">';
        
        // Limiter l'affichage pour les grandes structures
        const maxDisplay = { tablettes: 5, positions: 5 };
        
        for (let t = 1; t <= Math.min(nbTablettes, maxDisplay.tablettes); t++) {
            html += `
                <div class="tablette mb-3">
                    <h6 class="text-success">
                        <i class="fas fa-table me-2"></i>
                        Tablette ${prefixTablette}${String(t).padStart(2, '0')}
                    </h6>
                    <div class="ms-3">
                        <div class="d-flex flex-wrap gap-1">
            `;
            
            for (let p = 1; p <= Math.min(nbPositions, maxDisplay.positions); p++) {
                html += `
                    <span class="badge bg-light text-dark border">
                        P${String(p).padStart(3, '0')}
                    </span>
                `;
            }
            
            if (nbPositions > maxDisplay.positions) {
                html += `<span class="badge bg-secondary">... +${nbPositions - maxDisplay.positions}</span>`;
            }
            
            html += `
                        </div>
                    </div>
                </div>
            `;
        }
        
        if (nbTablettes > maxDisplay.tablettes) {
            html += `<div class="text-muted"><small>... et ${nbTablettes - maxDisplay.tablettes} autre(s) tablette(s)</small></div>`;
        }
        
        html += '</div>';
        
        document.getElementById('structurePreview').innerHTML = html;
    }

    // Fonction d'aide
    function showHelp() {
        alert('Aide: Une travée est un ensemble de tablettes dans une salle. Configurez le nombre de tablettes et positions selon vos besoins.');
    }

    // Initialiser l'aperçu
    document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endpush

@push('styles')
<style>
    .structure-preview .tablette {
        border-left: 3px solid #28a745;
        padding-left: 15px;
    }

    .alert-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    .card-body h4 {
        font-size: 2rem;
        font-weight: 700;
    }

    .invalid-feedback {
        font-size: 0.875rem;
    }

    .form-text {
        font-size: 0.8rem;
    }
</style>
@endpush