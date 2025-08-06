@extends('layouts.admin')

@section('title', 'Ajouter un Plan de Classement')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-plus me-2"></i>
            Ajouter un Plan de Classement
        </h1>
        <a href="{{ route('admin.plan-classement.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour à la liste
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.plan-classement.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="code_classement" class="form-label">
                                    Code de Classement <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('code_classement') is-invalid @enderror" 
                                       id="code_classement" 
                                       name="code_classement" 
                                       value="{{ old('code_classement') }}" 
                                       placeholder="Ex: 100.10.1"
                                       pattern="^[0-9]+(\.[0-9]+)*$"
                                       required>
                                @error('code_classement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Format numérique (ex: 100.10.1).</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="category" class="form-label">Catégorie</label>
                                <select class="form-select" id="category" name="category" onchange="updateCodeSuggestion()">
                                    <option value="">Sélectionner une catégorie</option>
                                    @foreach($categories as $code => $name)
                                        <option value="{{ $code }}" {{ old('category') == $code ? 'selected' : '' }}>
                                            {{ $code }} - {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Sélectionnez pour une suggestion de code.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="code_preview" class="form-label">Aperçu du Code</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-tag"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control bg-light" 
                                           id="code_preview" 
                                           readonly
                                           value="---">
                                </div>
                                <div class="form-text">Format d'affichage du code.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="objet_classement" class="form-label">
                            Objet de Classement <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('objet_classement') is-invalid @enderror" 
                                  id="objet_classement" 
                                  name="objet_classement" 
                                  rows="4"
                                  maxlength="500"
                                  placeholder="Description détaillée de l'objet de classement..."
                                  required>{{ old('objet_classement') }}</textarea>
                        @error('objet_classement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <span id="char-count">0</span>/500 caractères utilisés.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description (optionnelle)</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="3"
                                  placeholder="Description complémentaire ou notes...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Suggestions d'objets de classement par catégorie -->
                    <div class="card border-info mb-3" id="suggestions-card" style="display: none;">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-lightbulb me-2"></i>
                                Suggestions pour cette catégorie
                            </h6>
                        </div>
                        <div class="card-body" id="suggestions-content">
                            <!-- Suggestions dynamiques -->
                        </div>
                    </div>

                    <!-- Aperçu du plan -->
                    <div class="card border-success mb-3" id="preview-card" style="display: none;">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-eye me-2"></i>
                                Aperçu du Plan de Classement
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="badge bg-primary fs-5" id="preview-code">---</span>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 fw-bold" id="preview-objet">Objet de classement...</p>
                                    <p class="mb-0 text-muted small" id="preview-description">Description...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.plan-classement.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>
                            Créer le Plan
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
    const suggestions = {
        '100': [
            'Organisation générale de l\'administration douanière',
            'Gestion du personnel et ressources humaines', 
            'Organisation des services et structures'
        ],
        '510': [
            'Déclarations d\'importation de marchandises générales',
            'Déclarations d\'exportation et réexportation',
            'Régimes économiques en douane - Entrepôts'
        ],
        '520': [
            'Documents de transit international - Carnet TIR',
            'Transport routier international de marchandises',
            'Manifestes de cargaison et documents de transport'
        ],
        '530': [
            'Infractions douanières et amendes',
            'Contentieux judiciaire en matière douanière',
            'Procès-verbaux et sanctions administratives'
        ],
        '540': [
            'Recours administratifs contre décisions douanières',
            'Réclamations des usagers et entreprises',
            'Procédures de contestation et révision'
        ],
        '550': [
            'Contrôle a posteriori des déclarations',
            'Vérifications comptables chez les entreprises',
            'Audits et contrôles documentaires'
        ],
        '560': [
            'Facilitations commerciales et procédures simplifiées',
            'Opérateur économique agréé (OEA)',
            'Guichet unique et dématérialisation'
        ],
        '610': [
            'Dédouanement des marchandises à l\'importation',
            'Liquidation et perception des droits et taxes',
            'Mainlevée et enlèvement des marchandises'
        ]
    };

    document.addEventListener('DOMContentLoaded', function() {
        const codeInput = document.getElementById('code_classement');
        const codePreview = document.getElementById('code_preview');
        const objetInput = document.getElementById('objet_classement');
        const descriptionInput = document.getElementById('description');
        const charCount = document.getElementById('char-count');
        const previewCard = document.getElementById('preview-card');
        const previewCode = document.getElementById('preview-code');
        const previewObjet = document.getElementById('preview-objet');
        const previewDescription = document.getElementById('preview-description');
        const categorySelect = document.getElementById('category');
        const suggestionsCard = document.getElementById('suggestions-card');

        // Update code preview
        codeInput.addEventListener('input', function() {
            const value = this.value.trim();
            codePreview.value = value || '---';
            previewCode.textContent = value || '---';
            updatePreview();
        });

        // Update character count and preview
        objetInput.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            // Change color based on usage
            if (length > 450) {
                charCount.className = 'text-danger fw-bold';
            } else if (length > 350) {
                charCount.className = 'text-warning fw-bold';
            } else {
                charCount.className = 'text-muted';
            }

            previewObjet.textContent = this.value || 'Objet de classement...';
            updatePreview();
        });

        descriptionInput.addEventListener('input', function() {
            previewDescription.textContent = this.value || 'Description...';
            updatePreview();
        });

        function updatePreview() {
            const hasCode = codeInput.value.trim() !== '';
            const hasObjet = objetInput.value.trim() !== '';
            
            if (hasCode || hasObjet) {
                previewCard.style.display = 'block';
            } else {
                previewCard.style.display = 'none';
            }
        }

        // Initialize
        charCount.textContent = objetInput.value.length;
        updatePreview();
    });

    function updateCodeSuggestion() {
        const categorySelect = document.getElementById('category');
        const codeInput = document.getElementById('code_classement');
        const suggestionsCard = document.getElementById('suggestions-card');
        const suggestionsContent = document.getElementById('suggestions-content');
        
        const selectedCategory = categorySelect.value;
        
        if (selectedCategory) {
            // Suggest code format
            if (!codeInput.value) {
                codeInput.value = selectedCategory + '.';
                codeInput.dispatchEvent(new Event('input'));
                codeInput.focus();
                codeInput.setSelectionRange(codeInput.value.length, codeInput.value.length);
            }
            
            // Show suggestions
            if (suggestions[selectedCategory]) {
                let html = '<p class="mb-2">Cliquez sur une suggestion :</p><div class="row">';
                suggestions[selectedCategory].forEach(suggestion => {
                    html += `<div class="col-12 mb-2">
                        <button type="button" class="btn btn-outline-info btn-sm w-100 text-start" 
                                onclick="fillSuggestion('${suggestion}')">
                            ${suggestion}
                        </button>
                    </div>`;
                });
                html += '</div>';
                suggestionsContent.innerHTML = html;
                suggestionsCard.style.display = 'block';
            }
        } else {
            suggestionsCard.style.display = 'none';
        }
    }

    function fillSuggestion(text) {
        document.getElementById('objet_classement').value = text;
        document.getElementById('objet_classement').dispatchEvent(new Event('input'));
        document.getElementById('objet_classement').focus();
    }

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const code = document.getElementById('code_classement').value.trim();
        const objet = document.getElementById('objet_classement').value.trim();
        
        if (!code || !objet) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return false;
        }

        // Validate code format
        const codeRegex = /^[0-9]+(\.[0-9]+)*$/;
        if (!codeRegex.test(code)) {
            e.preventDefault();
            alert('Le code doit être au format numérique (ex: 100.10.1).');
            return false;
        }

        if (objet.length < 10) {
            e.preventDefault();
            alert('L\'objet de classement doit contenir au moins 10 caractères.');
            return false;
        }

        if (objet.length > 500) {
            e.preventDefault();
            alert('L\'objet de classement ne peut pas dépasser 500 caractères.');
            return false;
        }
    });
</script>
@endpush