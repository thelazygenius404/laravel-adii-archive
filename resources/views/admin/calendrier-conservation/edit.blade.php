@extends('layouts.admin')

@section('title', 'Modifier une Règle de Conservation')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-edit me-2"></i>
            Modifier une Règle de Conservation
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
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Modification de la règle : {{ $calendrierConservation->NO_regle }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.calendrier-conservation.update', $calendrierConservation) }}">
                    @csrf
                    @method('PUT')
                    
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
                                       value="{{ old('NO_regle', $calendrierConservation->NO_regle) }}" 
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
                                                {{ old('plan_classement_id', $calendrierConservation->plan_classement_id) == $plan->id ? 'selected' : '' }}>
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
                                    <option value="C" {{ old('sort_final', $calendrierConservation->sort_final) == 'C' ? 'selected' : '' }}>
                                        C - Conservation
                                    </option>
                                    <option value="E" {{ old('sort_final', $calendrierConservation->sort_final) == 'E' ? 'selected' : '' }}>
                                        E - Élimination
                                    </option>
                                    <option value="T" {{ old('sort_final', $calendrierConservation->sort_final) == 'T' ? 'selected' : '' }}>
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
                                       value="{{ old('nature_dossier', $calendrierConservation->nature_dossier) }}" 
                                       placeholder="Ex: Déclarations import marchandises générales"
                                       maxlength="50"
                                       required>
                                @error('nature_dossier')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <span id="nature-count">{{ strlen($calendrierConservation->nature_dossier) }}</span>/50 caractères.
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
                                           value="{{ old('delais_legaux', $calendrierConservation->delais_legaux) }}" 
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
                                           value="{{ old('archive_courant', $calendrierConservation->archive_courant) }}" 
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
                                           value="{{ old('archive_intermediaire', $calendrierConservation->archive_intermediaire) }}" 
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
                                           value="{{ $calendrierConservation->total_duration }} ans">
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
                                  required>{{ old('reference', $calendrierConservation->reference) }}</textarea>
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
                                  placeholder="Observations complémentaires sur l'application de cette règle...">{{ old('observation', $calendrierConservation->observation) }}</textarea>
                        @error('observation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Informations supplémentaires -->
                    <div class="card border-light mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Informations sur la Règle
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Date de création :</strong> {{ $calendrierConservation->created_at->format('d/m/Y à H:i') }}</p>
                                    <p><strong>Dernière modification :</strong> {{ $calendrierConservation->updated_at->format('d/m/Y à H:i') }}</p>
                                    <p><strong>Plan de classement actuel :</strong> 
                                        <span class="badge bg-primary">{{ $calendrierConservation->planClassement->formatted_code }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Sort final actuel :</strong> 
                                        <span class="badge {{ $calendrierConservation->status_badge_class }}">{{ $calendrierConservation->status }}</span>
                                    </p>
                                    <p><strong>Durée totale actuelle :</strong> 
                                        <span class="badge bg-dark">{{ $calendrierConservation->total_duration }} ans</span>
                                    </p>
                                    <p><strong>Délais légaux actuels :</strong> 
                                        <span class="badge bg-secondary">{{ $calendrierConservation->delais_legaux }} ans</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aperçu de la règle modifiée -->
                    <div class="card border-success mb-3" id="rule-preview">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-eye me-2"></i>
                                Aperçu de la Règle Modifiée
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-secondary me-2" id="preview-rule">{{ $calendrierConservation->NO_regle }}</span>
                                        <span class="badge bg-primary me-2" id="preview-plan">{{ $calendrierConservation->planClassement->formatted_code }}</span>
                                        <span class="badge {{ $calendrierConservation->status_badge_class }}" id="preview-sort">{{ $calendrierConservation->status }}</span>
                                    </div>
                                    <p class="mb-1 fw-bold" id="preview-nature">{{ $calendrierConservation->nature_dossier }}</p>
                                    <small class="text-muted" id="preview-reference">{{ $calendrierConservation->reference }}</small>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-end">
                                        <div class="mb-1">
                                            <span class="badge bg-info me-1" id="preview-ac">{{ $calendrierConservation->archive_courant }}AC</span>
                                            <span class="badge bg-warning me-1" id="preview-ai">{{ $calendrierConservation->archive_intermediaire }}AI</span>
                                        </div>
                                        <div>
                                            <span class="badge bg-dark" id="preview-legal">{{ $calendrierConservation->delais_legaux }} ans légaux</span>
                                        </div>
                                        <div class="mt-1">
                                            <small class="text-muted">Total: <span id="preview-total">{{ $calendrierConservation->total_duration }} ans</span></small>
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
                            <i class="fas fa-save me-2"></i>
                            Enregistrer les modifications
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
            reference: document.getElementById('preview-reference')
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
            const rule = inputs.rule.value || '{{ $calendrierConservation->NO_regle }}';
            const planOption = inputs.plan.options[inputs.plan.selectedIndex];
            const planCode = planOption ? planOption.getAttribute('data-code') || '{{ $calendrierConservation->planClassement->formatted_code }}' : '{{ $calendrierConservation->planClassement->formatted_code }}';
            const sortValue = inputs.sort.value;
            const nature = inputs.nature.value || '{{ $calendrierConservation->nature_dossier }}';
            const legal = inputs.legal.value || '{{ $calendrierConservation->delais_legaux }}';
            const current = inputs.current.value || '{{ $calendrierConservation->archive_courant }}';
            const intermediate = inputs.intermediate.value || '{{ $calendrierConservation->archive_intermediaire }}';
            const reference = inputs.reference.value || '{{ $calendrierConservation->reference }}';
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

            const config = sortConfig[sortValue] || { text: '{{ $calendrierConservation->status }}', class: '{{ $calendrierConservation->status_badge_class }}' };
            preview.sort.textContent = config.text;
            preview.sort.className = 'badge ' + config.class;
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

        // Warn about significant changes
        const originalRule = '{{ $calendrierConservation->NO_regle }}';
        const originalPlan = '{{ $calendrierConservation->plan_classement_id }}';
        const originalSort = '{{ $calendrierConservation->sort_final }}';
        
        const newRule = document.querySelector('[name="NO_regle"]').value;
        const newPlan = document.querySelector('[name="plan_classement_id"]').value;
        const newSort = document.querySelector('[name="sort_final"]').value;
        
        if (newRule !== originalRule || newPlan !== originalPlan || newSort !== originalSort) {
            if (!confirm('Vous avez modifié des éléments critiques de cette règle. Êtes-vous sûr de vouloir continuer ?')) {
                e.preventDefault();
                return false;
            }
        }
    });
</script>
@endpush