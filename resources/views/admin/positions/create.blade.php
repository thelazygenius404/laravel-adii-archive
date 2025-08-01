{{-- resources/views/admin/positions/create.blade.php --}}
@extends('layouts.admin')

@section('title', 'Ajouter une Nouvelle Position')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-plus me-2"></i>
            Ajouter une Nouvelle Position
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
            <button type="button" class="btn btn-info" onclick="showBulkCreate()">
                <i class="fas fa-layer-group me-2"></i>
                Création en lot
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
                    Informations de la Position
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.positions.store') }}" method="POST" id="positionForm">
                    @csrf
                    
                    <!-- Informations de base -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label">Nom de la position <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                   id="nom" name="nom" value="{{ old('nom') }}" required>
                            @error('nom')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Exemple: P001, Position-A-001, etc.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="tablette_id" class="form-label">Tablette <span class="text-danger">*</span></label>
                            <select class="form-select @error('tablette_id') is-invalid @enderror" 
                                    id="tablette_id" name="tablette_id" required>
                                <option value="">Sélectionner une tablette</option>
                                @foreach($tablettes as $tablette)
                                    <option value="{{ $tablette->id }}" 
                                            {{ old('tablette_id', request('tablette_id')) == $tablette->id ? 'selected' : '' }}
                                            data-travee="{{ $tablette->travee->nom }}"
                                            data-salle="{{ $tablette->travee->salle->nom }}"
                                            data-organisme="{{ $tablette->travee->salle->organisme->nom_org }}">
                                        {{ $tablette->nom }} ({{ $tablette->travee->nom }} - {{ $tablette->travee->salle->nom }})
                                    </option>
                                @endforeach
                            </select>
                            @error('tablette_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Statut initial -->
                    <div class="mb-3">
                        <label class="form-label">Statut initial</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="vide" id="vide_true" 
                                           value="1" {{ old('vide', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="vide_true">
                                        <i class="fas fa-circle text-warning me-2"></i>Position libre
                                    </label>
                                    <small class="form-text text-muted d-block">La position est disponible pour recevoir une boîte</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="vide" id="vide_false" 
                                           value="0" {{ old('vide') == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="vide_false">
                                        <i class="fas fa-times-circle text-danger me-2"></i>Position réservée
                                    </label>
                                    <small class="form-text text-muted d-block">La position est réservée ou occupée</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Coordonnées physiques optionnelles -->
                    <hr class="my-4">
                    <h6 class="mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Coordonnées Physiques (Optionnel)
                    </h6>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="niveau" class="form-label">Niveau</label>
                            <input type="text" class="form-control @error('niveau') is-invalid @enderror" 
                                   id="niveau" name="niveau" value="{{ old('niveau') }}" maxlength="10">
                            @error('niveau')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Ex: N1, Haut, Bas</small>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="colonne" class="form-label">Colonne</label>
                            <input type="text" class="form-control @error('colonne') is-invalid @enderror" 
                                   id="colonne" name="colonne" value="{{ old('colonne') }}" maxlength="10">
                            @error('colonne')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Ex: A, B, C ou 1, 2, 3</small>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="rangee" class="form-label">Rangée</label>
                            <input type="text" class="form-control @error('rangee') is-invalid @enderror" 
                                   id="rangee" name="rangee" value="{{ old('rangee') }}" maxlength="10">
                            @error('rangee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Ex: R1, R2, Gauche, Droite</small>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="code_barre" class="form-label">Code-barres</label>
                            <input type="text" class="form-control @error('code_barre') is-invalid @enderror" 
                                   id="code_barre" name="code_barre" value="{{ old('code_barre') }}" maxlength="50">
                            @error('code_barre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Code unique pour la position</small>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Informations complémentaires sur la position</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Créer la position
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="generateNom()">
                            <i class="fas fa-magic me-2"></i>
                            Générer nom
                        </button>
                        <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Aperçu de la position -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-eye me-2"></i>
                    Aperçu de la Position
                </h6>
            </div>
            <div class="card-body">
                <div id="positionPreview">
                    <div class="text-center">
                        <div class="position-visual bg-light p-4 rounded mb-3">
                            <i class="fas fa-map-marker-alt fa-3x text-primary" id="positionIcon"></i>
                            <h5 class="mt-2 mb-0" id="positionName">Position</h5>
                            <small class="text-muted" id="positionStatus">Libre</small>
                        </div>
                    </div>
                    <div id="positionDetails">
                        <!-- Détails générés dynamiquement -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations sur la tablette sélectionnée -->
        <div class="card mt-3" id="tabletteInfo" style="display: none;">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-table me-2"></i>
                    Tablette Sélectionnée
                </h6>
            </div>
            <div class="card-body">
                <div id="tabletteDetails">
                    <!-- Contenu généré dynamiquement -->
                </div>
            </div>
        </div>

        <!-- Guide de nomenclature -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    Guide de Nomenclature
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="text-primary">Exemples de noms :</h6>
                    <ul class="list-unstyled">
                        <li><code>P001</code> - Simple et séquentiel</li>
                        <li><code>A-001</code> - Avec niveau</li>
                        <li><code>T1-E1-P001</code> - Hiérarchique complet</li>
                        <li><code>N1-A-R1-001</code> - Avec coordonnées</li>
                    </ul>
                </div>

                <div class="alert alert-info alert-sm">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>
                        <strong>Conseil :</strong> Utilisez une nomenclature cohérente dans toute votre organisation.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour création en lot -->
<div class="modal fade" id="bulkCreateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-layer-group me-2"></i>
                    Création en Lot de Positions
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="bulkCreateForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bulk_tablette_id" class="form-label">Tablette <span class="text-danger">*</span></label>
                            <select class="form-select" id="bulk_tablette_id" name="tablette_id" required>
                                <option value="">Sélectionner une tablette</option>
                                @foreach($tablettes as $tablette)
                                    <option value="{{ $tablette->id }}">
                                        {{ $tablette->nom }} ({{ $tablette->travee->nom }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="bulk_nombre" class="form-label">Nombre de positions <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="bulk_nombre" name="nombre_positions" 
                                   min="1" max="100" value="10" required>
                            <small class="form-text text-muted">Maximum 100 positions</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bulk_prefix" class="form-label">Préfixe <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="bulk_prefix" name="prefix" 
                                   value="P" maxlength="10" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="bulk_start" class="form-label">Numéro de début</label>
                            <input type="number" class="form-control" id="bulk_start" name="start_number" 
                                   value="1" min="1">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="bulk_zero_pad" name="zero_pad" checked>
                            <label class="form-check-label" for="bulk_zero_pad">
                                Compléter avec des zéros (001, 002, 003...)
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Aperçu des noms :</label>
                        <div class="bg-light p-3 rounded" id="bulkPreview">
                            P001, P002, P003...
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="submitBulkCreate()">
                    <i class="fas fa-save me-2"></i>
                    Créer les positions
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Mise à jour en temps réel de l'aperçu
    function updatePreview() {
        const nom = document.getElementById('nom').value || 'Position';
        const vide = document.querySelector('input[name="vide"]:checked').value;
        const niveau = document.getElementById('niveau').value;
        const colonne = document.getElementById('colonne').value;
        const rangee = document.getElementById('rangee').value;

        // Mise à jour du nom
        document.getElementById('positionName').textContent = nom;

        // Mise à jour du statut et icône
        const icon = document.getElementById('positionIcon');
        const status = document.getElementById('positionStatus');
        
        if (vide === '1') {
            status.textContent = 'Libre';
            status.className = 'text-success';
            icon.className = 'fas fa-map-marker-alt fa-3x text-success';
        } else {
            status.textContent = 'Réservée';
            status.className = 'text-warning';
            icon.className = 'fas fa-map-marker-alt fa-3x text-warning';
        }

        // Mise à jour des détails
        let details = '<div class="row text-center">';
        if (niveau) {
            details += `<div class="col-4"><small class="text-muted">Niveau</small><div class="fw-bold">${niveau}</div></div>`;
        }
        if (colonne) {
            details += `<div class="col-4"><small class="text-muted">Colonne</small><div class="fw-bold">${colonne}</div></div>`;
        }
        if (rangee) {
            details += `<div class="col-4"><small class="text-muted">Rangée</small><div class="fw-bold">${rangee}</div></div>`;
        }
        details += '</div>';
        
        document.getElementById('positionDetails').innerHTML = details;
    }

    // Écouteurs d'événements
    ['nom', 'niveau', 'colonne', 'rangee'].forEach(id => {
        document.getElementById(id).addEventListener('input', updatePreview);
    });

    document.querySelectorAll('input[name="vide"]').forEach(radio => {
        radio.addEventListener('change', updatePreview);
    });

    // Gestion du changement de tablette
    document.getElementById('tablette_id').addEventListener('change', function() {
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            showTabletteInfo(selectedOption);
        } else {
            document.getElementById('tabletteInfo').style.display = 'none';
        }
    });

    function showTabletteInfo(option) {
        const tabletteInfo = document.getElementById('tabletteInfo');
        const tabletteDetails = document.getElementById('tabletteDetails');
        
        tabletteDetails.innerHTML = `
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Travée:</span>
                <span class="fw-bold">${option.dataset.travee}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Salle:</span>
                <span class="fw-bold">${option.dataset.salle}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Organisme:</span>
                <span class="fw-bold">${option.dataset.organisme}</span>
            </div>
        `;
        
        tabletteInfo.style.display = 'block';
    }

    // Générer un nom automatiquement
    function generateNom() {
        const tablette = document.getElementById('tablette_id');
        const niveau = document.getElementById('niveau').value;
        const colonne = document.getElementById('colonne').value;
        const rangee = document.getElementById('rangee').value;
        
        if (!tablette.value) {
            alert('Veuillez d\'abord sélectionner une tablette.');
            return;
        }

        // Logique de génération basée sur les coordonnées
        let nom = 'P';
        if (niveau) nom += niveau + '-';
        if (colonne) nom += colonne + '-';
        if (rangee) nom += rangee + '-';
        
        // Ajouter un numéro séquentiel (simulation)
        nom += '001';
        
        document.getElementById('nom').value = nom;
        updatePreview();
    }

    // Modal de création en lot
    function showBulkCreate() {
        const modal = new bootstrap.Modal(document.getElementById('bulkCreateModal'));
        modal.show();
        updateBulkPreview();
    }

    // Mise à jour de l'aperçu en lot
    function updateBulkPreview() {
        const prefix = document.getElementById('bulk_prefix').value || 'P';
        const nombre = parseInt(document.getElementById('bulk_nombre').value) || 1;
        const start = parseInt(document.getElementById('bulk_start').value) || 1;
        const zeroPad = document.getElementById('bulk_zero_pad').checked;
        
        let preview = '';
        const maxShow = Math.min(nombre, 5);
        
        for (let i = 0; i < maxShow; i++) {
            const num = start + i;
            const numStr = zeroPad ? String(num).padStart(3, '0') : String(num);
            preview += prefix + numStr;
            if (i < maxShow - 1) preview += ', ';
        }
        
        if (nombre > 5) {
            preview += `... (${nombre} positions au total)`;
        }
        
        document.getElementById('bulkPreview').textContent = preview;
    }

    // Écouteurs pour l'aperçu en lot
    ['bulk_prefix', 'bulk_nombre', 'bulk_start'].forEach(id => {
        document.getElementById(id).addEventListener('input', updateBulkPreview);
    });
    document.getElementById('bulk_zero_pad').addEventListener('change', updateBulkPreview);

    // Soumettre la création en lot
    function submitBulkCreate() {
        const form = document.getElementById('bulkCreateForm');
        const formData = new FormData(form);
        
        fetch('{{ route("admin.positions.bulk-create") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`${data.created} position(s) créée(s) avec succès.`);
                bootstrap.Modal.getInstance(document.getElementById('bulkCreateModal')).hide();
                window.location.href = '{{ route("admin.positions.index") }}';
            } else {
                alert('Erreur lors de la création des positions');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la création des positions');
        });
    }

    // Initialiser l'aperçu
    document.addEventListener('DOMContentLoaded', function() {
        updatePreview();
        
        // Si une tablette est pré-sélectionnée
        const tabletteSelect = document.getElementById('tablette_id');
        if (tabletteSelect.value) {
            const selectedOption = tabletteSelect.options[tabletteSelect.selectedIndex];
            showTabletteInfo(selectedOption);
        }
    });
</script>
@endpush

@push('styles')
<style>
    .position-visual {
        transition: all 0.3s ease;
    }

    .position-visual:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .alert-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
    }

    code {
        background-color: #f8f9fa;
        padding: 0.125rem 0.25rem;
        border-radius: 0.25rem;
        font-size: 0.875em;
    }

    .list-unstyled li {
        padding: 0.25rem 0;
    }

    .form-check-label {
        font-weight: 500;
    }
</style>
@endpush