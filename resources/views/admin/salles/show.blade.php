{{-- resources/views/admin/salles/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Détails de la Salle - ' . $salle->nom)

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="page-title">
            <i class="fas fa-home me-2"></i>
            {{ $salle->nom }}
            <span class="badge bg-primary ms-2">{{ $salle->organisme->nom_org }}</span>
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.salles.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
            <a href="{{ route('admin.salles.edit', $salle) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                Modifier
            </a>
            <a href="{{ route('admin.stockage.hierarchy') }}?salle={{ $salle->id }}" class="btn btn-success">
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
                                <td><strong>Nom de la salle :</strong></td>
                                <td>{{ $salle->nom }}</td>
                            </tr>
                            <tr>
                                <td><strong>Organisme :</strong></td>
                                <td>
                                    <span class="badge bg-primary">{{ $salle->organisme->nom_org }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Capacité maximale :</strong></td>
                                <td>{{ number_format($salle->capacite_max) }} positions</td>
                            </tr>
                            <tr>
                                <td><strong>Capacité actuelle :</strong></td>
                                <td>
                                    <span class="badge bg-{{ $salle->capacite_actuelle > ($salle->capacite_max * 0.8) ? 'warning' : 'success' }}">
                                        {{ number_format($salle->capacite_actuelle) }} positions
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
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
                            <tr>
                                <td><strong>Date de création :</strong></td>
                                <td>{{ $salle->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Dernière modification :</strong></td>
                                <td>{{ $salle->updated_at->format('d/m/Y H:i') }}</td>
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
                    <h2 class="text-primary">{{ $stats['total_travees'] }}</h2>
                    <small class="text-muted">Travées</small>
                </div>
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-info">{{ $stats['total_tablettes'] }}</h4>
                        <small class="text-muted">Tablettes</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ $stats['total_positions'] }}</h4>
                        <small class="text-muted">Positions</small>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <h5 class="text-warning">{{ $stats['positions_occupees'] }}</h5>
                        <small class="text-muted">Occupées</small>
                    </div>
                    <div class="col-6">
                        <h5 class="text-info">{{ $stats['positions_libres'] }}</h5>
                        <small class="text-muted">Libres</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Structure de la salle -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-sitemap me-2"></i>
                    Structure de la Salle
                </h5>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('admin.travees.create') }}?salle_id={{ $salle->id }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Ajouter une travée
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($salle->travees->count() > 0)
                    <div class="row">
                        @foreach($salle->travees as $travee)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border-success">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-layer-group text-success fa-2x me-3"></i>
                                            <div>
                                                <h6 class="card-title mb-0">{{ $travee->nom }}</h6>
                                                <small class="text-muted">Travée</small>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <span class="badge bg-info">{{ $travee->tablettes->count() }} tablettes</span>
                                        </div>

                                        @if($travee->tablettes->count() > 0)
                                            <div class="tablettes-grid">
                                                @foreach($travee->tablettes->take(6) as $tablette)
                                                    <div class="tablette-mini mb-1">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small><i class="fas fa-table me-1"></i>{{ $tablette->nom }}</small>
                                                            <span class="badge bg-secondary badge-sm">{{ $tablette->positions->count() }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @if($travee->tablettes->count() > 6)
                                                    <small class="text-muted">... et {{ $travee->tablettes->count() - 6 }} autre(s)</small>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="mt-3">
                                            <div class="btn-group btn-group-sm w-100">
                                                <a href="{{ route('admin.travees.show', $travee) }}" class="btn btn-outline-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.travees.edit', $travee) }}" class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('admin.tablettes.create') }}?travee_id={{ $travee->id }}" class="btn btn-outline-success">
                                                    <i class="fas fa-plus"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune travée</h5>
                        <p class="text-muted">Cette salle ne contient encore aucune travée.</p>
                        <a href="{{ route('admin.travees.create') }}?salle_id={{ $salle->id }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Créer la première travée
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .tablette-mini {
        background-color: #f8f9fa;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        border-left: 3px solid #17a2b8;
    }

    .badge-sm {
        font-size: 0.7rem;
    }

    .progress {
        height: 8px;
    }

    .card-body .table-borderless td {
        padding: 0.5rem 0;
        border: none;
    }

    .tablettes-grid {
        max-height: 150px;
        overflow-y: auto;
    }
</style>
@endpush