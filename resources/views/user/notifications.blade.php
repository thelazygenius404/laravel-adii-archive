<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - Utilisateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .notification-item {
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        .notification-item:hover {
            background-color: #f8f9fa;
            transform: translateX(2px);
        }
        .notification-item.unread {
            border-left-color: #0d6efd;
            background-color: #f8f9ff;
        }
        .notification-item.read {
            border-left-color: #6c757d;
        }
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .filter-tabs .nav-link {
            border-radius: 20px;
            margin-right: 10px;
        }
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #6c757d;
        }
    </style>
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
                        <li><a class="dropdown-item" href="{{ route('user.profile') }}">
                            <i class="fas fa-user-edit me-2"></i>Mon Profil
                        </a></li>
                        <li><a class="dropdown-item active" href="{{ route('user.notifications') }}">
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
                <li class="breadcrumb-item active">Notifications</li>
            </ol>
        </nav>

        <!-- En-tête avec statistiques -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-gradient bg-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h1 class="h3 mb-1">
                                    <i class="fas fa-bell me-2"></i>Mes Notifications
                                </h1>
                                <p class="mb-0">Gérez vos notifications et alertes système</p>
                            </div>
                            <div class="col-auto">
                                <div class="row text-center">
                                    <div class="col">
                                        <div class="h4 mb-0">0</div>
                                        <small>Non lues</small>
                                    </div>
                                    <div class="col">
                                        <div class="h4 mb-0">0</div>
                                        <small>Total</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres et actions -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <ul class="nav nav-pills filter-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" data-filter="all">
                            <i class="fas fa-list me-1"></i>Toutes (0)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-filter="unread">
                            <i class="fas fa-circle me-1"></i>Non lues (0)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-filter="system">
                            <i class="fas fa-cog me-1"></i>Système (0)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-filter="alerts">
                            <i class="fas fa-exclamation-triangle me-1"></i>Alertes (0)
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-lg-4 text-end">
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary btn-sm" disabled>
                        <i class="fas fa-check-double me-1"></i>Marquer tout comme lu
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" disabled>
                        <i class="fas fa-trash me-1"></i>Tout supprimer
                    </button>
                </div>
            </div>
        </div>

        <!-- Liste des notifications -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body p-0">
                        <!-- État vide -->
                        <div class="empty-state">
                            <div class="mb-4">
                                <i class="fas fa-bell-slash fa-4x text-muted"></i>
                            </div>
                            <h5 class="text-muted">Aucune notification</h5>
                            <p class="text-muted mb-4">
                                Vous n'avez actuellement aucune notification.<br>
                                Les nouvelles notifications apparaîtront ici.
                            </p>
                            
                            <!-- Exemples de types de notifications -->
                            <div class="row mt-5">
                                <div class="col-md-4">
                                    <div class="text-center p-3">
                                        <div class="notification-icon bg-info text-white mx-auto mb-2">
                                            <i class="fas fa-info"></i>
                                        </div>
                                        <h6>Notifications système</h6>
                                        <small class="text-muted">Mises à jour, maintenance</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3">
                                        <div class="notification-icon bg-warning text-white mx-auto mb-2">
                                            <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                                        <h6>Alertes importantes</h6>
                                        <small class="text-muted">Problèmes, échéances</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3">
                                        <div class="notification-icon bg-success text-white mx-auto mb-2">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <h6>Confirmations</h6>
                                        <small class="text-muted">Actions réussies</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Exemple de notifications (cachées par défaut) -->
                        <div id="notifications-list" style="display: none;">
                            <!-- Notification système -->
                            <div class="notification-item unread p-3 border-bottom">
                                <div class="d-flex">
                                    <div class="notification-icon bg-info text-white me-3">
                                        <i class="fas fa-info"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="mb-1">Mise à jour système</h6>
                                            <small class="text-muted">Il y a 2 heures</small>
                                        </div>
                                        <p class="mb-1 text-muted">
                                            Le système a été mis à jour avec de nouvelles fonctionnalités.
                                        </p>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Marquer comme lu
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash me-1"></i>Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notification d'alerte -->
                            <div class="notification-item read p-3 border-bottom">
                                <div class="d-flex">
                                    <div class="notification-icon bg-warning text-white me-3">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="mb-1">Profil incomplet</h6>
                                            <small class="text-muted">Hier</small>
                                        </div>
                                        <p class="mb-1 text-muted">
                                            Veuillez compléter votre profil pour une meilleure expérience.
                                        </p>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('user.profile') }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-user-edit me-1"></i>Compléter le profil
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash me-1"></i>Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notification de bienvenue -->
                            <div class="notification-item read p-3">
                                <div class="d-flex">
                                    <div class="notification-icon bg-success text-white me-3">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h6 class="mb-1">Bienvenue dans le système !</h6>
                                            <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-1 text-muted">
                                            Votre compte a été créé avec succès. Explorez les fonctionnalités disponibles.
                                        </p>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('user.dashboard') }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-home me-1"></i>Tableau de bord
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash me-1"></i>Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Paramètres de notification -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-cog me-2"></i>Paramètres de notification
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="emailNotifications" checked disabled>
                                    <label class="form-check-label" for="emailNotifications">
                                        <i class="fas fa-envelope me-2"></i>Notifications par email
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="systemNotifications" checked disabled>
                                    <label class="form-check-label" for="systemNotifications">
                                        <i class="fas fa-desktop me-2"></i>Notifications système
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="securityAlerts" checked disabled>
                                    <label class="form-check-label" for="securityAlerts">
                                        <i class="fas fa-shield-alt me-2"></i>Alertes de sécurité
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="maintenanceAlerts" disabled>
                                    <label class="form-check-label" for="maintenanceAlerts">
                                        <i class="fas fa-tools me-2"></i>Alertes de maintenance
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Les paramètres de notification seront bientôt configurables. Pour le moment, vous recevrez toutes les notifications importantes.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script pour la gestion des filtres -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterTabs = document.querySelectorAll('.filter-tabs .nav-link');
            const notificationsList = document.getElementById('notifications-list');
            const emptyState = document.querySelector('.empty-state');
            
            // Gestion des filtres
            filterTabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Supprimer la classe active de tous les onglets
                    filterTabs.forEach(t => t.classList.remove('active'));
                    
                    // Ajouter la classe active à l'onglet cliqué
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-filter');
                    
                    // Pour la démonstration, on peut montrer/cacher les notifications d'exemple
                    if (filter === 'all') {
                        // Afficher un exemple de notifications
                        // emptyState.style.display = 'none';
                        // notificationsList.style.display = 'block';
                    }
                });
            });
            
            // Simulation d'actions sur les notifications
            document.addEventListener('click', function(e) {
                if (e.target.closest('.btn-outline-primary')) {
                    const button = e.target.closest('.btn-outline-primary');
                    if (button.innerHTML.includes('Marquer comme lu')) {
                        button.innerHTML = '<i class="fas fa-check me-1"></i>Lu';
                        button.classList.remove('btn-outline-primary');
                        button.classList.add('btn-success');
                        button.disabled = true;
                        
                        // Marquer la notification comme lue
                        const notificationItem = button.closest('.notification-item');
                        notificationItem.classList.remove('unread');
                        notificationItem.classList.add('read');
                    }
                }
                
                if (e.target.closest('.btn-outline-danger')) {
                    const button = e.target.closest('.btn-outline-danger');
                    if (button.innerHTML.includes('Supprimer')) {
                        if (confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
                            const notificationItem = button.closest('.notification-item');
                            notificationItem.style.transition = 'all 0.3s ease';
                            notificationItem.style.opacity = '0';
                            notificationItem.style.transform = 'translateX(-100%)';
                            
                            setTimeout(() => {
                                notificationItem.remove();
                                
                                // Vérifier s'il reste des notifications
                                const remainingNotifications = document.querySelectorAll('.notification-item');
                                if (remainingNotifications.length === 0) {
                                    notificationsList.style.display = 'none';
                                    emptyState.style.display = 'block';
                                }
                            }, 300);
                        }
                    }
                }
            });
            
            // Animation au survol des notifications
            const notificationItems = document.querySelectorAll('.notification-item');
            notificationItems.forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(5px)';
                });
                
                item.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
        });
    </script>
</body>
</html>