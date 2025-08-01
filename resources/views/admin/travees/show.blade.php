{{-- resources/views/admin/travees/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Détails de la Travée - ' . $travee->nom)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-layer-group me-2"></i>
            {{ $travee->nom }}
            <span class="badge bg-primary ms-2">{{ $travee->salle->organisme->nom_org }}</span>
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.travees.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
            <a href="{{ route('admin.travees.edit', $travee) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                Modifier
            </a>
            <a href="{{ route('admin.stockage.hierarchy') }}?travee={{ $travee->id }}" class="btn btn-success">
                <i class="fas fa-sitemap me-2"></i>
                Vue hiérarchique
            </a>
        </div>
    </div>
</div>

<!-- Informations générales -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informations Générales
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Nom de la travée :</strong></td>
                                <td>{{ $travee->nom }}</td>
                            </tr>
                            <tr>
                                <td><strong>Salle :</strong></td>
                                <td>
                                    <a href="{{ route('admin.salles.show', $travee->salle) }}" class="text-decoration-none">
                                        {{ $travee->salle->nom }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Organisme :</strong></td>
                                <td>
                                    <span class="badge bg-primary">{{ $travee->salle->organisme->nom_org }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Date de création :</strong></td>
                                <td>{{ $travee->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Dernière modification :</strong></td>
                                <td>{{ $travee->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Utilisation :</strong></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress me-2" style="width: 100px;">
                                            <div class="progress-bar bg-{{ $stats['utilisation_percentage'] < 50 ? 'success' : ($stats['utilisation_percentage'] < 80 ? 'warning' : 'danger') }}" 
                                                 style="width: {{ $stats['utilisation_percentage'] }}%"></div>
                                        </div>
                                        <span>{{ number_format($stats['utilisation_percentage'], 1) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Statistiques
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <h2 class="text-primary">{{ $stats['total_tablettes'] }}</h2>
                    <small class="text-muted">Tablettes</small>
                </div>
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-success">{{ $stats['total_positions'] }}</h4>
                        <small class="text-muted">Positions</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info">{{ $stats['positions_occupees'] }}</h4>
                        <small class="text-muted">Occupées</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Structure de la travée -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-table me-2"></i>
                    Tablettes de la Travée
                </h5>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.tablettes.create') }}?travee_id={{ $travee->id }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Ajouter une tablette
                    </a>
                    <button class="btn btn-outline-info" onclick="toggleView('table')">
                        <i class="fas fa-table"></i>
                    </button>
                    <button class="btn btn-outline-info" onclick="toggleView('grid')">
                        <i class="fas fa-th"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($travee->tablettes->count() > 0)
                    <!-- Vue tableau -->
                    <div id="tableView">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tablette</th>
                                        <th>Positions totales</th>
                                        <th>Positions occupées</th>
                                        <th>Utilisation</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($travee->tablettes as $tablette)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-table text-info fa-2x me-3"></i>
                                                    <div>
                                                        <h6 class="mb-0">{{ $tablette->nom }}</h6>
                                                        <small class="text-muted">ID: {{ $tablette->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $tablette->total_positions }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ $tablette->positions_occupees }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-2" style="width: 80px; height: 8px;">
                                                        <div class="progress-bar bg-{{ $tablette->utilisation_percentage < 50 ? 'success' : ($tablette->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                                                             style="width: {{ $tablette->utilisation_percentage }}%"></div>
                                                    </div>
                                                    <small>{{ number_format($tablette->utilisation_percentage, 1) }}%</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.tablettes.show', $tablette) }}" 
                                                       class="btn btn-outline-info" title="Voir">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.tablettes.edit', $tablette) }}" 
                                                       class="btn btn-outline-primary" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.positions.create') }}?tablette_id={{ $tablette->id }}" 
                                                       class="btn btn-outline-success" title="Ajouter des positions">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Vue grille -->
                    <div id="gridView" style="display: none;">
                        <div class="row">
                            @foreach($travee->tablettes as $tablette)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-info">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-table text-info fa-2x me-3"></i>
                                                <div>
                                                    <h6 class="card-title mb-0">{{ $tablette->nom }}</h6>
                                                    <small class="text-muted">Tablette</small>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-2">
                                                <span class="badge bg-info">{{ $tablette->positions->count() }} positions</span>
                                            </div>

                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <small class="text-muted">Utilisation</small>
                                                    <small>{{ number_format($tablette->utilisation_percentage, 1) }}%</small>
                                                </div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-{{ $tablette->utilisation_percentage < 50 ? 'success' : ($tablette->utilisation_percentage < 80 ? 'warning' : 'danger') }}" 
                                                         style="width: {{ $tablette->utilisation_percentage }}%"></div>
                                                </div>
                                            </div>

                                            @if($tablette->positions->count() > 0)
                                                <div class="positions-grid mb-3">
                                                    @foreach($tablette->positions->take(8) as $position)
                                                        <div class="position-mini mb-1">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <small><i class="fas fa-map-marker-alt me-1"></i>{{ $position->nom }}</small>
                                                                <span class="badge badge-sm {{ $position->vide ? 'bg-warning' : 'bg-success' }}">
                                                                    {{ $position->vide ? 'L' : 'O' }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    @if($tablette->positions->count() > 8)
                                                        <small class="text-muted">... et {{ $tablette->positions->count() - 8 }} autre(s)</small>
                                                    @endif
                                                </div>
                                            @endif

                                            <div class="btn-group btn-group-sm w-100">
                                                <a href="{{ route('admin.tablettes.show', $tablette) }}" class="btn btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.tablettes.edit', $tablette) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.positions.create') }}?tablette_id={{ $tablette->id }}" class="btn btn-outline-success">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-table fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune tablette</h5>
                        <p class="text-muted">Cette travée ne contient encore aucune tablette.</p>
                        <a href="{{ route('admin.tablettes.create') }}?travee_id={{ $travee->id }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Créer la première tablette
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Actions Rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="d-grid">
                            <a href="{{ route('admin.tablettes.create') }}?travee_id={{ $travee->id }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Ajouter tablette
                            </a>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-grid">
                            <button class="btn btn-outline-info" onclick="generatePositions()">
                                <i class="fas fa-magic me-2"></i>
                                Générer positions
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-grid">
                            <button class="btn btn-outline-success" onclick="exportTravee()">
                                <i class="fas fa-download me-2"></i>
                                Exporter structure
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-grid">
                            <button class="btn btn-outline-warning" onclick="optimizeTravee()">
                                <i class="fas fa-chart-line me-2"></i>
                                Optimiser
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .position-mini {
        background-color: #f8f9fa;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        border-left: 3px solid #17a2b8;
        font-size: 0.8rem;
    }

    .badge-sm {
        font-size: 0.65rem;
        padding: 0.2rem 0.4rem;
    }

    .progress {
        height: 8px;
    }

    .card-body .table-borderless td {
        padding: 0.5rem 0;
        border: none;
    }

    .positions-grid {
        max-height: 120px;
        overflow-y: auto;
    }

    .btn-group .btn {
        flex: 1;
    }

    .card-title {
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
    // Basculer entre les vues
    function toggleView(view) {
        const tableView = document.getElementById('tableView');
        const gridView = document.getElementById('gridView');
        
        if (view === 'grid') {
            tableView.style.display = 'none';
            gridView.style.display = 'block';
        } else {
            tableView.style.display = 'block';
            gridView.style.display = 'none';
        }
    }

    // Générer des positions automatiquement
    function generatePositions() {
        if (confirm('Voulez-vous générer automatiquement des positions pour toutes les tablettes vides ?')) {
            // Implémentation à ajouter selon vos besoins
            fetch(`{{ route('admin.positions.bulk-create') }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    travee_id: {{ $travee->id }},
                    positions_per_tablette: 10
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Erreur lors de la génération des positions');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de la génération des positions');
            });
        }
    }

    // Exporter la structure
    function exportTravee() {
        window.location.href = `{{ route('admin.travees.export') }}?travee_id={{ $travee->id }}`;
    }

    // Optimiser la travée
    function optimizeTravee() {
        alert('Fonctionnalité d\'optimisation à implémenter selon vos besoins spécifiques.');
    }
</script>
@endpush