@extends('layouts.admin')

@section('title', 'Modifier un utilisateur')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-user-edit me-2"></i>
            Modifier un utilisateur
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
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Informations de l'utilisateur : {{ $user->full_name }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    
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
                                       value="{{ old('nom', $user->nom) }}" 
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
                                       value="{{ old('prenom', $user->prenom) }}" 
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
                               value="{{ old('email', $user->email) }}" 
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
                               value="{{ old('login', $user->login ?? '') }}" 
                               placeholder="Nom d'utilisateur (optionnel)">
                        <div class="form-text">Si laissé vide, l'email sera utilisé comme login.</div>
                    </div>

                    <!-- Password Section -->
                    <div class="card border-info mb-3">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-key me-2"></i>
                                Changer le mot de passe
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Laissez ces champs vides si vous ne souhaitez pas changer le mot de passe.
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">
                                            Nouveau mot de passe
                                        </label>
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password" 
                                                   placeholder="Minimum 8 caractères">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                                <i class="fas fa-eye" id="password-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">
                                            Confirmer le nouveau mot de passe
                                        </label>
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation" 
                                                   placeholder="Confirmer le mot de passe">
                                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                                <i class="fas fa-eye" id="password_confirmation-eye"></i>
                                            </button>
                                        </div>
                                    </div>
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
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                        Administrateur
                                    </option>
                                    <option value="gestionnaire_archives" {{ old('role', $user->role) == 'gestionnaire_archives' ? 'selected' : '' }}>
                                        Gestionnaire d'archives
                                    </option>
                                    <option value="service_producteurs" {{ old('role', $user->role) == 'service_producteurs' ? 'selected' : '' }}>
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
                                    <option value="ADII" {{ old('organisme', $user->organisme ?? '') == 'ADII' ? 'selected' : '' }}>
                                        ADII - Administration des Douanes et Impôts Indirects
                                    </option>
                                    <option value="DGI" {{ old('organisme', $user->organisme ?? '') == 'DGI' ? 'selected' : '' }}>
                                        DGI - Direction Générale des Impôts
                                    </option>
                                    <option value="TGR" {{ old('organisme', $user->organisme ?? '') == 'TGR' ? 'selected' : '' }}>
                                        TGR - Trésorerie Générale du Royaume
                                    </option>
                                    <option value="ANCFCC" {{ old('organisme', $user->organisme ?? '') == 'ANCFCC' ? 'selected' : '' }}>
                                        ANCFCC - Agence Nationale de la Conservation Foncière
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="card border-light mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Informations supplémentaires
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Date de création :</strong> {{ $user->date_creation->format('d/m/Y à H:i') }}</p>
                                    <p><strong>Dernière mise à jour :</strong> {{ $user->updated_at->format('d/m/Y à H:i') }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Email vérifié :</strong> 
                                        @if($user->email_verified_at)
                                            <span class="badge bg-success">Oui</span>
                                            <small class="text-muted">({{ $user->email_verified_at->format('d/m/Y') }})</small>
                                        @else
                                            <span class="badge bg-warning">Non</span>
                                        @endif
                                    </p>
                                    <p><strong>Statut du compte :</strong> 
                                        <span class="badge bg-success">Actif</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
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

    // Auto-generate login from email (only if login is empty)
    document.getElementById('email').addEventListener('input', function() {
        const loginField = document.getElementById('login');
        if (!loginField.value) {
            loginField.value = this.value.split('@')[0];
        }
    });

    // Password strength indicator
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        
        // Remove existing indicator
        const existing = this.parentNode.parentNode.querySelector('.password-strength');
        if (existing) existing.remove();
        
        if (password.length > 0) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            const strengthIndicator = document.createElement('div');
            const colors = ['danger', 'warning', 'warning', 'info', 'success'];
            const texts = ['Très faible', 'Faible', 'Moyen', 'Fort', 'Très fort'];
            
            strengthIndicator.className = `form-text text-${colors[strength - 1]} password-strength`;
            strengthIndicator.textContent = `Force du mot de passe: ${texts[strength - 1]}`;
            
            this.parentNode.parentNode.appendChild(strengthIndicator);
        }
    });

    // Warn if changing role of current user
    @if($user->id === auth()->id())
    document.getElementById('role').addEventListener('change', function() {
        if (this.value !== '{{ $user->role }}') {
            if (!confirm('Attention : Vous modifiez votre propre rôle. Cela pourrait affecter vos permissions. Êtes-vous sûr de vouloir continuer ?')) {
                this.value = '{{ $user->role }}';
            }
        }
    });
    @endif
</script>
@endpush