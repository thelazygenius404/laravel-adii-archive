@extends('layouts.admin')

@section('title', 'Ajouter une Entité Productrice')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-plus me-2"></i>
            Ajouter une Entité Productrice
        </h1>
        <a href="{{ route('admin.entites.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour à la liste
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.entites.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom_entite" class="form-label">
                                    Nom de l'Entité <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nom_entite') is-invalid @enderror" 
                                       id="nom_entite" 
                                       name="nom_entite" 
                                       value="{{ old('nom_entite') }}" 
                                       placeholder="Ex: Direction des Opérations"
                                       required>
                                @error('nom_entite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Veuillez remplir ce champ.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="code_entite" class="form-label">
                                    Code de l'Entité <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('code_entite') is-invalid @enderror" 
                                       id="code_entite" 
                                       name="code_entite" 
                                       value="{{ old('code_entite') }}" 
                                       placeholder="Ex: ADII-DOD"
                                       required>
                                @error('code_entite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Code unique pour identifier l'entité.</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="id_organisme" class="form-label">
                                    Organisme <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('id_organisme') is-invalid @enderror" 
                                        id="id_organisme" 
                                        name="id_organisme" 
                                        required>
                                    <option value="">Sélectionner un organisme</option>
                                    @foreach($organismes as $organisme)
                                        <option value="{{ $organisme->id }}" {{ old('id_organisme') == $organisme->id ? 'selected' : '' }}>
                                            {{ $organisme->nom_org }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_organisme')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Organisme auquel appartient cette entité.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="entite_parent" class="form-label">
                                    Entité Parent
                                </label>
                                <select class="form-select @error('entite_parent') is-invalid @enderror" 
                                        id="entite_parent" 
                                        name="entite_parent">
                                    <option value="">Aucune (Entité racine)</option>
                                    @foreach($parentEntites as $parent)
                                        <option value="{{ $parent->id }}" 
                                                data-organisme="{{ $parent->id_organisme }}"
                                                {{ old('entite_parent') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->nom_entite }} ({{ $parent->organisme->nom_org }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('entite_parent')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Entité parent dans la hiérarchie (optionnel).</div>
                            </div>
                        </div>
                    </div>

                    <!-- Hierarchy Preview -->
                    <div class="card border-info mb-3" id="hierarchy-preview" style="display: none;">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-sitemap me-2"></i>
                                Aperçu de la Hiérarchie
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="hierarchy-path" class="text-muted"></div>
                        </div>
                    </div>

                    <!-- Auto-generated Code Preview -->
                    <div class="card border-secondary mb-3" id="code-preview" style="display: none;">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-code me-2"></i>
                                Suggestion de Code
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">Code suggéré basé sur vos sélections :</p>
                            <code id="suggested-code" class="bg-light p-2 rounded d-block"></code>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="useSuggestedCode()">
                                <i class="fas fa-copy me-1"></i>Utiliser ce code
                            </button>
                        </div>
                    </div>

                    <!-- Common Entity Templates -->
                    <div class="card border-warning mb-3">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0">
                                <i class="fas fa-lightbulb me-2"></i>
                                Modèles d'Entités Courantes
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">Cliquez sur un modèle pour remplir automatiquement les champs :</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action" onclick="fillTemplate('Direction Générale', 'DG')">
                                            Direction Générale
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action" onclick="fillTemplate('Direction des Opérations', 'DO')">
                                            Direction des Opérations
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action" onclick="fillTemplate('Direction du Contrôle', 'DC')">
                                            Direction du Contrôle
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action" onclick="fillTemplate('Direction Administrative', 'DA')">
                                            Direction Administrative
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action" onclick="fillTemplate('Service Transit', 'ST')">
                                            Service Transit
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action" onclick="fillTemplate('Service Dédouanement', 'SD')">
                                            Service Dédouanement
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action" onclick="fillTemplate('Brigade Mobile', 'BM')">
                                            Brigade Mobile
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action" onclick="fillTemplate('Service Informatique', 'SI')">
                                            Service Informatique
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.entites.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>
                            Créer l'Entité
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const organismeSelect = document.getElementById('id_organisme');
        const parentSelect = document.getElementById('entite_parent');
        const nomEntiteInput = document.getElementById('nom_entite');
        const codeEntiteInput = document.getElementById('code_entite');
        const hierarchyPreview = document.getElementById('hierarchy-preview');
        const hierarchyPath = document.getElementById('hierarchy-path');
        const codePreview = document.getElementById('code-preview');
        const suggestedCode = document.getElementById('suggested-code');

        // Filter parent entities based on selected organisme
        organismeSelect.addEventListener('change', function() {
            const selectedOrganisme = this.value;
            
            // Reset parent select
            Array.from(parentSelect.options).forEach(option => {
                if (option.value === '') {
                    option.style.display = 'block';
                } else {
                    const optionOrganisme = option.getAttribute('data-organisme');
                    option.style.display = optionOrganisme === selectedOrganisme ? 'block' : 'none';
                }
            });
            
            // Reset parent selection if incompatible
            if (parentSelect.value) {
                const selectedOption = parentSelect.options[parentSelect.selectedIndex];
                if (selectedOption.getAttribute('data-organisme') !== selectedOrganisme) {
                    parentSelect.value = '';
                }
            }
            
            updateHierarchyPreview();
            updateCodeSuggestion();
        });

        // Update hierarchy preview when parent changes
        parentSelect.addEventListener('change', function() {
            updateHierarchyPreview();
            updateCodeSuggestion();
        });

        // Update code suggestion when name changes
        nomEntiteInput.addEventListener('input', function() {
            updateCodeSuggestion();
        });

        function updateHierarchyPreview() {
            const parentValue = parentSelect.value;
            
            if (parentValue) {
                const parentOption = parentSelect.options[parentSelect.selectedIndex];
                const parentName = parentOption.text.split(' (')[0]; // Remove organisme part
                
                hierarchyPath.innerHTML = `
                    <i class="fas fa-sitemap me-2"></i>
                    ${parentName} <i class="fas fa-arrow-right mx-2"></i> 
                    <strong class="text-primary">[Nouvelle Entité]</strong>
                `;
                hierarchyPreview.style.display = 'block';
            } else {
                hierarchyPath.innerHTML = `
                    <i class="fas fa-sitemap me-2"></i>
                    <strong class="text-warning">[Entité Racine]</strong>
                `;
                hierarchyPreview.style.display = 'block';
            }
        }

        function updateCodeSuggestion() {
            const organismeValue = organismeSelect.value;
            const parentValue = parentSelect.value;
            const nomValue = nomEntiteInput.value.trim();
            
            if (organismeValue && nomValue) {
                let suggestion = '';
                
                // Get organisme code from the select option text
                const organismeOption = organismeSelect.options[organismeSelect.selectedIndex];
                const organismeText = organismeOption.text;
                
                // Extract organisme code (assuming format like "ADII - Full Name")
                let organismeCode = '';
                if (organismeText.includes(' - ')) {
                    organismeCode = organismeText.split(' - ')[0];
                } else if (organismeText.includes('ADII')) {
                    organismeCode = 'ADII';
                } else if (organismeText.includes('DGI')) {
                    organismeCode = 'DGI';
                } else if (organismeText.includes('TGR')) {
                    organismeCode = 'TGR';
                } else if (organismeText.includes('ANCFCC')) {
                    organismeCode = 'ANCFCC';
                }
                
                suggestion = organismeCode;
                
                // Add parent code if exists
                if (parentValue) {
                    const parentOption = parentSelect.options[parentSelect.selectedIndex];
                    const parentText = parentOption.text.split(' (')[0];
                    
                    // Try to extract abbreviation from parent name
                    const parentAbbrev = getAbbreviation(parentText);
                    if (parentAbbrev) {
                        suggestion += '-' + parentAbbrev;
                    }
                }
                
                // Add current entity abbreviation
                const entityAbbrev = getAbbreviation(nomValue);
                if (entityAbbrev) {
                    suggestion += '-' + entityAbbrev;
                }
                
                suggestedCode.textContent = suggestion;
                codePreview.style.display = 'block';
            } else {
                codePreview.style.display = 'none';
            }
        }

        function getAbbreviation(text) {
            // Common French words to ignore
            const ignoredWords = ['de', 'des', 'du', 'la', 'le', 'les', 'et', 'ou', 'pour', 'dans', 'sur', 'avec'];
            
            const words = text.split(' ').filter(word => 
                word.length > 0 && !ignoredWords.includes(word.toLowerCase())
            );
            
            if (words.length === 1) {
                // Single word - take first 3-4 characters
                return words[0].substring(0, Math.min(4, words[0].length)).toUpperCase();
            } else if (words.length <= 4) {
                // Multiple words - take first letter of each
                return words.map(word => word.charAt(0)).join('').toUpperCase();
            } else {
                // Many words - take first letter of first 4 words
                return words.slice(0, 4).map(word => word.charAt(0)).join('').toUpperCase();
            }
        }

        // Global functions for templates and suggestions
        window.useSuggestedCode = function() {
            codeEntiteInput.value = suggestedCode.textContent;
            codeEntiteInput.focus();
        };

        window.fillTemplate = function(nom, codeAbbrev) {
            nomEntiteInput.value = nom;
            
            // Trigger code suggestion update
            updateCodeSuggestion();
            
            // Focus on the organisme field if not selected
            if (!organismeSelect.value) {
                organismeSelect.focus();
            } else {
                // Auto-generate code with template
                const organismeOption = organismeSelect.options[organismeSelect.selectedIndex];
                const organismeText = organismeOption.text;
                let organismeCode = '';
                
                if (organismeText.includes('ADII')) organismeCode = 'ADII';
                else if (organismeText.includes('DGI')) organismeCode = 'DGI';
                else if (organismeText.includes('TGR')) organismeCode = 'TGR';
                else if (organismeText.includes('ANCFCC')) organismeCode = 'ANCFCC';
                
                let fullCode = organismeCode;
                
                // Add parent code if selected
                if (parentSelect.value) {
                    const parentOption = parentSelect.options[parentSelect.selectedIndex];
                    const parentText = parentOption.text.split(' (')[0];
                    const parentAbbrev = getAbbreviation(parentText);
                    if (parentAbbrev) {
                        fullCode += '-' + parentAbbrev;
                    }
                }
                
                fullCode += '-' + codeAbbrev;
                codeEntiteInput.value = fullCode;
            }
        };

        // Initialize on page load
        if (organismeSelect.value) {
            organismeSelect.dispatchEvent(new Event('change'));
        }

        // Handle URL parameters for pre-filling
        const urlParams = new URLSearchParams(window.location.search);
        const organismeParam = urlParams.get('organisme');
        const parentParam = urlParams.get('parent');

        if (organismeParam) {
            organismeSelect.value = organismeParam;
            organismeSelect.dispatchEvent(new Event('change'));
        }

        if (parentParam) {
            setTimeout(() => {
                parentSelect.value = parentParam;
                parentSelect.dispatchEvent(new Event('change'));
            }, 100);
        }
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const nomEntite = document.getElementById('nom_entite').value.trim();
        const codeEntite = document.getElementById('code_entite').value.trim();
        const organisme = document.getElementById('id_organisme').value;
        
        if (!nomEntite || !codeEntite || !organisme) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return false;
        }
        
        // Check code format
        if (!/^[A-Z0-9\-_]+$/i.test(codeEntite)) {
            e.preventDefault();
            alert('Le code de l\'entité ne doit contenir que des lettres, chiffres, tirets et underscores.');
            return false;
        }

        // Check minimum lengths
        if (nomEntite.length < 3) {
            e.preventDefault();
            alert('Le nom de l\'entité doit contenir au moins 3 caractères.');
            return false;
        }

        if (codeEntite.length < 2) {
            e.preventDefault();
            alert('Le code de l\'entité doit contenir au moins 2 caractères.');
            return false;
        }
    });
</script>
@endpush