@extends('layouts.admin')

@section('title', 'Modifier un Plan de Classement')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-edit me-2"></i>
            Modifier un Plan de Classement
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
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Modification du plan : {{ $planClassement->formatted_code }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.plan-classement.update', $planClassement) }}">
                    @csrf
                    @method('PUT')
                    
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
                                       value="{{ old('code_classement', $planClassement->code_classement) }}" 
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
                                           value="{{ $planClassement->formatted_code }}">
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
                                  required>{{ old('objet_classement', $planClassement->objet_classement) }}</textarea>
                        @error('objet_classement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <span id="char-count">{{ strlen($planClassement->objet_classement) }}</span>/500 caractères utilisés.
                        </div>
                    </div>

                    <!-- Informations sur le plan -->
                    <div class="card border-light mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Informations sur le Plan
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Date de création :</strong> {{ $planClassement->created_at->format('d/m/Y à H:i') }}</p>
                                    <p><strong>Dernière modification :</strong> {{ $planClassement->updated_at->format('d/m/Y à H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Nombre de règles de conservation :</strong> 
                                        <span class="badge bg-info">{{ $planClassement->calendrierConservation()->count() }}</span>
                                    </p>
                                    @if($planClassement->calendrierConservation()->count() > 0)
                                        <p>
                                            <a href="{{ route('admin.calendrier-conservation.index', ['plan_classement' => $planClassement->id]) }}" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-calendar me-1"></i>
                                                Voir les règles
                                            </a>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Warning for rules -->
                    @if($planClassement->calendrierConservation()->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Attention :</strong> Ce plan de classement contient {{ $planClassement->calendrierConservation()->count() }} règle(s) de conservation. 
                            Toute modification peut affecter ces règles.
                        </div>
                    @endif

                    <!-- Aperçu du plan modifié -->
                    <div class="card border-success mb-3" id="preview-card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-eye me-2"></i>
                                Aperçu du Plan de Classement Modifié
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <span class="badge bg-primary fs-5" id="preview-code">{{ $planClassement->formatted_code }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-0 fw-bold" id="preview-description">{{ $planClassement->objet_classement }}</p>
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
        const codeInput = document.getElementById('code_classement');
        const codePreview = document.getElementById('code_preview');
        const descriptionInput = document.getElementById('objet_classement');
        const charCount = document.getElementById('char-count');
        const previewCode = document.getElementById('preview-code');
        const previewDescription = document.getElementById('preview-description');

        // Update code preview
        codeInput.addEventListener('input', function() {
            const value = this.value || '1';
            const formattedCode = value.toString().padStart(3, '0');
            codePreview.value = formattedCode;
            previewCode.textContent = formattedCode;
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
        });

        // Initialize character count
        const initialLength = descriptionInput.value.length;
        charCount.textContent = initialLength;
        
        if (initialLength > 450) {
            charCount.className = 'text-danger fw-bold';
        } else if (initialLength > 350) {
            charCount.className = 'text-warning fw-bold';
        }
    });

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

        // Warn if changing code of plan with rules
        @if($planClassement->calendrierConservation()->count() > 0)
            const originalCode = {{ $planClassement->code_classement }};
            if (parseInt(code) !== originalCode) {
                if (!confirm('Ce plan de classement a des règles de conservation associées. Êtes-vous sûr de vouloir modifier son code ?')) {
                    e.preventDefault();
                    return false;
                }
            }
        @endif
    });
</script>
@endpush