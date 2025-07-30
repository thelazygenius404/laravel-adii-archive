{{-- resources/views/admin/stockage/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Gestion des Espaces de Stockage')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-warehouse me-2"></i>
            Gestion des Espaces de Stockage
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.stockage.hierarchy') }}" class="btn btn-primary">
                <i class="fas fa-sitemap me-2"></i>
                Vue Hiérarchique
            </a>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-cogs me-1"></i>
                    Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('admin.stockage.optimize') }}">
                        <i class="fas fa-magic me-2"></i>Optimiser le stockage
                    </a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.stockage.export') }}">
                        <i class="fas fa-download me-2"></i>Exporter rapport
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('admin.salles.create') }}">
                        <i class="fas fa-plus me-2"></i>Ajouter une salle
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques générales -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-primary">
            <div class="card-body text-center">
                <i class="fas fa-home text-primary fa-3x mb-3"></i>
                <h3 class="text-primary">{{ $stats['total_salles'] }}</h3>
                <p class="text-muted mb-0">Salles Totales</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-info">
            <div class="card-body text-center">
                <i class="fas fa-map-marker-alt text-info fa-3x mb-3"></i>
                <h3 class="text-info">{{ $stats['total_positions'] }}</h3>
                <p class="text-muted mb-0">Positions Totales</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-success">
            <div class="card-body text-center">
                <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                <h3 class="text-success">{{ $stats['positions_occupees'] }}</h3>
                <p class="text-muted mb-0">Positions Occupées</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card border-warning">
            <div class="card-body text-center">
                <i class="fas fa-circle text-warning fa-3x mb-3"></i>
                <h3 class="text-warning">{{ $stats['positions_libres'] }}</h3>
                <p class="text-muted mb-0">Positions Libres</p>
            </div>
        </div>
    </div>
</div>

