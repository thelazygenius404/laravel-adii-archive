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
                                <input type="number" 
                                       class="form-control @error('code_classement') is-invalid @enderror" 
                                       id="code_classement" 
                                       name="code_classement" 
                                       value="{{ old('code_classement', $nextCode) }}" 
                                       min="1"
                                       required>
                                @error('code_classement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Numéro unique d'identification du plan.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
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
                                           value="{{ str_pad($nextCode, 3, '0', STR_PAD_LEFT) }}">
                                </div>
                                <div class="form-text">Format d'affichage du code de classement.</div>
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

                    <!-- Suggestions d'objets de classement -->
                    <div class="card border-info mb-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-lightbulb me-2"></i>
                                Suggestions d'Objets de Classement
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">Cliquez sur une suggestion pour la pré-remplir :</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action text-start" 
                                                onclick="fillSuggestion('Dossiers de dédouanement des marchandises - Déclarations en détail Import/Export')">
                                            Dédouanement Import/Export
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action text-start" 
                                                onclick="fillSuggestion('Dossiers de contentieux douanier - Infractions, amendes et poursuites judiciaires')">
                                            Contentieux Douanier
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action text-start" 
                                                onclick="fillSuggestion('Dossiers de régimes économiques en douane - Admission temporaire, entrepôt, zone franche')">
                                            Régimes Économiques
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action text-start" 
                                                onclick="fillSuggestion('Dossiers de transit international - Carnet TIR et documents de transit douanier')">
                                            Transit International
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="list-group">
                                        <button type="button" class="list-group-item list-group-item-action text-start" 
                                                onclick="fillSuggestion('Dossiers de contrôle a posteriori - Vérifications comptables et contrôles documentaires')">
                                            Contrôle A Posteriori
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action text-start" 
                                                onclick="fillSuggestion('Dossiers de personnel et ressources humaines - Gestion administrative du personnel')">
                                            Ressources Humaines
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action text-start" 
                                                onclick="fillSuggestion('Dossiers financiers et comptables - Budgets, factures, paiements et recouvrement')">
                                            Financier et Comptable
                                        </button>
                                        <button type="button" class="list-group-item list-group-item-action text-start" 
                                                onclick="fillSuggestion('Dossiers de coopération internationale - Accords douaniers et échanges d\'informations')">
                                            Coopération Internationale
                                        </button>
                                    </div>
                                </div>
                            </div>
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
                                    <span class="badge bg-primary fs-5" id="preview-code">001</span>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-bold" id="preview-description">Description du plan de classement...</p>
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
    document.addEventListener('DOMContentLoaded', function() {
        const codeInput = document.getElementById('code_classement');
        const codePreview = document.getElementById('code_preview');
        const descriptionInput = document.getElementById('objet_classement');
        const charCount = document.getElementById('char-count');
        const previewCard = document.getElementById('preview-card');
        const previewCode = document.getElementById('preview-code');
        const previewDescription = document.getElementById('preview-description');

        // Update code preview
        codeInput.addEventListener('input', function() {
            const value = this.value || '1';
            const formattedCode = value.toString().padStart(3, '0');
            codePreview.value = formattedCode;
            previewCode.textContent = formattedCode;
            updatePreview();
        });

        // Update character count and preview
        descriptionInput.addEventListener('input', function() {
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

            previewDescription.textContent = this.value || 'Description du plan de classement...';
            updatePreview();
        });

        function updatePreview() {
            const hasCode = codeInput.value.trim() !== '';
            const hasDescription = descriptionInput.value.trim() !== '';
            
            if (hasCode || hasDescription) {
                previewCard.style.display = 'block';
            } else {
                previewCard.style.display = 'none';
            }
        }

        // Initialize character count
        charCount.textContent = descriptionInput.value.length;
        
        // Initialize preview
        updatePreview();
    });

    function fillSuggestion(text) {
        document.getElementById('objet_classement').value = text;
        document.getElementById('objet_classement').dispatchEvent(new Event('input'));
        document.getElementById('objet_classement').focus();
    }

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const code = document.getElementById('code_classement').value.trim();
        const description = document.getElementById('objet_classement').value.trim();
        
        if (!code || !description) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return false;
        }
        
        if (parseInt(code) < 1) {
            e.preventDefault();
            alert('Le code de classement doit être supérieur à 0.');
            return false;
        }

        if (description.length < 10) {
            e.preventDefault();
            alert('L\'objet de classement doit contenir au moins 10 caractères.');
            return false;
        }

        if (description.length > 500) {
            e.preventDefault();
            alert('L\'objet de classement ne peut pas dépasser 500 caractères.');
            return false;
        }
    });

    // Auto-suggest next code
    document.getElementById('code_classement').addEventListener('focus', function() {
        if (!this.value) {
            this.value = {{ $nextCode }};
            this.dispatchEvent(new Event('input'));
        }
    });
</script>
@endpush