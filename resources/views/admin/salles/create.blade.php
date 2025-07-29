{{-- resources/views/admin/salles/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Créer une Nouvelle Salle')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-plus me-2"></i>
            Créer une Nouvelle Salle
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.salles.index') }}" class="btn btn-outline-secondary">
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
                    Informations de la Salle
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.salles.store') }}" method="POST" id="salleForm">
                    @csrf
                    
                    <!-- Informations de base -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom de la salle <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" name="nom" value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Exemple: Salle A, Dépôt Principal, etc.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="organisme_id" class="form-label">Organisme <span class="text-danger">*</span></label>
                            <select class="form-select @error('organisme_id') is-invalid @enderror" 
                                    id="organisme_id" name="organisme_id" required>
                                <option value="">Sélectionner un organisme</option>
                                @foreach($organismes as $organisme)
                                    <option value="{{ $organisme->id }}" {{ old('organisme_id') == $organisme->id ? 'selected' : '' }}>
                                        {{ $organisme->nom_org }}
                                    </option>
                                @endforeach
                            </select>
                            @error('organisme_id')
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
                        <small class="form-text text-muted">Description détaillée de la salle et de son usage</small>
                    </div>

                    <!-- Localisation -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <input type="text" class="form-control @error('adresse') is-invalid @enderror" 
                                   id="adresse" name="adresse" value="{{ old('adresse') }}">
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="batiment" class="form-label">Bâtiment</label>
                            <input type="text" class="form-control @error('batiment') is-invalid @enderror" 
                                   id="batiment" name="batiment" value="{{ old('batiment') }}">
                            @error('batiment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="etage" class="form-label">Étage</label>
                            <input type="text" class="form-control @error('etage') is-invalid @enderror" 
                                   id="etage" name="etage" value="{{ old('etage') }}">
                            @error('etage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Configuration de la structure -->
                    <hr class="my-4">
                    <h6 class="mb-3">
                        <i class="fas fa-sitemap me-2"></i>
                        Configuration de la Structure
                    </h6>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nb_travees" class="form-label">Nombre de travées <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('nb_travees') is-invalid @enderror" 
                                   id="nb_travees" name="nb_travees" value="{{ old('nb_travees', 1) }}" min="1" max="50" required>
                            @error('nb_travees')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="nb_tablettes_par_travee" class="form-label">Tablettes par travée <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('nb_tablettes_par_travee') is-invalid @enderror" 
                                   id="nb_tablettes_par_travee" name="nb_tablettes_par_travee" value="{{ old('nb_tablettes_par_travee', 5) }}" min="1" max="20" required>
                            @error('nb_tablettes_par_travee')
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
                    </div>

                    <!-- Nomenclature -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="prefix_travee" class="form-label">Préfixe des travées</label>
                            <input type="text" class="form-control @error('prefix_travee') is-invalid @enderror" 
                                   id="prefix_travee" name="prefix_travee" value="{{ old('prefix_travee', 'T') }}" maxlength="3">
                            @error('prefix_travee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Ex: T → T01, T02...</small>
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

                        <div class="col-md-4 mb-3">
                            <label for="prefix_position" class="form-label">Préfixe des positions</label>
                            <input type="text" class="form-control @error('prefix_position') is-invalid @enderror" 
                                   id="prefix_position" name="prefix_position" value="{{ old('prefix_position', 'P') }}" maxlength="3">
                            @error('prefix_position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Ex: P → P001, P002...</small>
                        </div>
                    </div>

                    <!-- Options avancées -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="auto_generate_structure" name="auto_generate_structure" 
                               {{ old('auto_generate_structure', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="auto_generate_structure">
                            Générer automatiquement la structure complète
                        </label>
                        <small class="form-text text-muted d-block">
                            Crée automatiquement toutes les travées, tablettes et positions selon la configuration
                        </small>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="actif" name="actif" 
                               {{ old('actif', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="actif">
                            Salle active
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Créer la salle
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="previewStructure()">
                            <i class="fas fa-eye me-2"></i>
                            Prévisualiser
                        </button>
                        <a href="{{ route('admin.salles.index') }}" class="btn btn-outline-secondary">
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
                        <div class="col-4">
                            <h6 class="text-info mb-1" id="totalTravees">0</h6>
                            <small class="text-muted">Travées</small>
                        </div>
                        <div class="col-4">
                            <h6 class="text-success mb-1" id="totalTablettes">0</h6>
                            <small class="text-muted">Tablettes</small>
                        </div>
                        <div class="col-4">
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
                            <span id="exempleNomenclature">Salle → T01 → E01 → P001</span>
                        </small>
                    </div>
                </div>

                <div class="alert alert-info alert-sm">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>
                        <strong>Conseil :</strong> Commencez par une structure simple que vous pourrez étendre plus tard.
                    </small>
                </div>
            </div>
        </div>

        <!-- Guide de bonnes pratiques -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    Bonnes Pratiques
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-primary"><i class="fas fa-check me-2"></i>Nomenclature</h6>
                    <ul class="list-unstyled mb-0">
                        <li><small>• Utilisez des préfixes courts et clairs</small></li>
                        <li><small>• Prévoyez de l'espace pour l'expansion</small></li>
                        <li><small>• Restez cohérent dans votre organisation</small></li>
                    </ul>
                </div>

                <div class="mb-3">
                    <h6 class="text-success"><i class="fas fa-check me-2"></i>Dimensionnement</h6>
                    <ul class="list-unstyled mb-0">
                        <li><small>• 5-10 tablettes par travée optimal</small></li>
                        <li><small>• 10-20 positions par tablette recommandé</small></li>
                        <li><small>• Laissez 10-15% d'espace libre</small></li>
                    </ul>
                </div>

                <div>
                    <h6 class="text-info"><i class="fas fa-check me-2"></i>Organisation</h6>
                    <ul class="list-unstyled mb-0">
                        <li><small>• Groupez par organisme si possible</small></li>
                        <li><small>• Prévoyez les zones d'accès fréquent</small></li>
                        <li><small>• Documentez votre organisation</small></li>
                    </ul>
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

<!-- Modal d'aide -->
<div class="modal fade" id="helpModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-question-circle me-2"></i>
                    Guide de Création d'une Salle
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary">1. Informations de base</h6>
                        <p class="text-muted">
                            Définissez le nom de la salle et associez-la à un organisme. Le nom doit être unique et descriptif.
                        </p>

                        <h6 class="text-primary">2. Localisation</h6>
                        <p class="text-muted">
                            Renseignez l'adresse physique pour faciliter la localisation des documents.
                        </p>

                        <h6 class="text-primary">3. Structure hiérarchique</h6>
                        <p class="text-muted">
                            La structure suit l'ordre : Salle → Travée → Tablette → Position. Chaque position peut contenir une boîte.
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success">Exemples de configuration</h6>
                        
                        <div class="card mb-2">
                            <div class="card-body p-2">
                                <h6 class="card-title h6">Petite salle</h6>
                                <small class="text-muted">
                                    3 travées × 5 tablettes × 10 positions = 150 positions
                                </small>
                            </div>
                        </div>

                        <div class="card mb-2">
                            <div class="card-body p-2">
                                <h6 class="card-title h6">Salle moyenne</h6>
                                <small class="text-muted">
                                    5 travées × 7 tablettes × 15 positions = 525 positions
                                </small>
                            </div>
                        </div>

                        <div class="card mb-2">
                            <div class="card-body p-2">
                                <h6 class="card-title h6">Grande salle</h6>
                                <small class="text-muted">
                                    10 travées × 10 tablettes × 20 positions = 2000 positions
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Calcul en temps réel de la configuration
    function updatePreview() {
        const nbTravees = parseInt(document.getElementById('nb_travees').value) || 0;
        const nbTablettes = parseInt(document.getElementById('nb_tablettes_par_travee').value) || 0;
        const nbPositions = parseInt(document.getElementById('nb_positions_par_tablette').value) || 0;
        
        const totalTravees = nbTravees;
        const totalTablettes = nbTravees * nbTablettes;
        const totalPositions = totalTravees * totalTablettes * nbPositions;
        const capaciteEstimee = Math.floor(totalPositions * 0.8); // 80% d'utilisation estimée

        document.getElementById('totalTravees').textContent = totalTravees;
        document.getElementById('totalTablettes').textContent = totalTablettes;
        document.getElementById('totalPositions').textContent = totalPositions;
        document.getElementById('capaciteEstimee').textContent = capaciteEstimee;

        // Mise à jour de l'exemple de nomenclature
        const prefixTravee = document.getElementById('prefix_travee').value || 'T';
        const prefixTablette = document.getElementById('prefix_tablette').value || 'E';
        const prefixPosition = document.getElementById('prefix_position').value || 'P';
        const nomSalle = document.getElementById('nom').value || 'Salle';

        const exemple = `${nomSalle} → ${prefixTravee}01 → ${prefixTablette}01 → ${prefixPosition}001`;
        document.getElementById('exempleNomenclature').textContent = exemple;
    }

    // Écouteurs d'événements pour la mise à jour en temps réel
    ['nb_travees', 'nb_tablettes_par_travee', 'nb_positions_par_tablette', 
     'prefix_travee', 'prefix_tablette', 'prefix_position', 'nom'].forEach(id => {
        document.getElementById(id).addEventListener('input', updatePreview);
    });

    // Initialiser l'aperçu
    document.addEventListener('DOMContentLoaded', updatePreview);

    // Prévisualisation de la structure
    function previewStructure() {
        const nbTravees = parseInt(document.getElementById('nb_travees').value) || 0;
        const nbTablettes = parseInt(document.getElementById('nb_tablettes_par_travee').value) || 0;
        const nbPositions = parseInt(document.getElementById('nb_positions_par_tablette').value) || 0;
        
        if (nbTravees === 0 || nbTablettes === 0 || nbPositions === 0) {
            alert('Veuillez remplir tous les champs de configuration.');
            return;
        }

        if (nbTravees * nbTablettes * nbPositions > 1000) {
            if (!confirm('Cette configuration génèrera plus de 1000 positions. Êtes-vous sûr de vouloir continuer ?')) {
                return;
            }
        }

        generateStructurePreview(nbTravees, nbTablettes, nbPositions);
        
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    }

    // Générer l'aperçu de la structure
    function generateStructurePreview(nbTravees, nbTablettes, nbPositions) {
        const prefixTravee = document.getElementById('prefix_travee').value || 'T';
        const prefixTablette = document.getElementById('prefix_tablette').value || 'E';
        const prefixPosition = document.getElementById('prefix_position').value || 'P';
        
        let html = '<div class="structure-preview">';
        
        // Limiter l'affichage pour les grandes structures
        const maxDisplay = { travees: 3, tablettes: 3, positions: 5 };
        
        for (let t = 1; t <= Math.min(nbTravees, maxDisplay.travees); t++) {
            html += `
                <div class="travee mb-3">
                    <h6 class="text-primary">
                        <i class="fas fa-layer-group me-2"></i>
                        Travée ${prefixTravee}${String(t).padStart(2, '0')}
                    </h6>
                    <div class="ms-3">
            `;
            
            for (let ta = 1; ta <= Math.min(nbTablettes, maxDisplay.tablettes); ta++) {
                html += `
                    <div class="tablette mb-2">
                        <h6 class="text-success mb-1">
                            <i class="fas fa-table me-2"></i>
                            Tablette ${prefixTablette}${String(ta).padStart(2, '0')}
                        </h6>
                        <div class="ms-3">
                            <div class="d-flex flex-wrap gap-1">
                `;
                
                for (let p = 1; p <= Math.min(nbPositions, maxDisplay.positions); p++) {
                    html += `
                        <span class="badge bg-light text-dark border">
                            ${prefixPosition}${String(p).padStart(3, '0')}
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
            
            html += `
                    </div>
                </div>
            `;
        }
        
        if (nbTravees > maxDisplay.travees) {
            html += `<div class="text-muted"><small>... et ${nbTravees - maxDisplay.travees} autre(s) travée(s)</small></div>`;
        }
        
        html += '</div>';
        
        document.getElementById('structurePreview').innerHTML = html;
    }

    // Afficher l'aide
    function showHelp() {
        const modal = new bootstrap.Modal(document.getElementById('helpModal'));
        modal.show();
    }

    // Validation du formulaire
    document.getElementById('salleForm').addEventListener('submit', function(e) {
        const nbTravees = parseInt(document.getElementById('nb_travees').value);
        const nbTablettes = parseInt(document.getElementById('nb_tablettes_par_travee').value);
        const nbPositions = parseInt(document.getElementById('nb_positions_par_tablette').value);
        
        const totalPositions = nbTravees * nbTablettes * nbPositions;
        
        if (totalPositions > 5000) {
            e.preventDefault();
            alert('Cette configuration génèrerait plus de 5000 positions, ce qui pourrait affecter les performances. Veuillez réduire la taille de la structure.');
            return false;
        }
        
        if (totalPositions < 10) {
            e.preventDefault();
            alert('Cette configuration génèrerait moins de 10 positions, ce qui semble insuffisant. Veuillez augmenter la taille de la structure.');
            return false;
        }
    });

    // Auto-complétion basée sur l'organisme sélectionné
    document.getElementById('organisme_id').addEventListener('change', function() {
        if (this.value && !document.getElementById('nom').value) {
            const organismeText = this.options[this.selectedIndex].text;
            document.getElementById('nom').value = `Salle ${organismeText}`;
            updatePreview();
        }
    });
</script>
@endpush

@push('styles')
<style>
    .form-label {
        font-weight: 600;
        color: #495057;
    }

    .alert-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .structure-preview .travee {
        border-left: 3px solid #007bff;
        padding-left: 15px;
    }

    .structure-preview .tablette {
        border-left: 2px solid #28a745;
        padding-left: 10px;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    .card-body h4 {
        font-size: 2rem;
        font-weight: 700;
    }

    .card-body h6 {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .invalid-feedback {
        font-size: 0.875rem;
    }

    .form-text {
        font-size: 0.8rem;
    }

    .badge {
        font-size: 0.75em;
    }

    .list-unstyled li {
        padding: 0.1rem 0;
    }
</style>
@endpush