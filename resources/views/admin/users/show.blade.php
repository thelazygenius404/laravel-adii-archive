@extends('layouts.admin')

@section('title', 'Détails de l\'Utilisateur')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title" role="heading" aria-level="1">
            <i class="fas fa-user me-2" aria-hidden="true"></i>
            Détails de l'Utilisateur
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary" aria-label="Modifier l'utilisateur">
                <i class="fas fa-edit me-2" aria-hidden="true"></i>
                Modifier
            </a>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary" aria-label="Retour à la liste des utilisateurs">
                <i class="fas fa-arrow-left me-2" aria-hidden="true"></i>
                Retour
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Main Information -->
    <div class="col-lg-8">
        <!-- Informations Personnelles -->
        <div class="card" role="region" aria-labelledby="personal-info">
            <div class="card-header">
                <h5 class="card-title mb-0" id="personal-info">
                    <i class="fas fa-id-card me-2" aria-hidden="true"></i>
                    Informations Personnelles
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <!-- Avatar -->
                        <div class="avatar mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                                 style="width: 120px; height: 120px; font-size: 2.5rem;"
                                 aria-label="Avatar de l'utilisateur">
                                {{ strtoupper(substr($user->nom ?? '', 0, 1) . substr($user->prenom ?? '', 0, 1)) }}
                            </div>
                        </div>
                        
                        <!-- Statut -->
                        <div class="mb-3">
                            @if($user->email_verified_at)
                                <span class="badge bg-success" role="status">
                                    <i class="fas fa-check-circle me-1" aria-hidden="true"></i>
                                    Compte vérifié
                                </span>
                            @else
                                <span class="badge bg-warning" role="status">
                                    <i class="fas fa-exclamation-triangle me-1" aria-hidden="true"></i>
                                    Email non vérifié
                                </span>
                            @endif
                        </div>
                        
                        <!-- Dernière connexion -->
                        <div class="text-muted">
                            <small>
                                <i class="fas fa-clock me-1" aria-hidden="true"></i>
                                Dernière activité<br>
                                {{ $user->updated_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Nom complet :</strong></td>
                                        <td>{{ $user->full_name ?? ($user->nom . ' ' . $user->prenom) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Nom :</strong></td>
                                        <td>{{ $user->nom }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Prénom :</strong></td>
                                        <td>{{ $user->prenom }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email :</strong></td>
                                        <td>
                                            <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                                {{ $user->email }}
                                            </a>
                                            @if($user->email_verified_at)
                                                <i class="fas fa-check-circle text-success ms-1" title="Email vérifié" aria-hidden="true"></i>
                                            @else
                                                <i class="fas fa-exclamation-triangle text-warning ms-1" title="Email non vérifié" aria-hidden="true"></i>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Type de compte :</strong></td>
                                        <td>
                                            @php
                                                $roleDisplayMap = [
                                                    'admin' => ['label' => 'Administrateur', 'class' => 'bg-danger'],
                                                    'gestionnaire_archives' => ['label' => 'Gestionnaire d\'archives', 'class' => 'bg-info'],
                                                    'service_producteurs' => ['label' => 'Service producteurs', 'class' => 'bg-success'],
                                                ];
                                                $roleInfo = $roleDisplayMap[$user->role] ?? ['label' => $user->role, 'class' => 'bg-secondary'];
                                            @endphp
                                            <span class="badge {{ $roleInfo['class'] }}">
                                                {{ $roleInfo['label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date de création :</strong></td>
                                        <td>{{ ($user->date_creation ?? $user->created_at)->format('d/m/Y à H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Dernière modification :</strong></td>
                                        <td>{{ $user->updated_at->format('d/m/Y à H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>ID Utilisateur :</strong></td>
                                        <td><code class="user-id" role="button" aria-label="Copier l'ID utilisateur">#{{ $user->id }}</code></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Entité Productrice (si applicable) -->
        @if($user->entiteProductrice)
            <div class="card mt-4" role="region" aria-labelledby="entite-productrice">
                <div class="card-header">
                    <h5 class="card-title mb-0" id="entite-productrice">
                        <i class="fas fa-building me-2" aria-hidden="true"></i>
                        Entité Productrice Assignée
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-1">
                                <span class="badge bg-primary me-2">{{ $user->entiteProductrice->code_entite }}</span>
                                {{ $user->entiteProductrice->nom_entite }}
                            </h6>
                            <p class="text-muted mb-1">
                                <i class="fas fa-sitemap me-1" aria-hidden="true"></i>
                                <strong>Organisme :</strong> {{ $user->entiteProductrice->organisme->nom_org ?? 'N/A' }}
                            </p>
                            @if($user->entiteProductrice->parent)
                                <p class="text-muted mb-1">
                                    <i class="fas fa-arrow-up me-1" aria-hidden="true"></i>
                                    <strong>Entité parent :</strong> {{ $user->entiteProductrice->parent->nom_entite }}
                                </p>
                            @endif
                            <p class="text-muted mb-0">
                                <i class="fas fa-layer-group me-1" aria-hidden="true"></i>
                                <strong>Hiérarchie complète :</strong> {{ $user->entiteProductrice->full_name }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('admin.entites.show', $user->entiteProductrice) }}" 
                               class="btn btn-outline-primary btn-sm" aria-label="Voir les détails de l'entité">
                                <i class="fas fa-eye me-2" aria-hidden="true"></i>
                                Voir l'entité
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Permissions et Accès -->
        <div class="card mt-4" role="region" aria-labelledby="permissions">
            <div class="card-header">
                <h5 class="card-title mb-0" id="permissions">
                    <i class="fas fa-shield-alt me-2" aria-hidden="true"></i>
                    Permissions et Accès
                </h5>
            </div>
            <div class="card-body">
                @php
                    $permissions = [
                        'admin' => [
                            'Gestion complète du système',
                            'Configuration des droits d\'accès',
                            'Supervision des utilisateurs',
                            'Gestion des entités productrices',
                            'Gestion du plan de classement',
                            'Gestion du calendrier de conservation',
                            'Rapports administratifs complets',
                            'Export et import de données'
                        ],
                        'gestionnaire_archives' => [
                            'Gestion des collections d\'archives',
                            'Classification et indexation',
                            'Validation des versements',
                            'Maintenance documentaire',
                            'Recherche dans les archives',
                            'Consultation du calendrier de conservation',
                            'Rapports d\'activité'
                        ],
                        'service_producteurs' => [
                            'Dépôt de documents',
                            'Gestion des entités productrices assignées',
                            'Suivi des versements',
                            'Consultation personnalisée',
                            'Recherche dans les archives autorisées',
                            'Consultation du plan de classement'
                        ]
                    ];
                    $userPermissions = $permissions[$user->role] ?? ['Permissions non définies'];
                @endphp

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">Droits d'accès accordés :</h6>
                        <ul class="list-unstyled">
                            @foreach(array_slice($userPermissions, 0, ceil(count($userPermissions)/2)) as $permission)
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2" aria-hidden="true"></i>
                                    {{ $permission }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">&nbsp;</h6>
                        <ul class="list-unstyled">
                            @foreach(array_slice($userPermissions, ceil(count($userPermissions)/2)) as $permission)
                                <li class="mb-2">
                                    <i class="fas fa-check-circle text-success me-2" aria-hidden="true"></i>
                                    {{ $permission }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Restrictions -->
                @if($user->role !== 'admin')
                    <div class="alert alert-info mt-3" role="alert">
                        <i class="fas fa-info-circle me-2" aria-hidden="true"></i>
                        <strong>Restrictions :</strong> Cet utilisateur n'a pas accès aux fonctions d'administration système.
                        @if($user->role === 'service_producteurs')
                            L'accès est limité aux entités productrices assignées.
                        @endif
                    </div>
                @endif

                <!-- Role Change Form -->
                <div class="mt-4">
                    <h6 class="text-muted mb-3">Changer le rôle :</h6>
                    <form action="{{ route('admin.users.update-role', $user) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        @method('PATCH')
                        <select name="role" class="form-select w-auto" aria-label="Sélectionner un rôle">
                            @foreach($roleDisplayMap as $role => $config)
                                <option value="{{ $role }}" {{ $user->role === $role ? 'selected' : '' }}>
                                    {{ $config['label'] }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm" aria-label="Mettre à jour le rôle">
                            <i class="fas fa-save me-2" aria-hidden="true"></i>
                            Mettre à jour
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Historique d'Activité -->
        <div class="card mt-4" role="region" aria-labelledby="activity-log">
            <div class="card-header">
                <h5 class="card-title mb-0" id="activity-log">
                    <i class="fas fa-history me-2" aria-hidden="true"></i>
                    Historique d'Activité
                </h5>
            </div>
            <div class="card-body">
                @php
                    $activityLogs = $user->activityLogs()->latest()->paginate(10);
                @endphp
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>
                                    <i class="fas fa-calendar me-1" aria-hidden="true"></i>
                                    Date
                                </th>
                                <th>
                                    <i class="fas fa-cogs me-1" aria-hidden="true"></i>
                                    Action
                                </th>
                                <th>
                                    <i class="fas fa-info-circle me-1" aria-hidden="true"></i>
                                    Détails
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activityLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ $log->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-history fa-3x mb-3" aria-hidden="true"></i>
                                            <p class="mb-0">Aucune activité enregistrée</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($activityLogs->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Affichage de {{ $activityLogs->firstItem() }} à {{ $activityLogs->lastItem() }} sur {{ $activityLogs->total() }} résultats
                        </div>
                        <div>
                            {{ $activityLogs->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card" role="region" aria-labelledby="quick-stats">
            <div class="card-header">
                <h5 class="card-title mb-0" id="quick-stats">
                    <i class="fas fa-chart-bar me-2" aria-hidden="true"></i>
                    Statistiques
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-primary mb-1">{{ $user->id }}</h4>
                            <small class="text-muted">ID Utilisateur</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h4 class="text-success mb-1">
                                {{ ($user->created_at ?? $user->date_creation)->diffInDays(now()) }}
                            </h4>
                            <small class="text-muted">Jours depuis création</small>
                        </div>
                    </div>
                </div>

                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Statut :</span>
                        <span class="badge bg-success">Actif</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Email vérifié :</span>
                        @if($user->email_verified_at)
                            <span class="badge bg-success">Oui</span>
                        @else
                            <span class="badge bg-warning">Non</span>
                        @endif
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span>Type de compte :</span>
                        <span class="badge {{ $roleInfo['class'] }}">{{ $roleInfo['label'] }}</span>
                    </div>
                    @if($user->entiteProductrice)
                        <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>Entité assignée :</span>
                            <span class="badge bg-info">Oui</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4" role="region" aria-labelledby="quick-actions">
            <div class="card-header">
                <h5 class="card-title mb-0" id="quick-actions">
                    <i class="fas fa-bolt me-2" aria-hidden="true"></i>
                    Actions Rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary btn-sm" aria-label="Modifier cet utilisateur">
                        <i class="fas fa-edit me-2" aria-hidden="true"></i>
                        Modifier cet utilisateur
                    </a>
                    
                    <button class="btn btn-outline-info btn-sm" onclick="sendEmail('{{ $user->email }}')" aria-label="Envoyer un email">
                        <i class="fas fa-envelope me-2" aria-hidden="true"></i>
                        Envoyer un email
                    </button>
                    
                    @if(!$user->email_verified_at)
                        <button class="btn btn-outline-warning btn-sm" onclick="resendVerification({{ $user->id }})" aria-label="Renvoyer vérification email">
                            <i class="fas fa-paper-plane me-2" aria-hidden="true"></i>
                            Renvoyer vérification email
                        </button>
                    @endif
                    
                    @if($user->entiteProductrice)
                        <a href="{{ route('admin.entites.show', $user->entiteProductrice) }}" 
                           class="btn btn-outline-info btn-sm" aria-label="Voir l'entité assignée">
                            <i class="fas fa-building me-2" aria-hidden="true"></i>
                            Voir son entité
                        </a>
                    @else
                        <a href="{{ route('admin.users.edit', $user) }}" 
                           class="btn btn-outline-warning btn-sm" aria-label="Assigner une entité">
                            <i class="fas fa-building me-2" aria-hidden="true"></i>
                            Assigner une entité
                        </a>
                    @endif
                    
                    <!-- Export Options -->
                    <div class="dropdown">
                        <button class="btn btn-outline-success btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" 
                                aria-expanded="false" aria-label="Exporter les données">
                            <i class="fas fa-download me-2" aria-hidden="true"></i>
                            Exporter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="exportUserData('pdf')">PDF</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportUserData('csv')">CSV</a></li>
                            <li><a class="dropdown-item" href="#" onclick="exportUserData('json')">JSON</a></li>
                        </ul>
                    </div>
                    
                    <button class="btn btn-outline-secondary btn-sm" onclick="printUser()" aria-label="Imprimer les détails">
                        <i class="fas fa-print me-2" aria-hidden="true"></i>
                        Imprimer
                    </button>
                    
                    @if($user->id !== auth()->id())
                        <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete('{{ $user->id }}', '{{ $user->full_name ?? ($user->nom . ' ' . $user->prenom) }}')" 
                                aria-label="Supprimer cet utilisateur">
                            <i class="fas fa-trash me-2" aria-hidden="true"></i>
                            Supprimer
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Two-Factor Authentication -->
        <div class="card mt-4" role="region" aria-labelledby="two-factor-auth">
            <div class="card-header">
                <h5 class="card-title mb-0" id="two-factor-auth">
                    <i class="fas fa-lock me-2" aria-hidden="true"></i>
                    Authentification à Deux Facteurs
                </h5>
            </div>
            <div class="card-body">
                @if($user->two_factor_enabled)
                    <div class="alert alert-success py-2" role="alert">
                        <i class="fas fa-check-circle me-2" aria-hidden="true"></i>
                        <small>2FA activé le {{ $user->two_factor_enabled_at->format('d/m/Y') }}</small>
                    </div>
                    <button class="btn btn-outline-warning btn-sm" onclick="disable2FA({{ $user->id }})" aria-label="Désactiver 2FA">
                        <i class="fas fa-unlock me-2" aria-hidden="true"></i>
                        Désactiver 2FA
                    </button>
                @else
                    <div class="alert alert-warning py-2" role="alert">
                        <i class="fas fa-exclamation-triangle me-2" aria-hidden="true"></i>
                        <small>2FA non activé</small>
                    </div>
                    <button class="btn btn-outline-success btn-sm" onclick="enable2FA({{ $user->id }})" aria-label="Activer 2FA">
                        <i class="fas fa-lock me-2" aria-hidden="true"></i>
                        Activer 2FA
                    </button>
                @endif
            </div>
        </div>

        <!-- Utilisateurs Similaires -->
        @php
            $utilisateursSimilaires = \App\Models\User::where('role', $user->role)
                ->where('id', '!=', $user->id)
                ->limit(5)
                ->get();
        @endphp

        @if($utilisateursSimilaires->count() > 0)
            <div class="card mt-4" role="region" aria-labelledby="similar-users">
                <div class="card-header">
                    <h5 class="card-title mb-0" id="similar-users">
                        <i class="fas fa-users me-2" aria-hidden="true"></i>
                        Autres {{ $roleInfo['label'] }}s
                    </h5>
                </div>
                <div class="card-body">
                    @foreach($utilisateursSimilaires as $utilisateur)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;"
                                         aria-label="Avatar de {{ $utilisateur->nom }} {{ $utilisateur->prenom }}">
                                        {{ strtoupper(substr($utilisateur->nom ?? '', 0, 1) . substr($utilisateur->prenom ?? '', 0, 1)) }}
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $utilisateur) }}" 
                                       class="text-decoration-none">
                                        {{ $utilisateur->full_name ?? ($utilisateur->nom . ' ' . $utilisateur->prenom) }}
                                    </a>
                                    <br>
                                    <small class="text-muted">{{ $utilisateur->email }}</small>
                                </div>
                            </div>
                            <div class="btn-group">
                                <a href="{{ route('admin.users.show', $utilisateur) }}" 
                                   class="btn btn-outline-info btn-sm" aria-label="Voir les détails de {{ $utilisateur->nom }} {{ $utilisateur->prenom }}">
                                    <i class="fas fa-eye" aria-hidden="true"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach

                    <div class="text-center mt-3">
                        <a href="{{ route('admin.users.index', ['role' => $user->role]) }}" 
                           class="btn btn-outline-primary btn-sm" aria-label="Voir tous les {{ strtolower($roleInfo['label']) }}s">
                            <i class="fas fa-list me-1" aria-hidden="true"></i>
                            Voir tous les {{ strtolower($roleInfo['label']) }}s
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if($user->id !== auth()->id())
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="userName"></strong> ?</p>
                    <p class="text-danger"><small>Cette action est irréversible.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Annuler">Annuler</button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" aria-label="Supprimer définitivement">
                            <i class="fas fa-trash me-2" aria-hidden="true"></i>
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Toast Notification Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="actionToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Fermer"></button>
        </div>
        <div class="toast-body"></div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar {
        transition: transform 0.3s ease;
    }
    
    .avatar:hover {
        transform: scale(1.05);
    }
    
    .user-id:hover {
        background-color: #e9ecef;
        transition: background-color 0.2s;
    }
    
    @media print {
        .btn, .card-header, .page-header .btn-group, .toast-container {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .page-title {
            font-size: 1.5rem !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function showToast(message, type = 'success') {
        const toast = document.getElementById('actionToast');
        const toastBody = toast.querySelector('.toast-body');
        toastBody.textContent = message;
        toast.classList.remove('bg-success', 'bg-danger', 'bg-info');
        toast.classList.add(`bg-${type}`);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }

    function confirmDelete(userId, userName) {
        document.getElementById('userName').textContent = userName;
        document.getElementById('deleteForm').action = '{{ route("admin.users.index") }}/' + userId;
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
    
    function exportUserData(format = 'pdf') {
        const form = document.createElement('form');
        form.method = 'GET';
        form.action = '{{ route("admin.users.export") }}';
        
        const userInput = document.createElement('input');
        userInput.type = 'hidden';
        userInput.name = 'user_id';
        userInput.value = '{{ $user->id }}';
        form.appendChild(userInput);
        
        const formatInput = document.createElement('input');
        formatInput.type = 'hidden';
        formatInput.name = 'format';
        formatInput.value = format;
        form.appendChild(formatInput);
        
        document.body.appendChild(form);
        form.submit();
        showToast(`Exportation des données au format ${format.toUpperCase()} en cours...`, 'info');
    }
    
    function printUser() {
        window.print();
        showToast('Impression lancée', 'info');
    }
    
    function sendEmail(email) {
        window.location.href = 'mailto:' + email;
        showToast('Ouverture du client de messagerie', 'info');
    }
    
    function resendVerification(userId) {
        if (confirm('Renvoyer l\'email de vérification à cet utilisateur ?')) {
            fetch(`/admin/users/${userId}/resend-verification`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                showToast(data.message, data.success ? 'success' : 'danger');
            })
            .catch(error => {
                showToast('Erreur lors de l\'envoi', 'danger');
            });
        }
    }

    function enable2FA(userId) {
        if (confirm('Activer l\'authentification à deux facteurs pour cet utilisateur ?')) {
            fetch(`/admin/users/${userId}/enable-2fa`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                showToast(data.message, data.success ? 'success' : 'danger');
                if (data.success) location.reload();
            })
            .catch(error => {
                showToast('Erreur lors de l\'activation de 2FA', 'danger');
            });
        }
    }

    function disable2FA(userId) {
        if (confirm('Désactiver l\'authentification à deux facteurs pour cet utilisateur ?')) {
            fetch(`/admin/users/${userId}/disable-2fa`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                showToast(data.message, data.success ? 'success' : 'danger');
                if (data.success) location.reload();
            })
            .catch(error => {
                showToast('Erreur lors de la désactivation de 2FA', 'danger');
            });
        }
    }

    // Copy to clipboard functionality
    document.addEventListener('DOMContentLoaded', function() {
        const userId = document.querySelector('.user-id');
        if (userId) {
            userId.addEventListener('click', function() {
                navigator.clipboard.writeText(this.textContent.replace('#', '')).then(function() {
                    const originalText = userId.textContent;
                    userId.textContent = 'Copié!';
                    userId.style.backgroundColor = '#28a745';
                    userId.style.color = 'white';
                    showToast('ID utilisateur copié dans le presse-papiers', 'success');
                    
                    setTimeout(function() {
                        userId.textContent = originalText;
                        userId.style.backgroundColor = '';
                        userId.style.color = '';
                    }, 1500);
                });
            });
        }
    });
</script>
@endpush