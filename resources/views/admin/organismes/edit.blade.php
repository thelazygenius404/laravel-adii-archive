@extends('layouts.admin')

@section('title', 'Modifier un Organisme')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-edit me-2"></i>
            Modifier un Organisme
        </h1>
        <a href="{{ route('admin.organismes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour à la liste
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Modifier : {{ $organisme->nom_org }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.organismes.update', $organisme) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="nom_org" class="form-label">
                            Nom de l'Organisme <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nom_org') is-invalid @enderror" 
                               id="nom_org" 
                               name="nom_org" 
                               value="{{ old('nom_org', $organisme->nom_org) }}" 
                               placeholder="Ex: ADII - Administration des Douanes et Impôts Indirects"
                               required>
                        @error('nom_org')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Veuillez saisir le nom complet de l'organisme.</div>
                    </div>

                    <!-- Information supplémentaire -->
                    <div class="card border-light mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Informations sur l'Organisme
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <p><strong>Date de création :</strong> {{ $organisme->created_at->format('d/m/Y à H:i') }}</p>
                                    <p><strong>Dernière modification :</strong> {{ $organisme->updated_at->format('d/m/Y à H:i') }}</p>
                                    <p><strong>Nombre d'entités productrices :</strong> 
                                        <span class="badge bg-info">{{ $organisme->entiteProductrices()->count() }}</span>
                                    </p>
                                    <p><strong>Nombre d'utilisateurs :</strong> 
                                        <span class="badge bg-success">{{ $organisme->users()->count() }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Warning for entities -->
                    @if($organisme->entiteProductrices()->count() > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Attention :</strong> Cet organisme contient {{ $organisme->entiteProductrices()->count() }} entité(s) productrice(s). 
                            La modification du nom affectera l'affichage dans toutes les entités associées.
                        </div>
                    @endif

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.organismes.index') }}" class="btn btn-secondary">
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
    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const nomOrg = document.getElementById('nom_org').value.trim();
        
        if (!nomOrg) {
            e.preventDefault();
            alert('Veuillez saisir le nom de l\'organisme.');
            return false;
        }
        
        if (nomOrg.length < 3) {
            e.preventDefault();
            alert('Le nom de l\'organisme doit contenir au moins 3 caractères.');
            return false;
        }

        // Confirm if there are related entities
        @if($organisme->entiteProductrices()->count() > 0)
            if (!confirm('Cet organisme a des entités productrices associées. Êtes-vous sûr de vouloir modifier son nom ?')) {
                e.preventDefault();
                return false;
            }
        @endif
    });
</script>
@endpush