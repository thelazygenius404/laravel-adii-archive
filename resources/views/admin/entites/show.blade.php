@extends('layouts.admin')

@section('title', 'Détails de l\'Entité Productrice')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-eye me-2"></i>
            Détails de l'Entité Productrice
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.entites.edit', $entite) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                Modifier
            </a>
            <a href="{{ route('admin.entites.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Main Information -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>
                    {{ $entite->nom_entite }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nom de l'entité :</strong></td>
                                <td>{{ $entite->nom_entite }}</td>
                            </tr>
                            <tr>
                                <td><strong>Code :</strong></td>
                                <td><span class="badge bg-secondary">{{ $entite->code_entite }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Organisme :</strong></td>
                                <td><span class="badge bg-primary">{{ $entite->organisme->nom_org }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Entité parent :</strong></td>
                                <td>
                                    @if($entite->parent)
                                        <a href="{{ route('admin.entites.show', $entite->parent) }}" class="text-decoration-none">
                                            {{ $entite->parent->nom_entite }}
                                        </a>
                                    @else
                                        <span class="badge bg-warning">Entité racine</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Date de création :</strong></td>
                                <td>{{ $entite->created_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Dernière modification :</strong></td>
                                <td>{{ $entite->updated_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Niveau hiérarchique :</strong></td>
                                <td>
                                    <span class="badge bg-info">Niveau {{ $stats['depth_level'] + 1 }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Hiérarchie complète :</strong></td>
                                <td>
                                    <small class="text-muted">{{ $entite->full_name }}</small>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hierarchy Tree -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-sitemap me-2"></i>
                    Hiérarchie
                </h5>
            </div>
            <div class="card-body">
                <!-- Parent chain -->
                @if($entite->parent)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Entités parentes :</h6>
                        <div class="d-flex align-items-center flex-wrap">
                            @php
                                $hierarchy = [];
                                $current = $entite->parent;
                                while($current) {
                                    array_unshift($hierarchy, $current);
                                    $current = $current->parent;
                                }
                            @endphp
                            
                            @foreach($hierarchy as $index => $ancestor)
                                @if($index > 0)
                                    <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                @endif
                                <a href="{{ route('admin.entites.show', $ancestor) }}" 
                                   class="btn btn-sm btn-outline-secondary mb-1">
                                    {{ $ancestor->nom_entite }}
                                </a>
                            @endforeach
                            
                            <i class="fas fa-arrow-right mx-2 text-muted"></i>
                            <span class="btn btn-sm btn-primary mb-1">
                                {{ $entite->nom_entite }}
                            </span>
                        </div>
                    </div>
                @endif

                <!-- Children -->
                @if($entite->children->count() > 0)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Sous-entités directes :</h6>
                        <div class="row">
                            @foreach($entite->children as $child)
                                <div class="col-md-6 mb-3">
                                    <div class="card border-left-success">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <a href="{{ route('admin.entites.show', $child) }}" class="text-decoration-none">
                                                            {{ $child->nom_entite }}
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted">{{ $child->code_entite }}</small>
                                                    <div class="mt-1">
                                                        <span class="badge bg-success badge-sm">
                                                            {{ $child->children->count() }} sous-entité(s)
                                                        </span>
                                                        <span class="badge bg-info badge-sm">
                                                            {{ $child->users->count() }} utilisateur(s)
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="btn-group">
                                                    <a href="{{ route('admin.entites.show', $child) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.entites.edit', $child) }}" 
                                                       class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Users List -->
        @if($entite->users->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>
                        Utilisateurs Assignés ({{ $entite->users->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($entite->users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar me-2">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 0.8rem;">
                                                        {{ strtoupper(substr($user->nom, 0, 1) . substr($user->prenom, 0, 1)) }}
                                                    </div>
                                                </div>
                                                {{ $user->nom }} {{ $user->prenom }}
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @php
                                                $roleClass = match($user->role) {
                                                    'admin' => 'bg-danger',
                                                    'gestionnaire_archives' => 'bg-info',
                                                    'service_producteurs' => 'bg-success',
                                                    default => 'bg-secondary'
                                                };
                                                
                                                $roleLabel = match($user->role) {
                                                    'admin' => 'Administrateur',
                                                    'gestionnaire_archives' => 'Gestionnaire Archives',
                                                    'service_producteurs' => 'Service Producteurs',
                                                    default => $user->role
                                                };
                                            @endphp
                                            <span class="badge {{ $roleClass }}">{{ $roleLabel }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users.show', $user) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Statistics Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistiques
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h3 class="text-success mb-1">{{ $stats['direct_children'] }}</h3>
                            <small class="text-muted">Sous-entités directes</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h3 class="text-info mb-1">{{ $stats['total_users'] }}</h3>
                            <small class="text-muted">Utilisateurs assignés</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h3 class="text-warning mb-1">{{ $stats['depth_level'] + 1 }}</h3>
                            <small class="text-muted">Niveau hiérarchique</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h3 class="text-secondary mb-1">{{ $stats['total_descendants'] ?? 0 }}</h3>
                            <small class="text-muted">Descendants totaux</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Actions Rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.entites.edit', $entite) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>
                        Modifier cette entité
                    </a>
                    
                    <a href="{{ route('admin.entites.create') }}?parent={{ $entite->id }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>
                        Ajouter une sous-entité
                    </a>
                    
                    <a href="{{ route('admin.users.create') }}?entite={{ $entite->id }}" class="btn btn-info">
                        <i class="fas fa-user-plus me-2"></i>
                        Ajouter un utilisateur
                    </a>
                    
                    <button class="btn btn-outline-secondary" onclick="exportEntiteData()">
                        <i class="fas fa-download me-2"></i>
                        Exporter les données
                    </button>
                    
                    @if($entite->children->count() == 0 && $entite->users->count() == 0)
                        <button class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i>
                            Supprimer cette entité
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Hierarchy Browser -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-sitemap me-2"></i>
                    Navigation Hiérarchique
                </h5>
            </div>
            <div class="card-body">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        @php
                            $breadcrumb = [];
                            $current = $entite;
                            while($current) {
                                array_unshift($breadcrumb, $current);
                                $current = $current->parent;
                            }
                        @endphp
                        
                        @foreach($breadcrumb as $index => $item)
                            @if($index < count($breadcrumb) - 1)
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.entites.show', $item) }}">{{ $item->nom_entite }}</a>
                                </li>
                            @else
                                <li class="breadcrumb-item active" aria-current="page">{{ $item->nom_entite }}</li>
                            @endif
                        @endforeach
                    </ol>
                </nav>

                <!-- Siblings -->
                @if($entite->parent && $entite->parent->children->count() > 1)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">Entités sœurs :</h6>
                        @foreach($entite->parent->children as $sibling)
                            @if($sibling->id !== $entite->id)
                                <a href="{{ route('admin.entites.show', $sibling) }}" 
                                   class="btn btn-sm btn-outline-secondary me-1 mb-1">
                                    {{ $sibling->nom_entite }}
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif

                <!-- Direct children navigation -->
                @if($entite->children->count() > 0)
                    <div>
                        <h6 class="text-muted mb-2">Naviguer vers :</h6>
                        @foreach($entite->children as $child)
                            <a href="{{ route('admin.entites.show', $child) }}" 
                               class="btn btn-sm btn-outline-primary me-1 mb-1">
                                {{ $child->nom_entite }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history me-2"></i>
                    Activité Récente
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="mb-1">Entité créée</h6>
                            <small class="text-muted">{{ $entite->created_at->format('d/m/Y à H:i') }}</small>
                        </div>
                    </div>
                    
                    @if($entite->updated_at != $entite->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Dernière modification</h6>
                                <small class="text-muted">{{ $entite->updated_at->format('d/m/Y à H:i') }}</small>
                            </div>
                        </div>
                    @endif
                    
                    @if($entite->users->count() > 0)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">{{ $entite->users->count() }} utilisateur(s) assigné(s)</h6>
                                <small class="text-muted">Dernière assignation : {{ $entite->users->max('created_at')?->format('d/m/Y') ?? 'N/A' }}</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer l'entité <strong>{{ $entite->nom_entite }}</strong> ?</p>
                <p class="text-danger"><small>Cette action est irréversible.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.entites.destroy', $entite) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-left-success {
        border-left: 4px solid #28a745 !important;
    }
    
    .timeline {
        position: relative;
        padding-left: 20px;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 15px;
    }
    
    .timeline-marker {
        position: absolute;
        left: -25px;
        top: 5px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    
    .timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: -21px;
        top: 15px;
        width: 2px;
        height: calc(100% + 5px);
        background-color: #dee2e6;
    }
    
    .timeline-content h6 {
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmDelete() {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
    
    function exportEntiteData() {
        // Create a form to export this specific entite
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.entites.bulk-action") }}';
        
        // CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Action
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'export';
        form.appendChild(actionInput);
        
        // Entite ID
        const entiteInput = document.createElement('input');
        entiteInput.type = 'hidden';
        entiteInput.name = 'entite_ids[]';
        entiteInput.value = '{{ $entite->id }}';
        form.appendChild(entiteInput);
        
        document.body.appendChild(form);
        form.submit();
    }
    
    // Auto-refresh statistics every 30 seconds
    setTimeout(function() {
        fetch(`{{ route('api.entites.statistics', $entite) }}`)
            .then(response => response.json())
            .then(data => {
                // Update statistics if needed
                console.log('Statistics updated', data);
            })
            .catch(error => console.log('Statistics update failed', error));
    }, 30000);
</script>
@endpush