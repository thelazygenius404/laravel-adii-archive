@extends('layouts.admin')

@section('title', 'Ajouter une Règle de Conservation')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-plus me-2"></i>
            Ajouter une Règle de Conservation
        </h1>
        <a href="{{ route('admin.calendrier-conservation.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour à la liste
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.calendrier-conservation.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="NO_regle" class="form-label">
                                    Numéro de Règle <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('NO_regle') is-invalid @enderror" 
                                       id="NO_regle" 
                                       name="NO_regle" 
                                       value="{{ old('NO_regle', $nextRule) }}" 
                                       placeholder="Ex: R001"
                                       maxlength="10"
                                       required>
                                @error('NO_regle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Identifiant unique de la règle.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="plan_classement_id" class="form-label">
                                    Plan de Classement <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('plan_classement_id') is-invalid @enderror" 
                                        id="plan_classement_id" 
                                        name="plan_classement_id" 
                                        required>
                                    <option value="">Sélectionner un plan</option>
                                    @foreach($planClassements as $plan)
                                        <option value="{{ $plan->id }}" 
                                                data-code="{{ $plan->formatted_code }}"
                                                {{ old('plan_classement_id') == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->formatted_code }} - {{ $plan->short_description }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_classement_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sort_final" class="form-label">
                                    Sort Final <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('sort_final') is-invalid @enderror" 
                                        id="sort_final" 
                                        name="sort_final" 
                                        required>
                                    <option value="">Sélectionner un sort</option>
                                    <option value="C" {{ old('sort_final') == 'C' ? 'selected' : '' }}>
                                        C - Conservation
                                    </option>
                                    <option value="E" {{ old('sort_final') == 'E' ? 'selected' : '' }}>
                                        E - Élimination
                                    </option>
                                    <option value="T" {{ old('sort_final') == 'T' ? 'selected' : '' }}>
                                        T - Tri
                                    </option>
                                </select>
                                @error('sort_final')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nature_dossier" class="form-label">
                                    Nature du Dossier <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nature_dossier') is-invalid @enderror" 
                                       id="nature_dossier" 
                                       name="nature_dossier" 
                                       value="{{ old('nature_dossier') }}" 
                                       placeholder="Ex: Déclarations import marchandises générales"
                                       maxlength="50"
                                       required>
                                @error('nature_dossier')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <span id="nature-count">0</span>/50 caractères.
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="delais_legaux" class="form-label">
                                    Délais Légaux (années) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('delais_legaux') is-invalid @enderror" 
                                           id="delais_legaux" 
                                           name="delais_legaux" 
                                           value="{{ old('delais_legaux') }}" 
                                           min="0"
                                           max="100"
                                           required>
                                    <span class="input-group-text">ans</span>
                                </div>
                                @error('delais_legaux')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="archive_courant" class="form-label">
                                    Archive Courante (années) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('archive_courant') is-invalid @enderror" 
                                           id="archive_courant" 
                                           name="archive_courant" 
                                           value="{{ old('archive_courant') }}" 
                                           min="0"
                                           max="50"
                                           required>
                                    <span class="input-group-text">ans</span>
                                </div>
                                @error('archive_courant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="archive_intermediaire" class="form-label">
                                    Archive Intermédiaire (années) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('archive_intermediaire') is-invalid @enderror" 
                                           id="archive_intermediaire" 
                                           name="archive_intermediaire" 
                                           value="{{ old('archive_intermediaire') }}" 
                                           min="0"
                                           max="100"
                                           required>
                                    <span class="input-group-text">ans</span>
                                </div>
                                @error('archive_intermediaire')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="duree_totale" class="form-label">Durée Totale</label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control bg-light" 
                                           id="duree_totale" 
                                           readonly
                                           value="0 ans">
                                    <span class="input-group-text bg-info text-white">
                                        <i class="fas fa-calculator"></i>
                                    </span>
                                </div>
                                <div class="form-text">Calculé automatiquement.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reference" class="form-label">
                            Référence Légale <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('reference') is-invalid @enderror" 
                                  id="reference" 
                                  name="reference" 
                                  rows="2"
                                  placeholder="Ex: Code des Douanes - Article 78"
                                  required>{{ old('reference') }}</textarea>
                        @error('reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="observation" class="form-label">Observations</label>
                        <textarea class="form-control @error('observation') is-invalid @enderror" 
                                  id="observation" 
                                  name="observation" 
                                  rows="3"
                                  placeholder="Observations complémentaires sur l'application de cette règle...">{{ old('observation') }}</textarea>
                        @error('observation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Aperçu de la règle -->
                    <div class="card border-success mb-3" id="rule-preview" style="display: none;">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-eye me-2"></i>
                                Aperçu de la Règle de Conservation
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-secondary me-2" id="preview-rule">R001</span>
                                        <span class="badge bg-primary me-2" id="preview-plan">001</span>
                                        <span class="badge" id="preview-sort">Conservation</span>
                                    </div>
                                    <p class="mb-1 fw-bold" id="preview-nature">Nature du dossier...</p>
                                    <small class="text-muted" id="preview-reference">Référence légale...</small>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-end">
                                        <div class="mb-1">
                                            <span class="badge bg-info me-1" id="preview-ac">0AC</span>
                                            <span class="badge bg-warning me-1" id="preview-ai">0AI</span>
                                        </div>
                                        <div>
                                            <span class="badge bg-dark" id="preview-legal">0 ans légaux</span>
                                        </div>
                                        <div class="mt-1">
                                            <small class="text-muted">Total: <span id="preview-total">0 ans</span></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.calendrier-conservation.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>
                            Créer la Règle
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
        const inputs = {
            rule: document.getElementById('NO_regle'),
            plan: document.getElementById('plan_classement_id'),
            sort: document.getElementById('sort_final'),
            nature: document.getElementById('nature_dossier'),
            legal: document.getElementById('delais_legaux'),
            current: document.getElementById('archive_courant'),
            intermediate: document.getElementById('archive_intermediaire'),
            reference: document.getElementById('reference'),
            total: document.getElementById('duree_totale')
        };

        const preview = {
            rule: document.getElementById('preview-rule'),
            plan: document.getElementById('preview-plan'),
            sort: document.getElementById('preview-sort'),
            nature: document.getElementById('preview-nature'),
            legal: document.getElementById('preview-legal'),
            ac: document.getElementById('preview-ac'),
            ai: document.getElementById('preview-ai'),
            total: document.getElementById('preview-total'),
            reference: document.getElementById('preview-reference'),
            card: document.getElementById('rule-preview')
        };

        const natureCount = document.getElementById('nature-count');

        // Character count for nature
        inputs.nature.addEventListener('input', function() {
            natureCount.textContent = this.value.length;
            updatePreview();
        });

        // Calculate total duration
        function calculateTotal() {
            const current = parseInt(inputs.current.value) || 0;
            const intermediate = parseInt(inputs.intermediate.value) || 0;
            const total = current + intermediate;
            inputs.total.value = total + ' ans';
            return total;
        }

        // Update preview
        function updatePreview() {
            const rule = inputs.rule.value || 'R001';
            const planOption = inputs.plan.options[inputs.plan.selectedIndex];
            const planCode = planOption ? planOption.getAttribute('data-code') || '001' : '001';
            const sortValue = inputs.sort.value;
            const nature = inputs.nature.value || 'Nature du dossier...';
            const legal = inputs.legal.value || '0';
            const current = inputs.current.value || '0';
            const intermediate = inputs.intermediate.value || '0';
            const reference = inputs.reference.value || 'Référence légale...';
            const total = calculateTotal();

            // Update preview elements
            preview.rule.textContent = rule;
            preview.plan.textContent = planCode;
            preview.nature.textContent = nature;
            preview.legal.textContent = legal + ' ans légaux';
            preview.ac.textContent = current + 'AC';
            preview.ai.textContent = intermediate + 'AI';
            preview.total.textContent = total + ' ans';
            preview.reference.textContent = reference;

            // Update sort badge
            const sortConfig = {
                'C': { text: 'Conservation', class: 'bg-success' },
                'E': { text: 'Élimination', class: 'bg-danger' },
                'T': { text: 'Tri', class: 'bg-warning' }
            };

            const config = sortConfig[sortValue] || { text: 'Non défini', class: 'bg-secondary' };
            preview.sort.textContent = config.text;
            preview.sort.className = 'badge ' + config.class;

            // Show preview if has content
            const hasContent = rule || nature !== 'Nature du dossier...' || reference !== 'Référence légale...';
            preview.card.style.display = hasContent ? 'block' : 'none';
        }

        // Add event listeners
        Object.values(inputs).forEach(input => {
            if (input && input !== inputs.total) {
                input.addEventListener('input', updatePreview);
                input.addEventListener('change', updatePreview);
            }
        });

        // Special handling for duration inputs
        [inputs.current, inputs.intermediate].forEach(input => {
            input.addEventListener('input', function() {
                calculateTotal();
                updatePreview();
            });
        });

        // Initialize
        natureCount.textContent = inputs.nature.value.length;
        calculateTotal();
        updatePreview();
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const requiredFields = ['NO_regle', 'plan_classement_id', 'sort_final', 'nature_dossier', 'delais_legaux', 'archive_courant', 'archive_intermediaire', 'reference'];
        
        for (const field of requiredFields) {
            const input = document.querySelector(`[name="${field}"]`);
            if (!input.value.trim()) {
                e.preventDefault();
                alert(`Le champ "${input.previousElementSibling.textContent.replace(' *', '')}" est obligatoire.`);
                input.focus();
                return false;
            }
        }

        // Validate numeric fields
        const numericFields = ['delais_legaux', 'archive_courant', 'archive_intermediaire'];
        for (const field of numericFields) {
            const input = document.querySelector(`[name="${field}"]`);
            const value = parseInt(input.value);
            if (isNaN(value) || value < 0) {
                e.preventDefault();
                alert(`Le champ "${input.previousElementSibling.textContent.replace(' *', '')}" doit être un nombre positif.`);
                input.focus();
                return false;
            }
        }

        // Validate rule number format
        const ruleInput = document.querySelector('[name="NO_regle"]');
        if (!/^R\d{3}$/.test(ruleInput.value.trim())) {
            if (!confirm('Le format recommandé pour le numéro de règle est "R001". Voulez-vous continuer avec "' + ruleInput.value + '" ?')) {
                e.preventDefault();
                ruleInput.focus();
                return false;
            }
        }

        // Validate total duration logic
        const delaisLegaux = parseInt(document.querySelector('[name="delais_legaux"]').value);
        const archiveCourant = parseInt(document.querySelector('[name="archive_courant"]').value);
        const archiveIntermediaire = parseInt(document.querySelector('[name="archive_intermediaire"]').value);
        const totalArchive = archiveCourant + archiveIntermediaire;

        if (totalArchive > delaisLegaux + 10) {
            if (!confirm(`La durée totale d'archivage (${totalArchive} ans) semble élevée par rapport aux délais légaux (${delaisLegaux} ans). Voulez-vous continuer ?`)) {
                e.preventDefault();
                return false;
            }
        }
    });

    // Auto-suggest rule number
    document.getElementById('NO_regle').addEventListener('focus', function() {
        if (!this.value) {
            this.value = '{{ $nextRule }}';
            this.dispatchEvent(new Event('input'));
        }
    });

    // Smart suggestions based on plan classement
    document.getElementById('plan_classement_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const planText = selectedOption.text.toLowerCase();
        
        // Auto-suggest nature based on plan
        const natureInput = document.getElementById('nature_dossier');
        if (!natureInput.value) {
            let suggestion = '';
            
            if (planText.includes('dédouanement')) {
                suggestion = 'Déclarations import/export';
            } else if (planText.includes('contentieux')) {
                suggestion = 'Infractions douanières';
            } else if (planText.includes('transit')) {
                suggestion = 'Documents de transit';
            } else if (planText.includes('personnel')) {
                suggestion = 'Dossiers individuels';
            } else if (planText.includes('financier')) {
                suggestion = 'Documents comptables';
            }
            
            if (suggestion) {
                natureInput.value = suggestion;
                natureInput.dispatchEvent(new Event('input'));
            }
        }

        // Auto-suggest reference based on plan
        const referenceInput = document.getElementById('reference');
        if (!referenceInput.value) {
            let refSuggestion = '';
            
            if (planText.includes('dédouanement') || planText.includes('contentieux')) {
                refSuggestion = 'Code des Douanes - Article ';
            } else if (planText.includes('personnel')) {
                refSuggestion = 'Statut de la Fonction Publique';
            } else if (planText.includes('financier')) {
                refSuggestion = 'Loi Organique des Finances';
            } else if (planText.includes('international')) {
                refSuggestion = 'Convention internationale';
            } else {
                refSuggestion = 'Règlement ADII';
            }
            
            referenceInput.value = refSuggestion;
            referenceInput.dispatchEvent(new Event('input'));
        }
    });

    // Quick duration presets
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.altKey) {
            const currentField = document.activeElement;
            
            if (currentField.name === 'archive_courant' || currentField.name === 'archive_intermediaire') {
                let value = '';
                
                switch(e.key) {
                    case '1': value = '1'; break;
                    case '2': value = '2'; break;
                    case '3': value = '3'; break;
                    case '5': value = '5'; break;
                    case '0': value = '10'; break;
                }
                
                if (value) {
                    e.preventDefault();
                    currentField.value = value;
                    currentField.dispatchEvent(new Event('input'));
                }
            }
        }
    });
</script>

<style>
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .badge-lg {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
    }
    
    #rule-preview .card-body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    }
    
    .input-group-text.bg-info {
        border-color: #0dcaf0;
    }
    
    .form-text {
        font-size: 0.875rem;
    }
    
    .preview-section {
        transition: all 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    #rule-preview {
        animation: fadeIn 0.3s ease when showing;
    }
</style>
@endpush