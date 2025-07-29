<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('user.dashboard') }}">
                <i class="fas fa-archive me-2"></i>
                Système d'Archives
            </a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('user.dashboard') }}">
                    <i class="fas fa-home me-1"></i>Tableau de bord
                </a>
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                        {{ $user->full_name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item active" href="{{ route('user.profile') }}">
                            <i class="fas fa-user-edit me-2"></i>Mon Profil
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('user.notifications') }}">
                            <i class="fas fa-bell me-2"></i>Notifications
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('user.dashboard') }}">Tableau de bord</a></li>
                <li class="breadcrumb-item active">Mon Profil</li>
            </ol>
        </nav>

        <!-- Messages de succès/erreur -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Erreurs détectées :</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <!-- Informations du profil -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                        </div>
                        <h5 class="card-title">{{ $user->full_name }}</h5>
                        <p class="text-muted mb-1">{{ $user->role_display }}</p>
                        <p class="text-muted mb-3">{{ $user->email }}</p>
                        
                        <div class="row text-center">
                            <div class="col">
                                <div class="border-end">
                                    <div class="h6 mb-0">{{ $user->entiteProductrice ? $user->entiteProductrice->nom_entite : 'Non assigné' }}</div>
                                    <small class="text-muted">Entité</small>
                                </div>
                            </div>
                            <div class="col">
                                <div class="h6 mb-0">{{ $user->created_at->format('M Y') }}</div>
                                <small class="text-muted">Membre depuis</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations système -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>Informations système
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-6"><strong>ID utilisateur :</strong></div>
                            <div class="col-6">{{ $user->id }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Statut :</strong></div>
                            <div class="col-6">
                                <span class="badge bg-success">Actif</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Créé le :</strong></div>
                            <div class="col-6">{{ $user->created_at->format('d/m/Y') }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6"><strong>Modifié le :</strong></div>
                            <div class="col-6">{{ $user->updated_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulaire de modification -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-edit me-2"></i>Modifier mes informations
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PATCH')

                            <!-- Informations personnelles -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="fas fa-user me-2"></i>Informations personnelles
                                    </h6>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror" 
                                           id="nom" name="nom" value="{{ old('nom', $user->nom) }}" required>
                                    @error('nom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('prenom') is-invalid @enderror" 
                                           id="prenom" name="prenom" value="{{ old('prenom', $user->prenom) }}" required>
                                    @error('prenom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Changement de mot de passe -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary border-bottom pb-2 mb-3">
                                        <i class="fas fa-lock me-2"></i>Changer le mot de passe
                                    </h6>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="current_password" class="form-label">Mot de passe actuel</label>
                                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" name="current_password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Requis uniquement si vous souhaitez changer votre mot de passe</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Nouveau mot de passe</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Minimum 8 caractères</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>

                            <!-- Informations en lecture seule -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-secondary border-bottom pb-2 mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Informations système (lecture seule)
                                    </h6>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Rôle</label>
                                    <input type="text" class="form-control" value="{{ $user->role_display }}" readonly>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Entité productrice</label>
                                    <input type="text" class="form-control" 
                                           value="{{ $user->entiteProductrice ? $user->entiteProductrice->nom_entite : 'Non assigné' }}" readonly>
                                </div>

                                @if($user->organisme)
                                <div class="col-12 mb-3">
                                    <label class="form-label">Organisme</label>
                                    <input type="text" class="form-control" 
                                           value="{{ $user->organisme->nom_org }}" readonly>
                                </div>
                                @endif
                            </div>

                            <!-- Boutons d'action -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Retour
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script pour la validation côté client -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('password_confirmation');
            const currentPasswordField = document.getElementById('current_password');
            
            // Validation du mot de passe
            function validatePasswords() {
                if (passwordField.value && passwordField.value !== confirmPasswordField.value) {
                    confirmPasswordField.setCustomValidity('Les mots de passe ne correspondent pas');
                } else {
                    confirmPasswordField.setCustomValidity('');
                }
            }
            
            // Vérifier si le mot de passe actuel est requis
            function checkCurrentPasswordRequired() {
                if (passwordField.value && !currentPasswordField.value) {
                    currentPasswordField.setCustomValidity('Le mot de passe actuel est requis pour changer le mot de passe');
                } else {
                    currentPasswordField.setCustomValidity('');
                }
            }
            
            passwordField.addEventListener('input', function() {
                validatePasswords();
                checkCurrentPasswordRequired();
            });
            
            confirmPasswordField.addEventListener('input', validatePasswords);
            currentPasswordField.addEventListener('input', checkCurrentPasswordRequired);
        });
    </script>
</body>
</html>