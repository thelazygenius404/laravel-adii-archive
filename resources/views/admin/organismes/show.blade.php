@extends('layouts.admin')

@section('title', 'Détails de l\'Organisme')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-eye me-2"></i>
            Détails de l'Organisme
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.organismes.edit', $organisme) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                Modifier
            </a>
            <a href="{{ route('admin.organismes.index') }}" class="btn btn-outline-secondary">
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
                    <i class="fas fa-sitemap me-2"></i>
                    {{ $organisme->nom_org }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nom de l'organisme :</strong></td>
                                <td>{{ $organisme->nom_org }}</td>
                            </tr>
                            <tr>
                                <td><strong>Date de création :</strong></td>
                                <td>{{ $organisme->created_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Dernière modification :</strong></td>
                                <td>{{ $organisme->updated_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Entités productrices :</strong></td>
                                <td><span class="badge bg-info">{{ $stats['total_entites'] }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Entités racines :</strong></td>
                                <td><span class="badge bg-warning">{{ $stats['root_entites'] }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Utilisateurs totaux :</strong></td>
                                <td><span class="badge bg-success">{{ $stats['total_users'] }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Entités Productrices Tree -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building me-2"></i>
                    Structure des Entités Productrices
                </h5>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleTreeView()" id="tree-toggle">
                        <i class="fas fa-compress-alt me-1"></i>
                        Réduire tout
                    </button>
                    <a href="{{ route('admin.entites.create') }}?organisme={{ $organisme->id }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus me-1"></i>
                        Ajouter une entité
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($organisme->entiteProductrices->count() > 0)
                    <div class="hierarchy-tree" id="hierarchy-tree">
                        @foreach($organisme->entiteProductrices->where('entite_parent', null) as $rootEntity)
                            @include('admin.organismes.partials.entity-tree-item', ['entity' => $rootEntity, 'level' => 0])
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-building fa-3x mb-3"></i>
                        <p class="mb-0">Aucune entité productrice trouvée</p>
                        <p class="mt-2">
                            <a href="{{ route('admin.entites.create') }}?organisme={{ $organisme->id }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Créer la première entité
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Entities -->
        @if($stats['recent_entites']->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Entités Récemment Créées
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Code</th>
                                    <th>Parent</th>
                                    <th>Utilisateurs</th>
                                    <th>Date de création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['recent_entites'] as $entite)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.entites.show', $entite) }}" class="text-decoration-none">
                                                {{ $entite->nom_entite }}
                                            </a>
                                        </td>
                                        <td><span class="badge bg-secondary">{{ $entite->code_entite }}</span></td>
                                        <td>
                                            @if($entite->parent)
                                                <small class="text-muted">{{ $entite->parent->nom_entite }}</small>
                                            @else
                                                <span class="badge bg-warning">Racine</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $entite->users->count() }}</span>
                                        </td>
                                        <td>{{ $entite->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.entites.show', $entite) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.entites.edit', $entite) }}" 
                                                   class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Users by Entity -->
        @if($stats['total_users'] > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users me-2"></i>
                        Répartition des Utilisateurs par Entité
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $entitesWithUsers = $organisme->entiteProductrices->filter(function($entite) {
                            return $entite->users->count() > 0;
                        })->sortByDesc(function($entite) {
                            return $entite->users->count();
                        });
                    @endphp
                    
                    @forelse($entitesWithUsers as $entite)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <a href="{{ route('admin.entites.show', $entite) }}" class="text-decoration-none">
                                    {{ $entite->nom_entite }}
                                </a>
                                <small class="text-muted d-block">{{ $entite->code_entite }}</small>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="progress me-3" style="width: 100px; height: 10px;">
                                    @php
                                        $percentage = ($entite->users->count() / $stats['total_users']) * 100;
                                    @endphp
                                    <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                </div>
                                <span class="badge bg-primary">{{ $entite->users->count() }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center">Aucun utilisateur assigné aux entités</p>
                    @endforelse
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
                            <h3 class="text-info mb-1">{{ $stats['total_entites'] }}</h3>
                            <small class="text-muted">Entités totales</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h3 class="text-warning mb-1">{{ $stats['root_entites'] }}</h3>
                            <small class="text-muted">Entités racines</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            <h3 class="text-success mb-1">{{ $stats['total_users'] }}</h3>
                            <small class="text-muted">Utilisateurs</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="border rounded p-3">
                            @php
                                $maxDepth = 0;
                                function calculateDepth($entities, $depth = 0) {
                                    global $maxDepth;
                                    $maxDepth = max($maxDepth, $depth);
                                    foreach ($entities as $entity) {
                                        calculateDepth($entity->children, $depth + 1);
                                    }
                                }
                                calculateDepth($organisme->entiteProductrices->where('entite_parent', null));
                            @endphp
                            <h3 class="text-secondary mb-1">{{ $maxDepth + 1 }}</h3>
                            <small class="text-muted">Niveaux max</small>
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
                    <a href="{{ route('admin.organismes.edit', $organisme) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>
                        Modifier cet organisme
                    </a>
                    
                    <a href="{{ route('admin.entites.create') }}?organisme={{ $organisme->id }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>
                        Ajouter une entité
                    </a>
                    
                    <a href="{{ route('admin.entites.index') }}?organisme={{ $organisme->id }}" class="btn btn-info">
                        <i class="fas fa-list me-2"></i>
                        Voir toutes les entités
                    </a>
                    
                    <a href="{{ route('admin.users.index') }}?organisme={{ $organisme->id }}" class="btn btn-outline-info">
                        <i class="fas fa-users me-2"></i>
                        Voir tous les utilisateurs
                    </a>
                    
                    <button class="btn btn-outline-secondary" onclick="exportOrganismeData()">
                        <i class="fas fa-download me-2"></i>
                        Exporter les données
                    </button>
                    
                    <button class="btn btn-outline-primary" onclick="printHierarchy()">
                        <i class="fas fa-print me-2"></i>
                        Imprimer la hiérarchie
                    </button>
                    
                    @if($stats['total_entites'] == 0)
                        <button class="btn btn-outline-danger" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i>
                            Supprimer cet organisme
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Hierarchy Summary -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-sitemap me-2"></i>
                    Résumé de la Hiérarchie
                </h5>
            </div>
            <div class="card-body">
                @if($organisme->entiteProductrices->count() > 0)
                    @php
                        $levelCounts = [];
                        function countByLevel($entities, $level = 0) {
                            global $levelCounts;
                            $levelCounts[$level] = ($levelCounts[$level] ?? 0) + $entities->count();
                            foreach ($entities as $entity) {
                                countByLevel($entity->children, $level + 1);
                            }
                        }
                        countByLevel($organisme->entiteProductrices->where('entite_parent', null));
                    @endphp
                    
                    @foreach($levelCounts as $level => $count)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">Niveau {{ $level + 1 }} :</span>
                            <span class="badge bg-primary">{{ $count }}</span>
                        </div>
                    @endforeach
                    
                    <hr class="my-3">
                    
                    <!-- Coverage Stats -->
                    <div class="mt-3">
                        <h6 class="text-muted mb-2">Couverture Utilisateurs :</h6>
                        @php
                            $entitesWithUsers = $organisme->entiteProductrices->filter(function($entite) {
                                return $entite->users->count() > 0;
                            })->count();
                            $coverage = $stats['total_entites'] > 0 ? ($entitesWithUsers / $stats['total_entites']) * 100 : 0;
                        @endphp
                        
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $coverage }}%"></div>
                        </div>
                        <small class="text-muted">
                            {{ $entitesWithUsers }} sur {{ $stats['total_entites'] }} entités ont des utilisateurs
                        </small>
                    </div>
                @else
                    <p class="text-muted text-center">Aucune hiérarchie définie</p>
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
                            <h6 class="mb-1">Organisme créé</h6>
                            <small class="text-muted">{{ $organisme->created_at->format('d/m/Y à H:i') }}</small>
                        </div>
                    </div>
                    
                    @if($organisme->updated_at != $organisme->created_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Dernière modification</h6>
                                <small class="text-muted">{{ $organisme->updated_at->format('d/m/Y à H:i') }}</small>
                            </div>
                        </div>
                    @endif
                    
                    @if($stats['recent_entites']->count() > 0)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Dernière entité ajoutée</h6>
                                <small class="text-muted">{{ $stats['recent_entites']->first()->created_at->format('d/m/Y') }}</small>
                                <br><small class="text-primary">{{ $stats['recent_entites']->first()->nom_entite }}</small>
                            </div>
                        </div>
                    @endif
                    
                    @if($stats['total_users'] > 0)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">{{ $stats['total_users'] }} utilisateurs actifs</h6>
                                <small class="text-muted">Répartis dans {{ $entitesWithUsers ?? 0 }} entités</small>
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
                <p>Êtes-vous sûr de vouloir supprimer l'organisme <strong>{{ $organisme->nom_org }}</strong> ?</p>
                <p class="text-danger"><small>Cette action est irréversible et supprimera toutes les entités productrices associées.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('admin.organismes.destroy', $organisme) }}" method="POST" class="d-inline">
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
    .hierarchy-tree {
        font-family: monospace;
    }
    
    .entity-item {
        position: relative;
        margin-bottom: 8px;
        padding: 8px 12px;
        border: 1px solid #e9ecef;
        border-radius: 4px;
        background-color: #f8f9fa;
        transition: all 0.2s ease;
    }
    
    .entity-item:hover {
        background-color: #e9ecef;
        border-color: #007bff;
    }
    
    .entity-item.collapsed .entity-children {
        display: none;
    }
    
    .entity-item .toggle-btn {
        cursor: pointer;
        user-select: none;
        color: #6c757d;
        transition: transform 0.2s ease;
    }
    
    .entity-item.collapsed .toggle-btn {
        transform: rotate(-90deg);
    }
    
    .entity-children {
        margin-left: 20px;
        border-left: 2px solid #dee2e6;
        padding-left: 15px;
        margin-top: 8px;
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
    
    @media print {
        .btn, .card-header, .timeline, .page-header {
            display: none !important;
        }
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        .hierarchy-tree {
            font-size: 12px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let treeExpanded = true;
    
    function confirmDelete() {
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
    
    function exportOrganismeData() {
        // Export this specific organisme
        window.location.href = '{{ route("admin.organismes.export") }}?organisme={{ $organisme->id }}';
    }
    
    function printHierarchy() {
        window.print();
    }
    
    function toggleTreeView() {
        const tree = document.getElementById('hierarchy-tree');
        const toggle = document.getElementById('tree-toggle');
        const entities = tree.querySelectorAll('.entity-item');
        
        treeExpanded = !treeExpanded;
        
        entities.forEach(entity => {
            if (treeExpanded) {
                entity.classList.remove('collapsed');
            } else {
                entity.classList.add('collapsed');
            }
        });
        
        if (treeExpanded) {
            toggle.innerHTML = '<i class="fas fa-compress-alt me-1"></i> Réduire tout';
        } else {
            toggle.innerHTML = '<i class="fas fa-expand-alt me-1"></i> Développer tout';
        }
    }
    
    function toggleEntity(entityId) {
        const entity = document.getElementById('entity-' + entityId);
        entity.classList.toggle('collapsed');
    }
    
    // Load entity tree with AJAX for better performance
    document.addEventListener('DOMContentLoaded', function() {
        // Add click handlers for entity toggles
        document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const entityItem = this.closest('.entity-item');
                entityItem.classList.toggle('collapsed');
            });
        });
        
        // Auto-refresh statistics every 60 seconds
        setInterval(function() {
            // You can implement live statistics updates here
            console.log('Auto-refresh statistics');
        }, 60000);
    });
</script>
@endpush