
@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-tachometer-alt me-2"></i>
            Dashboard Administrateur
        </h1>
        <div class="text-muted">
            Bienvenue, {{ auth()->user()->nom }} {{ auth()->user()->prenom }}
        </div>
    </div>
</div>

<!-- Statistiques -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-start border-primary border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Total Utilisateurs</h6>
                        <h2 class="mb-0 text-primary">{{ App\Models\User::count() }}</h2>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-start border-danger border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Administrateurs</h6>
                        <h2 class="mb-0 text-danger">{{ App\Models\User::where('role', 'admin')->count() }}</h2>
                    </div>
                    <div class="text-danger">
                        <i class="fas fa-user-shield fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-start border-info border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Gestionnaires</h6>
                        <h2 class="mb-0 text-info">{{ App\Models\User::where('role', 'gestionnaire_archives')->count() }}</h2>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-archive fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-start border-success border-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-muted mb-1">Service Producteurs</h6>
                        <h2 class="mb-0 text-success">{{ App\Models\User::where('role', 'service_producteurs')->count() }}</h2>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-building fa-2x"></i>
                    </div>
                </div>
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
                    <i class="fas fa-bolt me-2"></i>
                    Actions rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="fas fa-user-plus fa-2x d-block mb-2"></i>
                            Ajouter un utilisateur
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-info w-100 mb-2">
                            <i class="fas fa-users fa-2x d-block mb-2"></i>
                            Gérer les utilisateurs
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-success w-100 mb-2">
                            <i class="fas fa-archive fa-2x d-block mb-2"></i>
                            Gestion des archives
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#" class="btn btn-outline-warning w-100 mb-2">
                            <i class="fas fa-cog fa-2x d-block mb-2"></i>
                            Paramètres système
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Utilisateurs récents -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-plus me-2"></i>
                    Utilisateurs récents
                </h5>
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                    Voir tout
                </a>
            </div>
            <div class="card-body">
                @php
                    $recentUsers = App\Models\User::orderBy('created_at', 'desc')->limit(5)->get();
                @endphp
                
                @forelse($recentUsers as $user)
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar me-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                {{ strtoupper(substr($user->nom, 0, 1) . substr($user->prenom, 0, 1)) }}
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $user->nom }} {{ $user->prenom }}</h6>
                            <small class="text-muted">{{ $user->email }}</small>
                            <div>
                                @php
                                    $badgeClass = match($user->role) {
                                        'admin' => 'bg-danger',
                                        'gestionnaire_archives' => 'bg-info',
                                        'service_producteurs' => 'bg-success',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} badge-sm">
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>
                            </div>
                        </div>
                        <div class="text-muted">
                            <small>{{ $user->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-users fa-3x mb-3"></i>
                        <p>Aucun utilisateur trouvé</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-server me-2"></i>
                    État du système
                </h5>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i class="fas fa-database text-success me-2"></i>
                            Base de données
                        </div>
                        <span class="badge bg-success rounded-pill">Connectée</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i class="fas fa-server text-success me-2"></i>
                            Serveur web
                        </div>
                        <span class="badge bg-success rounded-pill">Opérationnel</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div>
                            <i class="fas fa-shield-alt text-success me-2"></i>
                            Sécurité
                        </div>
                        <span class="badge bg-success rounded-pill">Active</span>
                    </div>
                </div>
                
                <div class="mt-3">
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Système opérationnel</strong> - Tous les services fonctionnent normalement.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection