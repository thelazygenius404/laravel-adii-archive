@extends('layouts.admin')

@section('title', 'Ajouter un utilisateur')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-user-plus me-2"></i>
            Ajouter un compte utilisateur
        </h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour à la liste
        </a>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nom" class="form-label">
                                    Nom/Prénom <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('nom') is-invalid @enderror" 
                                       id="nom" 
                                       name="nom" 
                                       value="{{ old('nom') }}" 
                                       placeholder="Nom"
                                       required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Veuillez remplir ce champ.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="prenom" class="form-label">
                                    &nbsp; <!-- Spacer for alignment -->
                                </label>
                                <input type="text" 
                                       class="form-control @error('prenom') is-invalid @enderror" 
                                       id="prenom" 
                                       name="prenom" 
                                       value="{{ old('prenom') }}" 
                                       placeholder="Prénom"
                                       required>
                                @error('prenom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               placeholder="exemple@domaine.com"
                               required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Veuillez remplir ce champ.</div>
                    </div>

                    <div class="mb-3">
                        <label for="login" class="form-label">
                            Login
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="login" 
                               name="login" 
                               value="{{ old('login') }}" 
                               placeholder="Nom d'utilisateur (optionnel)">
                        <div class="form-text">Si laissé vide, l'email sera utilisé comme login.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    Mot de passe <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Minimum 8 caractères"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Veuillez remplir ce champ.</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">
                                    Confirmer le mot de passe <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password_confirmation" 
                                           name="password_confirmation" 
                                           placeholder="Confirmer le mot de passe"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye" id="password_confirmation-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">
                                    Type du compte <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" 
                                        name="role" 
                                        required>
                                    <option value="">Sélectionner un Type</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                        Administrateur
                                    </option>
                                    <option value="gestionnaire_archives" {{ old('role') == 'gestionnaire_archives' ? 'selected' : '' }}>
                                        Gestionnaire d'archives
                                    </option>
                                    <option value="service_producteurs" {{ old('role') == 'service_producteurs' ? 'selected' : '' }}>
                                        Service producteurs
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="organisme" class="form-label">
                                    Organisme <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="organisme" name="organisme" required>
                                    <option value="">Sélectionner un Service</option>
                                    <option value="ADII">ADII - Administration des Douanes et Impôts Indirects</option>
                                    <option value="DGI">DGI - Direction Générale des Impôts</option>
                                    <option value="TGR">TGR - Trésorerie Générale du Royaume</option>
                                    <option value="ANCFCC">ANCFCC - Agence Nationale de la Conservation Foncière</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>
                            Valider
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
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const eyeIcon = document.getElementById(fieldId + '-eye');
        
        if (field.type === 'password') {
            field.type = 'text';
            eyeIcon.className = 'fas fa-eye-slash';
        } else {
            field.type = 'password';
            eyeIcon.className = 'fas fa-eye';
        }
    }

    // Auto-generate login from email
    document.getElementById('email').addEventListener('input', function() {
        const loginField = document.getElementById('login');
        if (!loginField.value) {
            loginField.value = this.value.split('@')[0];
        }
    });

    // Password strength indicator
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthIndicator = document.createElement('div');
        
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        // Remove existing indicator
        const existing = this.parentNode.parentNode.querySelector('.password-strength');
        if (existing) existing.remove();
        
        if (password.length > 0) {
            const colors = ['danger', 'warning', 'warning', 'info', 'success'];
            const texts = ['Très faible', 'Faible', 'Moyen', 'Fort', 'Très fort'];
            
            strengthIndicator.className = `form-text text-${colors[strength - 1]} password-strength`;
            strengthIndicator.textContent = `Force du mot de passe: ${texts[strength - 1]}`;
            
            this.parentNode.parentNode.appendChild(strengthIndicator);
        }
    });
</script>
@endpush