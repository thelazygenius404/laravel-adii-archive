@extends('layouts.admin')

@section('title', 'Gestion des Organismes')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-sitemap me-2"></i>
            Gestion des Organismes
        </h1>
        <a href="{{ route('admin.organismes.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>
            Ajouter un nouvel organisme
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filters and Search -->
        <div class="row mb-4">
            <!-- Recherche -->
            <div class="col-md-6">
                <form method="GET" class="d-flex">
                    <input type="text" 
                           name="search" 
                           class="form-control me-2" 
                           placeholder="Rechercher un organisme..."
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.organismes.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
            
            <!-- Actions et pagination -->
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <!-- Export -->
                    <form method="GET" action="{{ route('admin.organismes.export') }}">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <button type="submit" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-file-excel me-1"></i>
                            Exporter
                        </button>
                    </form>
                    
                    <!-- Pagination -->
                    <form method="GET" class="d-flex align-items-center">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        <span class="text-nowrap me-2">Afficher</span>
                        <select name="per_page" class="form-select form-select-sm" style="width: 80px;" onchange="this.form.submit()">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- Organismes Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>
                            <i class="fas fa-sitemap me-1"></i>
                            Nom de l'Organisme
                        </th>
                        <th>
                            <i class="fas fa-building me-1"></i>
                            Entités Productrices
                        </th>
                        <th>
                            <i class="fas fa-users me-1"></i>
                            Utilisateurs
                        </th>
                        <th>
                            <i class="fas fa-calendar me-1"></i>
                            Date de Création
                        </th>
                        <th width="150">
                            <i class="fas fa-cogs me-1"></i>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($organismes as $organisme)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-sitemap"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <strong>{{ $organisme->nom_org }}</strong>
                                        <br>
                                        <small class="text-muted">ID: {{ $organisme->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $organisme->entite_productrices_count ?? 0 }} entité(s)
                                </span>
                                @if($organisme->entite_productrices_count > 0)
                                    <a href="{{ route('admin.entites.index', ['organisme' => $organisme->id]) }}" 
                                       class="btn btn-sm btn-outline-info ms-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success">
                                    {{ $organisme->users_count ?? 0 }} utilisateur(s)
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $organisme->created_at->format('d/m/Y à H:i') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.organismes.show', $organisme) }}" 
                                       class="btn btn-sm btn-outline-info" 
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.organismes.edit', $organisme) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($organisme->entite_productrices_count == 0)
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Supprimer"
                                                onclick="confirmDelete('{{ $organisme->id }}', '{{ $organisme->nom_org }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @else
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger disabled" 
                                                title="Impossible de supprimer - contient des entités"
                                                disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-sitemap fa-3x mb-3"></i>
                                    <p class="mb-0">Aucun organisme trouvé</p>
                                    @if(request('search'))
                                        <p class="mt-2">
                                            <a href="{{ route('admin.organismes.index') }}" class="btn btn-sm btn-outline-primary">
                                                Voir tous les organismes
                                            </a>
                                        </p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($organismes->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $organismes->firstItem() }} à {{ $organismes->lastItem() }} sur {{ $organismes->total() }} résultats
                </div>
                <div>
                    {{ $organismes->onEachSide(1)->links('pagination::simple-bootstrap-4') }}
                </div>
            </div>
        @endif
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
                <p>Êtes-vous sûr de vouloir supprimer l'organisme <strong id="organismeName"></strong> ?</p>
                <p class="text-danger"><small>Cette action est irréversible.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Info Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-sitemap text-primary fa-2x mb-2"></i>
                <h4 class="text-primary">{{ $organismes->total() }}</h4>
                <p class="text-muted mb-0">Organismes Totaux</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="fas fa-building text-info fa-2x mb-2"></i>
                <h4 class="text-info">{{ $organismes->sum('entite_productrices_count') }}</h4>
                <p class="text-muted mb-0">Entités Totales</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-users text-success fa-2x mb-2"></i>
                <h4 class="text-success">{{ $organismes->sum('users_count') }}</h4>
                <p class="text-muted mb-0">Utilisateurs Totaux</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-calendar text-warning fa-2x mb-2"></i>
                <h4 class="text-warning">{{ $organismes->where('created_at', '>=', now()->subMonth())->count() }}</h4>
                <p class="text-muted mb-0">Créés ce mois</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(organismeId, organismeName) {
        document.getElementById('organismeName').textContent = organismeName;
        document.getElementById('deleteForm').action = '{{ route("admin.organismes.index") }}/' + organismeId;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }

    // Auto-refresh statistics every 30 seconds
    setTimeout(function() {
        // You can implement live statistics updates here
        console.log('Auto-refresh statistics');
    }, 30000);
</script>
@endpush