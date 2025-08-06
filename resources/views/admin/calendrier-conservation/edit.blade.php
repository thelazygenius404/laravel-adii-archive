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
                    Modification de la règle : {{ $calendrierConservation->plan_classement_code }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.calendrier-conservation.update', $calendrierConservation) }}">
                    @csrf
                    @method('PUT')
                    
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
                                    @foreach($planClassements as $plan)
                                        <option value="{{ $plan->code_classement }}" 
                                                {{ old('plan_classement_code', $calendrierConservation->plan_classement_code) == $plan->code_classement ? 'selected' : '' }}>
                                            {{ $plan->code_classement }} - {{ Str::limit($plan->objet_classement, 50) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_classement_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="principal_secondaire" class="form-label">Type</label>
                                <select class="form-select @error('principal_secondaire') is-invalid @enderror" 
                                        id="principal_secondaire" 
                                        name="principal_secondaire">
                                    <option value="">Non défini</option>
                                    <option value="P" {{ old('principal_secondaire', $calendrierConservation->principal_secondaire) == 'P' ? 'selected' : '' }}>
                                        Principal
                                    </option>
                                    <option value="S" {{ old('principal_secondaire', $calendrierConservation->principal_secondaire) == 'S' ? 'selected' : '' }}>
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
                                    <option value="C" {{ old('sort_final', $calendrierConservation->sort_final) == 'C' ? 'selected' : '' }}>
                                        C - Conservation
                                    </option>
                                    <option value="D" {{ old('sort_final', $calendrierConservation->sort_final) == 'D' ? 'selected' : '' }}>
                                        D - Destruction
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

                    <div class="mb-3">
                        <label for="pieces_constituant" class="form-label">Pièces Constituant le Dossier</label>
                        <textarea class="form-control @error('pieces_constituant') is-invalid @enderror" 
                                  id="pieces_constituant" 
                                  name="pieces_constituant" 
                                  rows="3"
                                  placeholder="Description des pièces et documents constituant le dossier...">{{ old('pieces_constituant', $calendrierConservation->pieces_constituant) }}</textarea>
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
                                       value="{{ old('delai_legal', $calendrierConservation->delai_legal) }}" 
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
                                       value="{{ old('reference_juridique', $calendrierConservation->reference_juridique) }}" 
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
                                       value="{{ old('archives_courantes', $calendrierConservation->archives_courantes) }}" 
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
                                       value="{{ old('archives_intermediaires', $calendrierConservation->archives_intermediaires) }}" 
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
                                        <span class="badge bg-primary">{{ $calendrierConservation->plan_classement_code }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Sort final actuel :</strong> 
                                        <span class="badge {{ $calendrierConservation->sort_final == 'C' ? 'bg-success' : ($calendrierConservation->sort_final == 'D' ? 'bg-danger' : 'bg-warning') }}">
                                            {{ $calendrierConservation->sort_final == 'C' ? 'Conservation' : ($calendrierConservation->sort_final == 'D' ? 'Destruction' : 'Tri') }}
                                        </span>
                                    </p>
                                    <p><strong>Type actuel :</strong> 
                                        <span class="badge {{ $calendrierConservation->principal_secondaire == 'P' ? 'bg-primary' : 'bg-secondary' }}">
                                            {{ $calendrierConservation->principal_secondaire == 'P' ? 'Principal' : 'Secondaire' }}
                                        </span>
                                    </p>
                                    <p><strong>Délai légal actuel :</strong> 
                                        <span class="badge bg-dark">{{ $calendrierConservation->delai_legal != '_' ? $calendrierConservation->delai_legal : 'Non défini' }}</span>
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
                                        <span class="badge bg-secondary me-2" id="preview-plan">{{ $calendrierConservation->plan_classement_code }}</span>
                                        <span class="badge {{ $calendrierConservation->sort_final == 'C' ? 'bg-success' : ($calendrierConservation->sort_final == 'D' ? 'bg-danger' : 'bg-warning') }}" id="preview-sort">
                                            {{ $calendrierConservation->sort_final == 'C' ? 'Conservation' : ($calendrierConservation->sort_final == 'D' ? 'Destruction' : 'Tri') }}
                                        </span>
                                        <span class="badge bg-info ms-2" id="preview-type">
                                            {{ $calendrierConservation->principal_secondaire == 'P' ? 'Principal' : 'Secondaire' }}
                                        </span>
                                    </div>
                                    <p class="mb-1 fw-bold" id="preview-pieces">{{ $calendrierConservation->pieces_constituant ?: 'Pièces constituant...' }}</p>
                                    <small class="text-muted" id="preview-reference">{{ $calendrierConservation->reference_juridique ?: 'Référence juridique...' }}</small>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-end">
                                        <div class="mb-1">
                                            <span class="badge bg-primary me-1" id="preview-legal">{{ $calendrierConservation->delai_legal != '_' ? $calendrierConservation->delai_legal : 'Non défini' }}</span>
                                        </div>
                                        <div class="mb-1">
                                            <span class="badge bg-info me-1" id="preview-ac">{{ $calendrierConservation->archives_courantes }}</span>
                                            <span class="badge bg-warning me-1" id="preview-ai">{{ $calendrierConservation->archives_intermediaires }}</span>
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
            reference: document.getElementById('preview-reference')
        };

        // Update preview
        function updatePreview() {
            const planOption = inputs.plan.options[inputs.plan.selectedIndex];
            const planCode = planOption ? planOption.value : '{{ $calendrierConservation->plan_classement_code }}';
            const sortValue = inputs.sort.value;
            const typeValue = inputs.type.value;
            const pieces = inputs.pieces.value || 'Pièces constituant...';
            const legal = inputs.legal.value || 'Non défini';
            const current = inputs.current.value || '{{ $calendrierConservation->archives_courantes }}';
            const intermediate = inputs.intermediate.value || '{{ $calendrierConservation->archives_intermediaires }}';
            const reference = inputs.reference.value || 'Référence juridique...';

            // Update preview elements
            preview.plan.textContent = planCode;
            preview.pieces.textContent = pieces;
            preview.legal.textContent = legal;
            preview.ac.textContent = current;
            preview.ai.textContent = intermediate;
            preview.reference.textContent = reference;

            // Update sort badge
            const sortConfig = {
                'C': { text: 'Conservation', class: 'bg-success' },
                'D': { text: 'Destruction', class: 'bg-danger' },
                'T': { text: 'Tri', class: 'bg-warning' }
            };

            const config = sortConfig[sortValue] || { 
                text: '{{ $calendrierConservation->sort_final == "C" ? "Conservation" : ($calendrierConservation->sort_final == "D" ? "Destruction" : "Tri") }}', 
                class: '{{ $calendrierConservation->sort_final == "C" ? "bg-success" : ($calendrierConservation->sort_final == "D" ? "bg-danger" : "bg-warning") }}' 
            };
            preview.sort.textContent = config.text;
            preview.sort.className = 'badge ' + config.class;

            // Update type badge
            const typeConfig = {
                'P': { text: 'Principal', class: 'bg-primary' },
                'S': { text: 'Secondaire', class: 'bg-info' }
            };

            const typeConf = typeConfig[typeValue] || { 
                text: '{{ $calendrierConservation->principal_secondaire == "P" ? "Principal" : "Secondaire" }}', 
                class: '{{ $calendrierConservation->principal_secondaire == "P" ? "bg-primary" : "bg-info" }}' 
            };
            preview.type.textContent = typeConf.text;
            preview.type.className = 'badge ' + typeConf.class + ' ms-2';
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

        // Warn about significant changes
        const originalPlan = '{{ $calendrierConservation->plan_classement_code }}';
        const originalSort = '{{ $calendrierConservation->sort_final }}';
        
        const newPlan = document.querySelector('[name="plan_classement_code"]').value;
        const newSort = document.querySelector('[name="sort_final"]').value;
        
        if (newPlan !== originalPlan || newSort !== originalSort) {
            if (!confirm('Vous avez modifié des éléments critiques de cette règle. Êtes-vous sûr de vouloir continuer ?')) {
                e.preventDefault();
                return false;
            }
        }
    });
</script>
@endpush