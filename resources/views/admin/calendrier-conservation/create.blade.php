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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="plan_classement_code" class="form-label">
                                    Plan de Classement <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('plan_classement_code') is-invalid @enderror" 
                                        id="plan_classement_code" 
                                        name="plan_classement_code" 
                                        required>
                                    <option value="">Sélectionner un plan</option>
                                    @foreach($availablePlans as $plan)
                                        <option value="{{ $plan->code_classement }}" 
                                                {{ old('plan_classement_code', request('plan_classement_code')) == $plan->code_classement ? 'selected' : '' }}>
                                            {{ $plan->code_classement }} - {{ Str::limit($plan->objet_classement, 50) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_classement_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Seuls les plans sans règle sont disponibles.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="principal_secondaire" class="form-label">Type</label>
                                <select class="form-select @error('principal_secondaire') is-invalid @enderror" 
                                        id="principal_secondaire" 
                                        name="principal_secondaire">
                                    <option value="">Non défini</option>
                                    <option value="P" {{ old('principal_secondaire') == 'P' ? 'selected' : '' }}>
                                        Principal
                                    </option>
                                    <option value="S" {{ old('principal_secondaire', 'S') == 'S' ? 'selected' : '' }}>
                                        Secondaire
                                    </option>
                                </select>
                                @error('principal_secondaire')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3">
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
                                    <option value="D" {{ old('sort_final', 'D') == 'D' ? 'selected' : '' }}>
                                        D - Destruction
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

                    <div class="mb-3">
                        <label for="pieces_constituant" class="form-label">Pièces Constituant le Dossier</label>
                        <textarea class="form-control @error('pieces_constituant') is-invalid @enderror" 
                                  id="pieces_constituant" 
                                  name="pieces_constituant" 
                                  rows="3"
                                  placeholder="Description des pièces et documents constituant le dossier...">{{ old('pieces_constituant') }}</textarea>
                        @error('pieces_constituant')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="delai_legal" class="form-label">Délai Légal</label>
                                <input type="text" 
                                       class="form-control @error('delai_legal') is-invalid @enderror" 
                                       id="delai_legal" 
                                       name="delai_legal" 
                                       value="{{ old('delai_legal', '_') }}" 
                                       placeholder="Ex: 5 ans, 10 ans, ou _ pour non défini"
                                       maxlength="50">
                                @error('delai_legal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Utilisez "_" si non défini.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reference_juridique" class="form-label">Référence Juridique</label>
                                <input type="text" 
                                       class="form-control @error('reference_juridique') is-invalid @enderror" 
                                       id="reference_juridique" 
                                       name="reference_juridique" 
                                       value="{{ old('reference_juridique') }}" 
                                       placeholder="Ex: Code des Douanes - Article 78">
                                @error('reference_juridique')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="archives_courantes" class="form-label">
                                    Archives Courantes <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('archives_courantes') is-invalid @enderror" 
                                       id="archives_courantes" 
                                       name="archives_courantes" 
                                       value="{{ old('archives_courantes', '3 ans') }}" 
                                       placeholder="Ex: 3 ans, 5 ans, 10 ans"
                                       maxlength="100"
                                       required>
                                @error('archives_courantes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Durée de conservation en archives courantes.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="archives_intermediaires" class="form-label">
                                    Archives Intermédiaires <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('archives_intermediaires') is-invalid @enderror" 
                                       id="archives_intermediaires" 
                                       name="archives_intermediaires" 
                                       value="{{ old('archives_intermediaires', '7 ans') }}" 
                                       placeholder="Ex: 7 ans, 10 ans, 15 ans"
                                       maxlength="50"
                                       required>
                                @error('archives_intermediaires')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Durée de conservation en archives intermédiaires.</div>
                            </div>
                        </div>
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
                                        <span class="badge bg-secondary me-2" id="preview-plan">---</span>
                                        <span class="badge" id="preview-sort">Sort final</span>
                                        <span class="badge bg-info ms-2" id="preview-type">Type</span>
                                    </div>
                                    <p class="mb-1 fw-bold" id="preview-pieces">Pièces constituant...</p>
                                    <small class="text-muted" id="preview-reference">Référence juridique...</small>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-end">
                                        <div class="mb-1">
                                            <span class="badge bg-primary me-1" id="preview-legal">Délai légal</span>
                                        </div>
                                        <div class="mb-1">
                                            <span class="badge bg-info me-1" id="preview-ac">AC</span>
                                            <span class="badge bg-warning me-1" id="preview-ai">AI</span>
                                        </div>
                                        <div class="mt-1">
                                            <small class="text-muted">Durée totale</small>
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
            plan: document.getElementById('plan_classement_code'),
            sort: document.getElementById('sort_final'),
            type: document.getElementById('principal_secondaire'),
            pieces: document.getElementById('pieces_constituant'),
            legal: document.getElementById('delai_legal'),
            current: document.getElementById('archives_courantes'),
            intermediate: document.getElementById('archives_intermediaires'),
            reference: document.getElementById('reference_juridique')
        };

        const preview = {
            plan: document.getElementById('preview-plan'),
            sort: document.getElementById('preview-sort'),
            type: document.getElementById('preview-type'),
            pieces: document.getElementById('preview-pieces'),
            legal: document.getElementById('preview-legal'),
            ac: document.getElementById('preview-ac'),
            ai: document.getElementById('preview-ai'),
            reference: document.getElementById('preview-reference'),
            card: document.getElementById('rule-preview')
        };

        // Update preview
        function updatePreview() {
            const planOption = inputs.plan.options[inputs.plan.selectedIndex];
            const planCode = planOption ? planOption.value : '---';
            const sortValue = inputs.sort.value;
            const typeValue = inputs.type.value;
            const pieces = inputs.pieces.value || 'Pièces constituant...';
            const legal = inputs.legal.value || 'Non défini';
            const current = inputs.current.value || '';
            const intermediate = inputs.intermediate.value || '';
            const reference = inputs.reference.value || 'Référence juridique...';

            // Update preview elements
            preview.plan.textContent = planCode;
            preview.pieces.textContent = pieces;
            preview.legal.textContent = legal;
            preview.ac.textContent = current || 'AC';
            preview.ai.textContent = intermediate || 'AI';
            preview.reference.textContent = reference;

            // Update sort badge
            const sortConfig = {
                'C': { text: 'Conservation', class: 'bg-success' },
                'D': { text: 'Destruction', class: 'bg-danger' },
                'T': { text: 'Tri', class: 'bg-warning' }
            };

            const config = sortConfig[sortValue] || { text: 'Sort final', class: 'bg-secondary' };
            preview.sort.textContent = config.text;
            preview.sort.className = 'badge ' + config.class;

            // Update type badge
            const typeConfig = {
                'P': { text: 'Principal', class: 'bg-primary' },
                'S': { text: 'Secondaire', class: 'bg-info' }
            };

            const typeConf = typeConfig[typeValue] || { text: 'Type', class: 'bg-secondary' };
            preview.type.textContent = typeConf.text;
            preview.type.className = 'badge ' + typeConf.class + ' ms-2';

            // Show preview if has content
            const hasContent = planCode !== '---' || pieces !== 'Pièces constituant...' || reference !== 'Référence juridique...';
            preview.card.style.display = hasContent ? 'block' : 'none';
        }

        // Add event listeners
        Object.values(inputs).forEach(input => {
            if (input) {
                input.addEventListener('input', updatePreview);
                input.addEventListener('change', updatePreview);
            }
        });

        // Initialize
        updatePreview();

        // Auto-suggest based on plan selection
        inputs.plan.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (!selectedOption.value) return;
            
            const planText = selectedOption.text.toLowerCase();
            
            // Auto-suggest pieces based on plan
            if (!inputs.pieces.value) {
                let suggestion = '';
                
                if (planText.includes('dédouanement')) {
                    suggestion = 'Déclarations en détail, documents d\'accompagnement, justificatifs de valeur';
                } else if (planText.includes('contentieux')) {
                    suggestion = 'Procès-verbaux d\'infraction, correspondances, décisions administratives';
                } else if (planText.includes('transit')) {
                    suggestion = 'Carnets TIR, manifestes, documents de transport';
                } else if (planText.includes('personnel')) {
                    suggestion = 'Dossiers individuels, contrats, évaluations';
                } else if (planText.includes('financier')) {
                    suggestion = 'Factures, bons de commande, pièces justificatives';
                }
                
                if (suggestion) {
                    inputs.pieces.value = suggestion;
                    updatePreview();
                }
            }
            
            // Auto-suggest reference based on plan
            if (!inputs.reference.value) {
                let refSuggestion = '';
                
                if (planText.includes('dédouanement') || planText.includes('contentieux')) {
                    refSuggestion = 'Code des Douanes';
                } else if (planText.includes('personnel')) {
                    refSuggestion = 'Statut de la Fonction Publique';
                } else if (planText.includes('financier')) {
                    refSuggestion = 'Loi Organique des Finances';
                } else {
                    refSuggestion = 'Règlement ADII';
                }
                
                inputs.reference.value = refSuggestion;
                updatePreview();
            }
        });
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const requiredFields = ['plan_classement_code', 'sort_final', 'archives_courantes', 'archives_intermediaires'];
        
        for (const field of requiredFields) {
            const input = document.querySelector(`[name="${field}"]`);
            if (!input.value.trim()) {
                e.preventDefault();
                alert(`Le champ "${input.previousElementSibling.textContent.replace(' *', '')}" est obligatoire.`);
                input.focus();
                return false;
            }
        }
    });

    // Quick duration presets
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.altKey) {
            const currentField = document.activeElement;
            
            if (currentField.name === 'archives_courantes' || currentField.name === 'archives_intermediaires') {
                let value = '';
                
                switch(e.key) {
                    case '1': value = '1 an'; break;
                    case '2': value = '2 ans'; break;
                    case '3': value = '3 ans'; break;
                    case '5': value = '5 ans'; break;
                    case '7': value = '7 ans'; break;
                    case '0': value = '10 ans'; break;
                }
                
                if (value) {
                    e.preventDefault();
                    currentField.value = value;
                    updatePreview();
                }
            }
        }
    });
</script>
@endpush