<!-- Barres de statistiques boîtes et dossiers -->
<div class="row mb-4">
    <div class="col-lg-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-box me-2"></i>
                    Statistiques des Boîtes
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h4 class="text-info">{{ $stats['total_boites'] }}</h4>
                        <small class="text-muted">Boîtes Actives</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-success">{{ $stats['total_dossiers'] }}</h4>
                        <small class="text-muted">Dossiers Stockés</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-warning">{{ $stats['dossiers_actifs'] }}</h4>
                        <small class="text-muted">Dossiers Actifs</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Alertes
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($stats['dossiers_due_elimination'] > 0)
                        <a href="{{ route('admin.dossiers.elimination') }}" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-trash me-2"></i>
                            {{ $stats['dossiers_due_elimination'] }} dossier(s) à éliminer
                        </a>
                    @endif
                    <button class="btn btn-outline-info btn-sm" onclick="showOptimizationSuggestions()">
                        <i class="fas fa-lightbulb me-2"></i>
                        Suggestions d'optimisation
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Utilisation par organisme -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Utilisation par Organisme
                </h5>
                <div class="btn-group btn-group-sm">
                    <input type="text" class="form-control form-control-sm" placeholder="Rechercher un organisme..." id="searchOrganisme">
                    <button class="btn btn-outline-primary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Organisme</th>
                                <th>Capacité Max</th>
                                <th>Capacité Actuelle</th>
                                <th>Utilisation</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($utilisationParOrganisme as $utilisation)
                                <tr>
                                    <td>
                                        <strong>{{ $utilisation['nom'] }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $utilisation['capacite_max'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $utilisation['capacite_actuelle'] }}</span>
                                    </td>
                                    <td>
                                        <div class="progress" style="width: 200px;">
                                            <div class="progress-bar 
                                                @if($utilisation['utilisation_percentage'] < 50) bg-success
                                                @elseif($utilisation['utilisation_percentage'] < 80) bg-warning
                                                @else bg-danger @endif"
                                                style="width: {{ $utilisation['utilisation_percentage'] }}%">
                                                {{ number_format($utilisation['utilisation_percentage'], 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.stockage.hierarchy') }}?organisme={{ $utilisation['nom'] }}" 
                                               class="btn btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.stockage.statistics', ['organisme' => $utilisation['nom']]) }}" 
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-chart-line"></i>
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
    </div>
</div>

<!-- Activités récentes -->
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock me-2"></i>
                    Nouveaux Dossiers
                </h5>
            </div>
            <div class="card-body">
                @if($activitesRecentes['nouveaux_dossiers']->count() > 0)
                    @foreach($activitesRecentes['nouveaux_dossiers'] as $dossier)
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-3">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="fas fa-file-alt" style="font-size: 0.8rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $dossier->numero }}</div>
                                <small class="text-muted">{{ $dossier->titre }}</small>
                            </div>
                            <small class="text-muted">{{ $dossier->created_at->diffForHumans() }}</small>
                        </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.dossiers.index') }}" class="btn btn-sm btn-outline-primary">
                            Voir tous les dossiers
                        </a>
                    </div>
                @else
                    <p class="text-muted text-center">Aucun nouveau dossier</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-box me-2"></i>
                    Boîtes Pleines
                </h5>
            </div>
            <div class="card-body">
                @if($activitesRecentes['boites_pleines']->count() > 0)
                    @foreach($activitesRecentes['boites_pleines'] as $boite)
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-3">
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="fas fa-box" style="font-size: 0.8rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $boite->numero }}</div>
                                <small class="text-muted">{{ $boite->full_location }}</small>
                            </div>
                            <span class="badge bg-warning">{{ $boite->utilisation_percentage }}%</span>
                        </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.boites.index') }}?status=full" class="btn btn-sm btn-outline-warning">
                            Voir toutes les boîtes
                        </a>
                    </div>
                @else
                    <p class="text-muted text-center">Aucune boîte pleine</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-trash me-2"></i>
                    Dossiers à Éliminer
                </h5>
            </div>
            <div class="card-body">
                @if($activitesRecentes['dossiers_elimination']->count() > 0)
                    @foreach($activitesRecentes['dossiers_elimination'] as $dossier)
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-3">
                                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="fas fa-exclamation-triangle" style="font-size: 0.8rem;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $dossier->numero }}</div>
                                <small class="text-muted">
                                    Échéance: {{ $dossier->date_elimination_prevue?->format('d/m/Y') }}
                                </small>
                            </div>
                            <span class="badge bg-danger">{{ $dossier->days_until_elimination }} j</span>
                        </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.dossiers.elimination') }}" class="btn btn-sm btn-outline-danger">
                            Gérer les éliminations
                        </a>
                    </div>
                @else
                    <p class="text-muted text-center">Aucun dossier en attente</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les suggestions d'optimisation -->
<div class="modal fade" id="optimizationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-lightbulb me-2"></i>
                    Suggestions d'Optimisation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="optimizationContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                    <p class="mt-2">Analyse en cours...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a href="{{ route('admin.stockage.optimize') }}" class="btn btn-primary">
                    <i class="fas fa-magic me-2"></i>
                    Voir le rapport complet
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Recherche dans le tableau des organismes
    document.getElementById('searchOrganisme').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('tbody tr');
        
        tableRows.forEach(row => {
            const organismeCell = row.querySelector('td:first-child strong');
            if (organismeCell) {
                const organismeName = organismeCell.textContent.toLowerCase();
                row.style.display = organismeName.includes(searchTerm) ? '' : 'none';
            }
        });
    });

    // Fonction pour afficher les suggestions d'optimisation
    async function showOptimizationSuggestions() {
        const modal = new bootstrap.Modal(document.getElementById('optimizationModal'));
        modal.show();
        
        const contentElement = document.getElementById('optimizationContent');
        
        // Réinitialiser le contenu avec le spinner
        contentElement.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-2">Analyse en cours...</p>
            </div>
        `;
        
        try {
            // Construire l'URL avec le paramètre ajax
            const url = new URL('{{ route("admin.stockage.optimize") }}', window.location.origin);
            url.searchParams.append('ajax', '1');
            
            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.optimisations && Array.isArray(data.optimisations) && data.optimisations.length > 0) {
                let html = '<div class="list-group">';
                
                // Limiter à 5 suggestions
                const optimisationsToShow = data.optimisations.slice(0, 5);
                
                optimisationsToShow.forEach(optimisation => {
                    // Vérifier que les propriétés existent
                    const boiteNom = optimisation.boite?.numero || optimisation.boite || 'N/A';
                    const localisation = optimisation.localisation || 'Non spécifiée';
                    const tauxOccupation = optimisation.taux_occupation || 0;
                    const suggestions = Array.isArray(optimisation.suggestions) ? optimisation.suggestions : [];
                    
                    html += `
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">${escapeHtml(boiteNom)}</h6>
                                <small class="text-muted">${tauxOccupation}%</small>
                            </div>
                            <p class="mb-1">
                                <small class="text-muted">${escapeHtml(localisation)}</small>
                            </p>
                            <div class="d-flex gap-1 flex-wrap">
                                ${suggestions.map(suggestion => 
                                    `<span class="badge bg-light text-dark">${escapeHtml(suggestion)}</span>`
                                ).join('')}
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                
                // Ajouter un message s'il y a plus de 5 suggestions
                if (data.optimisations.length > 5) {
                    html += `<p class="text-center mt-3 text-muted">Et ${data.optimisations.length - 5} autre(s) suggestion(s)...</p>`;
                }
                
                contentElement.innerHTML = html;
            } else {
                // Aucune optimisation nécessaire
                contentElement.innerHTML = `
                    <div class="text-center text-success">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <p>Aucune optimisation nécessaire. Votre stockage est bien organisé !</p>
                    </div>
                `;
            }
            
        } catch (error) {
            console.error('Erreur lors du chargement des suggestions:', error);
            contentElement.innerHTML = `
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <p>Erreur lors du chargement des suggestions.</p>
                    <small class="text-muted">Détails: ${error.message}</small>
                </div>
            `;
        }
    }

    // Fonction utilitaire pour échapper le HTML
    function escapeHtml(text) {
        if (typeof text !== 'string') return text;
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

    // Auto-refresh des statistiques toutes les 5 minutes
    setInterval(function() {
        // Vous pouvez implémenter un refresh automatique des stats ici
        console.log('Auto-refresh statistiques');
    }, 300000); // 5 minutes
</script>
@endpush

@push('styles')
<style>
    .progress {
        height: 8px;
    }
    
    .avatar {
        flex-shrink: 0;
    }
    
    .card-body .list-group-item {
        border: none;
        padding: 0.5rem 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    .card-body .list-group-item:last-child {
        border-bottom: none;
    }
    
    .spinner-border {
        width: 3rem;
        height: 3rem;
    }
</style>
@endpush