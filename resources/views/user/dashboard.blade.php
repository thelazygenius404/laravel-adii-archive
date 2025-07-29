<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Utilisateur</title>
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
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>
                        {{ $user->full_name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('user.profile') }}">
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
        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h1 class="h3 mb-1">Bienvenue, {{ $user->prenom }} !</h1>
                                <p class="mb-0">
                                    <i class="fas fa-building me-2"></i>{{ $stats['entite'] }}
                                    @if($stats['organisme'] !== 'Non assigné')
                                        <span class="ms-3">
                                            <i class="fas fa-sitemap me-2"></i>{{ $stats['organisme'] }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-auto">
                                <div class="text-center">
                                    <div class="h4 mb-0">{{ $stats['profile_completion'] }}%</div>
                                    <small>Profil complété</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="text-success mb-3">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                        <h5 class="card-title">Statut du compte</h5>
                        <p class="card-text">
                            <span class="badge bg-success">Actif</span>
                        </p>
                        <small class="text-muted">
                            Membre depuis le {{ $stats['account_created']->format('d/m/Y') }}
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="text-info mb-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h5 class="card-title">Dernière activité</h5>
                        <p class="card-text">
                            {{ $stats['last_login']->diffForHumans() }}
                        </p>
                        <small class="text-muted">
                            {{ $stats['last_login']->format('d/m/Y à H:i') }}
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="text-warning mb-3">
                            <i class="fas fa-bell fa-2x"></i>
                        </div>
                        <h5 class="card-title">Notifications</h5>
                        <p class="card-text">
                            <span class="h4">0</span> nouvelles
                        </p>
                        <a href="{{ route('user.notifications') }}" class="btn btn-sm btn-outline-warning">
                            Voir toutes
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-bolt me-2"></i>Actions rapides
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="{{ route('user.profile') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3">
                                    <i class="fas fa-user-edit fa-2x mb-2"></i>
                                    <span>Modifier mon profil</span>
                                </a>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <button class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" disabled>
                                    <i class="fas fa-search fa-2x mb-2"></i>
                                    <span>Rechercher des archives</span>
                                    <small class="text-muted">(Bientôt disponible)</small>
                                </button>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <button class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" disabled>
                                    <i class="fas fa-download fa-2x mb-2"></i>
                                    <span>Mes téléchargements</span>
                                    <small class="text-muted">(Bientôt disponible)</small>
                                </button>
                            </div>
                            
                            <div class="col-md-3 mb-3">
                                <button class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" disabled>
                                    <i class="fas fa-question-circle fa-2x mb-2"></i>
                                    <span>Aide & Support</span>
                                    <small class="text-muted">(Bientôt disponible)</small>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations du profil -->
        @if($stats['profile_completion'] < 100)
        <div class="row">
            <div class="col-12">
                <div class="alert alert-warning">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-1">Profil incomplet</h6>
                            <p class="mb-2">Votre profil n'est complété qu'à {{ $stats['profile_completion'] }}%. Complétez-le pour une meilleure expérience.</p>
                            <a href="{{ route('user.profile') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit me-1"></i>Compléter mon profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>