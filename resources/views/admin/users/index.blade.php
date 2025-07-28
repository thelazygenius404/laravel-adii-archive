@extends('layouts.admin')

@section('title', 'Gestion des utilisateurs')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-users me-2"></i>
            Gestion des utilisateurs
        </h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>
            Ajouter un nouveau utilisateur
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filters and Search - Version compacte -->
        <div class="row mb-4">
            <!-- Recherche -->
            <div class="col-md-3">
                <form method="GET" class="d-flex">
                    <input type="text" 
                           name="search" 
                           class="form-control me-2" 
                           placeholder="Rechercher..."
                           value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search') || request('role'))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </form>
            </div>
            
            <!-- Filtre par rôle -->
            <div class="col-md-3">
                <form method="GET">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <select name="role" class="form-select" onchange="this.form.submit()">
                        <option value="">Tous les rôles</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="gestionnaire_archives" {{ request('role') == 'gestionnaire_archives' ? 'selected' : '' }}>Gestionnaire</option>
                        <option value="service_producteurs" {{ request('role') == 'service_producteurs' ? 'selected' : '' }}>Service Prod.</option>
                    </select>
                </form>
            </div>
            
            <!-- Actions et pagination -->
            <div class="col-md-6">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <!-- Export -->
                    <form method="GET" action="{{ route('admin.users.export') }}">
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                        @if(request('role'))
                            <input type="hidden" name="role" value="{{ request('role') }}">
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
                        @if(request('role'))
                            <input type="hidden" name="role" value="{{ request('role') }}">
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

        <!-- Users Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>
                            <i class="fas fa-sort me-1"></i>
                            Nom/Prénom
                        </th>
                        <th>
                            <i class="fas fa-envelope me-1"></i>
                            Email
                        </th>
                        <th>
                            <i class="fas fa-user-tag me-1"></i>
                            Type du compte
                        </th>
                        <th>
                            <i class="fas fa-calendar me-1"></i>
                            Date de création
                        </th>
                        <th width="150">
                            <i class="fas fa-cogs me-1"></i>
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($user->nom ?? '', 0, 1) . substr($user->prenom ?? '', 0, 1)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <strong>{{ $user->full_name ?? ($user->nom . ' ' . $user->prenom) }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="mailto:{{ $user->email }}" class="text-decoration-none">
                                    {{ $user->email }}
                                </a>
                            </td>
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
                            <td>
                                <small class="text-muted">
                                    {{ ($user->date_creation ?? $user->created_at)->format('d/m/Y à H:i') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Supprimer"
                                                onclick="confirmDelete('{{ $user->id }}', '{{ $user->full_name ?? ($user->nom . ' ' . $user->prenom) }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>

                                    @endif
                                    <div class="btn-group" role="group">
                                    <a href="{{ route('admin.users.show', $user) }}" 
                                      class="btn btn-sm btn-outline-info" 
                                       title="Voir détails">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <p class="mb-0">Aucun utilisateur trouvé</p>
                                    @if(request('search') || request('role'))
                                        <p class="mt-2">
                                            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">
                                                Voir tous les utilisateurs
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
        @if($users->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $users->firstItem() }} à {{ $users->lastItem() }} sur {{ $users->total() }} résultats
                </div>
                <div>
                    {{ $users->links() }}
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
                <p>Êtes-vous sûr de vouloir supprimer l'utilisateur <strong id="userName"></strong> ?</p>
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
@endsection

@push('scripts')
<script>
    function confirmDelete(userId, userName) {
        document.getElementById('userName').textContent = userName;
        document.getElementById('deleteForm').action = '{{ route("admin.users.index") }}/' + userId;
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
</script>
@endpush