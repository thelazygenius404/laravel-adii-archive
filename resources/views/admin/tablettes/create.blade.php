{{-- resources/views/admin/tablettes/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Créer une Nouvelle Tablette')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-plus me-2"></i>
            Créer une Nouvelle Tablette
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.tablettes.index') }}" class="btn btn-outline-secondary">
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
                    Informations de la Tablette
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.tablettes.store') }}" method="POST" id="tabletteForm">
                    @csrf
                    
                    <!-- Informations de base -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom de la tablette <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" name="nom" value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Exemple: E01, Tablette A, etc.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="travee_id" class="form-label">Travée <span class="text-danger">*</span></label>
                            <select class="form-select @error('travee_id') is-invalid @enderror" 
                                    id="travee_id" name="travee_id" required>
                                <option value="">Sélectionner une travée</option>
                                @foreach($travees as $travee)
                                    <option value="{{ $travee->id }}" 
                                            {{ old('travee_id', request('travee_id')) == $travee->id ? 'selected' : '' }}
                                            data-salle="{{ $travee->salle->nom }}"
                                            data-organisme="{{ $travee->salle->organisme->nom_org }}">
                                        {{ $travee->nom }} ({{ $travee->salle->nom }})
                                    </option>
                                @endforeach
                            </select>
                            @error('travee_id')
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
                        <small class="form-text text-muted">Description détaillée de la tablette</small>
                    </div>

                    <!-- Configuration des positions -->
                    <hr class="my-4">
                    <h6 class="mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Configuration des Positions
                    </h6>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nb_positions" class="form-label">Nombre de positions <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('nb_positions') is-invalid @enderror" 
                                   id="nb_positions" name="nb_positions" value="{{ old('nb_positions', 10) }}" min="1" max="50" required>
                            @error('nb_positions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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

                        <div class="col-md-4 mb-3">
                            <label for="start_number" class="form-label">Numéro de début</label>
                            <input type="number" class="form-control @error('start_number') is-invalid @enderror" 
                                   id="start_number" name="start_number" value="{{ old('start_number', 1) }}" min="1">
                            @error('start_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Premier numéro de position</small>
                        </div>
                    </div>

                    <!-- Type de disposition -->
                    <div class="mb-3">
                        <label class="form-label">Disposition des positions</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="disposition" id="disposition_lineaire" 
                                           value="lineaire" {{ old('disposition', 'lineaire') == 'lineaire' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="disposition_lineaire">
                                        <i class="fas fa-arrows-alt-h me-2"></i>Linéaire
                                    </label>
                                    <small class="form-text text-muted d-block">Positions en ligne (P001, P002, P003...)</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="disposition" id="disposition_double" 
                                           value="double" {{ old('disposition') == 'double' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="disposition_double">
                                        <i class="fas fa-th-large me-2"></i>Double rangée
                                    </label>
                                    <small class="form-text text-muted d-block">Positions sur 2 niveaux (A001, B001...)</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="disposition" id="disposition_personnalise" 
                                           value="personnalise" {{ old('disposition') == 'personnalise' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="disposition_personnalise">
                                        <i class="fas fa-cogs me-2"></i>Personnalisée
                                    </label>
                                    <small class="form-text text-muted d-block">Configuration manuelle</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Options avancées -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="auto_generate_positions" name="auto_generate_positions" 
                               {{ old('auto_generate_positions', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="auto_generate_positions">
                            Générer automatiquement toutes les positions
                        </label>
                        <small class="form-text text-muted d-block">
                            Crée automatiquement toutes les positions selon la configuration
                        </small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Créer la tablette
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="previewPositions()">
                            <i class="fas fa-eye me-2"></i>
                            Prévisualiser
                        </button>
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
                    <div class="text-center mb-3">
                        <div class="bg-light p-3 rounded">
                            <h4 class="text-primary mb-1" id="totalPositions">0</h4>
                            <small class="text-muted">Positions à créer</small>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <h6>Exemple de nomenclature :</h6>
                    <div class="bg-light p-2 rounded">
                        <small class="text-muted">
                            <span id="exempleNomenclature">Tablette → P001</span>
                        </small>
                    </div>
                </div>

                <div class="alert alert-info alert-sm">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>
                        <strong>Conseil :</strong> Une tablette type contient 10-20 positions.
                    </small>
                </div>
            </div>
        </div>

        <!-- Informations sur la travée sélectionnée -->
        <div class="card mt-3" id="traveeInfo" style="display: none;">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-layer-group me-2"></i>
                    Travée Sélectionnée
                </h6>
            </div>
            <div class="card-body">
                <div id="traveeDetails">
                    <!-- Contenu généré dynamiquement -->
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
                        <li><small>• Préfixes courts et logiques</small></li>
                        <li><small>• Numérotation séquentielle</small></li>
                        <li><small>• Cohérence dans l'organisation</small></li>
                    </ul>
                </div>

                <div class="mb-3">
                    <h6 class="text-success"><i class="fas fa-check me-2"></i>Dimensionnement</h6>
                    <ul class="list-unstyled mb-0">
                        <li><small>• 10-20 positions par tablette</small></li>
                        <li><small>• Prévoir de l'espace pour extension</small></li>
                        <li><small>• Tenir compte de l'accès physique</small></li>
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
                    Prévisualisation des Positions
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="positionsPreview">
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
        const nbPositions = parseInt(document.getElementById('nb_positions').value) || 0;
        const prefix = document.getElementById('prefix_position').value || 'P';
        const startNumber = parseInt(document.getElementById('start_number').value) || 1;
        const nom = document.getElementById('nom').value || 'Tablette';

        document.getElementById('totalPositions').textContent = nbPositions;

        // Mise à jour de l'exemple de nomenclature
        const exemple = `${nom} → ${prefix}${String(startNumber).padStart(3, '0')}`;
        document.getElementById('exempleNomenclature').textContent = exemple;
    }

    // Écouteurs d'événements pour la mise à jour en temps réel
    ['nb_positions', 'prefix_position', 'start_number', 'nom'].forEach(id => {
        document.getElementById(id).addEventListener('input', updatePreview);
    });

    // Gestion du changement de travée
    document.getElementById('travee_id').addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            showTraveeInfo(selectedOption);
        } else {
            document.getElementById('traveeInfo').style.display = 'none';
        }
    });

    function showTraveeInfo(option) {
        const traveeInfo = document.getElementById('traveeInfo');
        const traveeDetails = document.getElementById('traveeDetails');
        
        traveeDetails.innerHTML = `
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Salle:</span>
                <span class="fw-bold">${option.dataset.salle}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Organisme:</span>
                <span class="fw-bold">${option.dataset.organisme}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Tablettes existantes:</span>
                <span class="fw-bold">3</span>
            </div>
        `;
        
        traveeInfo.style.display = 'block';
    }

    // Prévisualisation des positions
    function previewPositions() {
        const nbPositions = parseInt(document.getElementById('nb_positions').value) || 0;
        const prefix = document.getElementById('prefix_position').value || 'P';
        const startNumber = parseInt(document.getElementById('start_number').value) || 1;
        const disposition = document.querySelector('input[name="disposition"]:checked').value;
        
        if (nbPositions === 0) {
            alert('Veuillez remplir le nombre de positions.');
            return;
        }

        generatePositionsPreview(nbPositions, prefix, startNumber, disposition);
        
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    }

    // Générer l'aperçu des positions
    function generatePositionsPreview(nbPositions, prefix, startNumber, disposition) {
        let html = '<div class="positions-preview">';
        
        if (disposition === 'lineaire') {
            html += '<h6 class="text-info mb-3">Disposition Linéaire</h6>';
            html += '<div class="d-flex flex-wrap gap-2">';
            
            for (let i = 0; i < Math.min(nbPositions, 20); i++) {
                const posNumber = String(startNumber + i).padStart(3, '0');
                html += `<span class="badge bg-light text-dark border">${prefix}${posNumber}</span>`;
            }
            
            if (nbPositions > 20) {
                html += `<span class="badge bg-secondary">... +${nbPositions - 20}</span>`;
            }
            
            html += '</div>';
        } else if (disposition === 'double') {
            html += '<h6 class="text-info mb-3">Disposition Double Rangée</h6>';
            html += '<div class="row">';
            
            const halfPositions = Math.ceil(nbPositions / 2);
            
            // Rangée A
            html += '<div class="col-6"><h6 class="text-success">Rangée A</h6><div class="d-flex flex-wrap gap-1">';
            for (let i = 0; i < Math.min(halfPositions, 10); i++) {
                const posNumber = String(startNumber + i).padStart(3, '0');
                html += `<span class="badge bg-success text-white">A${posNumber}</span>`;
            }
            html += '</div></div>';
            
            // Rangée B
            html += '<div class="col-6"><h6 class="text-warning">Rangée B</h6><div class="d-flex flex-wrap gap-1">';
            for (let i = 0; i < Math.min(nbPositions - halfPositions, 10); i++) {
                const posNumber = String(startNumber + i).padStart(3, '0');
                html += `<span class="badge bg-warning text-dark">B${posNumber}</span>`;
            }
            html += '</div></div>';
            
            html += '</div>';
        } else {
            html += '<h6 class="text-warning mb-3">Configuration Personnalisée</h6>';
            html += '<p class="text-muted">Les positions seront créées selon vos spécifications personnalisées.</p>';
        }
        
        html += '</div>';
        
        document.getElementById('positionsPreview').innerHTML = html;
    }

    // Fonction d'aide
    function showHelp() {
        alert('Aide: Une tablette est un ensemble de positions dans une travée. Configurez le nombre et la disposition des positions selon vos besoins.');
    }

    // Initialiser l'aperçu
    document.addEventListener('DOMContentLoaded', function() {
        updatePreview();
        
        // Si une travée est pré-sélectionnée (via URL)
        const traveeSelect = document.getElementById('travee_id');
        if (traveeSelect.value) {
            const selectedOption = traveeSelect.options[traveeSelect.selectedIndex];
            showTraveeInfo(selectedOption);
        }
    });
</script>
@endpush

@push('styles')
<style>
    .positions-preview .badge {
        margin: 2px;
        font-size: 0.75rem;
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

    .form-check-label {
        font-weight: 500;
    }

    .list-unstyled li {
        padding: 0.1rem 0;
    }
</style>
@endpush