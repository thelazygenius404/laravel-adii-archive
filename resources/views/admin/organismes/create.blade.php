@extends('layouts.admin')

@section('title', 'Ajouter un Organisme')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-plus me-2"></i>
            Ajouter un Organisme
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
            <div class="card-body">
                <form method="POST" action="{{ route('admin.organismes.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="nom_org" class="form-label">
                            Nom de l'Organisme <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nom_org') is-invalid @enderror" 
                               id="nom_org" 
                               name="nom_org" 
                               value="{{ old('nom_org') }}" 
                               placeholder="Ex: ADII - Administration des Douanes et Impôts Indirects"
                               required>
                        @error('nom_org')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Veuillez saisir le nom complet de l'organisme.</div>
                    </div>

                    <!-- Suggestions -->
                    <div class="card border-info mb-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-lightbulb me-2"></i>
                                Suggestions d'organismes
                            </h6>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">Vous pouvez utiliser l'un de ces modèles :</p>
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm text-start" onclick="fillForm('ADII - Administration des Douanes et Impôts Indirects')">
                                    ADII - Administration des Douanes et Impôts Indirects
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm text-start" onclick="fillForm('DGI - Direction Générale des Impôts')">
                                    DGI - Direction Générale des Impôts
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm text-start" onclick="fillForm('TGR - Trésorerie Générale du Royaume')">
                                    TGR - Trésorerie Générale du Royaume
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm text-start" onclick="fillForm('ANCFCC - Agence Nationale de la Conservation Foncière')">
                                    ANCFCC - Agence Nationale de la Conservation Foncière
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm text-start" onclick="fillForm('MEF - Ministère de l\\'Économie et des Finances')">
                                    MEF - Ministère de l'Économie et des Finances
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.organismes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>
                            Créer l'Organisme
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
    function fillForm(nom) {
        document.getElementById('nom_org').value = nom;
        document.getElementById('nom_org').focus();
    }

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
    });
</script>
@endpush